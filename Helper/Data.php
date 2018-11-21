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
 * @package     Mageplaza_Bannerslider
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\BannerSlider\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\BannerSlider\Model\BannerFactory;
use Mageplaza\BannerSlider\Model\SliderFactory;
use Mageplaza\Core\Helper\AbstractData;

class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mpbannerslider';

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var BannerFactory
     */
    public $bannerFactory;

    /**
     * @var SliderFactory
     */
    public $sliderFactory;

    /**
     * Data constructor.
     *
     * @param DateTime $date
     * @param Context $context
     * @param Filesystem $filesystem
     * @param HttpContext $httpContext
     * @param DirectoryList $directoryList
     * @param BannerFactory $bannerFactory
     * @param SliderFactory $sliderFactory
     * @param StoreManagerInterface $storeManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        DateTime $date,
        Context $context,
        Filesystem $filesystem,
        HttpContext $httpContext,
        DirectoryList $directoryList,
        BannerFactory $bannerFactory,
        SliderFactory $sliderFactory,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager
    )
    {
        $this->date          = $date;
        $this->fileSystem    = $filesystem;
        $this->httpContext   = $httpContext;
        $this->directoryList = $directoryList;
        $this->bannerFactory = $bannerFactory;
        $this->sliderFactory = $sliderFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Retrieve all configuration options for banner slider
     *
     * @return string
     * @throws \Zend_Serializer_Exception
     */
    public function getAllOptions()
    {
        $sliderOptions = '';
        $allConfig     = $this->getModuleConfig('mpbannerslider_design');
        foreach ($allConfig as $key => $value) {
            if ($key == 'item_slider') {
                $sliderOptions = $sliderOptions . $this->getResponseValue();
            } elseif ($key != 'responsive') {
                if (in_array($key, ['autoWidth', 'autoHeight', 'loop', 'nav', 'dots', 'lazyLoad', 'autoplay'])) {
                    $value = $value ? 'true' : 'false';
                }
                $sliderOptions = $sliderOptions . $key . ':' . $value . ',';
            }
        }

        return $sliderOptions;
    }

    /**
     * Retrieve responsive values for banner slider
     *
     * @return string
     * @throws \Zend_Serializer_Exception
     */
    public function getResponseValue()
    {
        $responsiveOptions = '';
        $isResponsive      = $this->getModuleConfig('mpbannerslider_design/responsive') == 1;
        if ($isResponsive) {
            $responsiveConfig = $this->unserialize($this->getModuleConfig('mpbannerslider_design/item_slider'));

            foreach ($responsiveConfig as $config) {
                if ($config['size'] && $config['items']) {
                    $responsiveOptions = $responsiveOptions . $config['size'] . ':{items:' . $config['items'] . '},';
                }
            }

            $responsiveOptions = rtrim($responsiveOptions, ',');

            return 'responsive:{' . $responsiveOptions . '}';
        } else {
            return 'items: 1';
        }
    }

    /**
     * @param null $id
     *
     * @return \Mageplaza\BannerSlider\Model\ResourceModel\Banner\Collection
     */
    public function getBannerCollection($id = null)
    {

        $collection = $this->bannerFactory->create()->getCollection();

        $collection->join(
            ['banner_slider' => $collection->getTable('mageplaza_bannerslider_banner_slider')],
            'main_table.banner_id=banner_slider.banner_id AND banner_slider.slider_id=' . $id,
            ['position']
        );

        $collection->addOrder('position', 'ASC');

        return $collection;
    }

    /**
     * @return Collection
     */
    public function getActiveSliders()
    {
        /** @var Collection $collection */
        $collection = $this->sliderFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_group_ids', ['finset' => $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP)])
            ->addFieldToFilter('status', 1)
            ->addOrder('priority');

        $collection->getSelect()
            ->where('FIND_IN_SET(0, store_ids) OR FIND_IN_SET(?, store_ids)', $this->storeManager->getStore()->getId())
            ->where('from_date is null OR from_date <= ?', $this->date->date())
            ->where('to_date is null OR to_date >= ?', $this->date->date());

        return $collection;
    }

    /**
     * @param $relativePath
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function readFile($relativePath)
    {
        $rootDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::ROOT);

        return $rootDirectory->readFile($relativePath);
    }

    /**
     * Get base template path
     * @return string
     */
    public function getBaseTemplatePath()
    {
        // Get directory of Data.php
        $currentDir = __DIR__;

        // Get root directory(path of magento's project folder)
        $rootPath = $this->directoryList->getRoot();

        $currentDirArr = explode('\\', $currentDir);
        if (count($currentDirArr) == 1) {
            $currentDirArr = explode('/', $currentDir);
        }

        $rootPathArr = explode('/', $rootPath);
        if (count($rootPathArr) == 1) {
            $rootPathArr = explode('\\', $rootPath);
        }

        $basePath = '';
        for ($i = count($rootPathArr); $i < count($currentDirArr) - 1; $i++) {
            $basePath .= $currentDirArr[$i] . '/';
        }

        return $basePath . 'view/base/templates/demo/';
    }
}
