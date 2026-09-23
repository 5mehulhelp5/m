<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Test\Unit\Export\Parallelization;

use Amasty\ExportCore\Export\Parallelization\ChunkStorage;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use Magento\Framework\Filesystem\File\WriteInterface as FileWriteInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface as DirectoryWriteInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Amasty\ExportCore\Export\Parallelization\ChunkStorage
 */
class ChunkStorageTest extends TestCase
{
    /**
     * @var ChunkStorage
     */
    private $chunkStorage;

    /**
     * @var TmpFileManagement|MockObject
     */
    private $tmpMock;

    /**
     * @var DirectoryWriteInterface|MockObject
     */
    private $tempDirectoryMock;

    /**
     * @var FileWriteInterface|MockObject
     */
    private $tempFileMock;

    protected function setUp(): void
    {
        $processIdentity = 'string';
        $this->tmpMock = $this->createMock(TmpFileManagement::class);
        $this->tempDirectoryMock = $this->createMock(DirectoryWriteInterface::class);
        $this->tempFileMock = $this->createMock(FileWriteInterface::class);
        $this->tmpMock->expects($this->once())
            ->method('getTempDirectory')
            ->with($processIdentity)
            ->willReturn($this->tempDirectoryMock);

        $this->chunkStorage = new ChunkStorage($this->tmpMock, $processIdentity);
    }

    public function testSaveChunk()
    {
        $chunkId = 1;
        $json = '{"a":1,"b":2,"c":3}';
        $outputFile = 2;
        $arr = ['a' => 1, 'b' => 2, 'c' => 3];
        $chunk = 'chunk_1';

        $this->tempDirectoryMock->expects($this->once())
            ->method('openFile')
            ->with($chunk)
            ->willReturn($this->tempFileMock);
        $this->tempFileMock->expects($this->once())
            ->method('write')
            ->with($json)
            ->willReturn($outputFile);
        $this->tempFileMock->expects($this->once())
            ->method('close')
            ->willReturn(true);

        $this->assertSame($this->chunkStorage, $this->chunkStorage->saveChunk($arr, $chunkId));
    }

    /**
     * @param array $statSize
     * @param int $chunkSize
     * @dataProvider chunkSizeDataProvider
     */
    public function testChunkSize(array $statSize, int $chunkSize)
    {
        $chunkId = 1;
        $chunk = 'chunk_1';

        $this->tempDirectoryMock->expects($this->once())
            ->method('stat')
            ->with($chunk)
            ->willReturn($statSize);

        $this->assertEquals($chunkSize, $this->chunkStorage->chunkSize($chunkId));
    }

    public function testReadChunk()
    {
        $chunkId = 1;
        $chunk = 'chunk_1';
        $json = '{"a":1,"b":2,"c":3}';
        $arr = ['a' => 1, 'b' => 2, 'c' => 3];

        $this->tempDirectoryMock->expects($this->once())
            ->method('readFile')
            ->with($chunk)
            ->willReturn($json);

        $this->assertEquals($arr, $this->chunkStorage->readChunk($chunkId));
    }

    /**
     * @param array $fileNameList
     * @param array $chunkIds
     * @dataProvider getAllChunkIdsDataProvider
     */
    public function testGetAllChunkIds(array $fileNameList, array $chunkIds)
    {
        $this->tempDirectoryMock->expects($this->any())
            ->method('read')
            ->willReturn($fileNameList);

        $this->assertEquals($chunkIds, $this->chunkStorage->getAllChunkIds());
    }

    public function testDeleteChunk()
    {
        $chunkId = 1;
        $chunk = 'chunk_1';

        $this->tempDirectoryMock->expects($this->once())
            ->method('delete')
            ->with($chunk);

        $this->assertSame($this->chunkStorage, $this->chunkStorage->deleteChunk($chunkId));
    }

    /**
     * Data provider for testChunkSize
     *
     * @return array
     */
    public function chunkSizeDataProvider(): array
    {
        return [
            'chunkHasSize' => [['size' => 5000], 5000],
            'chunkHasNoSize' => [['size' => null], 0]
        ];
    }

    /**
     * Data provider for testGetAllChunkIds
     *
     * @return array
     */
    public function getAllChunkIdsDataProvider(): array
    {
        return [
            'chunkIds' => [['chunk_1'], [1]],
            'noChunkIds' => [['not_suitable_filename'], []]
        ];
    }
}
