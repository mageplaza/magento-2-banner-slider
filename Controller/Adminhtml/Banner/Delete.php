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

class Delete extends \Mageplaza\BetterSlider\Controller\Adminhtml\Banner
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('banner_id');
        if ($id) {
            $name = "";
            try {
                /** @var \Mageplaza\BetterSlider\Model\Banner $banner */
                $banner = $this->bannerFactory->create();
                $banner->load($id);
                $name = $banner->getName();
                $banner->delete();
                $this->messageManager->addSuccess(__('The Banner has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_mageplaza_betterslider_banner_on_delete',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('mageplaza_betterslider/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageplaza_betterslider_banner_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('mageplaza_betterslider/*/edit', ['banner_id' => $id]);
                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('Banner to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('mageplaza_betterslider/*/');
        return $resultRedirect;
    }
}
