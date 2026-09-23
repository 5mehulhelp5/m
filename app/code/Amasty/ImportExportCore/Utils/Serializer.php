<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import-Export Core for Magento 2 (System)
 */

namespace Amasty\ImportExportCore\Utils;

use Amasty\ImportExportCore\Utils\Internal\ArrayToObjectConvert;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Webapi\ServiceOutputProcessor;

class Serializer
{
    /**
     * @var ServiceOutputProcessor
     */
    private $serviceOutputProcessor;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ArrayToObjectConvert
     */
    private $arrayToObjectConvert;

    public function __construct(
        ServiceOutputProcessor $serviceOutputProcessor,
        ArrayToObjectConvert $arrayToObjectConvert,
        DataObjectHelper $dataObjectHelper,
        Json $jsonSerializer
    ) {
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->jsonSerializer = $jsonSerializer;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->arrayToObjectConvert = $arrayToObjectConvert;
    }

    /**
     * Serializes object of specified type into string
     *
     * @param object $object
     * @param string $type
     * @return string
     */
    public function serialize($object, string $type): string
    {
        return $this->jsonSerializer->serialize(
            $this->convertObjectToArray($object, $type)
        );
    }

    /**
     * Unserializes string into object of specified type
     *
     * @param string $serialized
     * @param string $type
     * @return mixed
     * @throws \Magento\Framework\Exception\SerializationException
     */
    public function unserialize(string $serialized, string $type)
    {
        return $this->arrayToObjectConvert->convertValue(
            $this->convertSerializedToArray($serialized),
            $type
        );
    }

    /**
     * Converts object into array
     *
     * @param object $object
     * @param string $type
     * @return array|object
     */
    public function convertObjectToArray($object, string $type)
    {
        return $this->serviceOutputProcessor->convertValue($object, $type);
    }

    /**
     * Converts serialized string into array
     *
     * @param string $serialized
     * @return array|bool|float|int|mixed|string|null
     */
    public function convertSerializedToArray(string $serialized)
    {
        return $this->jsonSerializer->unserialize($serialized);
    }
}
