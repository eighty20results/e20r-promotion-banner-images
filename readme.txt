=== E20R Promotion Banner Image Widget ===
Contributors: eighty20results
Donate link: https://eighty20results.com/wordpress-plugins/donations
Tags: image banner sidebar, sidebar image, ad image shortcode, ,ads in sidebar
Requires at least: 4.4
Tested up to: 5.3
Stable tag: 2.3

Add scheduled image banners in widget areas on your site. You can upload or select images from the site Media Library, schedule the start or stop date for when the banner is supposed to be visible, set the title, etc.

== Description ==

This plugin allows inclusion of image banners or small ads in the sidebar or any widget area of your theme or pages. You can add image banners as a widget using the following step:

*   You have to drag and drop the "E20R Promotion Banner Image Widget" widget to your required widget area.
*   Enter the image url directly, or select/upload an image from/to the Media Library. You may specify a link (URI) to use as a hotlink for the image.
*   Configure a title, description as you need. Choose where and when to allow the widget content to be visisble.
*   Save & Close. The image will resize to the container (widget area) it has been loaded for.

== Shortcodes ==

[[e20r_promotion_image]] - Add the promotion image and info (specified as attributes for the shortcode) to any post or page that supports shortcodes

=== Attributes ==
1. image_url: URL to the banner image to use (default: none)
1. alt_text: The 'ALT' text for the image. This is typically what's shown if the image can't be shown (default: none)
1. title: The title for the promotion area, located above the image. (default: none)
1. promotion_link: Makes the image 'clickable' and the user is sent to the specified URL if they click the banner/image. Useful for promotion landing pages, etc. (default: none)
1. image_title: The image title (what the image is called). Important for SEO. (default: none)
1. text_description: Descriptive text for the promotion. The specified text/html will be below the image. (default: none).
1. home_page: Whether to allow the promotion banner to be visible on the site homepage (default: 'off', accepts 'on' or 'off' )
1. auto_fit: Whether to allow the area to take up the available space on the page, or to limit its size to that of the image you specified (default: 'on', accepts 'on' or 'off')
1. target: Should the `link` attribute open in the current tab ('_self'), a new tab/window ('_blank') or the top of the current HTML frame ('_top') (default: '_self' )
1. show_on: Date for the first day the promotion image will be visible (Format: YYYY-MM-DD) (default: today's date)
1. hide_after: Date for after when the content of the shortcode will no longer be visible (Format: YYYY-MM-DD) (default: none)

=== Example ===
[[e20r_promotion_image image_url="http://example.com/wp-content/uploads/2017/12/black-friday-discounts.jpg" alt="Massive Holiday Discounts" title="Special Pricing for Black Friday!" link="http://example.com/black-friday-sale-2017/" text_description="These discounted prices are only available on Friday November 24th!" show_on="2017-11-24" hide_after="2017-11-24"]]

== Known Issues ==
* If you update the image (replace it), the "Save" button may not be active/clickable. Try modifying one of the other fields and the button should 'fix itself'.

== Installation ==

1.  Download the widget and upload it to your server through \`WP Admin -> Plugins -> Add New -> Upload\`
2.  After the upload is complete, activate the plugin.
3.  Go to Appearance -> Widgets page, drag and drop the widget to your sidebar.
4.  Fill in the settings as required. You can enter, title, upload image, image link, enter description you want to have displayed.

== Frequently Asked Questions ==

**How to install the plugin?**

Upload to the plugin folder, navigate to the "Plugins" section of your WordPress site, and click on the 'Activate' link. Once installed, you can place the widget in any widget area on your site via the "Appearance" -> "Widgets" menu.

== Screenshots ==
1. TBD

= Change Log =

== v2.3 ==

* ENHANCEMENT: Support for PHP 7.2+

== v2.2 ==

* BUG FIX: Didn't show the image text

== v2.1 ==

* BUG FIX: Didn't load the widget properly

== 2.0 ==

* BUG FIX: Incorrect handling of checkboxes
* BUG FIX: Various translation label updates/fixes
* ENHANCEMENT: More natural(?) variable names for shortcode attributes
* ENHANCEMENT: Updated the readme.txt file with better documentation.

== 1.2 ==

* ENHANCEMENT: Added support for [e20r_promotion_image] shortcode.

== 1.2 ==

* BUG FIX: Button for Media Library 'went missing'

== 1.1 ==

* Initial release

