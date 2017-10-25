<?php
/**
 * Mageplaza_BetterSlider extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Mageplaza
 * @package        Mageplaza_BetterSlider
 * @copyright      Copyright (c) 2016
 * @author         Sam
 * @license        Mageplaza License
 */


namespace Mageplaza\BetterSlider\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\View\Element\Template\Context;
use Mageplaza\BetterSlider\Model\SliderFactory as SliderModelFactory;
use Mageplaza\BetterSlider\Model\BannerFactory as BannerModelFactory;


class Slider extends \Magento\Framework\View\Element\Template
{
	protected $sliderFactory;
	protected $bannerFactory;

	public function __construct(
		Context $context,
		SliderModelFactory $sliderFactory,
		BannerModelFactory $bannerFactory
	)
	{
		$this->sliderFactory = $sliderFactory;
		$this->bannerFactory = $bannerFactory;
		parent::__construct($context);
	}

	protected function _prepareLayout()
	{
	}

	public function getSliders()
	{
		$sliderId = $this->getBannerId();
		$model = $this->sliderFactory->create()->load($sliderId);
		if($model && $model->getStatus()==1){
			$banners = $model->getSelectedBannersCollection()->addOrder('position','asc')->addFieldToFilter('status','1');
			return $banners;
		} else{
			return null;
		}

	}

}
