<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Test\Unit\Model\Code;

use Amasty\GiftCard\Model\Code\Code;
use Amasty\GiftCard\Model\Code\CodeGenerator;
use Amasty\GiftCard\Model\Code\CodeTemplateValidator;
use Amasty\GiftCard\Model\Code\Repository;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Escaper;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @see CodeGenerator
 */
class CodeGeneratorTest extends TestCase
{
    /**
     * @var CodeGenerator
     */
    private $codeGenerator;

    /**
     * @var Repository|MockObject
     */
    private $codeRepository;

    /**
     * @var Filesystem|MockObject
     */
    private $filesystem;

    /**
     * @var Escaper|MockObject
     */
    private $escaper;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var CodeTemplateValidator|MockObject
     */
    private $codeTemplateValidator;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
    }

    protected function initCodeGenerator($codePoolId = null)
    {
        $this->codeRepository = $this->createPartialMock(
            Repository::class,
            [
                'getCodesByTemplate',
                'getCodesCountByTemplate',
                'getEmptyCodeModel',
                'save',
                'getAllCodes'
            ]
        );
        $this->filesystem = $this->createPartialMock(Filesystem::class, ['getDirectoryRead']);
        $this->escaper = $this->createPartialMock(Escaper::class, ['escapeHtml']);
        $this->codeTemplateValidator = $this->createMock(CodeTemplateValidator::class);
        $ioFile = $this->createPartialMock(File::class, []);

        $codeResource = $this->createMock(\Amasty\GiftCard\Model\Code\ResourceModel\Code::class);

        $this->codeGenerator = $this->objectManager->getObject(
            CodeGenerator::class,
            [
                'repository' => $this->codeRepository,
                'filesystem' => $this->filesystem,
                'escaper' => $this->escaper,
                'ioFile' => $ioFile,
                'codeResource' => $codeResource,
                'codePoolId' => $codePoolId,
                'codeTemplateValidator' => $this->codeTemplateValidator
            ]
        );
    }

    public function testGenerateCodes()
    {
        $this->initCodeGenerator(1);
        $template = 'TEST_{D}{L}';
        $qty = 5;

        $this->initEscaperMock($template);

        $this->codeTemplateValidator->expects($this->once())
            ->method('getCodesByTemplate')
            ->with($template)
            ->willReturn([]);
        $this->codeTemplateValidator->expects($this->any())
            ->method('getTemplateMasksList')
            ->with($template)
            ->willReturn([
                '{D}',
                '{L}'
            ]);

        $this->codeGenerator->generateCodes($template, $qty);
    }

    public function testGenerateCodesWithoutCodePool()
    {
        $this->initCodeGenerator();
        $template = 'TEST_{L}';
        $qty = 1;
        $this->initEscaperMock($template);

        $this->expectExceptionMessage('Please specify Code Pool ID before codes generation');
        $this->codeGenerator->generateCodes($template, $qty);
    }

    public function testGenerateCodesFromCsv()
    {
        $this->initCodeGenerator(1);
        $file = [
            'name' => 'test.csv',
            'size' => 2000,
            'tmp_name' => 'tmp.csv'
        ];
        $this->codeRepository->expects($this->once())->method('getAllCodes')->willReturn([]);

        $stream = $this->createPartialMock(\Magento\Framework\Filesystem\File\Read::class, ['readCsv']);
        $stream->method('readCsv')->willReturn(['TEST_CODE1'], ['TEST_CODE2'], ['TEST_CODE3'], null);

        $reader = $this->createPartialMock(Read::class, ['isExist', 'openFile']);
        $reader->expects($this->once())->method('isExist')->with($file['tmp_name'])
            ->willReturn(true);
        $reader->expects($this->once())->method('openFile')->with($file['tmp_name'])
            ->willReturn($stream);

        $this->filesystem->expects($this->once())->method('getDirectoryRead')
            ->with(DirectoryList::SYS_TMP)
            ->willReturn($reader);

        $code = $this->createPartialMock(Code::class, []);
        $this->codeRepository->expects($this->any())->method('getEmptyCodeModel')->willReturn($code);

        $this->codeGenerator->generateCodesFromCsv($file);
    }

    /**
     * @dataProvider generateCodesFromCsvWrongFileDataProvider
     */
    public function testGenerateCodesFromCsvWrongFile($file, $exceptionMessage)
    {
        $this->initCodeGenerator(1);

        $this->expectExceptionMessage($exceptionMessage);
        $this->codeGenerator->generateCodesFromCsv($file);
    }

    /**
     * @param string $template
     *
     * @throws \ReflectionException
     */
    private function initEscaperMock($template)
    {
        $this->escaper->expects($this->once())->method('escapeHtml')->with($template)->willReturn($template);
    }

    /**
     * @return array
     */
    public function generateCodesFromCsvWrongFileDataProvider()
    {
        return [
            [
                [
                    'name' => 'test.jpg',
                    'size' => 200
                ],
                'Wrong file extension. Please use only .csv files.'
            ],
            [
                [
                    'name' => 'test.csv',
                    'size' => CodeGenerator::MAX_FILE_SIZE + 1
                ],
                'The file size is too big.'
            ]
        ];
    }
}
