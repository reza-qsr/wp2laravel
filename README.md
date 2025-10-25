# 🪶 Wp2Laravel

[![Status](https://img.shields.io/badge/status-experimental-orange.svg)]()
[![PHP](https://img.shields.io/badge/PHP-%5E8.2-blue.svg)]()
[![Laravel](https://img.shields.io/badge/Laravel-11.x-f61500.svg)]()
[![License](https://img.shields.io/badge/license-MIT-green.svg)]()

**Wp2Laravel** is a bridge between **WordPress database schema** and **Laravel**, designed to make it easy to query,
manage, and manipulate WordPress data directly from your Laravel application — including posts, taxonomies, users,
terms, and metadata — while maintaining WordPress-compatible logic and function naming.

---

## ⚠️ Important Notice

🚧 This package is currently in **testing / experimental phase**.  
It may contain **bugs or incomplete features**.  
Before running it on a production environment:

> 🔒 **Always make a full database backup**,  
> or use a **cloned test version** of your WordPress database.

---

## ✨ Features

- 🧩 Query and manipulate **WordPress posts** from Laravel.
- 🗂 Handle **taxonomies and terms** in a WordPress-like way.
- 👤 Manage **users**, their **roles**, and **metadata**.
- 🔑 Includes a **WordPress-compatible password hasher**.
- 🧪 Built-in **Orchestra Testbench** integration for testing.

---

## ⚙️ Requirements

| Requirement | Version                           |
|-------------|-----------------------------------|
| PHP         | ^8.2                              |
| Laravel     | 11.x                              |
| Database    | MySQL (existing WordPress schema) |
| Composer    | ^2.6                              |
| Testbench   | ^10.0                             |

---

## 🧱 Installation

1. Add the package to your Laravel project:
   ```bash
   composer require rezaqsr/wp2laravel
   ```
2. Register the service provider (if not auto-discovered):

    ```bash
   'providers' => [
        RezaQsr\Wp2Laravel\Providers\Wp2LaravelServiceProvider::class,
   ],
   ```
3. Publish config if available
   ```bash
   php artisan vendor:publish --tag=wp2laravel-config
   ```
---
## 📄 License

This project is open-sourced under the MIT License.

---
## 🧔 Author

Developed by Reza Qsr
Designed with ❤️ for developers migrating WordPress data into Laravel.