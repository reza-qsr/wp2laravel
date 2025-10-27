# WP2Laravel

**This package allows you to interact with WordPress database tables (such as options, posts, terms, taxonomies, and users) using familiar WordPress-inspired methods, while leveraging Laravel's Eloquent ORM and dependency injection. It bridges Laravel applications with a WordPress database, enabling seamless data manipulation without needing to switch contexts or use raw SQL queries.**

> ⚠️ **Important — Testing / Beta**  
> This package is **currently in testing mode**. It may contain bugs or breaking changes. **Always make a full backup of your WordPress database** or operate on a cloned copy before using in production.

---

## Table of contents

- [Requirements](#Requirements)
- [Installation](#installation)
- [Docs](#docs)
    - [Options](#options)
    - [Posts](#posts)
    - [Terms](#terms)
    - [Taxonomies](#taxonomies)
    - [Users](#users)
- [Contributing](#contributing)
- [License](#license)
- [Author](#Author)

---

## Requirements
- PHP >= 8.2
- Laravel >= 11.0

---
## Installation

Install the package via Composer:
```
composer require rezaqsr/wp2laravel
```
Publish the configuration file:
```
php artisan vendor:publish --tag=wp2laravel-config
```
This will create `config/wp2laravel.php`, where you can specify the database connection name for your WordPress.
---
## Docs

### Options
Manage WordPress options (stored in the wp_options table).


1. getOption(string $key, $default = null): Retrieve an option value by key. Returns the default if not found. Automatically unserializes array/object values.

```
$siteUrl = Wp2Laravel::getOption('siteurl', 'https://example.com');
```


2. updateOption(string $key, $value): bool: Update or create an option. Automatically serializes array/object values.
```
Wp2Laravel::updateOption('siteurl', 'https://new-example.com');
```


deleteOption(string $key): bool: Delete an option by key.
```
Wp2Laravel::deleteOption('temporary_option');
```


addOption(string $key, $value, string $autoload = 'yes'): bool: Add a new option only if it doesn't exist. Automatically serializes values.
```
Wp2Laravel::addOption('new_option', ['key' => 'value'], 'no');
```


---
## Contributing
Contributions are welcome! Please submit pull requests or open issues on the GitHub repository.

---
## License
This project is open-sourced under the MIT License.

---
## Author
Developed by Reza Qsr with ❤