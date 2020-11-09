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

namespace Mageplaza\BannerSlider\Block\Adminhtml\Slider\Edit\Tab;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\System\Store;
use Mageplaza\BannerSlider\Block\Adminhtml\Slider\Edit\Tab\Renderer\Snippet;
use Mageplaza\BannerSlider\Model\Config\Source\Location;

/**
 * Class Slider
 * @package Mageplaza\BannerSlider\Block\Adminhtml\Slider\Edit\Tab
 */
class Slider extends Generic implements TabInterface
{
    /**
     * Status options
     *
     * @var Enabledisable
     */
    protected $statusOptions;

    /**
     * @var Location
     */
    protected $_location;

    /**
     * @var Store
     */
    protected $_systemStore;

    /**
     * @var GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var DataObject
     */
    protected $_objectConverter;

    /**
     * Slider constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Enabledisable $statusOptions
     * @param Location $location
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObject $objectConverter
     * @param Store $systemStore
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Enabledisable $statusOptions,
        Location $location,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObject $objectConverter,
        Store $systemStore,
        array $data = []
    ) {
        $this->statusOptions = $statusOptions;
        $this->_location = $location;
        $this->_groupRepository = $groupRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_objectConverter = $objectConverter;
        $this->_systemStore = $systemStore;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\BannerSlider\Model\Slider $slider */
        $slider = $this->_coreRegistry->registry('mpbannerslider_slider');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('slider_');
        $form->setFieldNameSuffix('slider');
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Slider Information'),
            'class' => 'fieldset-wide'
        ]);
        if ($slider->getId()) {
            $fieldset->addField('slider_id', 'hidden', ['name' => 'slider_id']);
        }

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true,
        ]);

        $fieldset->addField('status', 'select', [
            'name' => 'status',
            'label' => __('Status'),
            'title' => __('Status'),
            'values' => array_merge(['' => ''], $this->statusOptions->toOptionArray()),
        ]);

        if (!$slider->hasData('store_ids')) {
            $slider->setStoreIds(0);
        }
        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'hidden', [
                'name' => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        } else {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(Element::class);
            $fieldset->addField('store_ids', 'multiselect', [
                'name' => 'store_ids',
                'label' => __('Store Views'),
                'title' => __('Store Views'),
                'required' => true,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);
        }

        $customerGroups = $this->_groupRepository->getList($this->_searchCriteriaBuilder->create())->getItems();
        $fieldset->addField('customer_group_ids', 'multiselect', [
            'name' => 'customer_group_ids[]',
            'label' => __('Customer Groups'),
            'title' => __('Customer Groups'),
            'required' => true,
            'values' => $this->_objectConverter->toOptionArray($customerGroups, 'id', 'code'),
            'note' => __('Select customer group(s) to display the slider to')
        ]);

        $fieldset->addField('location', 'multiselect', [
            'name' => 'location',
            'label' => __('Position'),
            'title' => __('Position'),
            'values' => $this->_location->toOptionArray(),
            'note' => __('Select the position to display block.'),
            'required' => true,
        ]);

        $fieldset->addField('from_date', 'date', [
            'name' => 'from_date',
            'label' => __('Display from'),
            'title' => __('Display from'),
            'date_format' => 'M/d/yyyy',
            'input_format' => DateTime::DATE_INTERNAL_FORMAT,
            'timezone' => false
        ]);

        $fieldset->addField('to_date', 'date', [
            'name' => 'to_date',
            'label' => __('Display to'),
            'title' => __('Display to'),
            'date_format' => 'M/d/yyyy',
            'input_format' => DateTime::DATE_INTERNAL_FORMAT,
            'timezone' => false
        ]);

        $fieldset->addField('priority', 'text', [
            'name' => 'priority',
            'label' => __('Priority'),
            'class' => 'validate-digits',
            'note' => __('Enter a number to set priority for the slider. A lower number represents a higher priority.')
        ]);

        $subfieldset = $form->addFieldset('sub_fieldset', [
            'legend' => __('Another way to add sliders to your page'),
            'class' => 'fieldset-wide'
        ]);
        $subfieldset->addField('snippet', Snippet::class, [
            'name' => 'snippet',
            'label' => __('How to use'),
            'title' => __('How to use'),
            'slider_id' => $slider->getId(),
        ]);

        $form->addValues($slider->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
