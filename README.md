# Magento 2 Banner slider extension by Mageplaza

**Mageplaza Banner slider for Magento 2** is an banner slider extension for Magento that enables you to create image carousel slider, image scroller and video LightBox. The extension supports images, YouTube, Vimeo, mp4 and webm videos. It's fully responsive, works on iPhone, iPad, Android, Firefox, Chrome, Safari, Opera and Internet Explorer.

Fully written in jQuery, touch enabled extension based on OWL Carousel that lets you create beautiful responsive carousel slider.

- User Guide: https://docs.mageplaza.com/banner-slider-m2/
- Report bug: https://github.com/mageplaza/magento-2-banner-slider/issues
- Discussions: http://magento.stackexchange.com/
- Contribute on Github: https://github.com/mageplaza/magento-2-banner-slider
- Product Page: https://www.mageplaza.com/magento-2-banner-slider-extension/




## Fully Customisable
Over 60 options. Easy for novice users and even more powerful for advanced developers.

![banner slider](https://www.mageplaza.com/assets/img/extensions-images/magento-2-banner-slider/demo_2.png)

## Touch and Drag Support
Designed specially to boost mobile browsing experience. Mouse drag works great on desktop too!


## Fully Responsive
Almost all options are responsive and include very intuitive breakpoints settings.


## Modern Browsers

Owl uses hardware acceleration with CSS3 Translate3d transitions. Its fast and works like a charm!

## Easy custom banner slider

This feature allows admin custom banner with an attractive text. You can choose an interesting content and add the position you want such as: top, right, bottom, left, top left, top right, bottom left, bottom right, middle, etc. Banner with content text will become more eye-catching and engaging.

## Create unlimited banners, slider

It is very easy for you to upload, edit and delete the image for banner in banner information in backend. Banner Slider extension supports multi image types as: jpg, jpeg, gif, png. If you require other image kinds, please contact us to configure.

![banner slider](https://www.mageplaza.com/assets/img/extensions-images/magento-2-banner-slider/demo.png)


## Custom banner slider effects

Normal types of sliders will be bored and reduce the effectiveness of your promotional campaign. Now, you can easily to create sliders with different effects and set the active time for them.

## Place banner slider everywhere

36 positions available to show sliders (banners) on your website. That is easy and convenient to choose the most suitable one in order to increase the effectiveness of your Banner Slider. Moreover, you can also preview how it looks when setting up banners.



## Full features

- Allow upload image for banner
- Support adding button and link for banner
- Easily add content text for banner
- Choose flexible and suitable position of content text
- Support multi effects for banner ( included over 12 effects)
- Preview banner while setting the content and position of banner
- Support 36 positions to put banner on site
- Support for SEO
- Can view reports on clicks and impressions of banners
- Can create your own slider style by inserting custom codes


## Other features

- Display Featured Products
- Open source 100%.
- Easy to install and configure.
- User-friendly interface.
- Fully compatible with Mageplaza extensions
- Supports multiple stores.
- Supports multiple languages.



---



## User Guide


In this guide, I will show you how to insert A banner slider into Homepage of Magento 2 store.


### Step 1: Add new banners


First of all, you should add banners into your store.

* From Magento 2 Admin > Banner Slider > Banners > Add New Banner

* Fill information, upload image file to Upload File file.

![mageplaza banner slider](https://i.imgur.com/wjRuf5O.png)

* Then Click Save Banner

You can add as many banners as you want. After finish adding banners, you can go to next step: add a new slider.


### Step 2: Add a new slider


* From Magento 2 Admin > Banner Slider > Sliders > Add New Slider


* Fill Slider information as the following:

![fill banner slider](https://i.imgur.com/uYMjdGh.png)


* Click on **Banners tab**, and choose uploaded banners. You also can sort order by position.

![banner tab](https://i.imgur.com/ypLnvww.png)


* Then **Save Banner**, you can see `slider_id` in this example is **1**


![save banner slider](https://i.imgur.com/E132Cib.png)


### Step 3: Insert into Homepage


* Go to `Admin > Content > Pages > Homepage > Content`

```
{{ block class="Mageplaza\BetterSlider\Block\Slider" template="Mageplaza_BetterSlider::slider.phtml" banner_id="1" }}
```

You can change your own banner_id value depend on your store.

* Then click `Save` Page.

### Step 4: Flush Cache and Check result


To flush Magento cache, you can follow this guide: Magento 2 how to flush cache

Navigate your browser to Magento homepage and check result. Here is what we get.




## How to insert Banner Slider in layout file


In Xml file, you can insert the following block of code::

 
```
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="../../../../../../../lib/internal/Magento/Framework/View/	Layout/etc/page_configuration.xsd">
    <referenceContainer name="content">
        <block template="slider.phtml" class="Mageplaza\BetterSlider\Block\Slider" name="mageplaza_betterslider"/>
    </referenceContainer>
</page>
```






## Why Mageplaza developed this module for Developers


- **Optimize performnace** do not slow your Magento 2 store by adding banner slider everywhere, every positions (~36 positions) on the site. 
- Details and quality documentations for developers.
- Implement with ease.
- No need, no added.
- Free, Open-source. 


