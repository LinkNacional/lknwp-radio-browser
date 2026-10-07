=== Radio Browser Stations ===
Contributors: linknacional
Tags: radio, streaming, audio, player, music
Requires at least: 5.0
Tested up to: 7.1
Stable tag: 1.1.2
Requires PHP: 8.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://paraquemdoar.org/doar/

Display and play online radio stations from Radio-Browser.info with a beautiful player and customizable radio list.

== Description ==

Integrate thousands of **online radio stations** into your [WordPress](https://www.linknacional.com.br/wordpress/) website with the **Radio Browser Stations** plugin. 

== Disclaimer ==
This plugin is an independent project developed by LinkNacional. It is **not affiliated, endorsed, or sponsored** by Radio-Browser.info, [WordPress](https://www.linknacional.com.br/wordpress/), or Select2.

* Radio-Browser.info is a free, public API and database of radio stations. This plugin uses their API to fetch station data and stream audio, but there is no official relationship or partnership.
* Select2 is an open-source JavaScript library used to enhance dropdowns and search fields. This plugin includes Select2 locally and does not load it from external servers.
* [WordPress](https://www.linknacional.com.br/wordpress/) is a registered trademark of the [WordPress](https://www.linknacional.com.br/wordpress/) Foundation. This plugin is designed for [WordPress](https://www.linknacional.com.br/wordpress/) but is not officially associated with the project.

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
* **Modern & Legacy Layouts:** Choose the redesigned layout (or the previous one) via the `layout` shortcode attribute
* **Beautiful Audio Player:** Modern, responsive HTML5 audio player with volume controls and an animated audio visualizer
* **Live Now-Playing Metadata:** Album cover, song/artist and current audience (listeners) fetched server-side
* **Collections:** Favorites and Recents views saved in the browser, plus a "Continue listening" block
* **Genre, Country & Language Filters:** Filter stations by country, genre and language, with category pills
* **Smart Search Functionality:** Find stations by name, country, or genre
* **Multiple Sort Options:** Sort by popularity, name, bitrate, or random order, ascending or descending
* **Light/Dark Theme:** Built-in theme toggle for the player and the list
* **SEO-Friendly URLs:** Clean, readable URLs for individual radio stations
* **Responsive Design:** Works perfectly on desktop, tablet, and mobile devices
* **Easy Integration:** Simple shortcodes to embed radio lists and players anywhere
* **Audio Streaming Proxy:** Built-in proxy for smooth audio streaming with CORS support
* **Station Information:** Display station logos, genres, country flags, and bitrate/codec statistics
* **Click Tracking:** Integration with Radio-Browser.info's click statistics

== Screenshots ==

1. Plugin settings screen
2. Radio list with the new layout (dark mode)
3. Radio list with the new layout (light mode)
4. Modern radio player (dark mode)
5. Modern radio player (light mode)
6. Radio player displaying a selected station with controls and information
7. Admin configuration panel for managing plugin settings
8. Mobile responsive radio player interface
9. Radio station list with country filter and search functionality

== Minimum Requirements ==

For this plugin to work correctly, you will need:

* [WordPress](https://www.linknacional.com.br/wordpress/) version 5.0 or later
* PHP version 8.2 or later
* An active internet connection for streaming radio content
* Modern web browser with HTML5 audio support

== External Libraries ==

* This plugin uses the [Select2](https://select2.org/) JavaScript library to enhance the search and selection experience for radio stations. Select2 provides a modern, responsive dropdown with search, filtering, and accessibility features, making it easier for users to find and select stations from large lists.

== Installation ==

There are two ways to install the Radio Browser Stations plugin:

= From your [WordPress](https://www.linknacional.com.br/wordpress/) Dashboard (Recommended) =

1. In your [WordPress](https://www.linknacional.com.br/wordpress/) admin panel, navigate to **Plugins > Add New**
2. Use the search bar to find "Radio Browser Stations"
3. Locate the plugin in the search results and click the **Install Now** button
4. Once the installation is complete, click the **Activate** button

= Manual Upload via .zip File =

1. Download the plugin's `.zip` file from the official [WordPress.org](https://www.linknacional.com.br/wordpress/) plugin page
2. In your [WordPress](https://www.linknacional.com.br/wordpress/) admin panel, navigate to **Plugins > Add New**
3. At the top of the page, click the **Upload Plugin** button
4. Click **Choose File** and select the `.zip` file you downloaded in step 1
5. Click **Install Now**
6. After the installation is complete, click the **Activate Plugin** button

After activation, you can start using the shortcodes immediately. No additional configuration is required.

== Usage ==

Using the Radio Browser plugin is straightforward with two simple shortcodes:

= Radio Station List =

Display a list of radio stations with filtering options:

`[radio_browser_list layout="modern"]`

**Available Parameters:**

* `layout` - Layout to render: "modern" (new layout) or "legacy"/absent (previous layout)
* `player_page` - The page slug where your radio player is located (default: "player")
* `countrycode` - Filter stations by country code (default: the country of the [WordPress](https://www.linknacional.com.br/wordpress/) locale, e.g. pt_BR → BR; "all" if none matches). Visitors can change it and the choice is remembered per browser.
* `limit` - Number of stations to display (default: 20)
* `sort` - Sort order: "clickcount", "name", "random", "bitrate" (default: "clickcount")
* `reverse` - Reverse the order: "1" (descending, default) or "0" (ascending)
* `search` - Pre-filter stations by search term
* `genre` - Pre-select a genre/tag filter (default: "all")
* `hide_country` - Hide country filter (yes/no)
* `hide_limit` - Hide limit field (yes/no)
* `hide_sort` - Hide sort options (yes/no)
* `hide_order` - Hide the order (reverse) button (yes/no)
* `hide_genre` - Hide genre filter (yes/no)
* `hide_search` - Hide search field (yes/no)
* `hide_button` - Hide the submit button (yes/no, legacy layout only)
* `hide_all_filters` - Hide entire filter form (yes/no)

**Example:**
`[radio_browser_list player_page="radio-player" layout="modern" countrycode="US" limit="50"]`

= Radio Player =

Display the audio player on a dedicated page:

`[radio_browser_player layout="modern"]`

This shortcode automatically detects the radio station from the URL and displays the appropriate player with controls and station information.

= Setting Up Your Radio Website =

1. Create a **Radio List Page:** Add a new page and insert the `[radio_browser_list layout="modern"]` shortcode
2. Create a **Player Page:** Add another page with the `[radio_browser_player layout="modern"]` shortcode
3. Configure the list shortcode to point to your player page using the `player_page` parameter
4. Publish both pages and start enjoying streaming radio!

== Enjoying the Plugin? ==

If you find the **Radio Browser Stations** plugin useful, please consider leaving a 5-star review on [WordPress.org](https://www.linknacional.com.br/wordpress/).

Your feedback is invaluable to us. It not only helps other website owners discover the plugin but also motivates us to continue developing and improving it. A positive review is the best way to show your support for our work.

[**Leave your review here!**](https://wordpress.org/support/plugin/lknwp-radio-browser/reviews/#new-post)

Thank you for being a part of our community!

== Frequently Asked Questions ==

= How many radio stations are available? =

The plugin connects to Radio-Browser.info, which contains over 30,000 radio stations from around the world. The database is constantly growing as new stations are added by the community.

= Do I need any API keys or accounts? =

No! The plugin works out of the box without requiring any API keys, accounts, or additional configuration. Simply install, activate, and start using the shortcodes.

= Can I customize the appearance of the radio player and lists? =

Yes, the plugin includes CSS classes that you can style with your theme's custom CSS. The player and lists are designed to be responsive and integrate well with most [WordPress](https://www.linknacional.com.br/wordpress/) themes.

= Does the plugin work on mobile devices? =

Absolutely! The radio player and station lists are fully responsive and work perfectly on desktop, tablet, and mobile devices with modern browsers that support HTML5 audio.

= Are there any bandwidth costs for streaming? =

The audio streams come directly from the radio stations' servers, so there are no bandwidth costs for your website. The plugin acts as a directory and player interface.

= Can I filter stations by genre or language? =

Yes. The list supports filtering by country, genre and language (inside the "Filtros" panel), plus category pills and a search box. The available genres and languages come from the Radio-Browser.info API.

= Is the plugin compatible with caching plugins? =

Yes, the plugin is designed to work with popular caching plugins. The radio streaming functionality bypasses cache for real-time audio delivery.

= What if a radio station stops working? =

Radio stations in the Radio-Browser.info database are maintained by the community. If a station stops working, it's usually updated or removed from the database automatically. You can also report issues to the Radio-Browser.info project.

== Support ==

If you need help or have questions, please post them in the [support forum](https://wordpress.org/support/plugin/lknwp-radio-browser/) for the plugin on [WordPress.org](https://www.linknacional.com.br/wordpress/). We will be happy to assist you there.

== Changelog ==

= 1.1.2 = *2026/10/07*
* Fixes a leading blank line before the `<?php` tag in the main plugin file that could corrupt the WordPress sitemap output.

= 1.1.1 = *2026/10/05*
* Country filter default now follows the [WordPress](https://www.linknacional.com.br/wordpress/) locale (e.g. pt_BR → BR); visitors can change it and the choice is remembered.
* Same-origin stream proxy (AJAX) so the audio visualizer works even when the station does not send CORS headers.
* Plugin scripts and styles are now only loaded on pages that actually use the plugin shortcodes.

= 1.1.0 = *2026/09/30*
* Full redesign of the player and radio list: violet theme, gradients, glassmorphism and glow effects.
* Premium list sidebar: "Radio" logo, Discover/Favorites/Recents menu and a "Browse by" section (Genres/Countries/Languages) with icons.
* Working Favorites and Recents views (saved in the browser) and a "Continue listening" block.
* Filters by country, genre and language, category pills (rock, MPB, electronic, sertanejo, pop, jazz, news) and sorting (popular, name, bitrate, random).
* Player with live metadata: album cover, song/artist and audience (listeners) fetched server-side via an AJAX proxy (no CORS errors).
* Animated audio visualizer (waveform) and a light/dark theme toggle.
* Station cards with logo, genre, country flag and bitrate/codec/votes chips.
* UI hardening against the theme CSS (round play button; self-contained icons and fields).
* Security: API data escaped when building the cards; metadata proxy with SSRF protection and per-IP rate limiting.
* Admin help page rebuilt into navigable sections, with search, FAQ and copy buttons.
* Notice (editors only) when the layout attribute is invalid; visitors see the legacy layout.

= 1.0.1 = *2025/03/05*
* New icons and banners for the plugin.

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

= 1.1.2 =
Fixes a leading blank line in the main plugin file that could break the WordPress sitemap.

= 1.1.1 =
Locale-based country filter, a same-origin stream proxy for the visualizer, and conditional asset loading.

= 1.1.0 =
New list and player layouts, Favorites/Recents, genre and language filters, and live now-playing metadata.

= 1.0.0 =
Initial release of Radio Browser Stations. Install to start streaming radio stations on your website.