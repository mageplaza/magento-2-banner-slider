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
namespace Mageplaza\BetterSlider\Model\ResourceModel;

class Banner extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Date model
     * 
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * Slider relation model
     * 
     * @var string
     */
    protected $bannerSliderTable;

    /**
     * Event Manager
     * 
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * constructor
     * 
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        $this->date         = $date;
        $this->eventManager = $eventManager;
        parent::__construct($context);
        $this->bannerSliderTable = $this->getTable('mageplaza_betterslider_banner_slider');
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_betterslider_banner', 'banner_id');
    }

    /**
     * Retrieves Banner Name from DB by passed id.
     *
     * @param string $id
     * @return string|bool
     */
    public function getBannerNameById($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'name')
            ->where('banner_id = :banner_id');
        $binds = ['banner_id' => (int)$id];
        return $adapter->fetchOne($select, $binds);
    }
    /**
     * before save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\Mageplaza\BetterSlider\Model\Banner $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->date->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->date->date());
        }
        return parent::_beforeSave($object);
    }
    /**
     * after save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\Mageplaza\BetterSlider\Model\Banner $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->saveSliderRelation($object);
        return parent::_afterSave($object);
    }

    /**
     * @param \Mageplaza\BetterSlider\Model\Banner $banner
     * @return array
     */
    public function getSlidersPosition(\Mageplaza\BetterSlider\Model\Banner $banner)
    {
        $select = $this->getConnection()->select()->from(
            $this->bannerSliderTable,
            ['slider_id', 'position']
        )
        ->where(
            'banner_id = :banner_id'
        );
        $bind = ['banner_id' => (int)$banner->getId()];
        return $this->getConnection()->fetchPairs($select, $bind);
    }

    /**
     * @param \Mageplaza\BetterSlider\Model\Banner $banner
     * @return $this
     */
    protected function saveSliderRelation(\Mageplaza\BetterSlider\Model\Banner $banner)
    {
        $banner->setIsChangedSliderList(false);
        $id = $banner->getId();
        $sliders = $banner->getSlidersData();
        if ($sliders === null) {
            return $this;
        }
        $oldSliders = $banner->getSlidersPosition();
        $insert = array_diff_key($sliders, $oldSliders);
        $delete = array_diff_key($oldSliders, $sliders);
        $update = array_intersect_key($sliders, $oldSliders);
        $_update = array();
        foreach ($update as $key=>$settings) {
            if (isset($oldSliders[$key]) && $oldSliders[$key] != $settings['position']) {
                $_update[$key] = $settings;
            }
        }
        $update = $_update;
        $adapter = $this->getConnection();
        if (!empty($delete)) {
            $condition = ['slider_id IN(?)' => array_keys($delete), 'banner_id=?' => $id];
            $adapter->delete($this->bannerSliderTable, $condition);
        }
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $sliderId => $position) {
                $data[] = [
                    'banner_id' => (int)$id,
                    'slider_id' => (int)$sliderId,
                    'position' => (int)$position['position']
                ];
            }
            $adapter->insertMultiple($this->bannerSliderTable, $data);
        }
        if (!empty($update)) {
            foreach ($update as $sliderId => $position) {
                $where = ['banner_id = ?' => (int)$id, 'slider_id = ?' => (int)$sliderId];
                $bind = ['position' => (int)$position['position']];
                $adapter->update($this->bannerSliderTable, $bind, $where);
            }
        }
        if (!empty($insert) || !empty($delete)) {
            $sliderIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'mageplaza_betterslider_banner_change_sliders',
                ['banner' => $banner, 'slider_ids' => $sliderIds]
            );
        }
        if (!empty($insert) || !empty($update) || !empty($delete)) {
            $banner->setIsChangedSliderList(true);
            $sliderIds = array_keys($insert + $delete + $update);
            $banner->setAffectedSliderIds($sliderIds);
        }
        return $this;
    }
}
