# WP2Laravel

**This package allows you to interact with WordPress database tables (such as options, posts, terms, taxonomies, and users) using familiar WordPress-inspired methods, while leveraging Laravel's Eloquent ORM and dependency injection. It bridges Laravel applications with a WordPress database, enabling seamless data manipulation without needing to switch contexts or use raw SQL queries.**

> ⚠️ **Important — Testing / Beta**  
> This package is **currently in testing mode**. It may contain bugs or breaking changes. **Always make a full backup of your WordPress database** or operate on a cloned copy before using in production.
If you found a bug in the package, please report it in the Issues section to fix.
---

## Table of contents

- [Requirements](#Requirements)
- [Installation](#installation)
- [Usage](#usage)
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
- Laravel >= 12.0

---
## Installation

Install the package via Composer :
```
composer require rezaqsr/wp2laravel
```
Publish the configuration file :
```
php artisan vendor:publish --tag=wp2laravel-config
```
This will create `config/wp2laravel.php`, where you can specify the database connection name for your WordPress.
in case tables have different prefix.

---
## Usage
The package provides a facade Wp2Laravel for easy access to all methods.
Import the facade at the top of your file :
```
use RezaQsr\Wp2Laravel\Facades\Wp2Laravel;
```
All methods are available through the facade or the manager instance.

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

**Supported $args keys:**

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

**Supported $data keys (with defaults):**

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

### Terms
Manage WordPress terms (stored in wp_terms, wp_term_taxonomy, and wp_term_relationships tables).


1. `getTerms(array $args = [])`: Retrieve terms with filters.

**Supported $args keys:**
- taxonomy: String or array (e.g., 'category').
- slug: String or array.
- search: String (search in name).

```
$terms = Wp2Laravel::getTerms(['taxonomy' => 'category', 'search' => 'news']);
```


2. `getTermBy(string $field, $value, string $taxonomy)`: Get term by field.

**Supported $field:**
- id 
- slug
- name
- term_taxonomy_id
```
$term = Wp2Laravel::getTermBy('slug', 'news', 'category');
```


3. `insertTerm(string $term, string $taxonomy, array $args = [])`: Insert a new term.

**Supported $args:**
- slug
- description
- parent


```
$newTerm = Wp2Laravel::insertTerm('New Term', 'category', ['description' => 'Desc']);
// Returns object with 'term' and 'taxonomy' models.
```


4. `updateTerm(int $termId, string $taxonomy, array $args = [])`: Update a term.

**Supported $args:**
- name
- slug
- description
- parent

```
Wp2Laravel::updateTerm(1, 'category', ['name' => 'Updated Term']);
// Returns object with updated 'term' and 'taxonomy'.
```


5. `deleteTerm(int $termId, string $taxonomy): bool`: Delete a term (cleans up relationships if no other taxonomies use it).
```
Wp2Laravel::deleteTerm(1, 'category');
```


6. `setPostTerms(int $postId, array $terms, string $taxonomy, bool $append = false): bool`: Set terms for a post (term IDs). If $append is false, replaces existing terms.
```
Wp2Laravel::setPostTerms(1, [1, 2], 'category');
```


7. `getPostTerms(int $postId, string $taxonomy)`: Get terms attached to a post.
```
$terms = Wp2Laravel::getPostTerms(1, 'category');
```

## Taxonomies
Manage WordPress taxonomies (stored in wp_term_taxonomy table).


1. `getTaxonomy(string $taxonomy)`: Get details for a single taxonomy.

```
$tax = Wp2Laravel::getTaxonomy('category');
// Returns object with 'name', 'description', 'parent', 'count'.
```


2. `getTaxonomies(array $args = [])`: Get multiple taxonomies.

**Supported $args:** 
- taxonomy (string or array to filter).

```
$taxonomies = Wp2Laravel::getTaxonomies(['taxonomy' => ['category', 'post_tag']]);
// Returns associative array of taxonomy objects.
```

## Users
Manage WordPress users (stored in wp_users table).


1. `insertUser(array $data)`: Insert a new user. Hashes password automatically.

**Required:** 
- user_login

**Supported $data:**
- user_login
- user_email
- user_pass' (plain text, will be hashed).
- user_nicename
- user_url
- display_name

**Defaults:**
- Subscriber role
- user_level 0

```
$newUser = Wp2Laravel::insertUser([
'user_login' => 'newuser',
'user_email' => 'user@example.com',
'user_pass' => 'password123',
]);
```

2. `updateUser(int $id, array $data): bool`: Update a user.

**Supported fields:** 
- 'user_login',
- 'user_email',
- 'display_name',
- 'user_nicename',
- 'user_url',
- 'user_pass' (plain text, will be hashed).
```
Wp2Laravel::updateUser(1, ['display_name' => 'Updated Name']);
```

3. `deleteUser(int $id): bool`: Delete a user (also deletes meta).
```
Wp2Laravel::deleteUser(1);
```


## User Meta
Manage user meta (stored in the wp_usermeta table).


1. getUserMeta(int $userId, string $key, $default = null): Get user meta value.
```
$meta = Wp2Laravel::getUserMeta(1, 'nickname');
```


2. `hasUserMeta(int $userId, string $key): bool`: Check if user meta exists.
```
if (Wp2Laravel::hasUserMeta(1, 'nickname')) { /* ... */ }
```


3. `updateUserMeta(int $userId, string $key, $value): bool`: Update or create user meta. Serializes if needed.
```
Wp2Laravel::updateUserMeta(1, 'nickname', 'Nick');
```

4. `deleteUserMeta(int $userId, string $key): bool`: Delete user meta by key.
```
Wp2Laravel::deleteUserMeta(1, 'nickname');
```

5. `getUserRoles(int $userId): array`: Get array of roles for a user (from 'wp_capabilities' meta).
```
$roles = Wp2Laravel::getUserRoles(1); // e.g., ['administrator']
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