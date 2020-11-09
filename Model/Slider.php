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
use Mageplaza\BannerSlider\Model\ResourceModel\Banner\Collection;
use Mageplaza\BannerSlider\Model\ResourceModel\Banner\CollectionFactory;

/**
 * @method Slider setName($name)
 * @method Slider setDescription($description)
 * @method Slider setStatus($status)
 * @method Slider setConfigSerialized($configSerialized)
 * @method mixed getName()
 * @method mixed getDescription()
 * @method mixed getStatus()
 * @method mixed getConfigSerialized()
 * @method Slider setCreatedAt(string $createdAt)
 * @method string getCreatedAt()
 * @method Slider setUpdatedAt(string $updatedAt)
 * @method string getUpdatedAt()
 * @method Slider setBannersData(array $data)
 * @method array getBannersData()
 * @method Slider setIsChangedBannerList(bool $flag)
 * @method bool getIsChangedBannerList()
 * @method Slider setAffectedBannerIds(array $ids)
 * @method bool getAffectedBannerIds()
 */
class Slider extends AbstractModel implements IdentityInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_bannerslider_slider';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_bannerslider_slider';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_bannerslider_slider';

    /**
     * Banner Collection
     *
     * @var Collection
     */
    protected $bannerCollection;

    /**
     * Banner Collection Factory
     *
     * @var CollectionFactory
     */
    protected $bannerCollectionFactory;

    /**
     * constructor
     *
     * @param CollectionFactory $bannerCollectionFactory
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        CollectionFactory $bannerCollectionFactory,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
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
        $this->_init('Mageplaza\BannerSlider\Model\ResourceModel\Slider');
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
        $values['status'] = '1';

        return $values;
    }

    /**
     * @return array|mixed
     */
    public function getBannersPosition()
    {
        if (!$this->getId()) {
            return [];
        }

        $array = $this->getData('banners_position');
        if ($array === null) {
            $array = $this->getResource()->getBannersPosition($this);
            $this->setData('banners_position', $array);
        }

        return $array;
    }

    /**
     * @return Collection
     */
    public function getSelectedBannersCollection()
    {
        if ($this->bannerCollection === null) {
            $collection = $this->bannerCollectionFactory->create();
            $collection->getSelect()->join(
                ['banner_slider' => $this->getResource()->getTable('mageplaza_bannerslider_banner_slider')],
                'main_table.banner_id=banner_slider.banner_id AND banner_slider.slider_id=' . $this->getId(),
                ['position']
            );
            $this->bannerCollection = $collection;
        }

        return $this->bannerCollection;
    }
}
