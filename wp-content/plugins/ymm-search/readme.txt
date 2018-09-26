=== Year Make Model Search for WooCommerce ===

Contributors: pektsekye
Tags: tyre search, part finder, ymm, year make model search, product filter, automotive, auto parts, auto parts website, auto parts store, make model year, woocommerce search
Requires at least: 4.7
Tested up to: 4.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Year Make Model Search plugin will help customer to find all products for specific vehicle make, model and year.


== Description ==

This simple plugin was created specially for WooCommerce.
It can be used only for automotive stores.
It has fixed number of drop-downs - three and the Year is the last drop-down.
There is no way to change order of the drop-downs and make the Year drop-down first.

Product restrictions are used as values for front-end drop-downs.
It means that if a customer can select "Acura CL 1997" there is a product with this restriction.
If you need to change "Bmw" to "BMW" in the drop-down you should find the product with that restriction and replace "Bmw" with "BMW".
You should be careful when you set product restrictions to not make duplicates like "Lexus ES-300" and "Lexus ES300". Because both of them will be selectable on front-end an they will point to different products.


**Main Features:**

* Search for products on home page
* Filter products on category page
* List applicable vehicles on front-end product view page
* CSV import / export for product restrictions


**Main Limitations:**

* Fixed number of drop-downs - three and Year is the last drop-down.
* Product restrictions are used as values for user drop-downs. So it is posible to make by mistake duplicates like "Lexus ES-300" and "Lexus ES300".
* Product restrictions use comma-separated format which can be difficult to fill manually.


You can check the demo website here:
**[DEMO Website](http://hottons.com/demo/wp/ymm/)**

You can read the installation instructions here:
**[README](http://hottons.com/demo/wp/ymm/README.html)**

If you need to change order of the drop-downs or to add more drop-down selects you should modify the code of this plugin.
List of YMM modifications [hottons.com/ymm_modifications](http://hottons.com/ymm_modifications)

Contact me by email <pektsekye@gmail.com> if you have questions or need help.


== Installation ==

Requires WordPress 4.7 and WooCommerce 3.0 or greater.

1. Unzip the downloaded zip file.
2. Upload the plugin folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `YMM Search` from Plugins page.
4. Add `YMM Search` widget to the sidebar in Wordpress admin panel > Appearance > Widgets
5. Set restriction to any product in the YMM tab of the Edit Product page.

Contact me by email <pektsekye@gmail.com> if you have questions or need help.


== Screenshots ==

1. Activate plugin
2. Add YMM widget to sidebar
3. Set a restriction for a product
4. YMM search box on frontend
5. Import / Export and the configurations page
6. Sample .csv file


== Changelog ==

= 1.0.9 - Released: Jan, 22 - 2018  =

Before upgrading to this version export your YMM vehicles as .csv file to not lose data!

* Main file was renamed from ymm.php to ymm-search.php . You may need to deactivate / activate this plugin to make it work with the new file.
* Text Domain was changed to "ymm-search" in all files to make this plugin use translations from translate.wordpress.org
* Template files were renamed to make translation programs find all strings for translation.
* A template file with all strings for translation was added. wp-content/plugins/ymm-search/i18n/languages/ymm-search.pot
* Code corrections.

= 1.0.8 - Released: Dec, 5 - 2017  =

* Bug fixed when WooCommerce menu is displayed wrong on the YMM results page.
* Code corrections.

= 1.0.7 - Released: Nov, 8 - 2017  =

* Add selected vehicle to the category page HTML title tag.
* Code corrections.

= 1.0.6 - Released: Nov, 6 - 2017  =

* Fixed installation code.

= 1.0.5 - Released: Nov, 5 - 2017  =

* Bug fixed: WordPress pages and posts are not available to edit or view when plugin is enabled.
* Code corrections.

= 1.0.4 - Released: Oct, 29 - 2017  =

* Faster AJAX requests for category drop-downs.

= 1.0.3 - Released: Oct, 28 - 2017  =

* The "search for restrictions" feature updated to not show values that already exist in the restriction text area.
* Show years in descending order.
* Custom PHP file for faster AJAX requests.

= 1.0.2 - Released: Oct, 23 - 2017  =

* Code corrected to make "search for restrictions" feature work when creating a new product.

= 1.0.1 - Released: Oct, 23 - 2017  =

* JavaScript corrected. New product restrictions were not saved.

= 1.0.0 - Released: Oct, 16 - 2017  =

* Initial release


== Other plugins ==

= If you like this plugin check also: =

* [Part Finder](http://hottons.com/woocommerce/partfinder-wp.html) (PAID)
  If you need vehicle makes to be separated from the product restrictions or you need more than three drop-down selects.
  
* [Attribute Search](http://hottons.com/woocommerce/attribute-search.html) (PAID)
  For tyre and rim search.
  
* [Simple Product Options](https://wordpress.org/plugins/product-options-for-woocommerce/)
  Let customer to select an option or enter a custom text before adding a product to the shopping cart.


== Translators ==

= Available Languages =
* English (Default)


== Documentation ==

Full documentation is available [here](http://hottons.com/demo/wp/ymm/README.html).


