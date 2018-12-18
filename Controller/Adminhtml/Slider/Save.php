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

namespace Mageplaza\BannerSlider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Mageplaza\BannerSlider\Controller\Adminhtml\Slider;
use Mageplaza\BannerSlider\Model\SliderFactory;

/**
 * Class Save
 * @package Mageplaza\BannerSlider\Controller\Adminhtml\Slider
 */
class Save extends Slider
{
    /**
     * JS helper
     *
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * Date filter
     *
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;

    /**
     * Save constructor.
     *
     * @param Js $jsHelper
     * @param SliderFactory $sliderFactory
     * @param Registry $registry
     * @param Context $context
     * @param Date $dateFilter
     */
    public function __construct(
        Js $jsHelper,
        SliderFactory $sliderFactory,
        Registry $registry,
        Context $context,
        Date $dateFilter
    )
    {
        $this->jsHelper    = $jsHelper;
        $this->_dateFilter = $dateFilter;

        parent::__construct($sliderFactory, $registry, $context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPost('slider')) {
            $data   = $this->_filterData($data);
            $slider = $this->initSlider();

            $banners = $this->getRequest()->getPost('banners', -1);
            if ($banners != -1) {
                $slider->setBannersData($this->jsHelper->decodeGridSerializedInput($banners));
            }
            $slider->addData($data);

            $this->_eventManager->dispatch(
                'mpbannerslider_slider_prepare_save',
                [
                    'slider'  => $slider,
                    'request' => $this->getRequest()
                ]
            );

            try {
                $slider->save();
                $this->messageManager->addSuccess(__('The Slider has been saved.'));
                $this->_session->setMageplazaBannerSliderSliderData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mpbannerslider/*/edit',
                        [
                            'slider_id' => $slider->getId(),
                            '_current'  => true
                        ]
                    );

                    return $resultRedirect;
                }
                $resultRedirect->setPath('mpbannerslider/*/');

                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Slider.'));
            }

            $this->_getSession()->setMageplazaBannerSliderSliderData($data);
            $resultRedirect->setPath(
                'mpbannerslider/*/edit',
                [
                    'slider_id' => $slider->getId(),
                    '_current'  => true
                ]
            );

            return $resultRedirect;
        }

        $resultRedirect->setPath('mpbannerslider/*/');

        return $resultRedirect;
    }

    /**
     * filter values
     *
     * @param array $data
     *
     * @return array
     */
    protected function _filterData($data)
    {
        $inputFilter = new \Zend_Filter_Input(['from_date' => $this->_dateFilter,], [], $data);
        $data        = $inputFilter->getUnescaped();

        if (isset($data['responsive_items'])) {
            unset($data['responsive_items']['__empty']);
        }

        if ($banners = $this->getRequest()->getParam('banners')) {
            $data['banner_ids'] = $banners;
        }

        return $data;
    }
}
