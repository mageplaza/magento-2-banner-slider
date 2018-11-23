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
namespace Mageplaza\BannerSlider\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Mageplaza\BannerSlider\Model\Config\Source\Image;

class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * @var Image
     */
    protected $imageModel;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Thumbnail constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Image $imageModel
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Image $imageModel,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageModel = $imageModel;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $path      = $this->imageModel->getBaseUrl();
            foreach ($dataSource['data']['items'] as & $item) {
                $banner = new \Magento\Framework\DataObject($item);
                if ($item['type'] == 0 && $item['image']) {
                    $item[$fieldName . '_src']      = $path . $item['image'];
                    $item[$fieldName . '_alt']      = $item['name'];
                    $item[$fieldName . '_orig_src'] = $path . $item['image'];
                }
                //  Get Video Image
                if ($item['type'] == 1 && $item['url_video'] != null) {
                    $url                            = $item['url_video'];
                    if (strpos($url,'&') >0) {
                        preg_match_all('/v=(.+?)&/m', $url, $videoId, PREG_SET_ORDER, 0);
                        $videoId = $videoId[0][1];
                    }
                    else $videoId                        = substr($url, strpos($url, '=') + 1);
                    $url                            = 'https://img.youtube.com/vi/' . $videoId . '/0.jpg';
                    $item[$fieldName . '_src']      = $url;
                    $item[$fieldName . '_alt']      = $item['name'];
                    $item[$fieldName . '_orig_src'] = $url;
                }

                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'mpbannerslider/banner/edit',
                    ['banner_id' => $banner->getBannerId(), 'store' => $this->context->getRequestParam('store')]
                );
            }
        }

        return $dataSource;
    }
}
