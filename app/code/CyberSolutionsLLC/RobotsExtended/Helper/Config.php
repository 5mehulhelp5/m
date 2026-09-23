<?php
/**
 * Copyright © CyberSolutions LLC. All rights reserved.
 */

namespace CyberSolutionsLLC\RobotsExtended\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Configuration helper for RobotsExtended module
 */
class Config extends AbstractHelper
{
	/**
	 * Configuration paths
	 */
	const XML_PATH_ENABLED = 'robots_extended/general/enabled';
	const XML_PATH_NOINDEX_FOLLOW_PATHS = 'robots_extended/noindex_follow/url_paths';
	const XML_PATH_FILTER_PARAMS = 'robots_extended/noindex_follow/filter_params';
	const XML_PATH_NOINDEX_NOFOLLOW_PATHS = 'robots_extended/noindex_nofollow/url_paths';

	/**
	 * @param Context $context
	 */
	public function __construct(Context $context)
	{
		parent::__construct($context);
	}

	/**
	 * Check if module is enabled
	 *
	 * @param int|null $storeId
	 * @return bool
	 */
	public function isEnabled($storeId = null): bool
	{
		return (bool)$this->scopeConfig->getValue(
			self::XML_PATH_ENABLED,
			ScopeInterface::SCOPE_STORE,
			$storeId
		);
	}

	/**
	 * Get NOINDEX,FOLLOW URL paths from configuration
	 *
	 * @param int|null $storeId
	 * @return array
	 */
	public function getNoindexFollowPaths($storeId = null): array
	{
		return $this->parseConfigValue(self::XML_PATH_NOINDEX_FOLLOW_PATHS, $storeId);
	}

	/**
	 * Get filter parameters from configuration
	 *
	 * @param int|null $storeId
	 * @return array
	 */
	public function getFilterParams($storeId = null): array
	{
		return $this->parseConfigValue(self::XML_PATH_FILTER_PARAMS, $storeId);
	}

	/**
	 * Get NOINDEX,NOFOLLOW URL paths from configuration
	 *
	 * @param int|null $storeId
	 * @return array
	 */
	public function getNoindexNofollowPaths($storeId = null): array
	{
		return $this->parseConfigValue(self::XML_PATH_NOINDEX_NOFOLLOW_PATHS, $storeId);
	}

	/**
	 * Parse configuration value into array
	 *
	 * @param string $configPath
	 * @param int|null $storeId
	 * @return array
	 */
	private function parseConfigValue(string $configPath, $storeId = null): array
	{
		$value = $this->scopeConfig->getValue(
			$configPath,
			ScopeInterface::SCOPE_STORE,
			$storeId
		);

		if (empty($value)) {
			return [];
		}

		// Split by newline and trim each value
		$values = explode("\n", $value);
		$values = array_map('trim', $values);

		// Remove empty values
		$values = array_filter($values, function ($item) {
			return !empty($item);
		});

		return array_values($values);
	}
}
