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

namespace Mageplaza\BannerSlider\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Location
 * @package Mageplaza\BannerSlider\Model\Config\Source
 */
class Location implements ArrayInterface
{
    const ALLPAGE_CONTENT_TOP = 'allpage.content-top';
    const ALLPAGE_CONTENT_BOTTOM = 'allpage.content-bottom';
    const ALLPAGE_PAGE_TOP = 'allpage.page-top';
    const ALLPAGE_PAGE_BOTTOM = 'allpage.footer-container';
    const HOMEPAGE_CONTENT_TOP = 'cms_index_index.content-top';
    const HOMEPAGE_CONTENT_BOTTOM = 'cms_index_index.content-bottom';
    const HOMEPAGE_PAGE_TOP = 'cms_index_index.page-top';
    const HOMEPAGE_PAGE_BOTTOM = 'cms_index_index.footer-container';
    const CATEGORY_CONTENT_TOP = 'catalog_category_view.content-top';
    const CATEGORY_CONTENT_BOTTOM = 'catalog_category_view.content-bottom';
    const CATEGORY_PAGE_TOP = 'catalog_category_view.page-top';
    const CATEGORY_PAGE_BOTTOM = 'catalog_category_view.footer-container';
    const CATEGORY_SIDEBAR_TOP = 'catalog_category_view.sidebar-top';
    const CATEGORY_SIDEBAR_BOTTOM = 'catalog_category_view.sidebar-bottom';
    const PRODUCT_CONTENT_TOP = 'catalog_product_view.content-top';
    const PRODUCT_CONTENT_BOTTOM = 'catalog_product_view.content-bottom';
    const PRODUCT_PAGE_TOP = 'catalog_product_view.page-top';
    const PRODUCT_PAGE_BOTTOM = 'catalog_product_view.footer-container';
    const USING_SNIPPET_CODE = 'custom.snippet-code';

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('All Page'),
                'value' => [
                    [
                        'label' => __('Top of content'),
                        'value' => self::ALLPAGE_CONTENT_TOP
                    ],
                    [
                        'label' => __('Bottom of content'),
                        'value' => self::ALLPAGE_CONTENT_BOTTOM
                    ],
                    [
                        'label' => __('Top of page'),
                        'value' => self::ALLPAGE_PAGE_TOP
                    ],
                    [
                        'label' => __('Bottom of page'),
                        'value' => self::ALLPAGE_PAGE_BOTTOM
                    ]
                ]
            ],
            [
                'label' => __('Home Page'),
                'value' => [
                    [
                        'label' => __('Top of content'),
                        'value' => self::HOMEPAGE_CONTENT_TOP
                    ],
                    [
                        'label' => __('Bottom of content'),
                        'value' => self::HOMEPAGE_CONTENT_BOTTOM
                    ],
                    [
                        'label' => __('Top of page'),
                        'value' => self::HOMEPAGE_PAGE_TOP
                    ],
                    [
                        'label' => __('Bottom of page'),
                        'value' => self::HOMEPAGE_PAGE_BOTTOM
                    ]
                ]
            ],
            [
                'label' => __('Category page'),
                'value' => [
                    [
                        'label' => __('Top of content'),
                        'value' => self::CATEGORY_CONTENT_TOP
                    ],
                    [
                        'label' => __('Bottom of content'),
                        'value' => self::CATEGORY_CONTENT_BOTTOM
                    ],
                    [
                        'label' => __('Top of page'),
                        'value' => self::CATEGORY_PAGE_TOP
                    ],
                    [
                        'label' => __('Bottom of page'),
                        'value' => self::CATEGORY_PAGE_BOTTOM
                    ],
                    [
                        'label' => __('Top of sidebar'),
                        'value' => self::CATEGORY_SIDEBAR_TOP
                    ],
                    [
                        'label' => __('Bottom of sidebar'),
                        'value' => self::CATEGORY_SIDEBAR_BOTTOM
                    ],
                ]
            ],
            [
                'label' => __('Product page'),
                'value' => [
                    [
                        'label' => __('Top of content'),
                        'value' => self::PRODUCT_CONTENT_TOP
                    ],
                    [
                        'label' => __('Bottom of content'),
                        'value' => self::PRODUCT_CONTENT_BOTTOM
                    ],
                    [
                        'label' => __('Top of page'),
                        'value' => self::PRODUCT_PAGE_TOP
                    ],
                    [
                        'label' => __('Bottom of page'),
                        'value' => self::PRODUCT_PAGE_BOTTOM
                    ]
                ]
            ],
            [
                'label' => __('Custom'),
                'value' => [
                    [
                        'label' => __('Using snippet code, widget'),
                        'value' => self::USING_SNIPPET_CODE
                    ]
                ]
            ]
        ];

        return $options;
    }
}
