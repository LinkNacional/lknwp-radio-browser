=== Radio Browser Stations ===
Contributors: linknacional
Tags: radio, streaming, audio, player, music
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.9.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://paraquemdoar.org/doar/

Display and play online radio stations from Radio-Browser.info with a beautiful player and customizable radio list.

== Description ==

Integrate thousands of **online radio stations** into your WordPress website with the **Radio Browser Stations** plugin. 

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
2. Admin configuration panel for managing plugin settings
3. Mobile responsive radio player interface
4. Radio station list with country filter and search functionality

== Minimum Requirements ==

For this plugin to work correctly, you will need:

* WordPress version 5.0 or later
* PHP version 7.4 or later
* An active internet connection for streaming radio content
* Modern web browser with HTML5 audio support

== External Libraries ==

* This plugin uses the [Select2](https://select2.org/) JavaScript library to enhance the search and selection experience for radio stations. Select2 provides a modern, responsive dropdown with search, filtering, and accessibility features, making it easier for users to find and select stations from large lists.

== Installation ==

There are two ways to install the Radio Browser Stations plugin:

= From your WordPress Dashboard (Recommended) =

1. In your WordPress admin panel, navigate to **Plugins > Add New**
2. Use the search bar to find "Radio Browser Stations"
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

If you find the **Radio Browser Stations** plugin useful, please consider leaving a 5-star review on WordPress.org.

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

= 1.9.1 = *2026/09/25*
* Header now on a single line: search on the left (up to 50% width) and the country flag + "Filtros" button on the right, with a gap between them.
* The "Filtros" button now uses the same pill style as the other controls (hardened against the theme button styles).

= 1.9.0 = *2026/09/25*
* Reorganized the list header: search bar + country flag + "Filtros" button.
* Genre, language and sorting now live inside the "Filtros" panel (open/close).
* Fixed the search field padding that was being overridden by the theme.

= 1.8.2 = *2026/09/25*
* Sidebar: items truly without background, with border-radius and left-aligned text (hardened against the theme CSS, which applies background/min-height to every <button>).

= 1.8.1 = *2026/09/25*
* Sidebar: items without background, left-aligned, with a left-to-right gradient on hover and on the selected item.
* The radio list now lives inside a block with its own scroll (no longer grows the page).
* Filter fields (country/genre/language/order) have fixed pill widths (the genre select no longer overflows).

= 1.8.0 = *2026/09/25*
* Premium sidebar on the list: "Radio" logo, Discover/Favorites/Recents menu, a "Browse by" section (Genres/Countries/Languages) with icons, and a footer with an animated wave + "Thousands of radios, one place.".
* Working views: Favorites and Recents filter the stations (saved in the browser).
* Language filter (Languages) using the Radio-Browser API language param.

= 1.7.0 = *2026/09/25*
* Radio list redesigned: premium dark UI (navy/indigo/purple), glassmorphism and micro-glow.
* Pill search bar, country/genre/order pill selects and category pills (rock, MPB, electronic, sertanejo, pop, jazz, news).
* Station cards with a featured logo, genre, country with flag, and bitrate/codec/votes chips (Radio-Browser API fields).
* Favorite a station (heart, saved in the browser) and a "● LISTENING NOW" state with an animated equalizer on the selected card.
* Security: API data is escaped when building the cards (prevents XSS).

= 1.6.1 = *2026/09/25*
* Fixed: the waves stopped working after a long time playing (e.g. 30 min) when pausing and resuming. The AudioContext is now resumed and the stream proxy is rebuilt when needed.

= 1.6.0 = *2026/09/25*
* Radio metadata is now fetched on the server (AJAX proxy endpoint), removing the CORS errors from the console.
* Below the waves it now shows the album cover, song/artist name and the current audience (listeners), when the station provides them.
* Album cover fetched from iTunes server-side.
* SSRF protection: only public hosts are queried.
* Per-IP rate limit on the endpoint (anti-abuse).
* Removed the public CORS proxies from the JS (no longer needed).

= 1.5.8 = *2026/09/25*
* Player: slightly smaller waves (height 160->145px), without changing the card height.

= 1.5.7 = *2026/09/25*
* Player: slightly smaller play button (158px) and slightly wider card (380px).

= 1.5.6 = *2026/09/25*
* Player: card reverted to the previous size; only the play button was enlarged (180px) with proportional white ball and icon, and the wave block grew to keep showing around it.

= 1.5.5 = *2026/09/25*
* Bigger player: wider card (420px), larger play button, cover, icon, waves and spacings, scaled proportionally.

= 1.5.4 = *2026/09/25*
* Player: increased the card height (~1.5x), keeping the width. The waves grew along to fill the block.

= 1.5.3 = *2026/09/25*
* Player: vertical spacing restored to the previous values and the card widened (~340px). The waves follow the new width.

= 1.5.2 = *2026/09/25*
* Visualizer: thicker bars (18 bars) and removed the white peak caps.
* Player: more vertical spacing (between the station title and the other sections, and at the bottom) so the component is taller and less compact.

= 1.5.1 = *2026/09/25*
* Bigger, more wave-like visualizer: taller block, 38 thinner/rounded bars and more visible peak caps.

= 1.5.0 = *2026/09/25*
* Reworked the audio visualizer into a smooth waveform: 30 rounded bars, eased animation (fast attack / slow release), peak caps that fall with "gravity" and a mirrored reflection with fade-out.
* More compact player: smaller play button, station cover, paddings and spacings.

= 1.4.3 = *2026/09/25*
* Player: play button back to the two-level look (big purple ball + inner white ball) with the dark play/pause icon centered.

= 1.4.2 = *2026/09/25*
* Player: the play button is now a perfect circle (box-sizing/aspect-ratio).
* Player: redesigned and centered the play/pause icon (removed the gray background circle inherited from the old theme).

= 1.4.1 = *2026/09/25*
* Player: fixed the play button being round again (the theme forced border-radius: 0 on every <button>).
* Player: fixed the "Copy link" icon not showing (the theme forced padding/min-height on buttons).
* Player: the play button turns green on hover (to play) and red on hover while playing again.

= 1.4.0 = *2026/09/25*
* Less rounded corners across the plugin (player, list and Help page), keeping a rounded yet subtler style.

= 1.3.3 = *2026/09/25*
* Fixed the last card in the list being taller than the others: all grid rows now share the same height (grid-auto-rows: 1fr).

= 1.3.2 = *2026/09/25*
* Removed the accent bar that appeared on top of the radio card on hover.
* Left-aligned the text in the Limit field and the Order button.

= 1.3.1 = *2026/09/25*
* List filter fields now grow to fill the whole row (flexbox layout), aligned with the search bar.

= 1.3.0 = *2026/09/25*
* Refactored the list filter form: all fields now share the same height, border radius, font size and spacing (no longer overridden by the theme).
* Filter layout is now a responsive grid that spans the full width.
* Fixed the Genre field (Select2): fills the full width, correct placeholder and themed dropdown.
* Labels without "caps-lock" (uppercase removed) and standardized sizes.
* Improved radio cards: larger logo, metadata line (country · genre · bitrate), top accent bar and richer hover.

= 1.2.3 = *2026/09/25*
* Softened the admin help hero shadow (removed the glow that radiated on all sides).

= 1.2.2 = *2026/09/25*
* Admin help hero now uses the lighter purple tone (same gradient as the active sidebar item and the copy button).

= 1.2.1 = *2026/09/25*
* Admin help page: WordPress notices (e.g. TGMPA/theme) are no longer injected inside the hero (added `wp-header-end`).
* Softened the hero shadow so it no longer darkens the section titles.
* Search field: icon moved to the right so it no longer overlaps the placeholder; clear button repositioned.

= 1.2.0 = *2026/09/25*
* Admin help page rebuilt into navigable sections with a sidebar (Getting Started, Player, List, Parameters, Hide Filters, Examples, FAQ).
* Added content search/filter to the documentation.
* New FAQ section.
* Copy button on each parameter row.
* Tab navigation (one section at a time) with URL hash support (#panel-...).

= 1.1.0 = *2026/09/25*
* Complete redesign of the player and radio list with a violet theme, gradients, glassmorphism and glow effects.
* Audio visualizer updated to violet/magenta tones.
* New brand color palette / design tokens added to colors.css.
* Visual refresh of the admin help page (hero + modern cards).
* Removed debug logs (error_log) from the radio listing.

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

= 1.0.0 =
Initial release of Radio Browser Stations. Install to start streaming radio stations on your website.