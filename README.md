# 📻 Radio Browser for WordPress

[![WordPress Plugin](https://img.shields.io/wordpress/plugin/v/lknwp-radio-browser.svg?style=flat-square)](https://wordpress.org/plugins/lknwp-radio-browser/)
[![Downloads](https://img.shields.io/wordpress/plugin/dt/lknwp-radio-browser.svg?style=flat-square)](https://wordpress.org/plugins/lknwp-radio-browser/)
[![Rating](https://img.shields.io/wordpress/plugin/r/lknwp-radio-browser.svg?style=flat-square)](https://wordpress.org/plugins/lknwp-radio-browser/)
[![License](https://img.shields.io/badge/license-GPLv2-blue.svg?style=flat-square)](https://opensource.org/licenses/GPL-2.0)

**Contributors:** [linknacional](https://github.com/LinkNacional)  
**Website:** [linknacional.com.br](https://www.linknacional.com.br/)  
**Tags:** radio, streaming, audio, player, music  
**Tested up to:** 6.8  
**Stable version:** 1.0.0  
**License:** GPLv2 or later  
**Translations:** English / Portuguese (Brazil)

Integrate thousands of online radio stations into your WordPress site with a responsive player and customizable lists.

---


## ✨ Features

- 🌍 **30,000+ Stations:** Access the Radio-Browser.info database with stations from all over the world
- 🎵 **HTML5 Player:** Modern, responsive audio player with volume controls
- 📱 **Responsive Design:** Works perfectly on desktop, tablet, and mobile
- 🔍 **Smart Search:** Find stations by name, country, or genre
- 🌐 **Country Filters:** Filter stations by any country in the world
- 📊 **Multiple Sorting Options:** Sort by popularity, name, bitrate, or random
- 🔗 **SEO-Friendly URLs:** SEO-friendly URLs for individual stations
- 🎯 **Simple Shortcodes:** Easy integration with shortcodes on any page
- 🛡️ **Streaming Proxy:** Built-in proxy for smooth streaming with CORS support
- 📈 **Statistics:** Integration with click statistics from Radio-Browser.info
- 🎨 **Customizable:** CSS classes for complete visual customization
- ⚡ **Performance:** Fast and optimized loading

---


## 📝 Description

**Radio Browser for WordPress** is a complete [WordPress](https://www.linknacional.com.br/wordpress/) plugin to integrate thousands of online radio stations into your site. It connects directly to the [Radio-Browser.info](https://www.radio-browser.info/) database, offering access to over 30,000 radio stations worldwide.

Perfect for music blogs, radio sites, entertainment portals, or any site that wants to offer audio streaming content to visitors. With a responsive design and modern HTML5 player, your users will have an exceptional experience on any device.

---


## ⚙️ How to Use

### 📋 Radio List
```
[radio_browser_list]
```

**Available Parameters:**
- `player_page` - Page where the player is located (default: "player")
- `countrycode` - Filter by country (default: "BR")
- `limit` - Number of stations (default: 20)
- `sort` - Sorting: "clickcount", "name", "random", "bitrate"
- `search` - Predefined search term
- `hide_country` - Hide country filter (yes/no)
- `hide_search` - Hide search field (yes/no)
- `hide_all_filters` - Hide all filters (yes/no)

**Example:**
```
[radio_browser_list player_page="radio-player" countrycode="US" limit="50"]
```

### 🎵 Radio Player
```
[radio_browser_player]
```

### 🚀 Quick Setup
1. Create a **List Page:** Add `[radio_browser_list]`
2. Create a **Player Page:** Add `[radio_browser_player]`
3. Set the `player_page` parameter in the list to point to your player page
4. Publish the pages and start streaming!

---


## 📦 Installation

1. Download the plugin or install directly from the WordPress repository.
2. In the WordPress admin panel, go to **Plugins > Add New**.
3. Click **Upload Plugin** and select the ZIP file (if downloaded).
4. Click **Install Now** and then **Activate Plugin**.
5. Start using the shortcodes immediately - no additional setup required!

---


## 🎯 Use Cases

- 📻 **Radio Sites:** Create a complete online radio portal
- 🎵 **Music Blogs:** Add stations related to your content
- 🌍 **Entertainment Portals:** Offer a variety of audio content
- 🏢 **Corporate Sites:** Background music for the workplace
- 📰 **News Portals:** Add news radio stations
- 🎓 **Educational Sites:** Educational and cultural stations

---


## 🔧 Minimum Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Internet connection for streaming
- Modern browser with HTML5 audio support

---


## 📖 Documentation & Support

- [Plugin Documentation](https://wordpress.org/plugins/lknwp-radio-browser/)
- [Link Nacional WordPress Specialist](https://www.linknacional.com.br/wordpress/)
- [Support](https://www.linknacional.com.br/wordpress/suporte/)
- [Radio-Browser.info API](https://www.radio-browser.info/)

---


## 📢 Turn your site into a global radio station with over 30,000 live stations!

---