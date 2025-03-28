# Laravel Social Stats

[![Latest Version](https://img.shields.io/packagist/v/putheakhem/laravel-social-stats.svg?style=flat-square)](https://packagist.org/packages/putheakhem/laravel-social-stats)

> A Laravel package to fetch follower/subscriber counts from various social media platforms including Telegram, YouTube, and Facebook Pages.

---

## ✨ Features

- 📊 Get real-time follower counts from:
    - Telegram Channels
    - YouTube Channels
    - Facebook Pages
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
```

---

## 🔧 Usage

```php
use SocialStats;

// Telegram
$count = SocialStats::platform('telegram')->fetchCount('your_channel_username'); // No @ symbol

// YouTube
$count = SocialStats::platform('youtube')->fetchCount('UCxxxxxxx'); // Channel ID

// Facebook Page
$count = SocialStats::platform('facebook')->fetchCount('your_page_id'); // Page ID Number (3127652********)
```

> The package uses Laravel’s built-in HTTP and cache systems.

---

## ✅ Supported Platforms

- ✅ Telegram
- ✅ YouTube
- ✅ Facebook Pages
- 🔜 Instagram, TikTok (planned)

---

## 🔪 Testing

To run tests (if available):

```bash
composer test
```

---

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

---

## 💡 Contributing

Feel free to submit PRs or open issues for suggestions and improvements.

