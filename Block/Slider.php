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
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\BannerSlider\Helper\Data as bannerHelper;

/**
 * Class Slider
 * @package Mageplaza\BannerSlider\Block
 */
class Slider extends Template
{
    /**
     * @type bannerHelper
     */
    public $helperData;

    /**
     * @type StoreManagerInterface
     */
    protected $store;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var FilterProvider
     */
    public $filterProvider;

    /**
     * Slider constructor.
     *
     * @param Template\Context $context
     * @param bannerHelper $helperData
     * @param CustomerRepositoryInterface $customerRepository
     * @param DateTime $dateTime
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        bannerHelper $helperData,
        CustomerRepositoryInterface $customerRepository,
        DateTime $dateTime,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->customerRepository = $customerRepository;
        $this->store = $context->getStoreManager();
        $this->_date = $dateTime;
        $this->filterProvider = $filterProvider;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('Mageplaza_BannerSlider::bannerslider.phtml');
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
     *
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
        return $this->helperData->getModuleConfig('mpbannerslider_design/lazyLoad') || $this->getSlider()->getData('lazyLoad');
    }
}
