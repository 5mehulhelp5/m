<?php
/**
 * Copyright © CyberSolutions LLC. All rights reserved.
 */

namespace CyberSolutionsLLC\RobotsExtended\Plugin;

use Magento\Catalog\Controller\Product\View as ProductViewController;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use CyberSolutionsLLC\RobotsExtended\Helper\Config as ConfigHelper;

/**
 * 301 redirect from /catalog/product/view/ system URLs to canonical SEO URLs.
 */
class ProductUrlRedirectPlugin
{
	/** @var UrlFinderInterface */
	private $urlFinder;

	/** @var StoreManagerInterface */
	private $storeManager;

	/** @var RedirectFactory */
	private $redirectFactory;

	/** @var ConfigHelper */
	private $configHelper;

	public function __construct(
		UrlFinderInterface    $urlFinder,
		StoreManagerInterface $storeManager,
		RedirectFactory       $redirectFactory,
		ConfigHelper          $configHelper
	) {
		$this->urlFinder = $urlFinder;
		$this->storeManager = $storeManager;
		$this->redirectFactory = $redirectFactory;
		$this->configHelper = $configHelper;
	}

	/**
	 * Redirect /catalog/product/view/ requests to the canonical product URL.
	 *
	 * Uses aroundExecute so Magento's response lifecycle (FPC, session, logging)
	 * is respected — no exit() required.
	 *
	 * @param ProductViewController $subject
	 * @param callable $proceed
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function aroundExecute(ProductViewController $subject, callable $proceed)
	{
		if (!$this->configHelper->isEnabled()) {
			return $proceed();
		}

		// Use getRequestUri() (original browser URL), not getPathInfo() which
		// Magento rewrites internally even for clean .html product URLs.
		$request = $subject->getRequest();
		if (strpos($request->getRequestUri(), '/catalog/product/view/') === false) {
			return $proceed();
		}

		$productId = (int)$request->getParam('id');
		if (!$productId) {
			return $proceed();
		}

		$storeId = (int)$this->storeManager->getStore()->getId();

		// REDIRECT_TYPE = 0 fetches the active canonical rewrite, not old 301/302 entries.
		$rewrite = $this->urlFinder->findOneByData([
			UrlRewrite::ENTITY_TYPE   => 'product',
			UrlRewrite::ENTITY_ID     => $productId,
			UrlRewrite::STORE_ID      => $storeId,
			UrlRewrite::REDIRECT_TYPE => 0,
		]);

		if ($rewrite) {
			$canonicalPath = $rewrite->getRequestPath();

			// Avoid redirect loops if the rewrite itself points to a system URL.
			if (strpos($canonicalPath, 'catalog/product/view') !== false) {
				return $proceed();
			}

			$baseUrl = $this->storeManager->getStore()->getBaseUrl();
			$canonicalUrl = rtrim($baseUrl, '/') . '/' . ltrim($canonicalPath, '/');

			$redirect = $this->redirectFactory->create();
			$redirect->setUrl($canonicalUrl);
			$redirect->setHttpResponseCode(301);
			return $redirect;
		}

		return $proceed();
	}
}
