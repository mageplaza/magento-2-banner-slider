<?php
/**
 * Mageplaza_BetterSlider extension
 *                     NOTICE OF LICENSE
 * 
 *                     This source file is subject to the Mageplaza License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 * 
 *                     @category  Mageplaza
 *                     @package   Mageplaza_BetterSlider
 *                     @copyright Copyright (c) 2016
 *                     @license   https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\BetterSlider\Model;

/**
 * @method Slider setName($name)
 * @method Slider setDescription($description)
 * @method Slider setStatus($status)
 * @method Slider setConfigSerialized($configSerialized)
 * @method mixed getName()
 * @method mixed getDescription()
 * @method mixed getStatus()
 * @method mixed getConfigSerialized()
 * @method Slider setCreatedAt(\string $createdAt)
 * @method string getCreatedAt()
 * @method Slider setUpdatedAt(\string $updatedAt)
 * @method string getUpdatedAt()
 * @method Slider setBannersData(array $data)
 * @method array getBannersData()
 * @method Slider setIsChangedBannerList(\bool $flag)
 * @method bool getIsChangedBannerList()
 * @method Slider setAffectedBannerIds(array $ids)
 * @method bool getAffectedBannerIds()
 */
class Slider extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     * 
     * @var string
     */
    const CACHE_TAG = 'mageplaza_betterslider_slider';

    /**
     * Cache tag
     * 
     * @var string
     */
    protected $_cacheTag = 'mageplaza_betterslider_slider';

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_betterslider_slider';

    /**
     * Banner Collection
     * 
     * @var \Mageplaza\BetterSlider\Model\ResourceModel\Banner\Collection
     */
    protected $bannerCollection;

    /**
     * Banner Collection Factory
     * 
     * @var \Mageplaza\BetterSlider\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $bannerCollectionFactory;

    /**
     * constructor
     * 
     * @param \Mageplaza\BetterSlider\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Mageplaza\BetterSlider\Model\ResourceModel\Banner\CollectionFactory $bannerCollectionFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\BetterSlider\Model\ResourceModel\Slider');
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
        $values = [];

        return $values;
    }
    /**
     * @return array|mixed
     */
    public function getBannersPosition()
    {
        if (!$this->getId()) {
            return array();
        }
        $array = $this->getData('banners_position');
        if (is_null($array)) {
            $array = $this->getResource()->getBannersPosition($this);
            $this->setData('banners_position', $array);
        }
        return $array;
    }

    /**
     * @return \Mageplaza\BetterSlider\Model\ResourceModel\Banner\Collection
     */
    public function getSelectedBannersCollection()
    {
        if (is_null($this->bannerCollection)) {
            $collection = $this->bannerCollectionFactory->create();
            $collection->join(
                'mageplaza_betterslider_banner_slider',
                'main_table.banner_id=mageplaza_betterslider_banner_slider.banner_id AND mageplaza_betterslider_banner_slider.slider_id='.$this->getId(),
                ['position']
            );
            $this->bannerCollection = $collection;
        }
        return $this->bannerCollection;
    }
}
