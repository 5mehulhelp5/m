<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Config\SchemaReader;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Config\Dom;
use Magento\Framework\Config\Dom\ValidationException as DomValidationException;
use Magento\Framework\Config\Dom\UrnResolver;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\Reader\Filesystem as FilesystemReader;
use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Phrase;
use Magento\Framework\View\TemplateEngine\Xhtml\CompilerInterface;

class Reader extends FilesystemReader
{
    /**
     * @var string
     */
    private $schemaContent;

    /**
     * @var ReadInterface
     */
    private $rootDirectory;

    /**
     * @var UrnResolver
     */
    private $urnResolver;

    /**
     * @var CompilerInterface
     */
    private $compiler;

    public function __construct(
        FileResolverInterface $fileResolver,
        ConverterInterface $converter,
        SchemaLocatorInterface $schemaLocator,
        ValidationStateInterface $validationState,
        CompilerInterface $compiler,
        Filesystem $filesystem,
        UrnResolver $urnResolver,
        $fileName,
        $idAttributes = [],
        $domDocumentClass = Dom::class,
        $defaultScope = 'global'
    ) {
        $this->urnResolver = $urnResolver;
        $this->rootDirectory = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->compiler = $compiler;
        $this->schemaContent = $this->prepareSchemaContent($schemaLocator->getSchema());

        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }

    protected function _readFiles($fileList)
    {
        /** @var Dom $configMerger */
        $configMerger = null;
        foreach ($fileList as $key => $content) {
            try {
                $content = $this->processDocument($content);

                if (!$configMerger) {
                    $configMerger = $this->_createConfigMerger($this->_domDocumentClass, $content);
                } else {
                    $configMerger->merge($content);
                }
            } catch (DomValidationException $e) {
                throw new LocalizedException(
                    new Phrase("Invalid XML in file %1:\n%2", [$key, $e->getMessage()])
                );
            }
        }
        $this->validateMergedContent($configMerger);

        return $this->prepareOutput($configMerger);
    }

    private function validateMergedContent(Dom $configMerger): Reader
    {
        if ($this->validationState->isValidationRequired()) {
            $errors = [];
            if ($configMerger && !$configMerger->validate($this->_schemaFile, $errors)) {
                $message = "Invalid Document \n";
                throw new LocalizedException(
                    new Phrase($message . implode("\n", $errors))
                );
            }
        }

        return $this;
    }

    private function prepareOutput(Dom $configMerger): array
    {
        $output = [];
        if ($configMerger) {
            $dom = $configMerger->getDom();
            if (!empty($this->schemaContent)) {
                $dom->schemaValidateSource($this->schemaContent, LIBXML_SCHEMA_CREATE);
            }
            $output = $this->_converter->convert($dom);
        }

        return $output;
    }

    private function processDocument(string $xml): string
    {
        $object = new DataObject();
        $document = new \DOMDocument();
        // Prevent checking xml schema
        libxml_use_internal_errors(true);
        $document->loadXML($xml);
        $this->compiler->compile($document->documentElement, $object, $object);
        libxml_use_internal_errors(false);

        return $document->saveXML();
    }

    private function prepareSchemaContent(string $schemaPath): string
    {
        try {
            $schemaContent = $this->rootDirectory->readFile($schemaPath);
            $this->replaceUrn(
                '/[\"\'](urn:[a-zA-Z]*:module:[A-Za-z0-9\_]*:.+)[\"\']/i',
                $schemaContent
            );
            $this->replaceUrn(
                '/[\"\'](urn:[a-zA-Z]*:framework[A-Za-z\-]*:.+)[\"\']/',
                $schemaContent
            );

            return $schemaContent;
        } catch (LocalizedException $e) {
            return '';
        }
    }

    private function replaceUrn(string $pattern, string &$schemaContent): Reader
    {
        if (preg_match_all($pattern, $schemaContent, $matches)) {
            foreach ($matches[1] as $urn) {
                $schemaContent = str_replace($urn, $this->urnResolver->getRealPath($urn), $schemaContent);
            }
        }

        return $this;
    }
}
