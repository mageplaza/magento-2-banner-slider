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

use DateTime;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Mageplaza\BannerSlider\Controller\Adminhtml\Slider;
use Mageplaza\BannerSlider\Model\SliderFactory;
use RuntimeException;
use Zend_Filter_Input;

/**
 * Class Save
 * @package Mageplaza\BannerSlider\Controller\Adminhtml\Slider
 */
class Save extends Slider
{
    /**
     * JS helper
     *
     * @var Js
     */
    protected $jsHelper;

    /**
     * Date filter
     *
     * @var Date
     */
    protected $_dateFilter;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Save constructor.
     *
     * @param Js $jsHelper
     * @param SliderFactory $sliderFactory
     * @param Registry $registry
     * @param Context $context
     * @param Date $dateFilter
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Js $jsHelper,
        SliderFactory $sliderFactory,
        Registry $registry,
        Context $context,
        Date $dateFilter,
        DataPersistorInterface $dataPersistor
    ) {
        $this->jsHelper = $jsHelper;
        $this->_dateFilter = $dateFilter;
        $this->dataPersistor = $dataPersistor;

        parent::__construct($sliderFactory, $registry, $context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->getPost('slider')) {
            $data = $this->_filterData($this->getRequest()->getPost('slider'));
            $slider = $this->initSlider();

            $fromDate = $toDate = null;
            if (isset($data['from_date']) && isset($data['to_date'])) {
                $fromDate = $data['from_date'];
                $toDate = $data['to_date'];
            }
            if ($fromDate && $toDate) {
                $fromDate = new DateTime($fromDate);
                $toDate = new DateTime($toDate);

                if ($fromDate > $toDate) {
                    $this->messageManager->addErrorMessage(__('End Date must follow Start Date.'));
                    $this->_session->setPageData($data);
                    $this->dataPersistor->set('mpbannerslider_slider', $data);
                    $this->_redirect('*/*/edit', ['slider_id' => $slider->getId()]);

                    return;
                }
            }

            $banners = $this->getRequest()->getPost('banners', -1);
            if ($banners != -1) {
                $slider->setBannersData($this->jsHelper->decodeGridSerializedInput($banners));
            }
            $slider->addData($data);

            $this->_eventManager->dispatch(
                'mpbannerslider_slider_prepare_save',
                [
                    'slider' => $slider,
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
                            '_current' => true
                        ]
                    );

                    return $resultRedirect;
                }
                $resultRedirect->setPath('mpbannerslider/*/');

                return $resultRedirect;
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($slider->getId()) {
                    $this->_redirect('mpbannerslider/*/edit', ['slider_id' => $slider->getId()]);
                } else {
                    $this->_redirect('mpbannerslider/*/new');
                }
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Slider.'));
                $this->_redirect('mpbannerslider/*/edit', [
                    'slider_id' => $slider->getId()
                ]);
            }

            $this->_getSession()->setMageplazaBannerSliderSliderData($data);
            $resultRedirect->setPath(
                'mpbannerslider/*/edit',
                [
                    'slider_id' => $slider->getId(),
                    '_current' => true
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
        $inputFilter = new Zend_Filter_Input(['from_date' => $this->_dateFilter,], [], $data);
        $data = $inputFilter->getUnescaped();

        if (isset($data['responsive_items'])) {
            unset($data['responsive_items']['__empty']);
        }

        if ($this->getRequest()->getParam('banners')) {
            $data['banner_ids'] = $this->getRequest()->getParam('banners');
        }

        return $data;
    }
}
