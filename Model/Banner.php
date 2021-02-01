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

namespace Mageplaza\BannerSlider\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Mageplaza\BannerSlider\Model\Config\Source\Image as configImage;
use Mageplaza\BannerSlider\Model\ResourceModel\Banner as ResourceBanner;
use Mageplaza\BannerSlider\Model\ResourceModel\Slider\Collection;
use Mageplaza\BannerSlider\Model\ResourceModel\Slider\CollectionFactory as sliderCollectionFactory;

/**
 * @method Banner setName($name)
 * @method Banner setUploadFile($uploadFile)
 * @method Banner setUrl($url)
 * @method Banner setType($type)
 * @method Banner setStatus($status)
 * @method mixed getName()
 * @method mixed getUploadFile()
 * @method mixed getUrl()
 * @method mixed getType()
 * @method mixed getStatus()
 * @method Banner setCreatedAt(string $createdAt)
 * @method string getCreatedAt()
 * @method Banner setUpdatedAt(string $updatedAt)
 * @method string getUpdatedAt()
 * @method Banner setSlidersData(array $data)
 * @method array getSlidersData()
 * @method Banner setSlidersIds(array $sliderIds)
 * @method array getSlidersIds()
 * @method Banner setIsChangedSliderList(bool $flag)
 * @method bool getIsChangedSliderList()
 * @method Banner setAffectedSliderIds(array $ids)
 * @method bool getAffectedSliderIds()
 */
class Banner extends AbstractModel implements IdentityInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_bannerslider_banner';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_bannerslider_banner';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_bannerslider_banner';

    /**
     * Slider Collection
     *
     * @var Collection
     */
    protected $sliderCollection;

    /**
     * Slider Collection Factory
     *
     * @var sliderCollectionFactory
     */
    protected $sliderCollectionFactory;

    /**
     * @var configImage
     */
    protected $imageModel;

    /**
     * Banner constructor.
     *
     * @param sliderCollectionFactory $sliderCollectionFactory
     * @param Context $context
     * @param Registry $registry
     * @param configImage $configImage
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        sliderCollectionFactory $sliderCollectionFactory,
        Context $context,
        Registry $registry,
        configImage $configImage,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->sliderCollectionFactory = $sliderCollectionFactory;
        $this->imageModel = $configImage;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceBanner::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        return ['status' => '1', 'type' => '0'];
    }

    /**
     * @return ResourceModel\Slider\Collection
     */
    public function getSelectedSlidersCollection()
    {
        if ($this->sliderCollection === null) {
            /** @var Collection $collection */
            $collection = $this->sliderCollectionFactory->create();
            $collection->getSelect()->join(
                ['banner_slider' => $this->getResource()->getTable('mageplaza_bannerslider_banner_slider')],
                'main_table.slider_id=banner_slider.slider_id AND banner_slider.banner_id=' . $this->getId(),
                ['position']
            );
            $collection->addFieldToFilter('status', 1);

            $this->sliderCollection = $collection;
        }

        return $this->sliderCollection;
    }

    /**
     * get full image url
     * @return string
     */
    public function getImageUrl()
    {
        return $this->imageModel->getBaseUrl() . $this->getImage();
    }

    /**
     * @return array
     */
    public function getSliderIds()
    {
        if (!$this->hasData('slider_ids')) {
            $ids = $this->getResource()->getSliderIds($this);

            $this->setData('slider_ids', $ids);
        }

        return (array)$this->getData('slider_ids');
    }
}
