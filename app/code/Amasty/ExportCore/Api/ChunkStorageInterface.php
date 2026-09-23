<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api;

interface ChunkStorageInterface
{
    public function saveChunk(array $data, int $chunkId): \Amasty\ExportCore\Api\ChunkStorageInterface;
    public function readChunk(int $chunkId): array;
    public function chunkSize(int $chunkId): int;
    public function deleteChunk(int $chunkId): \Amasty\ExportCore\Api\ChunkStorageInterface;
    public function getAllChunkIds(): array;
}
