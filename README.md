# Laravel Social Stats

[![Latest Version](https://img.shields.io/packagist/v/putheakhem/laravel-social-stats.svg?style=flat-square)](https://packagist.org/packages/putheakhem/laravel-social-stats)
[![Total Downloads](https://img.shields.io/packagist/dt/putheakhem/laravel-social-stats.svg?style=flat-square)](https://packagist.org/packages/putheakhem/laravel-social-stats)
[![License](https://img.shields.io/github/license/putheakhem/laravel-social-stats.svg?style=flat-square)](LICENSE)

> A Laravel package to fetch follower/subscriber counts from various social media platforms including Telegram, YouTube, and Facebook Pages.

---

## ✨ Features

- 📊 Get real-time follower counts from:
    - Telegram Channels
    - YouTube Channels
    - Facebook Pages
    - Instagram Followers (via connected Facebook Page)
    - TikTok Followers
- ⚡ Built Support Laravel 11+ & PHP 8.2+
- 🔒 Caching with Laravel’s new `Cache::flexible()`
- 🧹 Facade support: `SocialStats::platform('telegram')`

---

## 📦 Installation

```bash
composer require putheakhem/laravel-social-stats
```

> Ensure you’ve configured your Laravel app to support package discovery.

---

## ⚙️ Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=config
```

Set the following in `.env`:

```env
YOUTUBE_API_KEY=your_youtube_api_key
FACEBOOK_ACCESS_TOKEN=your_facebook_access_token
TELEGRAM_BOT_TOKEN=your_telegram_bot_token
INSTAGRAM_ACCESS_TOKEN=your_instagram_access_token
INSTAGRAM_PAGE_ID=your_instagram_business_account_id
```

---

## 🔧 Usage

```php
use SocialStats;

// Telegram
SocialStats::platform('telegram')->fetchCount('your_channel_username'); // No @ symbol

// YouTube
SocialStats::platform('youtube')->fetchCount('UCxxxxxxx'); // Channel ID

// Facebook Page
SocialStats::platform('facebook')->fetchCount('your_page_id'); // Page ID Number (3127652********)

// Instagram (uses page_id to discover linked Instagram account)
SocialStats::platform('instagram')->fetchCount('fintechcenterfsa'); // Instagram username

// TikTok
SocialStats::platform('tiktok')->fetchCount('your_tiktok_username'); // TikTok username
```

> The package uses Laravel’s built-in HTTP and cache systems.

---

## ✅ Supported Platforms

- ✅ Telegram Channels Subscribers
- ✅ YouTube Channels Subscribers
- ✅ Facebook Page Likes
- ✅ Instagram Followers (via connected Facebook Page)
- ✅ TikTok Follower
- ⏳ Twitter Follower (planned)

---

## 🔪 Testing

To run tests:

```bash
composer test
```

---

## Support Me

If you find this package useful, consider supporting my work:
- [Buy me a coffee](https://www.buymeacoffee.com/iamputhea)

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

---

## 💡 Contributing

Feel free to submit PRs or open issues for suggestions and improvements.

