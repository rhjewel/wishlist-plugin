# WordPress Wishlist Plugin Specification

**Project Type:** WordPress Plugin  
**Development Style:** PHP OOP, namespace-ready, WordPress Coding Standards  
**Target:** Any public custom post type + optional WooCommerce support  
**Primary Goal:** Build a reusable wishlist plugin that works for products, posts, tours, rooms, portfolio items, services, packages, and any selected custom post type.

---

## 1. Plugin Concept

This plugin will allow users to add any supported WordPress post type item to a wishlist. It must support guest users, logged-in users, AJAX add/remove, custom icons, shortcodes, admin settings, wishlist count, and frontend wishlist page.

The plugin should not be WooCommerce-only. It should be built as a generic `post_id` based wishlist system. WooCommerce features like price, stock status, and add-to-cart will be treated as optional enhancements when the post type is `product` and WooCommerce is active.

---

## 2. Core Feature List

### 2.1 Must-Have Features

- Add to wishlist button.
- Remove from wishlist button.
- AJAX add/remove without page reload.
- Guest wishlist support using cookie/session token.
- Logged-in user wishlist support using user ID.
- Guest wishlist merge after login.
- Duplicate wishlist item prevention.
- Wishlist page shortcode.
- Wishlist button shortcode.
- Wishlist count shortcode.
- Admin settings page.
- Any public custom post type support.
- Admin-selectable post types.
- Normal icon and added icon option.
- Default heart outline icon when item is not added.
- Default heart filled icon when item is already added.
- Custom SVG icon support.
- Custom button text.
- Toast notification.
- Translation ready.
- Secure nonce system.
- Proper sanitization and escaping.

### 2.2 Optional WooCommerce Features

Only run WooCommerce-specific features if WooCommerce is active.

- Show price on wishlist page.
- Show sale price and regular price.
- Show stock status.
- Add to cart button from wishlist page.
- Support simple products first.
- Support variable product variation in a later phase.
- WooCommerce hook position support.

---

## 3. Admin Panel Options

Create an admin menu page:

```text
Wishlist Settings
```

Recommended menu location:

```text
Settings > Wishlist Settings
```

Or for branded plugin:

```text
Wishlist
```

### 3.1 General Settings

| Option | Type | Default | Description |
|---|---|---:|---|
| Enable Wishlist | Toggle | Yes | Globally enable/disable wishlist feature. |
| Enable Guest Wishlist | Toggle | Yes | Allow non-logged-in users to use wishlist. |
| Enable AJAX | Toggle | Yes | Add/remove wishlist without reload. |
| Wishlist Page | Page Select | None | Select the page where `[wishlist_page]` shortcode is used. |
| Redirect Guest to Login | Toggle | No | If enabled, guest users must login before adding items. |
| Merge Guest Wishlist After Login | Toggle | Yes | Merge cookie/session wishlist into logged-in user wishlist. |
| Delete Data on Uninstall | Toggle | No | Remove plugin DB table/options when uninstalled. |

### 3.2 Post Type Settings

| Option | Type | Default | Description |
|---|---|---:|---|
| Enabled Post Types | Checkbox List | product if WooCommerce active, otherwise post | Show all public post types using `get_post_types( [ 'public' => true ], 'objects' )`. |
| Exclude Post Types | Hidden/Internal | attachment | Never show wishlist for media attachments. |
| Auto Display Button | Toggle | No | Automatically show button for selected post types. |
| Auto Display Position | Select | Manual shortcode only | Position where button appears. |

Auto display positions:

```text
Manual Shortcode Only
Before Content
After Content
After Title
WooCommerce Before Add to Cart
WooCommerce After Add to Cart
WooCommerce After Product Thumbnail
```

For custom post types, default should be `Manual Shortcode Only`, because every theme/template layout is different.

### 3.3 Button Settings

| Option | Type | Default | Description |
|---|---|---:|---|
| Button Text | Text | Add to Wishlist | Text when item is not added. |
| Added Button Text | Text | Added to Wishlist | Text when item is already added. |
| Remove Button Text | Text | Remove | Text for remove action on wishlist page. |
| Show Text | Toggle | Yes | Show/hide button text. |
| Show Icon | Toggle | Yes | Show/hide icon. |
| Button CSS Class | Text | empty | Extra class for custom styling. |

### 3.4 Icon Settings

| Option | Type | Default | Description |
|---|---|---:|---|
| Icon Type | Select | SVG | Preset/SVG/custom HTML. |
| Normal Icon | Textarea | Heart outline SVG | Icon before adding item. |
| Added Icon | Textarea | Heart filled SVG | Icon after item added. |
| Icon Size | Number | 18 | Icon size in pixels. |
| Icon Color | Color | inherit | Normal icon color. |
| Added Icon Color | Color | #e0245e | Added icon color. |

Default normal icon:

```html
<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M12.1 21.35l-1.1-1C5.4 15.24 2 12.16 2 8.38 2 5.3 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.08C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.3 22 8.38c0 3.78-3.4 6.86-9 11.97l-.9 1z" fill="none" stroke="currentColor" stroke-width="2"/></svg>
```

Default added icon:

```html
<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M12.1 21.35l-1.1-1C5.4 15.24 2 12.16 2 8.38 2 5.3 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.08C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.3 22 8.38c0 3.78-3.4 6.86-9 11.97l-.9 1z" fill="currentColor"/></svg>
```

Security note: SVG output must be filtered with a strict `wp_kses()` allowed tags array. Do not output raw untrusted SVG.

### 3.5 Notification Settings

| Option | Type | Default | Description |
|---|---|---:|---|
| Enable Toast | Toggle | Yes | Show notification after add/remove. |
| Added Message | Text | Item added to wishlist. | Message after add. |
| Removed Message | Text | Item removed from wishlist. | Message after remove. |
| Already Added Message | Text | Item already exists in wishlist. | Duplicate prevention message. |
| Toast Position | Select | Bottom Right | Position of notification. |

### 3.6 Wishlist Page Settings

| Option | Type | Default | Description |
|---|---|---:|---|
| Empty Wishlist Message | Textarea | Your wishlist is empty. | Message when no item exists. |
| Show Featured Image | Toggle | Yes | Show post thumbnail. |
| Show Title | Toggle | Yes | Show item title. |
| Show Post Type | Toggle | Yes | Show item post type. |
| Show Date Added | Toggle | No | Show added date. |
| Show Remove Button | Toggle | Yes | Remove item from wishlist page. |
| Show View Button | Toggle | Yes | Link to item single page. |
| Enable Share Wishlist | Toggle | No | Public share URL support. |

### 3.7 WooCommerce Settings

Only show this section if WooCommerce is active.

| Option | Type | Default | Description |
|---|---|---:|---|
| Enable WooCommerce Support | Toggle | Yes | Enable product-specific wishlist behavior. |
| Show Price | Toggle | Yes | Show product price on wishlist page. |
| Show Stock Status | Toggle | Yes | Show stock status. |
| Show Add to Cart | Toggle | Yes | Add product to cart from wishlist page. |
| Remove After Add to Cart | Toggle | No | Remove product from wishlist after cart add. |
| Single Product Button Position | Select | After Add to Cart | WooCommerce hook position. |
| Shop Loop Button Position | Select | After Product Thumbnail | WooCommerce archive/shop position. |

---

## 4. Frontend Behavior

### 4.1 Button State

When item is not in wishlist:

```text
♡ Add to Wishlist
```

When item is already in wishlist:

```text
♥ Added to Wishlist
```

Button should have dynamic class:

```html
<button class="egwl-button" data-post-id="123" data-post-type="tour">
```

When added:

```html
<button class="egwl-button is-added" data-post-id="123" data-post-type="tour">
```

### 4.2 Frontend Markup Standard

```php
<button
    type="button"
    class="egwl-button <?php echo $is_added ? 'is-added' : ''; ?>"
    data-post-id="<?php echo esc_attr( $post_id ); ?>"
    data-post-type="<?php echo esc_attr( get_post_type( $post_id ) ); ?>"
    aria-pressed="<?php echo $is_added ? 'true' : 'false'; ?>"
>
    <span class="egwl-icon egwl-icon-normal" aria-hidden="true">
        <?php echo wp_kses( $normal_icon, egwl_get_allowed_svg_tags() ); ?>
    </span>

    <span class="egwl-icon egwl-icon-added" aria-hidden="true">
        <?php echo wp_kses( $added_icon, egwl_get_allowed_svg_tags() ); ?>
    </span>

    <span class="egwl-text">
        <?php echo esc_html( $is_added ? $added_text : $button_text ); ?>
    </span>
</button>
```

### 4.3 CSS State

```css
.egwl-button .egwl-icon-added {
    display: none;
}

.egwl-button.is-added .egwl-icon-normal {
    display: none;
}

.egwl-button.is-added .egwl-icon-added {
    display: inline-flex;
}
```

### 4.4 Wishlist Page Layout

Wishlist page should show:

- Item image.
- Item title.
- Post type label.
- Added date, optional.
- Price, if WooCommerce product and option enabled.
- Stock status, if WooCommerce product and option enabled.
- Add to cart button, if WooCommerce product and option enabled.
- View item button.
- Remove button.

---

## 5. Shortcodes

### 5.1 Wishlist Button Shortcode

```php
[wishlist_button]
```

Uses current post ID automatically.

```php
[wishlist_button id="123"]
```

Specific post ID.

Optional attributes:

```php
[wishlist_button id="123" show_text="yes" show_icon="yes" class="custom-class"]
```

### 5.2 Wishlist Page Shortcode

```php
[wishlist_page]
```

Alternative alias:

```php
[my_wishlist]
```

### 5.3 Wishlist Count Shortcode

```php
[wishlist_count]
```

Output example:

```html
<span class="egwl-count">3</span>
```

### 5.4 Wishlist Link Shortcode

```php
[wishlist_link]
```

Output example:

```html
<a href="/wishlist/" class="egwl-link">Wishlist <span>3</span></a>
```

---

## 6. Database Design

Use a custom table for better scalability.

Table name:

```sql
{$wpdb->prefix}egwl_items
```

Fields:

```sql
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
user_id bigint(20) unsigned NULL DEFAULT NULL,
session_id varchar(64) NULL DEFAULT NULL,
post_id bigint(20) unsigned NOT NULL,
post_type varchar(64) NOT NULL,
variation_id bigint(20) unsigned NULL DEFAULT NULL,
quantity int unsigned NOT NULL DEFAULT 1,
created_at datetime NOT NULL,
PRIMARY KEY (id),
KEY user_id (user_id),
KEY session_id (session_id),
KEY post_id (post_id),
KEY post_type (post_type),
UNIQUE KEY unique_user_item (user_id, post_id, variation_id),
UNIQUE KEY unique_session_item (session_id, post_id, variation_id)
```

Important: MySQL unique key with nullable columns can behave unexpectedly. If needed, use `user_key` and `item_key` strategy instead:

```sql
owner_type varchar(20) NOT NULL,
owner_id varchar(64) NOT NULL,
item_key varchar(128) NOT NULL,
UNIQUE KEY unique_owner_item (owner_type, owner_id, item_key)
```

Recommended final table:

```sql
CREATE TABLE {$table_name} (
    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    owner_type varchar(20) NOT NULL,
    owner_id varchar(64) NOT NULL,
    post_id bigint(20) unsigned NOT NULL,
    post_type varchar(64) NOT NULL,
    variation_id bigint(20) unsigned NOT NULL DEFAULT 0,
    quantity int unsigned NOT NULL DEFAULT 1,
    created_at datetime NOT NULL,
    PRIMARY KEY  (id),
    UNIQUE KEY owner_item (owner_type, owner_id, post_id, variation_id),
    KEY post_id (post_id),
    KEY post_type (post_type),
    KEY owner_lookup (owner_type, owner_id)
) {$charset_collate};
```

Owner examples:

```text
owner_type = user
owner_id   = 25
```

```text
owner_type = guest
owner_id   = generated_session_hash
```

---

## 7. Plugin Folder Structure

```text
egns-wishlist/
│
├── egns-wishlist.php
├── uninstall.php
├── readme.txt
│
├── includes/
│   ├── Plugin.php
│   ├── Activator.php
│   ├── Deactivator.php
│   ├── Installer.php
│   ├── Database.php
│   ├── Wishlist_Manager.php
│   ├── Session_Manager.php
│   ├── Assets.php
│   ├── Shortcodes.php
│   ├── Ajax.php
│   ├── Admin.php
│   ├── Settings.php
│   ├── Template_Loader.php
│   ├── Icons.php
│   ├── Hooks.php
│   └── WooCommerce.php
│
├── templates/
│   ├── wishlist-button.php
│   ├── wishlist-page.php
│   ├── wishlist-item.php
│   └── empty-wishlist.php
│
├── assets/
│   ├── css/
│   │   ├── frontend.css
│   │   └── admin.css
│   └── js/
│       ├── frontend.js
│       └── admin.js
│
└── languages/
    └── wishflow.pot
```

---

## 8. PHP OOP Architecture

Namespace example:

```php
namespace Egns\Wishlist;
```

Main classes:

### 8.1 `Plugin`

Responsible for bootstrapping the plugin.

Methods:

```php
init()
load_dependencies()
register_hooks()
```

### 8.2 `Activator`

Responsible for activation tasks.

Methods:

```php
activate()
create_tables()
add_default_options()
```

### 8.3 `Database`

Responsible for database operations.

Methods:

```php
get_table_name()
insert_item()
delete_item()
item_exists()
get_items()
get_count()
merge_guest_to_user()
```

### 8.4 `Wishlist_Manager`

Business logic layer.

Methods:

```php
add( $post_id, $variation_id = 0, $quantity = 1 )
remove( $post_id, $variation_id = 0 )
toggle( $post_id, $variation_id = 0 )
is_added( $post_id, $variation_id = 0 )
get_items()
get_count()
```

### 8.5 `Session_Manager`

Handles guest wishlist session ID.

Methods:

```php
get_session_id()
maybe_create_session()
set_cookie()
clear_cookie()
```

Cookie should be secure:

- Use random token.
- Hash before storing if needed.
- Set expiry, for example 30 days.
- Use `httponly` if possible.
- Use secure flag when SSL is active.

### 8.6 `Ajax`

Handles AJAX requests.

Actions:

```php
wp_ajax_egwl_toggle_wishlist
wp_ajax_nopriv_egwl_toggle_wishlist
wp_ajax_egwl_remove_wishlist
wp_ajax_nopriv_egwl_remove_wishlist
```

Required:

- Verify nonce.
- Sanitize post ID.
- Validate post type.
- Check if post type is enabled.
- Return JSON with new state and count.

### 8.7 `Shortcodes`

Registers:

```php
[wishlist_button]
[wishlist_page]
[my_wishlist]
[wishlist_count]
[wishlist_link]
```

### 8.8 `Admin` and `Settings`

Responsible for settings page and options registration.

Use WordPress Settings API.

### 8.9 `Template_Loader`

Responsible for loading templates from plugin or theme override.

Theme override path:

```text
your-theme/egns-wishlist/wishlist-page.php
```

### 8.10 `WooCommerce`

Only loaded if WooCommerce is active.

Responsibilities:

- Add wishlist button to product single page.
- Add wishlist button to shop loop.
- Show price/stock/add-to-cart on wishlist page.
- Handle add-to-cart from wishlist page.

---

## 9. WordPress Standards Checklist

Follow these standards carefully:

### 9.1 Security

- Prevent direct file access:

```php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
```

- Use nonce for AJAX.
- Use capability checks for admin settings.
- Sanitize all input.
- Escape all output.
- Use prepared SQL queries.
- Do not trust shortcode attributes.
- Do not output raw SVG without strict allowlist.

### 9.2 Sanitization Examples

```php
$post_id = absint( $_POST['post_id'] ?? 0 );
$text    = sanitize_text_field( wp_unslash( $_POST['text'] ?? '' ) );
$enabled = ! empty( $_POST['enabled'] ) ? 1 : 0;
```

### 9.3 Escaping Examples

```php
echo esc_html( $title );
echo esc_attr( $post_id );
echo esc_url( $url );
echo wp_kses( $svg, egwl_get_allowed_svg_tags() );
```

### 9.4 Database Query Example

```php
$exists = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT id FROM {$table_name} WHERE owner_type = %s AND owner_id = %s AND post_id = %d AND variation_id = %d LIMIT 1",
        $owner_type,
        $owner_id,
        $post_id,
        $variation_id
    )
);
```

### 9.5 Internationalization

All user-facing text must be translation-ready.

```php
esc_html__( 'Add to Wishlist', 'wishflow' );
```

Text domain:

```text
wishflow
```

### 9.6 Coding Style

- Follow WordPress PHP Coding Standards.
- Use OOP classes.
- Use meaningful method names.
- Prefix functions/hooks/options with `egwl_` or plugin namespace.
- Avoid global variables where possible.
- Use dependency injection where practical.
- Keep WooCommerce logic separate from core wishlist logic.

---

## 10. AJAX Response Format

Successful add:

```json
{
  "success": true,
  "data": {
    "status": "added",
    "is_added": true,
    "count": 3,
    "message": "Item added to wishlist."
  }
}
```

Successful remove:

```json
{
  "success": true,
  "data": {
    "status": "removed",
    "is_added": false,
    "count": 2,
    "message": "Item removed from wishlist."
  }
}
```

Error example:

```json
{
  "success": false,
  "data": {
    "message": "Invalid request."
  }
}
```

---

## 11. JavaScript Requirements

Use vanilla JavaScript or jQuery. Since WordPress already includes jQuery, jQuery is acceptable, but do not make the frontend heavy.

Frontend JS must:

- Listen for wishlist button click.
- Prevent default action.
- Read `data-post-id`.
- Send AJAX request with nonce.
- Toggle `.is-added` class.
- Update button text.
- Update `aria-pressed`.
- Update all visible wishlist counters.
- Show toast notification.
- Handle errors gracefully.

Localized JS data:

```php
wp_localize_script(
    'egwl-frontend',
    'egwlData',
    array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'egwl_nonce' ),
    )
);
```

---

## 12. Frontend CSS Requirements

CSS classes:

```text
.egwl-button
.egwl-button.is-added
.egwl-icon
.egwl-icon-normal
.egwl-icon-added
.egwl-text
.egwl-count
.egwl-page
.egwl-item
.egwl-toast
```

CSS should be minimal and theme-friendly. Avoid aggressive styling.

---

## 13. Hooks and Filters for Developers

Add developer-friendly hooks.

### Actions

```php
do_action( 'egwl_before_add_item', $post_id, $owner );
do_action( 'egwl_after_add_item', $post_id, $owner );
do_action( 'egwl_before_remove_item', $post_id, $owner );
do_action( 'egwl_after_remove_item', $post_id, $owner );
do_action( 'egwl_before_wishlist_page' );
do_action( 'egwl_after_wishlist_page' );
```

### Filters

```php
apply_filters( 'egwl_enabled_post_types', $post_types );
apply_filters( 'egwl_button_text', $text, $post_id, $is_added );
apply_filters( 'egwl_button_icon', $icon, $post_id, $is_added );
apply_filters( 'egwl_wishlist_items', $items, $owner );
apply_filters( 'egwl_template_path', $template, $template_name );
```

---

## 14. Template Override Standard

Plugin should load templates from theme first:

```text
your-theme/egns-wishlist/wishlist-button.php
your-theme/egns-wishlist/wishlist-page.php
your-theme/egns-wishlist/wishlist-item.php
your-theme/egns-wishlist/empty-wishlist.php
```

If not found, load from plugin:

```text
plugin/templates/
```

---

## 15. Accessibility Requirements

- Button must use `button` element, not only `a` tag.
- Use `aria-pressed="true/false"`.
- Icon should have `aria-hidden="true"`.
- Text should be readable by screen readers.
- Toast should use `aria-live="polite"`.
- Keyboard users must be able to add/remove wishlist.
- Focus outline should not be removed.

---

## 16. Privacy and Data

The plugin stores wishlist data. It should mention this in documentation.

Stored data:

- User ID for logged-in wishlist.
- Guest session ID for guest wishlist.
- Post ID.
- Post type.
- Date added.

Recommended privacy features:

- Add personal data exporter support in later phase.
- Add personal data eraser support in later phase.
- Allow admin to delete data on uninstall.

---

## 17. Development Phases for Codex

### Phase 1: Plugin Foundation

- Create plugin folder.
- Create main plugin file.
- Add plugin header.
- Add constants.
- Add autoloader or manual includes.
- Create `Plugin` class.
- Register activation/deactivation hooks.

### Phase 2: Database and Installer

- Create custom table.
- Add default options.
- Add database helper class.
- Add insert/delete/exists/count methods.

### Phase 3: Session and Owner System

- Create guest session cookie.
- Detect logged-in user.
- Build owner resolver:

```text
logged-in user => owner_type=user, owner_id=user_id
guest user     => owner_type=guest, owner_id=session_id
```

### Phase 4: Wishlist Manager

- Add item.
- Remove item.
- Toggle item.
- Check item exists.
- Get wishlist items.
- Get wishlist count.
- Prevent duplicates.

### Phase 5: AJAX

- Register AJAX actions.
- Verify nonce.
- Validate post ID and post type.
- Return JSON response.

### Phase 6: Shortcodes

- Add `[wishlist_button]`.
- Add `[wishlist_page]`.
- Add `[my_wishlist]` alias.
- Add `[wishlist_count]`.
- Add `[wishlist_link]`.

### Phase 7: Frontend Assets

- Add CSS.
- Add JS.
- Localize AJAX data.
- Add toast notification.
- Update button state.
- Update wishlist count.

### Phase 8: Admin Settings

- Create settings page.
- Add general settings.
- Add post type settings.
- Add button settings.
- Add icon settings.
- Add notification settings.
- Add wishlist page settings.

### Phase 9: Any Custom Post Type Support

- Use public post type list.
- Save enabled post types.
- Validate wishlist button visibility.
- Add manual shortcode support for any selected post type.

### Phase 10: WooCommerce Integration

- Detect WooCommerce.
- Add button to product single page.
- Add button to shop loop.
- Show price on wishlist page.
- Show stock status.
- Add add-to-cart support.

### Phase 11: Guest to User Merge

- On login, merge guest wishlist items into user wishlist.
- Avoid duplicates.
- Clear guest cookie after merge if appropriate.

### Phase 12: Template Override and Developer Hooks

- Add template loader.
- Add theme override support.
- Add actions and filters.

### Phase 13: Final Testing

Test with:

- Logged-out user.
- Logged-in user.
- Post type `post`.
- Custom post type like `tour`.
- WooCommerce product.
- Empty wishlist.
- Duplicate item.
- AJAX disabled fallback.
- Cache plugin enabled.
- Elementor template with shortcode.

---

## 18. Codex Working Rules

When using Codex, do not ask it to build everything in one prompt if token limit is an issue. Build by phases.

After each phase, ask Codex to create or update a progress file:

```text
PROGRESS.md
```

The progress file must contain:

```text
Completed phases
Current phase
Files created
Files modified
Known issues
Next steps
Exact next prompt to continue
```

Also ask Codex to update a task checklist:

```text
TASKS.md
```

Recommended checklist format:

```markdown
# Wishlist Plugin Task Checklist

- [x] Phase 1: Plugin Foundation
- [x] Phase 2: Database and Installer
- [ ] Phase 3: Session and Owner System
- [ ] Phase 4: Wishlist Manager
```

---

## 19. Codex Initial Prompt

Use this prompt to start the project:

```text
You are a senior WordPress plugin developer. Build a WordPress wishlist plugin using PHP OOP and latest WordPress standards.

Read the full specification from `wordpress-wishlist-plugin-spec.md` first.

Important rules:
1. Build the plugin phase by phase.
2. Follow WordPress PHP Coding Standards.
3. Use secure nonce verification, sanitization, escaping, prepared SQL, and capability checks.
4. Use OOP classes.
5. Keep WooCommerce integration optional and separate from core wishlist logic.
6. The plugin must support any selected public custom post type, not only WooCommerce products.
7. Add normal heart outline icon and added heart filled icon support.
8. Add admin settings for custom SVG icons.
9. After finishing each phase, update `PROGRESS.md` and `TASKS.md`.
10. Do not move to the next phase until the current phase is complete and tested logically.

Start with Phase 1: Plugin Foundation.
Create the plugin folder and required base files.
```

---

## 20. Codex Continue Prompt After Token Limit

If Codex stops because of token limit or you continue next day, use this prompt:

```text
Continue from the last completed place.

First read these files:
1. `wordpress-wishlist-plugin-spec.md`
2. `PROGRESS.md`
3. `TASKS.md`

Then identify:
- last completed phase
- current incomplete phase
- files already created
- next required task

Continue exactly from the next pending task. Do not rewrite completed files unless needed. If modifying existing files, explain what changed.

After completing this step, update `PROGRESS.md` and `TASKS.md` again with the new status and the next prompt to continue.
```

---

## 21. Codex Phase-Specific Prompt Template

Use this when you want Codex to work on a specific phase:

```text
Read `wordpress-wishlist-plugin-spec.md`, `PROGRESS.md`, and `TASKS.md`.

Now work only on Phase [NUMBER]: [PHASE NAME].

Requirements:
- Follow the specification exactly.
- Use WordPress OOP plugin structure.
- Keep security and escaping in mind.
- Do not add unrelated features.
- Update all required files for this phase.
- At the end, update `PROGRESS.md` and `TASKS.md`.
- Add the exact next prompt I should use to continue.
```

---

## 22. Codex Review Prompt

Use this after several phases are complete:

```text
Review the current wishlist plugin codebase.

Check for:
1. WordPress Coding Standards issues.
2. Security issues.
3. Missing sanitization.
4. Missing escaping.
5. Missing nonce checks.
6. Bad SQL queries.
7. OOP structure problems.
8. WooCommerce dependency issues.
9. Custom post type compatibility issues.
10. Shortcode and AJAX issues.

Do not rewrite the whole plugin. Give a list of issues first, then fix them one by one. After fixing, update `PROGRESS.md` and `TASKS.md`.
```

---

## 23. Testing Checklist

### Admin Testing

- [ ] Settings page loads.
- [ ] Options save correctly.
- [ ] Post type checkboxes show public post types.
- [ ] Icon SVG saves correctly.
- [ ] Dangerous SVG/script is not output.
- [ ] Wishlist page selection works.

### Frontend Testing

- [ ] Button appears using shortcode.
- [ ] Button works on post.
- [ ] Button works on custom post type.
- [ ] Button works on WooCommerce product.
- [ ] AJAX add works.
- [ ] AJAX remove works.
- [ ] Counter updates.
- [ ] Toast appears.
- [ ] Wishlist page shows items.
- [ ] Remove from wishlist page works.
- [ ] Empty wishlist message works.

### Guest/User Testing

- [ ] Guest can add item.
- [ ] Guest wishlist remains after reload.
- [ ] Logged-in user can add item.
- [ ] Guest wishlist merges after login.
- [ ] Duplicate item is not inserted.

### WooCommerce Testing

- [ ] Price shows for product.
- [ ] Sale price shows correctly.
- [ ] Stock status shows correctly.
- [ ] Add to cart works for simple product.
- [ ] Wishlist product can be removed after add to cart if setting enabled.

---

## 24. Recommended Version Roadmap

### Version 1.0.0

- Generic wishlist system.
- Any selected post type support.
- Guest and logged-in support.
- AJAX add/remove.
- Shortcodes.
- Admin settings.
- Custom icons.
- Wishlist page.
- Wishlist count.

### Version 1.1.0

- WooCommerce price/stock/add-to-cart.
- Shop loop and single product button positions.

### Version 1.2.0

- Share wishlist public URL.
- Template override.
- Developer hooks.

### Version 1.3.0

- Gutenberg block.
- Elementor widget.
- REST API endpoints.

### Version 1.4.0

- Analytics: most wishlisted items.
- Admin wishlist reports.
- Email reminder integration.

---

## 25. Important Implementation Notes

1. Build the core wishlist system around `post_id`, not `product_id`.
2. Store `post_type` with every wishlist item.
3. WooCommerce is only an integration layer.
4. Custom SVG must be sanitized using strict allowed tags.
5. Always verify nonce in AJAX.
6. Always validate enabled post types before adding an item.
7. Use prepared SQL for all custom table queries.
8. Use `wp_enqueue_script` and `wp_enqueue_style`; do not hardcode assets.
9. Load frontend assets only when needed if possible.
10. Keep templates override-friendly.
11. Keep all user-facing text translation-ready.
12. Maintain `PROGRESS.md` and `TASKS.md` after every Codex session.

---

## 26. Reference Standards to Follow

Use these WordPress/WooCommerce references while implementing:

- WordPress Plugin Handbook: https://developer.wordpress.org/plugins/
- WordPress Security: https://developer.wordpress.org/plugins/security/
- WordPress Nonces: https://developer.wordpress.org/plugins/security/nonces/
- WordPress Sanitizing: https://developer.wordpress.org/apis/security/sanitizing/
- WordPress PHP Coding Standards: https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/
- WordPress Internationalization: https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/
- WooCommerce Developer Documentation: https://developer.woocommerce.com/docs/
