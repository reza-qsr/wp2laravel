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


1. `getOption(string $key, $default = null)`: Retrieve an option value by key. Returns the default if not found. Automatically unserializes array/object values.

```
$siteUrl = Wp2Laravel::getOption('siteurl', 'https://example.com');
```
2. `updateOption(string $key, $value): bool`: Update or create an option. Automatically serializes array/object values.
```
Wp2Laravel::updateOption('siteurl', 'https://new-example.com');
```
3. `deleteOption(string $key): bool`: Delete an option by key.
```
Wp2Laravel::deleteOption('temporary_option');
```
4. `addOption(string $key, $value, string $autoload = 'yes')`: bool: Add a new option only if it doesn't exist. Automatically serializes values.
```
Wp2Laravel::addOption('new_option', ['key' => 'value'], 'no');
```
### Posts
Manage WordPress posts (stored in the wp_posts table).


1. `getPost(int $id)` : Retrieve a single post by ID. Returns an Eloquent model or null.
```
$post = Wp2Laravel::getPost(1);
```


2. `getPosts(array $args = [])`: Retrieve multiple posts with query arguments. Supports WordPress-style filters.
Supported $args keys:

- post_type: String (e.g., 'post', 'page').
- post_status: String (e.g., 'publish', 'draft').
- include: Array of post IDs to include.
- exclude: Array of post IDs to exclude.
- author: Integer (post author ID).
- meta_query: Array of meta queries (similar to WP_Meta_Query).
- Each clause: ['key' => string, 'value' => mixed, 'compare' => string ('=', '!=', '>', etc.), 'type' => string ('NUMERIC', 'CHAR')].
Supports nested queries and 'relation' => 'AND'/'OR'.
- tax_query: Array of taxonomy queries (similar to WP_Tax_Query).
- Each clause: ['taxonomy' => string, 'terms' => array, 'operator' => string ('IN', 'NOT IN', 'AND', 'EXISTS', 'NOT EXISTS'), 'field' => string ('id', 'slug', 'name')].
Supports nested queries and 'relation' => 'AND'/'OR'.



```
$posts = Wp2Laravel::getPosts([
'post_type' => 'post',
'post_status' => 'publish',
'meta_query' => [
  'relation' => 'AND',
  ['key' => 'featured', 'value' => '1', 'compare' => '='],
 ],
'tax_query' => [
  ['taxonomy' => 'category', 'terms' => [1, 2], 'operator' => 'IN'],
],
]);
```

3. `insertPost(array $data)`: Insert a new post. Returns the created Eloquent model.
Supported $data keys (with defaults):

- post_author: 0
- post_date: Current timestamp
- post_date_gmt: Current UTC timestamp
- post_content: " "
- post_title: 'Draft'
- post_excerpt: " "
- post_status: 'draft'
- comment_status: 'open'
- ping_status: 'open'
- post_password: " "
- post_name: Auto-generated slug from title
- to_ping: " "
- pinged: " "
- post_modified: Current timestamp
- post_modified_gmt: Current UTC timestamp
- post_content_filtered: ''
- post_parent: 0
- guid: " "
- menu_order: 0
- post_type: 'post'
- post_mime_type: ''
- comment_count: 0

```
$newPost = Wp2Laravel::insertPost([
'post_title' => 'New Post',
'post_content' => 'Content here',
'post_status' => 'publish',
]);
```

4. `updatePost(int $id, array $data)`: bool: Update a post by ID.
```
Wp2Laravel::updatePost(1, ['post_title' => 'Updated Title']);
```


5. `deletePost(int $id): bool`: Delete a post by ID (also deletes associated meta and term relationships).
```
Wp2Laravel::deletePost(1);
```


### Post Meta
Manage post meta (stored in the wp_postmeta table).


1. `getPostMeta(int $postId, string $key, $default = null)`: Get meta value for a post. Unserializes if needed.
```
$meta = Wp2Laravel::getPostMeta(1, 'custom_field');
```


2. `hasPostMeta(int $postId, string $key): bool`: Check if meta exists for a post.
```
if (Wp2Laravel::hasPostMeta(1, 'custom_field')) { /* ... */ }
```


3. `updatePostMeta(int $postId, string $key, $value): bool`: Update or create post meta. Serializes if needed.
```
Wp2Laravel::updatePostMeta(1, 'custom_field', ['array' => 'value']);
```


4. `deletePostMeta(int $postId, string $key): bool`: Delete post meta by key.
```
Wp2Laravel::deletePostMeta(1, 'custom_field');
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