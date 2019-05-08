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

namespace Mageplaza\BannerSlider\Model\ResourceModel\Slider;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zend_Db_Select;

/**
 * Class Collection
 * @package Mageplaza\BannerSlider\Model\ResourceModel\Slider
 */
class Collection extends AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'slider_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_bannerslider_slider_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'slider_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\BannerSlider\Model\Slider', 'Mageplaza\BannerSlider\Model\ResourceModel\Slider');
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);

        return $countSelect;
    }

    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     *
     * @return array
     */
    protected function _toOptionArray($valueField = 'slider_id', $labelField = 'name', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * add if filter
     *
     * @param $sliderIds
     *
     * @return $this
     */
    public function addIdFilter($sliderIds)
    {
        $condition = '';

        if (is_array($sliderIds)) {
            if (!empty($sliderIds)) {
                $condition = ['in' => $sliderIds];
            }
        } elseif (is_numeric($sliderIds)) {
            $condition = $sliderIds;
        } elseif (is_string($sliderIds)) {
            $ids = explode(',', $sliderIds);
            if (empty($ids)) {
                $condition = $sliderIds;
            } else {
                $condition = ['in' => $ids];
            }
        }

        if ($condition !== '') {
            $this->addFieldToFilter('slider_id', $condition);
        }

        return $this;
    }

    /**
     * @param $customerGroup
     * @param $storeId
     *
     * @return $this
     */
    public function addActiveFilter($customerGroup = null, $storeId = null)
    {
        $this->addFieldToFilter('status', true)->setOrder('priority', Select::SQL_ASC);

        if (isset($customerGroup)) {
            $this->getSelect()
                ->where('FIND_IN_SET(0, customer_group_ids) OR FIND_IN_SET(?, customer_group_ids)', $customerGroup);
        }

        if (isset($storeId)) {
            $this->getSelect()
                ->where('FIND_IN_SET(0, store_ids) OR FIND_IN_SET(?, store_ids)', $storeId);
        }

        return $this;
    }
}
