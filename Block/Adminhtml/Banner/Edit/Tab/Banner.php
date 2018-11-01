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
namespace Mageplaza\BannerSlider\Block\Adminhtml\Banner\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mageplaza\BannerSlider\Block\Adminhtml\Banner\Edit\Tab\Render\Image as BannerImage;
use Mageplaza\BannerSlider\Helper\Image as HelperImage;
use Mageplaza\BannerSlider\Model\Config\Source\Type;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Framework\Convert\DataObject;

class Banner extends Generic implements TabInterface
{
    /**
     * Type options
     *
     * @var \Mageplaza\BannerSlider\Model\Config\Source\Type
     */
    protected $typeOptions;

    /**
     * Status options
     *
     * @var \Magento\Config\Model\Config\Source\Enabledisable
     */
    protected $statusOptions;

    /**
     * @var \Mageplaza\BannerSlider\Helper\Image
     */
    protected $imageHelper;

    /** @var \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory */
    protected $_fieldFactory;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;

    /**
     * Banner constructor.
     *
     * @param Type $typeOptions
     * @param Enabledisable $statusOptions
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param HelperImage $imageHelper
     * @param FieldFactory $fieldFactory
     * @param DataObject $objectConverter
     * @param array $data
     */
    public function __construct(
        Type $typeOptions,
        Enabledisable $statusOptions,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        HelperImage $imageHelper,
        FieldFactory $fieldFactory,
        DataObject $objectConverter,
        array $data = []
    )
    {
        $this->typeOptions = $typeOptions;
        $this->statusOptions = $statusOptions;
        $this->imageHelper = $imageHelper;
        $this->_fieldFactory = $fieldFactory;
        $this->_objectConverter            = $objectConverter;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\BannerSlider\Model\Banner $banner */
        $banner = $this->_coreRegistry->registry('mpbannerslider_banner');
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
            'status',
            'select',
            [
                'name'  => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => $this->statusOptions->toOptionArray(),
            ]
        );

        $typeBanner = $fieldset->addField(
            'type',
            'select',
            [
                'name'  => 'type',
                'label' => __('Type'),
                'title' => __('Type'),
                'values' => $this->typeOptions->toOptionArray(),
            ]
        );

        $uploadBanner = $fieldset->addField(
            'image',
            BannerImage::class,
            [
                'name' => 'image',
                'label' => __('Upload Image'),
                'title' => __('Upload Image'),
                'path' => $this->imageHelper->getBaseMediaPath(HelperImage::TEMPLATE_MEDIA_TYPE_BANNER)
            ]
        );

        $titleBanner = $fieldset->addField(
            'title',
            'text',
            [
                'name'  => 'title',
                'label' => __('Banner title'),
                'title' => __('Banner title'),
            ]
        );

        $urlBanner = $fieldset->addField(
            'url_banner',
            'text',
            [
                'name'  => 'url_banner',
                'label' => __('Url'),
                'title' => __('Url'),
            ]
        );

        $urlVideo = $fieldset->addField(
            'url_video',
            'text',
            [
                'name'  => 'url_video',
                'label' => __('Video Url'),
                'title' => __('Video Url'),
                'note'   => __('It supports Youtube video only. Just paste a Youtube video URL.')
            ]
        );

        $newtab = $fieldset->addField(
            'newtab',
            'select',
            [
                'name'  => 'newtab',
                'label' => __('Open new tab after click'),
                'title' => __('Open new tab after click'),
                'values' => $this->statusOptions->toOptionArray(),
                'note'   => __('Automatically open new tab after click on banner')

            ]
        );

        $fieldset->addField('sliders_ids', '\Mageplaza\BannerSlider\Block\Adminhtml\Banner\Edit\Tab\Render\Slider', [
                'name' => 'sliders_ids',
                'label' => __('Sliders'),
                'title' => __('Sliders'),
            ]
        );
        if (!$banner->getSlidersIds()) {
            $banner->setSlidersIds($banner->getSliderIds());
        }

        $bannerData = $this->_session->getData('mpbannerslider_banner_data', true);
        if ($bannerData) {
            $banner->addData($bannerData);
        } else {
            if (!$banner->getId()) {
                $banner->addData($banner->getDefaultValues());
            }
        }

        $dependencies = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap($typeBanner->getHtmlId(), $typeBanner->getName())
            ->addFieldMap($urlBanner->getHtmlId(), $urlBanner->getName())
            ->addFieldMap($uploadBanner->getHtmlId(), $uploadBanner->getName())
            ->addFieldMap($urlVideo->getHtmlId(), $urlVideo->getName())
            ->addFieldMap($titleBanner->getHtmlId(), $titleBanner->getName())
            ->addFieldMap($newtab->getHtmlId(), $newtab->getName())
            ->addFieldDependence($urlBanner->getName(),$typeBanner->getName(),'0')
            ->addFieldDependence($uploadBanner->getName(),$typeBanner->getName(),'0')
            ->addFieldDependence($titleBanner->getName(),$typeBanner->getName(),'0')
            ->addFieldDependence($newtab->getName(),$typeBanner->getName(),'0')
            ->addFieldDependence($urlVideo->getName(),$typeBanner->getName(),'1');

        // define field dependencies
        $this->setChild('form_after', $dependencies);

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
