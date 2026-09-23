<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Filesystem;

use Amasty\ImportCore\Api\Action\FileUploaderInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Action\DataPrepare\Validation\TerminateValidator;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use Amasty\ImportCore\Import\Source\SourceDataStructure;
use Magento\CatalogImportExport\Model\Import\Uploader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\TargetDirectory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Math\Random;
use Magento\MediaStorage\Helper\File\Storage;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\Validator\NotProtectedExtension;

abstract class FileUploader extends Uploader implements FileUploaderInterface
{
    private const HASH_ALGORITHM = 'sha256';

    /**
     * Filename of last uploaded by url file
     *
     * @var string|null
     */
    private ?string $newFileName = null;

    /**
     * @var array
     */
    private array $uploadedFiles = [];

    /**
     * @var array
     */
    private array $fileFields = [];

    /**
     * @var EntityConfigProvider
     */
    private EntityConfigProvider $entityConfigProvider;

    /**
     * @var TerminateValidator
     */
    private TerminateValidator $terminateValidator;

    public function __construct(
        Database $coreFileStorageDb,
        Storage $coreFileStorage,
        AdapterFactory $imageFactory,
        NotProtectedExtension $validator,
        Filesystem $filesystem,
        Filesystem\File\ReadFactory $readFactory,
        EntityConfigProvider $entityConfigProvider,
        TerminateValidator $terminateValidator,
        $filePath = null,
        ?Random $random = null,
        ?TargetDirectory $targetDirectory = null
    ) {
        parent::__construct(
            $coreFileStorageDb,
            $coreFileStorage,
            $imageFactory,
            $validator,
            $filesystem,
            $readFactory,
            $filePath,
            $random,
            $targetDirectory
        );
        $this->entityConfigProvider = $entityConfigProvider;
        $this->terminateValidator = $terminateValidator;
    }

    public function initialize(ImportProcessInterface $importProcess): void
    {
        $this->init();
        $this->setTmpDir(
            trim((string)$importProcess->getProfileConfig()->getImagesFileDirectory(), DIRECTORY_SEPARATOR)
        );
        $this->setDestDir($importProcess->getEntityConfig()->getFileUploaderConfig()->getStoragePath());
    }

    /**
     * Overriding original method to process file uploads by url
     *
     * @param string $fileName
     * @param bool $renameFileOff
     *
     * @return array|mixed
     * @throws LocalizedException
     */
    public function move($fileName, $renameFileOff = false)
    {
        if ($this->isUpdate($fileName)) {
            return ['file' => $fileName];
        }
        $fileName = ltrim($fileName, DIRECTORY_SEPARATOR);
        $this->validateFileName($fileName);

        $this->newFileName = null;
        if (!filter_var($fileName, FILTER_VALIDATE_URL)) {
            return parent::move($fileName, $renameFileOff);
        }

        $hash = $this->getFileHash($fileName);
        if (isset($this->uploadedFiles[$hash])) {
            return $this->uploadedFiles[$hash];
        }
        $this->newFileName = $this->getFilenameByUrl($fileName);

        return $this->uploadedFiles[$hash] = parent::move($fileName, $renameFileOff);
    }

    /**
     * Overriding original method to process file uploads by url
     *
     * @param string $destinationFolder
     * @param string|null $newFileName
     *
     * @return array|bool
     * @throws \Exception
     */
    public function save($destinationFolder, $newFileName = null)
    {
        return parent::save($destinationFolder, $this->newFileName ?: $newFileName);
    }

    protected function processEntityData(
        ImportProcessInterface $importProcess,
        array &$entityData,
        string $entityCode,
        bool $processSubEntities = true
    ): void {
        foreach ($entityData as &$row) {
            foreach ($this->getIsFileFields($entityCode) as $uploadRow) {
                if (empty($row[$uploadRow])) {
                    continue;
                }
                $this->processRow($importProcess, $row, $uploadRow, $processSubEntities);
            }
        }
    }

    private function processRow(
        ImportProcessInterface $importProcess,
        array &$row,
        string $uploadRow,
        bool $processSubEntities
    ): void {
        try {
            $uploadedFile = $this->move($row[$uploadRow], true);
            $row[$uploadRow] = $uploadedFile['file'] ?? '';
            if ($processSubEntities && !empty($row[SourceDataStructure::SUB_ENTITIES_DATA_KEY])) {
                foreach ($row[SourceDataStructure::SUB_ENTITIES_DATA_KEY] as $subEntityCode => &$data) {
                    $this->processEntityData($importProcess, $data, $subEntityCode);
                }
            }
        } catch (LocalizedException $e) {
            $row[$uploadRow] = '';
            if ($this->terminateValidator->isNeedToTerminate($importProcess)) {
                $importProcess->getImportResult()->terminateImport(true);
                $importProcess->addErrorMessage($e->getMessage());

                return;
            }
            $importProcess->addWarningMessage($e->getMessage());
        }
    }

    private function getIsFileFields(string $entityCode): array
    {
        if (array_key_exists($entityCode, $this->fileFields)) {
            return $this->fileFields[$entityCode];
        }

        $result = [];
        foreach ($this->entityConfigProvider->get($entityCode)->getFieldsConfig()->getFields() as $field) {
            if ($field->isFile()) {
                $result[] = $field->getName();
            }
        }
        $this->fileFields[$entityCode] = $result;

        return $this->fileFields[$entityCode];
    }

    private function getFilenameByUrl(string $url): string
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
        $parsedUrlPath = parse_url($url, PHP_URL_PATH);
        $urlPathValues = explode('/', $parsedUrlPath);

        return preg_replace('/[^a-z0-9._-]+/i', '', end($urlPathValues));
    }

    private function getFileHash(string $path): string
    {
        return hash_file(self::HASH_ALGORITHM, $path);
    }

    /**
     * @throws LocalizedException
     */
    private function validateFileName(string $fileName): void
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
        if (!pathinfo($fileName, PATHINFO_EXTENSION)) {
            throw new LocalizedException(
                __(' The file \'%1\' is missing a file extension.', $fileName)
            );
        }

        if (!filter_var($fileName, FILTER_VALIDATE_URL)) {
            $fullFilePath = $this->_directory->getAbsolutePath($this->getTmpDir() . DIRECTORY_SEPARATOR . $fileName);
            if (!$this->_directory->isExist($fullFilePath) && !$this->isUpdate($fileName)) {
                throw new LocalizedException(
                    __('File \'%1\' was not found or has read restriction.', $fileName)
                );
            }
        }
    }

    private function isUpdate(string $fileName): bool
    {
        $fullFilePath = $this->_directory->getAbsolutePath($this->getTmpDir() . DIRECTORY_SEPARATOR . $fileName);
        $distFilePath = $this->_directory->getAbsolutePath($this->getDestDir() . DIRECTORY_SEPARATOR . $fileName);

        return !$this->_directory->isExist($fullFilePath) && $this->_directory->isExist($distFilePath);
    }
}
