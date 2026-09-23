<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Plugin\Config\Model\Config\Structure;

use Amasty\GiftCardAccount\Model\GiftCardExtension\Quote\AllowedTotalModifiersPool;
use Magento\Config\Model\Config\ScopeDefiner;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Config\Model\Config\Structure;
use Magento\Config\Model\Config\Structure\ElementInterface;
use Magento\Framework\Module\Manager;

class AddAllowedSubtotalModifiers
{
    public const AMGCARD_ID = 'amgiftcard';
    public const DEFAULT_EXPECTED_SORTORDER = 5;

    /**
     * @var AllowedTotalModifiersPool
     */
    private $allowedTotalModifiersPool;

    /**
     * @var ScopeDefiner
     */
    private $scopeDefiner;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var array
     */
    private $fieldPaths = [];

    /**
     * Flag to prevent re-building of structure
     *
     * @var bool
     */
    private $isBuilt = false;

    public function __construct(
        AllowedTotalModifiersPool $allowedTotalModifiersPool,
        ScopeDefiner $scopeDefiner,
        Manager $moduleManager
    ) {
        $this->allowedTotalModifiersPool = $allowedTotalModifiersPool;
        $this->scopeDefiner = $scopeDefiner;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetElementByPathParts(
        Structure $subject,
        ElementInterface $result,
        array $pathParts
    ): ElementInterface {
        $moduleSection = $result->getData();
        if (!isset($moduleSection['id']) || $moduleSection['id'] !== self::AMGCARD_ID || $this->isBuilt) {
            return $result;
        }
        if (empty($this->allowedTotalModifiersPool->getAll())) {
            $this->isBuilt = true;
            return $result;
        }

        $moduleChildes = &$moduleSection['children']['general']['children'];
        $sortOrder = (int)($moduleChildes['allow_to_paid_for_tax']['sortOrder'] ?? self::DEFAULT_EXPECTED_SORTORDER);
        foreach ($this->allowedTotalModifiersPool->getAll() as $moduleName => $moduleConfig) {
            if (!$this->moduleManager->isEnabled($moduleName)) {
                continue;
            }
            foreach ($moduleConfig as $fieldConfig) {
                $moduleChildes[$fieldConfig['id']] = $this->buildField($fieldConfig, ++$sortOrder);
            }
        }
        uasort($moduleChildes, static function ($first, $second) {
            return (int)($first['sortOrder'] ?? 0) <=> (int)($second['sortOrder'] ?? 0);
        });
        $result->setData($moduleSection, $this->scopeDefiner->getScope());
        $this->isBuilt = true;

        return $result;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetFieldPaths(Structure $subject, array $result): array
    {
        return array_merge($result, $this->fieldPaths);
    }

    private function buildField(array $config, int $sortOrder): array
    {
        $path = 'amgiftcard/general/' . $config['id'];
        $this->fieldPaths[$path][] = $path;

        return [
            'id' => $config['id'],
            'translate' => 'label',
            'type' => 'select',
            'sortOrder' => (string)$sortOrder,
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => $config['label'],
            'source_model' => Yesno::class,
            '_elementType' => 'field',
            'path' => 'amgiftcard/general'
        ];
    }
}
