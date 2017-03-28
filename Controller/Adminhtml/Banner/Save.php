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
namespace Mageplaza\BetterSlider\Controller\Adminhtml\Banner;

class Save extends \Mageplaza\BetterSlider\Controller\Adminhtml\Banner
{
    /**
     * Upload model
     * 
     * @var \Mageplaza\BetterSlider\Model\Upload
     */
    protected $uploadModel;

    /**
     * Image model
     * 
     * @var \Mageplaza\BetterSlider\Model\Banner\Image
     */
    protected $imageModel;

    /**
     * JS helper
     * 
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * constructor
     * 
     * @param \Mageplaza\BetterSlider\Model\Upload $uploadModel
     * @param \Mageplaza\BetterSlider\Model\Banner\Image $imageModel
     * @param \Magento\Backend\Helper\Js $jsHelper
     * @param \Mageplaza\BetterSlider\Model\BannerFactory $bannerFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mageplaza\BetterSlider\Model\Upload $uploadModel,
        \Mageplaza\BetterSlider\Model\Banner\Image $imageModel,
        \Magento\Backend\Helper\Js $jsHelper,
        \Mageplaza\BetterSlider\Model\BannerFactory $bannerFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->uploadModel    = $uploadModel;
        $this->imageModel     = $imageModel;
        $this->jsHelper       = $jsHelper;
        parent::__construct($bannerFactory, $registry, $context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('banner');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->filterData($data);
            $banner = $this->initBanner();
            $banner->setData($data);

            $uploadFile = $this->uploadModel->uploadFileAndGetName('upload_file', $this->imageModel->getBaseDir(), $data);

            $banner->setUploadFile($uploadFile);
            $sliders = $this->getRequest()->getPost('sliders', -1);
            if ($sliders != -1) {
                $banner->setSlidersData($this->jsHelper->decodeGridSerializedInput($sliders));
            }
            $this->_eventManager->dispatch(
                'mageplaza_betterslider_banner_prepare_save',
                [
                    'banner' => $banner,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $banner->save();
                $this->messageManager->addSuccess(__('The Banner has been saved.'));
                $this->_session->setMageplazaBetterSliderBannerData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mageplaza_betterslider/*/edit',
                        [
                            'banner_id' => $banner->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('mageplaza_betterslider/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Banner.'));
            }
            $this->_getSession()->setMageplazaBetterSliderBannerData($data);
            $resultRedirect->setPath(
                'mageplaza_betterslider/*/edit',
                [
                    'banner_id' => $banner->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $resultRedirect->setPath('mageplaza_betterslider/*/');
        return $resultRedirect;
    }

    /**
     * filter values
     *
     * @param array $data
     * @return array
     */
    protected function filterData($data)
    {
        if (isset($data['status'])) {
            if (is_array($data['status'])) {
                $data['status'] = implode(',', $data['status']);
            }
        }
        return $data;
    }
}
