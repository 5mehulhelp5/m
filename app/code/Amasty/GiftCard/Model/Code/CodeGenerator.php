<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Code;

use Amasty\GiftCard\Api\Data\CodeInterface;
use Amasty\GiftCard\Model\Code\ResourceModel\Code as CodeResource;
use Amasty\GiftCard\Model\OptionSource\Status;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;

class CodeGenerator
{
    public const MAX_FILE_SIZE = 2500000;
    public const ALLOWED_FILE_EXTENSIONS = ['csv'];

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var File
     */
    private $ioFile;

    /**
     * @var CodeResource
     */
    private $codeResource;

    /**
     * @var int|null
     */
    private $codePoolId;

    /**
     * @var array
     */
    protected $generatedCodes = [];

    /**
     * @var array
     */
    protected $existingCodes = [];

    /**
     * @var array
     */
    private $templateMasksList = [];

    /**
     * @var CodeTemplateValidator|null
     */
    private $codeTemplateValidator;

    public function __construct(
        Repository $repository,
        Filesystem $filesystem,
        Escaper $escaper,
        File $ioFile,
        CodeResource $codeResource,
        ?int $codePoolId = null,
        ?CodeTemplateValidator $codeTemplateValidator = null
    ) {
        $this->repository = $repository;
        $this->filesystem = $filesystem;
        $this->escaper = $escaper;
        $this->ioFile = $ioFile;
        $this->codeResource = $codeResource;
        $this->codePoolId = $codePoolId;
        $this->codeTemplateValidator = $codeTemplateValidator
            ?? ObjectManager::getInstance()->get(CodeTemplateValidator::class);
    }

    public function generateCodes(string $template, int $qty)
    {
        $template = (string)$this->escaper->escapeHtml($template);
        $this->codeTemplateValidator->validateTemplate($template, $qty);
        $this->initializeByTemplate($template);
        for ($i = 0; $i < $qty; $i++) {
            $code = $this->generateCode($template);
            $this->generatedCodes[] = $code;
        }

        $this->saveGeneratedCodes();
        $this->clear();
    }

    public function generateCodesFromCsv(array $file)
    {
        $this->validateFile($file);
        $this->initializeByFile();
        $directoryReader = $this->filesystem->getDirectoryRead(DirectoryList::SYS_TMP);

        if (!$directoryReader->isExist($file['tmp_name'])) {
            return;
        }
        $stream = $directoryReader->openFile($file['tmp_name']);

        while ($csvLine = $stream->readCsv()) {
            if (!$csvLine) {
                continue;
            }
            $code = array_shift($csvLine);

            if (!$this->isCodeExist($code)) {
                $this->generatedCodes[] = $code;
            }
        }
        $this->saveGeneratedCodes();
        $this->clear();
    }

    /**
     * @throws LocalizedException
     */
    private function initializeByTemplate(string $template)
    {
        if (!$this->codePoolId) {
            throw new LocalizedException(__('Please specify Code Pool ID before codes generation.'));
        }
        $this->existingCodes = $this->codeTemplateValidator->getCodesByTemplate($template);
        $this->templateMasksList = $this->codeTemplateValidator->getTemplateMasksList($template);
    }

    /**
     * @throws LocalizedException
     */
    private function initializeByFile()
    {
        if (!$this->codePoolId) {
            throw new LocalizedException(__('Please specify Code Pool ID before codes generation.'));
        }
        $dbExistingCodes = $this->repository->getAllCodes();

        if ($this->existingCodes) {
            $this->existingCodes = array_merge($this->existingCodes, $dbExistingCodes);
        } else {
            $this->existingCodes = $dbExistingCodes;
        }
    }

    private function generateCode(string $template): string
    {
        $code = $template;

        foreach ($this->templateMasksList as $templateSymbol) {
            $possibleValues = CodeTemplateValidator::MASK[$templateSymbol];
            $symbol = (string)$possibleValues[array_rand($possibleValues)];
            $code = preg_replace('/' . preg_quote($templateSymbol, '/') . '/', $symbol, $code, 1);
        }

        if ($this->isCodeExist($code)) {
            return $this->generateCode($template);
        }

        return $code;
    }

    private function isCodeExist(string $code): bool
    {
        return in_array($code, $this->existingCodes) || in_array($code, $this->generatedCodes);
    }

    /**
     * @throws LocalizedException
     */
    private function validateFile(array $file)
    {
        if (!in_array($this->ioFile->getPathInfo($file['name'])['extension'], self::ALLOWED_FILE_EXTENSIONS)) {
            throw new LocalizedException(__('Wrong file extension. Please use only .csv files.'));
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new LocalizedException(__('The file size is too big.'));
        }
    }

    private function saveGeneratedCodes()
    {
        $insertData = [];

        foreach ($this->generatedCodes as $code) {
            $insertData[] = [
                CodeInterface::CODE => $code,
                CodeInterface::CODE_POOL_ID => $this->codePoolId,
                CodeInterface::STATUS => Status::AVAILABLE
            ];
        }
        $this->codeResource->insertMultipleCodes($insertData);
    }

    /**
     * Clears generated codes storage
     */
    private function clear()
    {
        $this->existingCodes = array_merge($this->generatedCodes, $this->existingCodes);
        $this->generatedCodes = [];
    }
}
