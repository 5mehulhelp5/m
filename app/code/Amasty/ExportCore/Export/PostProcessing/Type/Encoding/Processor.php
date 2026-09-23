<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\PostProcessing\Type\Encoding;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\PostProcessing\ProcessorInterface;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use Magento\Framework\Filesystem\Directory\WriteInterface as DirectoryWriteInterface;
use Magento\Framework\Filesystem\File\WriteInterface as FileWriteInterface;

class Processor implements ProcessorInterface
{
    public const TMP_FILE_SUFFIX = '.encoded';
    public const BUFFER_SIZE = 1024 * 1024 * 20;

    /**
     * @var DirectoryWriteInterface
     */
    private $tmpDirectory;

    /**
     * @var string
     */
    private $origFilename;

    /**
     * @var FileWriteInterface
     */
    private $inputFile;

    /**
     * @var FileWriteInterface
     */
    private $outputFile;

    /**
     * @var TmpFileManagement
     */
    private $tmp;

    public function __construct(
        TmpFileManagement $tmp
    ) {
        $this->tmp = $tmp;
    }

    public function process(ExportProcessInterface $exportProcess): ProcessorInterface
    {
        $encoding = $exportProcess->getProfileConfig()->getExtensionAttributes()->getEncoding();
        if ($encoding && $encoding !== ConfigInterface::DEFAULT_ENCODING) {
            $exportProcess->addInfoMessage('Started encoding the file.');

            $this->init($exportProcess);
            while (!$this->inputFile->eof()) {
                $this->outputFile->write(
                    mb_convert_encoding(
                        $this->inputFile->read(self::BUFFER_SIZE),
                        $encoding,
                        ConfigInterface::DEFAULT_ENCODING
                    )
                );
            }
            $this->finalize();
        }

        return $this;
    }

    private function init(ExportProcessInterface $exportProcess): void
    {
        $this->tmpDirectory = $this->tmp->getTempDirectory($exportProcess->getIdentity());
        $this->origFilename = $this->tmp->getResultTempFileName($exportProcess->getIdentity());
        $this->tmpDirectory->touch($this->origFilename . self::TMP_FILE_SUFFIX);

        $this->inputFile = $this->tmpDirectory->openFile($this->origFilename, 'r');
        $this->outputFile = $this->tmpDirectory->openFile($this->origFilename . self::TMP_FILE_SUFFIX, 'w');
    }

    private function finalize()
    {
        $this->inputFile->close();
        $this->inputFile = null;

        $this->outputFile->close();
        $this->outputFile = null;

        $this->tmpDirectory->delete($this->origFilename);
        $this->tmpDirectory->renameFile($this->origFilename . self::TMP_FILE_SUFFIX, $this->origFilename);
    }
}
