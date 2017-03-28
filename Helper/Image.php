<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\BetterSlider\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Mageplaza\BetterSlider\Model\BannerFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;

/**
 * Catalog image helper
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Image extends AbstractHelper
{
	protected $_width;
	protected $_height;
	protected $storeManager;

	protected $subDir = 'mageplaza/betterslider/banner/image';
	/**
	 * @var \Magento\Framework\Filesystem
	 */
	protected $fileSystem;

	public function __construct(
		Context $context,
		BannerFactory $bannerFactory,
		Filesystem $fileSystem,
		StoreManagerInterface $storeManager

	)
	{
		$this->fileSystem    = $fileSystem;
		$this->bannerFactory = $bannerFactory;
		$this->storeManager  = $storeManager;
		parent::__construct($context);
	}

	public function getBaseUrl()
	{
		return $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . $this->subDir . '/image/';
	}


	public function getBaseMediaPath()
	{
		return 'mageplaza/betterslider/banner';
	}

	/**
	 * @return string
	 */
	public function getBaseMediaUrl()
	{
		return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $this->subDir;
	}

	public function getMediaUrl($file)
	{
		return $this->getBaseMediaUrl() . '/' . $this->_prepareFile($file);
	}

	/**
	 * @param string $file
	 * @return string
	 */
	public function getMediaPath($file)
	{
		return $this->getBaseMediaPath() . '/' . $this->_prepareFile($file);
	}

	protected function _prepareFile($file)
	{
		return ltrim(str_replace('\\', '/', $file), '/');
	}

}
