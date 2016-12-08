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
namespace Mageplaza\BetterSlider\Block\Adminhtml\Banner\Edit\Tab;

class Slider extends \Magento\Backend\Block\Widget\Grid\Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Slider collection factory
     * 
     * @var \Mageplaza\BetterSlider\Model\ResourceModel\Slider\CollectionFactory
     */
    protected $sliderCollectionFactory;

    /**
     * Registry
     * 
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Slider factory
     * 
     * @var \Mageplaza\BetterSlider\Model\SliderFactory
     */
    protected $sliderFactory;

    /**
     * constructor
     * 
     * @param \Mageplaza\BetterSlider\Model\ResourceModel\Slider\CollectionFactory $sliderCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Mageplaza\BetterSlider\Model\SliderFactory $sliderFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Mageplaza\BetterSlider\Model\ResourceModel\Slider\CollectionFactory $sliderCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Mageplaza\BetterSlider\Model\SliderFactory $sliderFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    )
    {
        $this->sliderCollectionFactory = $sliderCollectionFactory;
        $this->coreRegistry            = $coreRegistry;
        $this->sliderFactory           = $sliderFactory;
        parent::__construct($context, $backendHelper, $data);
    }


    /**
     * Set grid params
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('slider_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getBanner()->getId()) {
            $this->setDefaultFilter(['in_sliders'=>1]);
        }
    }

    /**
     * prepare the collection

     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var \Mageplaza\BetterSlider\Model\ResourceModel\Slider\Collection $collection */
        $collection = $this->sliderCollectionFactory->create();
        if ($this->getBanner()->getId()) {
            $constraint = 'related.banner_id='.$this->getBanner()->getId();
        } else {
            $constraint = 'related.banner_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('mageplaza_betterslider_banner_slider')),
            'related.slider_id=main_table.slider_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_sliders',
            [
                'header_css_class'  => 'a-center',
                'type'   => 'checkbox',
                'name'   => 'in_slider',
                'values' => $this->_getSelectedSliders(),
                'align'  => 'center',
                'index'  => 'slider_id'
            ]
        );
        $this->addColumn(
            'slider_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'slider_id',
                'type' => 'number',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'title',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'name'   => 'position',
                'width'  => 60,
                'type'   => 'number',
                'validate_class' => 'validate-number',
                'index' => 'position',
                'editable'  => true,
            ]
        );
        return $this;
    }

    /**
     * Retrieve selected Sliders

     * @return array
     */
    protected function _getSelectedSliders()
    {
        $sliders = $this->getBannerSliders();
        if (!is_array($sliders)) {
            $sliders = $this->getBanner()->getSlidersPosition();
            return array_keys($sliders);
        }
        return $sliders;
    }

    /**
     * Retrieve selected Sliders

     * @return array
     */
    public function getSelectedSliders()
    {
        $selected = $this->getBanner()->getSlidersPosition();
        if (!is_array($selected)) {
            $selected = [];
        } else {
            foreach ($selected as $key => $value) {
                $selected[$key] = ['position' => $value];
            }
        }
        return $selected;
    }

    /**
     * @param \Mageplaza\BetterSlider\Model\Slider|\Magento\Framework\Object $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/slidersGrid',
            [
                'banner_id' => $this->getBanner()->getId()
            ]
        );
    }

    /**
     * @return \Mageplaza\BetterSlider\Model\Banner
     */
    public function getBanner()
    {
        return $this->coreRegistry->registry('mageplaza_betterslider_banner');
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_sliders') {
            $sliderIds = $this->_getSelectedSliders();
            if (empty($sliderIds)) {
                $sliderIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.slider_id', ['in'=>$sliderIds]);
            } else {
                if ($sliderIds) {
                    $this->getCollection()->addFieldToFilter('main_table.slider_id', ['nin'=>$sliderIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Sliders');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('mageplaza_betterslider/banner/sliders', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }
}
