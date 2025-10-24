=== Radio Browser for WP ===
Contributors: linknacional
Tags: radio, streaming, audio, player, music
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://paraquemdoar.org/doar/

Display and play online radio stations from Radio-Browser.info with a beautiful player and customizable radio list.

== Description ==

Integrate thousands of **online radio stations** into your WordPress website with the **Radio Browser for WP** plugin. 
== Disclaimer ==
This plugin is an independent project developed by LinkNacional. It is **not affiliated, endorsed, or sponsored** by Radio-Browser.info, WordPress, or Select2.

* Radio-Browser.info is a free, public API and database of radio stations. This plugin uses their API to fetch station data and stream audio, but there is no official relationship or partnership.
* Select2 is an open-source JavaScript library used to enhance dropdowns and search fields. This plugin includes Select2 locally and does not load it from external servers.
* WordPress is a registered trademark of the WordPress Foundation. This plugin is designed for WordPress but is not officially associated with the project.

All trademarks, service marks, and project names mentioned are the property of their respective owners. Usage in this plugin is solely for integration purposes and does not imply any affiliation.

== External Service Documentation ==
This plugin uses the public API from [Radio-Browser.info](https://www.radio-browser.info/) to fetch radio station data and stream audio. No user data is collected, stored, or transmitted by this plugin. The API is free to use and does not require registration or API keys.

**What is sent/received:**
- The plugin sends requests to Radio-Browser.info to retrieve station lists and streaming URLs.
- No personal or sensitive user data is sent to Radio-Browser.info.
- Audio streams are played directly from the radio station servers via the URLs provided by the API.

**Terms and Privacy:**
- [Radio-Browser.info Terms of Service](https://www.radio-browser.info/)
- [Radio-Browser.info Privacy Policy](https://www.radio-browser.info/privacy)

**No user tracking:**
This plugin does not track users or collect analytics data. Its sole purpose is to display and play radio stations using public data from Radio-Browser.info.

This powerful plugin connects to the [Radio-Browser.info](https://www.radio-browser.info/) database, providing access to over 30,000 radio stations worldwide with a beautiful, responsive radio player and customizable station lists.

Perfect for music blogs, radio websites, entertainment portals, or any site that wants to offer streaming audio content to visitors.

== Features ==

* **Global Radio Database:** Access to 30,000+ radio stations from Radio-Browser.info
* **Beautiful Audio Player:** Modern, responsive HTML5 audio player with volume controls
* **Customizable Radio Lists:** Display radio stations with filtering and sorting options
* **Smart Search Functionality:** Find stations by name, country, or genre
* **SEO-Friendly URLs:** Clean, readable URLs for individual radio stations
* **Responsive Design:** Works perfectly on desktop, tablet, and mobile devices
* **Easy Integration:** Simple shortcodes to embed radio lists and players anywhere
* **Audio Streaming Proxy:** Built-in proxy for smooth audio streaming with CORS support
* **Country Filtering:** Filter stations by country with support for all nations
* **Multiple Sort Options:** Sort by popularity, name, bitrate, or random order
* **Station Information:** Display station logos, descriptions, and statistics
* **Click Tracking:** Integration with Radio-Browser.info's click statistics

== Screenshots ==

1. Radio player displaying a selected station with controls and information
2. Radio station list with country filter and search functionality
3. Admin configuration panel for managing plugin settings
4. Mobile responsive radio player interface
5. Radio list with different sorting and filtering options applied

== Minimum Requirements ==

For this plugin to work correctly, you will need:

* WordPress version 5.0 or later
* PHP version 7.4 or later
* An active internet connection for streaming radio content
* Modern web browser with HTML5 audio support

== External Libraries ==

* This plugin uses the [Select2](https://select2.org/) JavaScript library to enhance the search and selection experience for radio stations. Select2 provides a modern, responsive dropdown with search, filtering, and accessibility features, making it easier for users to find and select stations from large lists.

== Installation ==

There are two ways to install the Radio Browser for WP plugin:

= From your WordPress Dashboard (Recommended) =

1. In your WordPress admin panel, navigate to **Plugins > Add New**
2. Use the search bar to find "Radio Browser for WP"
3. Locate the plugin in the search results and click the **Install Now** button
4. Once the installation is complete, click the **Activate** button

= Manual Upload via .zip File =

1. Download the plugin's `.zip` file from the official WordPress.org plugin page
2. In your WordPress admin panel, navigate to **Plugins > Add New**
3. At the top of the page, click the **Upload Plugin** button
4. Click **Choose File** and select the `.zip` file you downloaded in step 1
5. Click **Install Now**
6. After the installation is complete, click the **Activate Plugin** button

After activation, you can start using the shortcodes immediately. No additional configuration is required.

== Usage ==

Using the Radio Browser plugin is straightforward with two simple shortcodes:

= Radio Station List =

Display a list of radio stations with filtering options:

`[radio_browser_list]`

**Available Parameters:**

* `player_page` - The page slug where your radio player is located (default: "player")
* `countrycode` - Filter stations by country code (default: "BR" for Brazil)
* `limit` - Number of stations to display (default: 20)
* `sort` - Sort order: "clickcount", "name", "random", "bitrate" (default: "clickcount")
* `search` - Pre-filter stations by search term
* `hide_country` - Hide country filter (yes/no)
* `hide_limit` - Hide limit field (yes/no)
* `hide_sort` - Hide sort options (yes/no)
* `hide_search` - Hide search field (yes/no)
* `hide_all_filters` - Hide entire filter form (yes/no)

**Example:**
`[radio_browser_list player_page="radio-player" countrycode="US" limit="50"]`

= Radio Player =

Display the audio player on a dedicated page:

`[radio_browser_player]`

This shortcode automatically detects the radio station from the URL and displays the appropriate player with controls and station information.

= Setting Up Your Radio Website =

1. Create a **Radio List Page:** Add a new page and insert the `[radio_browser_list]` shortcode
2. Create a **Player Page:** Add another page with the `[radio_browser_player]` shortcode
3. Configure the list shortcode to point to your player page using the `player_page` parameter
4. Publish both pages and start enjoying streaming radio!

== Enjoying the Plugin? ==

If you find the **Radio Browser for WP** plugin useful, please consider leaving a 5-star review on WordPress.org.

Your feedback is invaluable to us. It not only helps other website owners discover the plugin but also motivates us to continue developing and improving it. A positive review is the best way to show your support for our work.

[**Leave your review here!**](https://wordpress.org/support/plugin/lknwp-radio-browser/reviews/#new-post)

Thank you for being a part of our community!

== Frequently Asked Questions ==

= How many radio stations are available? =

The plugin connects to Radio-Browser.info, which contains over 30,000 radio stations from around the world. The database is constantly growing as new stations are added by the community.

= Do I need any API keys or accounts? =

No! The plugin works out of the box without requiring any API keys, accounts, or additional configuration. Simply install, activate, and start using the shortcodes.

= Can I customize the appearance of the radio player and lists? =

Yes, the plugin includes CSS classes that you can style with your theme's custom CSS. The player and lists are designed to be responsive and integrate well with most WordPress themes.

= Does the plugin work on mobile devices? =

Absolutely! The radio player and station lists are fully responsive and work perfectly on desktop, tablet, and mobile devices with modern browsers that support HTML5 audio.

= Are there any bandwidth costs for streaming? =

The audio streams come directly from the radio stations' servers, so there are no bandwidth costs for your website. The plugin acts as a directory and player interface.

= Can I filter stations by genre or language? =

Currently, the plugin supports filtering by country and searching by station name. More advanced filtering options may be added in future versions based on user feedback.

= Is the plugin compatible with caching plugins? =

Yes, the plugin is designed to work with popular caching plugins. The radio streaming functionality bypasses cache for real-time audio delivery.

= What if a radio station stops working? =

Radio stations in the Radio-Browser.info database are maintained by the community. If a station stops working, it's usually updated or removed from the database automatically. You can also report issues to the Radio-Browser.info project.

== Support ==

If you need help or have questions, please post them in the [support forum](https://wordpress.org/support/plugin/lknwp-radio-browser/) for the plugin on WordPress.org. We will be happy to assist you there.

== Changelog ==

= 1.0.0 = *2025/10/03*
* Initial plugin release
* Radio station list with country filtering and search
* HTML5 audio player with volume controls
* Integration with Radio-Browser.info database
* SEO-friendly URLs for individual stations
* Responsive design for all devices
* Audio streaming proxy with CORS support
* Support for 30,000+ radio stations worldwide

== Upgrade Notice ==

= 1.0.0 =
Initial release of Radio Browser for WP. Install to start streaming radio stations on your website.