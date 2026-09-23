<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Source\Type\Xml;

use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Api\Source\SourceDataStructureInterface;
use Amasty\ImportCore\Api\Source\SourceReaderInterface;
use Amasty\ImportCore\Import\FileResolver\FileResolverAdapter;
use Amasty\ImportCore\Import\Source\Data\DataStructureProvider;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\ReadInterface as FileReader;
use Magento\Framework\Xml\ParserFactory;
use Magento\Framework\XsltProcessor\XsltProcessorFactory;

class Reader implements SourceReaderInterface
{
    public const TYPE_ID = 'xml';

    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * @var \SimpleXMLElement
     */
    private $document;

    /**
     * @var string
     */
    private $xmlContent;

    /**
     * @var \Generator
     */
    private $generator;

    /**
     * @var \SimpleXMLElement[]|\SimpleXMLElement
     */
    private $entityNodes;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var FileResolverAdapter
     */
    private $fileResolverAdapter;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var array
     */
    private $pathParts;

    /**
     * @var DataStructureProvider
     */
    private $dataStructureProvider;

    /**
     * @var SourceDataStructureInterface
     */
    private $dataStructure;

    /**
     * @var ParserFactory
     */
    private $parserFactory;

    /**s
     * @var XsltProcessorFactory
     */
    private $xsltProcessorFactory;

    public function __construct(
        FileResolverAdapter $fileResolverAdapter,
        Filesystem $filesystem,
        DataStructureProvider $dataStructureProvider,
        ParserFactory $parserFactory,
        XsltProcessorFactory $xsltProcessorFactory
    ) {
        $this->fileResolverAdapter = $fileResolverAdapter;
        $this->filesystem = $filesystem;
        $this->dataStructureProvider = $dataStructureProvider;
        $this->parserFactory = $parserFactory;
        $this->xsltProcessorFactory = $xsltProcessorFactory;
    }

    public function initialize(ImportProcessInterface $importProcess)
    {
        $fileName = $this->fileResolverAdapter->getFileResolver(
            $importProcess->getProfileConfig()->getFileResolverType()
        )->execute($importProcess);
        $this->config = $importProcess->getProfileConfig()->getExtensionAttributes()->getXmlSource();

        $directoryRead = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->fileReader = $directoryRead->openFile($fileName);
        $this->pathParts = explode('/', $this->config->getItemPath());

        $xmlParser = $this->parserFactory->create();
        $this->xmlContent = $this->fileReader->readAll();
        $xml = $xmlParser->loadXML($this->xmlContent);

        if ($xslTemplate = $this->config->getXslTemplate()) {
            $xslParser = $this->parserFactory->create();
            $xsl = $xslParser->loadXML($xslTemplate)->getDom();

            $xslProc = $this->xsltProcessorFactory->create();
            $xslProc->importStylesheet($xsl);
            $this->xmlContent = $xslProc->transformToDoc($xml->getDom())->saveXml();

            $xmlParser = $this->parserFactory->create();
            $xml = $xmlParser->loadXML($this->xmlContent);
        }
        $xmlData = $xml->xmlToArray();

        foreach ($this->pathParts as $path) {
            if (isset($xmlData[$path])) {
                $xmlData = $xmlData[$path];
            } elseif (isset($xmlData['_value'][$path])) {
                $xmlData = $xmlData['_value'][$path];
            } else {
                throw new \RuntimeException(__('Wrong Item XPath.')->getText());
            }
        }
        $this->dataStructure = $this->dataStructureProvider->getDataStructure(
            $importProcess->getEntityConfig(),
            $importProcess->getProfileConfig()
        );
    }

    public function estimateRecordsCount(): int
    {
        if ($this->document === null) {
            $this->initDocument();
        }

        return $this->entityNodes ? count($this->entityNodes) : 0;
    }

    public function readRow()
    {
        if ($this->document === null) {
            $this->initDocument();
        }
        $row = $this->generator->current();

        if (!is_array($row)) {
            return false;
        }
        $this->generator->next();

        if ($this->isRowEmpty($row)) {
            return $this->readRow();
        }
        $row = $this->parseSubEntities($row, $this->dataStructure);

        return $row;
    }

    protected function parseSubEntities(array $entity, SourceDataStructureInterface $dataStructure): array
    {
        $formattedEntity = [];
        $fields = $dataStructure->getFields();

        foreach ($entity as $key => $row) {
            if (!empty($row) && ($row instanceof \SimpleXMLElement)) {
                continue;
            }
            if (in_array($key, $fields)) {
                $formattedEntity[$key] = (string)$row;
            }
        }

        foreach ($dataStructure->getSubEntityStructures() as $subEntityStructure) {
            $subEntities = $entity[$subEntityStructure->getMap()] ?? null;
            if ($subEntities && $subEntities instanceof \SimpleXMLElement) {
                $subEntities = $subEntities->xpath(end($this->pathParts));
                if ($subEntities) {
                    foreach ($subEntities as $subEntity) {
                        $formattedEntity[$subEntityStructure->getMap()][] = $this->parseSubEntities(
                            (array)$subEntity,
                            $subEntityStructure
                        );
                    }
                } else {
                    $formattedEntity[$subEntityStructure->getMap()] = []; //empty tag
                }
            }
        }

        return $formattedEntity;
    }

    protected function initDocument()
    {
        $pathParts = $this->pathParts;
        unset($pathParts[0]);
        $itemsXpath = implode('/', $pathParts);

        $this->document = new \SimpleXMLElement($this->xmlContent);
        $this->generator = $this->fetchRecord($itemsXpath);

        if (!$this->generator->valid()) {
            throw new \RuntimeException('Wrong file content.');
        }
    }

    /**
     * @param string|null $xpath xpath expression for entity node
     * @return \Generator
     */
    protected function fetchRecord($xpath = null): \Generator
    {
        if ($xpath) {
            $this->entityNodes = $this->document->xpath($xpath);
        } else {
            $this->entityNodes = $this->document;
        }

        foreach ($this->entityNodes as $entityNode) {
            yield (array)$entityNode;
        }
    }

    private function isRowEmpty(array $row): bool
    {
        return empty(array_filter($row));
    }
}
