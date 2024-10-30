=== MessyMenu ===
Contributors: Doc4
Tags: dasboard menu, menu, admin, links, urls,
Requires at least: 2.7
Tested up to: 6.6.2
Stable tag: 4.0
License: GPL-2.0+
License URL: http://www.gnu.org/licenses/gpl-2.0.txt

Create additional, custom, internal and external dashboard links


== Description ==
MessyMenu, developed in Arkansas by Doc4 Design, is a ChatGPT plugin solution that enhances the functionality of the WordPress Dashboard navigation. With MessyMenu, users can effortlessly incorporate a wide range of new links into their navigation, whether external or internal.

The Settings page of MessyMenu simplifies the process of creating new dashboard navigation links. Users can easily input labels, URLs, a Location within the WP Dashboard Menu, and select from a number of Dashicons to achieve a more comprehensive and polished appearance.

When adding internal links, users only need to include the internal url, such as 'options-writing.php', while external links require the full URL (e.g., https://doc4design.com). To locate the specific link to an internal page, simply hover the mouse over the desired link or page and check the bottom of the browser window.

MessyMenu streamlines the process of expanding the WordPress Dashboard navigation, offering users a straightforward method to integrate additional links with Dashicons. Give MessyMenu a try and discover the convenience and customization it brings to your WordPress Dashboard navigation.


= ReOrdering =
* Note: Drag and Drop reordering has been removed in favor of specific menu placement. To change the location of the Link within the WordPress Dashboard Menu, add a Link and then edit the Menu Location #. If no Menu Location # is provided, the Menu Location # will default to 1, incrementally adding 1 for each new link. Special thanks to Nathan Ingram for suggesting this.


= Internal Links =
* To find the name of the page you wish to link to, hover your mouse over the desired link and check the bottom of your browser. For instance, if you want to link to the WordPress Admin Widgets page, you would hover over "Appearances > Widgets" and see the URL 'https://mysite.com/wp-admin/widgets.php'. In this case, you would copy 'widgets.php' and paste it into the URL field.


= External Links =
* Include the full URL path as found in the browser address bar. For example, linking to the Apple website would look like this "https://www.apple.com/"


= Plugin URL =
[MessyMenu](https://doc4design.com/messymenu/)

= Screenshots =
[View Screenshots](https://doc4design.com/messymenu/)




== Installation ==
To install the plugin just follow these simple steps:

1. Download the plugin and expand it.
2. Copy the MessyMenu folder into your plugins folder ( wp-content/plugins ).
3. Log-in to the WordPress administration panel and visit the Plugins page.
4. Locate the MessyMenu plugin and click on the 'activate' link.
5. Set up the plugin by visiting Settings > MessyMenu and add your links and icons.




== Changelog ==

= 4.0 = 
* Clean and User-Friendly Interface Update


= 3.4 = 
* Include Option to Change Link Menu Location
* Removed option to drag and drop the menu link order as this is now location-based


= 3.3 =
* Include Javascript Folder


= 3.2 =
* Reordering of Menu Links


= 3.1 =
* Sanitize, Escape, and Validate Data


= 2.7 =
* Added Required Form Nonce
* Updated Required Headers for readme.txt
* Updated Required Headers for messymenu.php


= 2.6.3 =
* Initial Upload