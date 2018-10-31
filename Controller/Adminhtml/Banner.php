<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_BannerSlider
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BannerSlider\Controller\Adminhtml;

/**
 * Class Banner
 * @package Mageplaza\BannerSlider\Controller\Adminhtml
 */
abstract class Banner extends \Magento\Backend\App\Action
{
    /**
     * Banner Factory
     * 
     * @var \Mageplaza\BannerSlider\Model\BannerFactory
     */
    protected $bannerFactory;

    /**
     * Core registry
     * 
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Result redirect factory
     * 
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */

    /**
     * constructor
     * 
     * @param \Mageplaza\BannerSlider\Model\BannerFactory $bannerFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mageplaza\BannerSlider\Model\BannerFactory $bannerFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->bannerFactory         = $bannerFactory;
        $this->coreRegistry          = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init Banner
     *
     * @return \Mageplaza\BannerSlider\Model\Banner
     */
    protected function initBanner()
    {
        $bannerId  = (int) $this->getRequest()->getParam('banner_id');
        /** @var \Mageplaza\BannerSlider\Model\Banner $banner */
        $banner    = $this->bannerFactory->create();
        if ($bannerId) {
            $banner->load($bannerId);
        }
        $this->coreRegistry->register('mpbannerslider_banner', $banner);

        return $banner;
    }
}
