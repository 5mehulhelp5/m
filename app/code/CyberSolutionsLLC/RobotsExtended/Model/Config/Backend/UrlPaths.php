<?php
/**
 * Copyright © CyberSolutions LLC. All rights reserved.
 */

namespace CyberSolutionsLLC\RobotsExtended\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Backend model for URL paths configuration
 */
class UrlPaths extends Value
{
	/**
	 * @var TypeListInterface
	 */
	protected $cacheTypeList;

	/**
	 * @param Context $context
	 * @param Registry $registry
	 * @param ScopeConfigInterface $config
	 * @param TypeListInterface $cacheTypeList
	 * @param AbstractResource|null $resource
	 * @param AbstractDb|null $resourceCollection
	 * @param array $data
	 */
	public function __construct(
		Context              $context,
		Registry             $registry,
		ScopeConfigInterface $config,
		TypeListInterface    $cacheTypeList,
		?AbstractResource    $resource = null,
		?AbstractDb          $resourceCollection = null,
		array                $data = []
	)
	{
		$this->cacheTypeList = $cacheTypeList;
		parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
	}

	/**
	 * Process data before saving
	 *
	 * @return $this
	 */
	public function beforeSave()
	{
		$value = $this->getValue();

		// Trim whitespace and remove empty lines
		if (is_string($value)) {
			$lines = explode("\n", $value);
			$lines = array_map('trim', $lines);
			$lines = array_filter($lines, function ($line) {
				return !empty($line);
			});

			// Ensure paths start with / and end with /
			$lines = array_map(function ($line) {
				// Add leading / if missing
				if (strpos($line, '/') !== 0) {
					$line = '/' . $line;
				}
				// Add trailing / if missing
				if (substr($line, -1) !== '/') {
					$line .= '/';
				}
				return $line;
			}, $lines);

			$this->setValue(implode("\n", $lines));
		}

		return parent::beforeSave();
	}

	/**
	 * Clear full page cache after saving
	 *
	 * @return $this
	 */
	public function afterSave()
	{
		$this->cacheTypeList->invalidate(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
		return parent::afterSave();
	}
}
