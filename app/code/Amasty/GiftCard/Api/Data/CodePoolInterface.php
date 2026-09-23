<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface CodePoolInterface extends ExtensibleDataInterface
{
    public const CODE_POOL_ID = 'pool_id';
    public const TITLE = 'title';
    public const TEMPLATE = 'template';
    public const QTY = 'qty';
    public const CODE_POOL_RULE = 'code_pool_rule';

    /**
     * @param int $id
     *
     * @return \Amasty\GiftCard\Api\Data\CodePoolInterface
     */
    public function setCodePoolId(int $id): CodePoolInterface;

    /**
     * @return int
     */
    public function getCodePoolId(): int;

    /**
     * @param string $title
     *
     * @return \Amasty\GiftCard\Api\Data\CodePoolInterface
     */
    public function setTitle(string $title): CodePoolInterface;

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @param string $template
     *
     * @return \Amasty\GiftCard\Api\Data\CodePoolInterface
     */
    public function setTemplate(string $template): CodePoolInterface;

    /**
     * @return string
     */
    public function getTemplate(): string;

    /**
     * @param int $qty
     *
     * @return \Amasty\GiftCard\Api\Data\CodePoolInterface
     */
    public function setQty(int $qty): CodePoolInterface;

    /**
     * @return int
     */
    public function getQty(): int;

    /**
     * @param CodePoolRuleInterface $rule
     *
     * @return \Amasty\GiftCard\Api\Data\CodePoolInterface
     */
    public function setCodePoolRule(
        CodePoolRuleInterface $rule
    ): CodePoolInterface;

    /**
     * @return \Amasty\GiftCard\Api\Data\CodePoolRuleInterface|null
     */
    public function getCodePoolRule();

    /**
     * @return \Amasty\GiftCard\Api\Data\CodePoolExtensionInterface
     */
    public function getExtensionAttributes(): CodePoolExtensionInterface;

    /**
     * @param \Amasty\GiftCard\Api\Data\CodePoolExtensionInterface $extensionAttributes
     *
     * @return \Amasty\GiftCard\Api\Data\CodePoolInterface
     */
    public function setExtensionAttributes(
        ?CodePoolExtensionInterface $extensionAttributes = null
    ): CodePoolInterface;
}
