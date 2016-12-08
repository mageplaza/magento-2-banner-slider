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

class Banner extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Type options
     * 
     * @var \Mageplaza\BetterSlider\Model\Banner\Source\Type
     */
    protected $typeOptions;

    /**
     * Status options
     * 
     * @var \Mageplaza\BetterSlider\Model\Banner\Source\Status
     */
    protected $statusOptions;

    /**
     * constructor
     * 
     * @param \Mageplaza\BetterSlider\Model\Banner\Source\Type $typeOptions
     * @param \Mageplaza\BetterSlider\Model\Banner\Source\Status $statusOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Mageplaza\BetterSlider\Model\Banner\Source\Type $typeOptions,
        \Mageplaza\BetterSlider\Model\Banner\Source\Status $statusOptions,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        $this->typeOptions   = $typeOptions;
        $this->statusOptions = $statusOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\BetterSlider\Model\Banner $banner */
        $banner = $this->_coreRegistry->registry('mageplaza_betterslider_banner');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('banner_');
        $form->setFieldNameSuffix('banner');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Banner Information'),
                'class'  => 'fieldset-wide'
            ]
        );
        $fieldset->addType('image', 'Mageplaza\BetterSlider\Block\Adminhtml\Banner\Helper\Image');
        if ($banner->getId()) {
            $fieldset->addField(
                'banner_id',
                'hidden',
                ['name' => 'banner_id']
            );
        }
        $fieldset->addField(
            'name',
            'text',
            [
                'name'  => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'upload_file',
            'image',
            [
                'name'  => 'upload_file',
                'label' => __('Upload File'),
                'title' => __('Upload File'),
            ]
        );
        $fieldset->addField(
            'url',
            'text',
            [
                'name'  => 'url',
                'label' => __('Banner Url'),
                'title' => __('Banner Url'),
            ]
        );
//        $fieldset->addField(
//            'type',
//            'select',
//            [
//                'name'  => 'type',
//                'label' => __('Type'),
//                'title' => __('Type'),
//                'values' => array_merge(['' => ''], $this->typeOptions->toOptionArray()),
//            ]
//        );
//
        $fieldset->addField(
            'status',
            'select',
            [
                'name'  => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => $this->statusOptions->toOptionArray(),
            ]
        );

        $bannerData = $this->_session->getData('mageplaza_betterslider_banner_data', true);
        if ($bannerData) {
            $banner->addData($bannerData);
        } else {
            if (!$banner->getId()) {
                $banner->addData($banner->getDefaultValues());
            }
        }
        $form->addValues($banner->getData());
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
        return __('Banner');
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
