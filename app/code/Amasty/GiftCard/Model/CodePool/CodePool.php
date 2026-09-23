<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\CodePool;

use Amasty\GiftCard\Api\Data\CodePoolExtensionInterface;
use Amasty\GiftCard\Api\Data\CodePoolInterface;
use Amasty\GiftCard\Api\Data\CodePoolInterfaceExtensionInterface;
use Amasty\GiftCard\Api\Data\CodePoolRuleInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class CodePool extends AbstractExtensibleModel implements CodePoolInterface
{
    public const DATA_PERSISTOR_KEY = 'amgcard_codepool';

    /**
     * @var string
     */
    protected $_eventPrefix = 'amasty_codepool';

    /**
     * @var string
     */
    protected $_eventObject = 'code_pool';

    public function _construct()
    {
        $this->_init(ResourceModel\CodePool::class);
        $this->setIdFieldName(CodePoolInterface::CODE_POOL_ID);
    }

    public function setCodePoolId(int $id): CodePoolInterface
    {
        return $this->setData(CodePoolInterface::CODE_POOL_ID, $id);
    }

    public function getCodePoolId(): int
    {
        return (int)$this->_getData(CodePoolInterface::CODE_POOL_ID);
    }

    public function setTitle(string $title): CodePoolInterface
    {
        return $this->setData(CodePoolInterface::TITLE, $title);
    }

    public function getTitle(): string
    {
        return $this->_getData(CodePoolInterface::TITLE);
    }

    public function setTemplate(string $template): CodePoolInterface
    {
        return $this->setData(CodePoolInterface::TEMPLATE, $template);
    }

    public function getTemplate(): string
    {
        return $this->_getData(CodePoolInterface::TEMPLATE);
    }

    public function setQty(int $qty): CodePoolInterface
    {
        return $this->setData(CodePoolInterface::QTY, $qty);
    }

    public function getQty(): int
    {
        return (int)$this->_getData(CodePoolInterface::QTY);
    }

    public function setCodePoolRule(CodePoolRuleInterface $rule): CodePoolInterface
    {
        return $this->setData(CodePoolInterface::CODE_POOL_RULE, $rule);
    }

    public function getCodePoolRule()
    {
        return $this->_getData(CodePoolInterface::CODE_POOL_RULE);
    }

    public function getExtensionAttributes(): CodePoolExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    public function setExtensionAttributes(
        ?CodePoolExtensionInterface $extensionAttributes = null
    ): CodePoolInterface {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
