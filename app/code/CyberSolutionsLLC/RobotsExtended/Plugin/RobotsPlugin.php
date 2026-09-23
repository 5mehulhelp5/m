<?php
/**
 * Copyright © CyberSolutions LLC. All rights reserved.
 */

namespace CyberSolutionsLLC\RobotsExtended\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use CyberSolutionsLLC\RobotsExtended\Helper\Config as ConfigHelper;

/**
 * Plugin to set custom robots meta tags based on URL patterns
 */
class RobotsPlugin
{
	/**
	 * @var RequestInterface
	 */
	private $request;

	/**
	 * @var ConfigHelper
	 */
	private $configHelper;

	/**
	 * @param RequestInterface $request
	 * @param ConfigHelper $configHelper
	 */
	public function __construct(
		RequestInterface $request,
		ConfigHelper     $configHelper
	)
	{
		$this->request = $request;
		$this->configHelper = $configHelper;
	}

	/**
	 * Set custom robots meta tags based on URL patterns
	 *
	 * @param PageConfig $subject
	 * @param string $result
	 * @return string
	 */
	public function afterGetRobots(PageConfig $subject, $result)
	{
		// Check if module is enabled
		if (!$this->configHelper->isEnabled()) {
			return $result;
		}

		$requestUri = $this->request->getRequestUri();
		$pathInfo = $this->request->getPathInfo();

		// Check for NOINDEX,FOLLOW URL paths
		if ($this->matchesPath($pathInfo, $this->configHelper->getNoindexFollowPaths())) {
			return 'NOINDEX,FOLLOW';
		}

		// Check for filtered and sorted URLs
		if ($this->matchesParameter($requestUri, $this->configHelper->getFilterParams())) {
			return 'NOINDEX,FOLLOW';
		}

		// Check for NOINDEX,NOFOLLOW URL paths
		if ($this->matchesPath($pathInfo, $this->configHelper->getNoindexNofollowPaths())) {
			return 'NOINDEX,NOFOLLOW';
		}

		return $result;
	}

	/**
	 * Check if path matches any configured paths
	 *
	 * @param string $pathInfo
	 * @param array $paths
	 * @return bool
	 */
	private function matchesPath(string $pathInfo, array $paths): bool
	{
		foreach ($paths as $path) {
			if (strpos($pathInfo, $path) !== false) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Check if request URI contains any configured parameters
	 *
	 * @param string $requestUri
	 * @param array $params
	 * @return bool
	 */
	private function matchesParameter(string $requestUri, array $params): bool
	{
		foreach ($params as $param) {
			if (strpos($requestUri, $param . '=') !== false) {
				return true;
			}
		}
		return false;
	}
}
