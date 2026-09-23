<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Source\Type\Xml;

use Amasty\ImportCore\Api\Source\SourceGeneratorInterface;
use Amasty\ImportCore\Import\Config\ProfileConfig;
use Amasty\ImportCore\Import\Utils\Type\Xml\Row\Converter;

class Generator implements SourceGeneratorInterface
{
    /**
     * @var Converter
     */
    private $rowConverter;

    public function __construct(
        Converter $rowConverter
    ) {
        $this->rowConverter = $rowConverter;
    }

    public function generate(ProfileConfig $profileConfig, array $data): string
    {
        list($header, $footer) = $this->getHeaderFooter($profileConfig);
        $output = $this->rowConverter->convert($profileConfig, $data);

        $xmlContent = "<?xml version=\"1.0\"?>\n{$header}{$output}{$footer}";
        $xml = new \SimpleXMLElement($xmlContent);

        return $xml->saveXML();
    }

    public function getExtension(): string
    {
        return 'xml';
    }

    private function getHeaderFooter(ProfileConfig $profileConfig): array
    {
        $itemPath = $profileConfig->getExtensionAttributes()->getXmlSource()->getItemPath();
        $itemPathParts = explode('/', $itemPath);
        array_pop($itemPathParts);
        $header = $footer = '';
        foreach ($itemPathParts as $path) {
            $header .= "<{$path}>\n";
            $footer .= "</{$path}>\n";
        }

        return [$header, $footer];
    }
}
