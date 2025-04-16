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

namespace Mageplaza\BannerSlider\Block;

use Exception;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\BannerSlider\Model\HyvaSliderConfigProvider;

class HyvaSlider extends Template
{
    /**
     * @var HyvaSliderConfigProvider
     */
    public HyvaSliderConfigProvider $helperData;
    /**
     * @var FilterProvider
     */
    public FilterProvider $filterProvider;

    /**
     * @param HyvaSliderConfigProvider $helperData
     * @param Context $context
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        HyvaSliderConfigProvider    $helperData,
        Template\Context            $context,
        FilterProvider              $filterProvider,
        array                       $data = []
    )
    {
        $this->helperData = $helperData;
        $this->filterProvider = $filterProvider;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('Mageplaza_BannerSlider::hyvacompat/bannerslider.phtml');
    }

    /**
     * Get Slider Id
     * @return string
     */
    public function getSliderId()
    {
        if ($this->getSlider()) {
            return $this->getSlider()->getSliderId();
        }

        return uniqid('-', false);
    }

    /**
     * @param $content
     * @return string
     * @throws Exception
     */
    public function getPageFilter($content)
    {
        return $this->filterProvider->getPageFilter()->filter($content);
    }

    /**
     * @return array|AbstractCollection
     */
    public function getBannerCollection()
    {
        $collection = [];
        if ($this->getSliderId()) {
            $collection = $this->helperData->getBannerCollection($this->getSliderId())->addFieldToFilter('status', 1);
        }

        return $collection;
    }

    /**
     * @return false|string
     */
    public function getBannerOptions()
    {
        return $this->helperData->getBannerOptions($this->getSlider());
    }


    /**
     * @return array|mixed
     */
    public function isLazyLoad()
    {
        if ($this->getSlider()->getDesign()) {
            return $this->getSlider()->getData('lazyLoad');
        }
        return $this->helperData->getMpHelper()->getModuleConfig('mpbannerslider_design/lazyLoad');
    }
}
