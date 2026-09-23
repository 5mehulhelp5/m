<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Test\Integration\Traits;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem;
use Magento\TestFramework\Helper\Bootstrap;
use Laminas\Stdlib\Parameters;

trait ImageUpload
{
    private function prepareCustomImage(string $inputName): array
    {
        $objectManager = Bootstrap::getObjectManager();

        $tmpWriter = $objectManager->create(Filesystem::class)->getDirectoryWrite(DirectoryList::TMP);
        $absolutePath = $tmpWriter->getAbsolutePath();
        if (!$tmpWriter->isDirectory($absolutePath)) {
            $tmpWriter->create($absolutePath);
        }

        $img = imagecreatetruecolor(300, 300);
        $color = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, 300, 300, $color);
        imagejpeg($img, $absolutePath . 'test.jpg', 100);

        $fileArray = [
            'name' => 'test.jpg',
            'tmp_name' => $tmpWriter->getAbsolutePath('test.jpg'),
            'type' => 'image/jpg',
            'size' =>  500,
            'error' => 0
        ];
        $_FILES = [
            $inputName => $fileArray
        ];
        /** @var RequestInterface $request */
        $request = $objectManager->get(RequestInterface::class);
        $request->setFiles(new Parameters($_FILES));

        return $fileArray;
    }
}
