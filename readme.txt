=== Affiliates ===
Contributors: itthinx
Donate link: http://www.itthinx.com/plugins/affiliates
Tags: ads, advertising, affiliate, affiliate marketing, affiliate plugin, affiliate tool, affiliates, bucks, contact form, crm, earn money, e-commerce, lead, link, marketing, money, online sale, order, partner, referral, referral links, referrer, shopping cart, sales, site, track, transaction, wordpress
Requires at least: 3.0
Tested up to: 3.2.1
Stable tag: 1.0.3

The Affiliates plugin provides the right tools to maintain a partner referral program.

== Description ==

The Affiliates plugin provides the right tools to maintain a partner referral program.

If you need to **track visits to your site** with **affiliate links**, the affiliates plugin is probably right for you. It provides the tools to maintain a **partner referral program**.

Simply put, the affiliates plugin is used to manage and track visits to your site through affiliate links.
Referrals can also be stored and attributed to an affiliate, for example if clients place orders on your site and you need to credit your affiliates.

If you require **support** or **customization** including **referrals integration** with your site, you may [contact me here](http://www.itthinx.com/) and consider making a [donation](http://www.itthinx.com/plugins/affiliates).

After installing the plugin you can start adding affiliates and provide them with affiliate links to your site.
The default options should be suitable for many, but there are some options you might want to consider before you really start.

Full documentation is accessible from the [Affiliates plugin page](http://www.itthinx.com/plugins/affiliates). 

Although the options are documented on each page and are generally intuitive or self-explaining, a quick introduction is useful ...

The 'Affiliates' menu provides these sections:

#### Affiliates ####

Here an overview is provided with summarized statistical data, including your currently operative affiliates, total hits, visits and referrals.

#### Manage Affiliates ####

This is where you add, remove and manage your affiliates. For each affiliate, the appropriate affiliate links are shown so that these can be conveniently employed on your affiliates' sites.

#### Visits & Referrals ####

Provides per-day information:

This page shows a summary of unique Visits, Hits and Referrals as well as a Ratio that shows the conversion rate (Referrals/Visits) on a daily basis.
Extended information can be shown, including information about pages or posts that produced referrals as well as their date and time and IP addresses that produced hits.
Data can be conveniently sorted and also filtered by affiliate and time period.

#### Affiliates & Referrals ####

Provides per-affiliate information:

For each affiliate, the number of unique Visitors, Hits, Referrals and Ratio (Referrals/Visitor) are shown.
Detailed information about each referral can be expanded as well as for hits.
Data can be conveniently sorted and also filtered by affiliate and time period.

#### Referrals ####

Provides per-referral information:

For each referral, the date and time, corresponding post and affiliate is shown.
Additional data and referral descriptions that have been recorded using the plugin's API are shown for each referral.
Data can be conveniently sorted and also filtered by affiliate and time period.

#### Options ####

##### Referral timeout #####

The referral timeout determines for how long a visit via an affiliate link will produce a referral.
This setting can be adjusted to range from the individual session to a number of days.

##### Direct referrals #####

The affiliates plugin can be used to store transaction data even if no affiliate was involved.
This settings determines if 'direct' referrals are stored. This are accessible through a 'Direct' affiliate who represents the site's owner or organization.

##### Robots #####

Hits from affiliate links that have originated from robots listed here will not be taken into account.

By default there are no entries but you can start with this example:

	Yahoo! Slurp
	YandexBot
	Googlebot
	DotBot
	discobot
	MJ12bot
	proximic
	Baiduspider
	bingbot
	Exabot
	AMZNKAssocBot

##### Affiliate ID encoding #####

Either plain or MD5-encoded affiliate IDs can be used. These are appended to your affiliate's link.

##### Permissions #####

For each role these permissions can be set:

* Access affiliates: to be able to see information accessible through the *Affiliates* menu in WordPress.
* Administer affiliates: to add, remove and manage affiliates.
* Administer options: grants access to make changes on this *Options* page.

##### Deactivation and data persistence #####

A convenient option is provided to delete all data that has been stored by the affiliates plugin.
This option is useful if you just need to start clean while you run tests.

#### What this plugin is not ####

It is not intended to keep track of links to other sites that you as a member of an affiliate program may have.

#### Thanks ####

Initial development of this plugin has been sponsored by [Indigourlaub](http://www.indigourlaub.com).

== Installation ==

1. Upload or extract the `affiliates` folder to your site's `/wp-content/plugins/` directory. Or you could use the *Add new* option found in the *Plugins* menu in WordPress.  
2. Enable the plugin from the *Plugins* menu in WordPress.
3. A new *Affiliates* menu will appear in WordPress, this is where you manage your affiliates and keep track of visits and referrals.

Now you can start adding affiliates and provide them with affiliate links to your site.
The default options should be suitable for many, but there are some options you might want to consider before you really start.

== Frequently Asked Questions ==

= Can we generate affiliate links for our partners? =

Yes.

This is one of the most important features.

= Can we track visits to our site through our partners' sites? =

Yes.

This is also one of the most important features and what the plugin is intended for, besides recording referrals and transaction data.

= Can we record referrals automatically when, for example, an order is placed? =

Yes.

The API provides the means to suggest referrals and record them.

= I am an affiliate of ACME and they provided me with an affiliate link. Is this plugin for me? =

No it isn't.

This plugin is for sites that need to manage *their* affiliates.

= Can I automatically store additional referral and transaction data? =

Yes you can do that through the plugin's API functions.

= I need to keep track of all transactions, including those that have not been initiated via an affiliate. Is this possible? =

Yes!

Referrals that are not attributable to an affiliate can be stored along with arbitrary transaction data and associated with the site owner.
There is a dedicated entry for that, called *Direct* in the affiliates list, representing the site owner.

= What about timezones? =

The plugin provides timezone-independent recording and retrieval of hits, visits and referrals.
Data is stored with reference to the server's settings and shown adjusted to the timezone settings in your WordPress site.

= How flexible is data recording and retrieval for referrals? =

You can store any information you need along with referrals.

= How ugly are affiliate links? =

Not very and there are several options including pretty permalinks.

Automatic affiliate id removal : the affiliate id is removed from your site's URL after the visitors land on your site.

= Is it possible to have permalinks that include affiliate data? =

Yes.

= Is it possible to have affiliate links to specific posts? =

Yes.

= How fine-grained are permissions? =

The plugin provides role-based permissions to access gathered affiliate data, administer affiliates and administer options.

== Screenshots ==

1. Overview - shows summarized information based on current and historic data
2. Manage Affiliates - where affiliates links for your site's partners are maintained
3. Visits & Referrals - per-day view of information about visits and referrals generated through affiliate links
4. Affiliates & Referrals - per-affiliate view of information about visits and referrals generated through affiliate links
5. Referrals I - per-referrals view of information about referrals
6. Referrals II - showing detailed information stored along with referrals obtained through the Affiliates Contact widget
7. Options - where general settings are maintained
8. Menu - the Affiliates menu

== Changelog ==

= 1.0.3 =
* Fixed bug in Affiliates Overview : number of referrals shown was wrong

= 1.0.2 =
* Fixed remnant hard-coded table names. Thanks to Gernot Brandstötter who spotted these!

= 1.0.1 =
* Fixed errors showing up for PHP < 5.3

= 1.0.0 =
* Initial release (tested & working on production sites).

== Upgrade Notice ==

= 1.0.2 =
Important bug-fixes that affect use of the plugin with installations using non-default table name prefixes and multi-site installations.

= 1.0.1 =
Please upgrade if you see errors like these or if you are on PHP < 5.3:

Warning: call_user_func_array() [function.call-user-func-array]: First argument is expected to be a valid callback, 'Affiliates_Contact::_enqueue_scripts' was given in /var/www/wptest/wp-includes/plugin.php on line 395

Warning: call_user_func_array() [function.call-user-func-array]: First argument is expected to be a valid callback, 'Affiliates_Contact::_print_styles' was given in /var/www/wptest/wp-includes/plugin.php on line 395

= 1.0.0 =
There is no need to upgrade yet.

== API ==

	affiliates_suggest_referral( $post_id, $description = '', $data = null )
	
Suggest to record a referral. This function is used to actually store referrals and associated information.

**Parameters:**
	
- **int $post_id** the referral post id; where the transaction or referral originates

- **string $description** the referral description

- **string|array $data** additional information that should be stored along with the referral
	 
**Returns:**
	
- affiliate id if a valid referral is recorded, otherwise `false`

Full documentation and examples are accessible from the [Affiliates plugin page](http://www.itthinx.com/plugins/affiliates).
