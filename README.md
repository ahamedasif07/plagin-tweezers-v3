# TickTweezers Quote Configurator

A standalone WordPress plugin that replaces the old Elementor-driven quote
flow with a fully custom, AJAX-powered 4-step wizard. Elementor's only job
now is to place the shortcode:

```
[ticktweezers_quote]
```

Everything else — markup, styling, validation, file upload, email, storage —
lives in this plugin.

## Why this over the old build

- The old flow mixed business logic into Elementor widgets, which meant the
  progress bar, validation, and layout were fragile and hard to change safely.
- This plugin keeps HTML (templates/), CSS (assets/css), JS (assets/js) and
  PHP (includes/) fully separated, with no inline styles/scripts.
- Every option (products, colors, sizes, quantity limits, upload rules,
  email recipients, success message) is editable from **Quote Settings** in
  wp-admin — nothing is hard-coded in the templates.

## Folder structure

```
ticktweezers-quote-plugin/
├── ticktweezers-quote.php     # bootstrap: constants, autoloader, activation
├── admin/
│   ├── settings-page.php      # renders the wp-admin settings screen
│   └── submission-meta-box.php# renders a saved quote request's details
├── assets/
│   ├── css/quote.css          # front-end styles (step nav, cards, overlay…)
│   ├── css/admin.css
│   ├── js/quote.js            # front-end wizard controller (vanilla JS)
│   └── js/admin.js            # settings-page repeater UI
├── includes/
│   ├── class-plugin.php       # singleton bootstrap, conditional asset loading
│   ├── class-shortcode.php    # [ticktweezers_quote]
│   ├── class-ajax.php         # wp_ajax_* endpoints (validate/upload/submit)
│   ├── class-validation.php   # all sanitize/validate logic, server-side
│   ├── class-email.php        # admin + customer email senders
│   └── class-admin.php        # settings screen, CPT registration, options
├── templates/
│   ├── quote-form.php         # wizard shell + step nav markup
│   ├── step-1.php / step-2.php / step-3.php / review.php / success.php
│   └── emails/                # HTML email templates
└── languages/                 # .pot / translations go here
```

## How the wizard avoids data loss on Back

All four `<section class="ttq-panel">` blocks are rendered into the DOM at
once (only the active one is visible). `quote.js` keeps an in-memory `state`
object mirrored into `sessionStorage`, so:

- Clicking **Back** never re-fetches or clears anything — it just toggles
  panel visibility and the step nav.
- A step bullet can be clicked directly to jump back to any completed step.
- A same-tab refresh restores exactly where the user left off.

## AJAX endpoints

| Action              | Purpose                                             |
|----------------------|------------------------------------------------------|
| `ttq_validate_step`  | Server-side validates one step's fields on "Next"   |
| `ttq_upload_logo`    | Uploads the logo immediately, returns a short-lived token + preview URL |
| `ttq_submit_quote`   | Re-validates everything, stores the request, sends emails |

All three are nonce-protected (`ttq_quote_nonce`) and registered for both
`wp_ajax_*` and `wp_ajax_nopriv_*` since this is a public lead-gen form.

The logo upload never trusts a client-supplied file path — `ttq_upload_logo`
stores the resolved server path behind a random `set_transient()` token, and
`ttq_submit_quote` only accepts that token.

## Storage

Submitted requests are saved as a lightweight custom post type (`ttq_quote`,
not publicly queryable) with the full submission array in post meta. This
was chosen over a custom DB table so submissions show up in a normal
wp-admin list table with zero extra migration code, while still being easy
to swap out later (see "Extending" below).

## Extending

- **Add a new field to a step**: add the input to the relevant
  `templates/step-*.php`, add sanitize/validate rules in
  `TTQ_Validation::validate_step()`, and read it in `quote.js`'s
  `readFormIntoState()` / `appendGroup()` calls.
- **Add a new product/color/size**: no code change needed — add it from
  **Quote Settings** in wp-admin.
- **Change the success animation**: it's pure SVG/CSS in `templates/success.php`
  + the `.ttq-tick-spinner` / `.ttq-success-check` rules in `quote.css`. Swap
  the placeholder tick SVG for your brand's icon whenever you have one —
  it's referenced in exactly one place.
- **Move storage to a custom table**: replace the `wp_insert_post()` call in
  `TTQ_Ajax::submit_quote()` — the rest of the plugin doesn't know or care
  how submissions are persisted.

## Requirements / setup notes

- PHP 7.4+, WordPress 5.8+.
- On activation the plugin creates `wp-content/uploads/ttq-logos/` with an
  `index.php` and `.htaccess` (`php_flag engine off`) to block script
  execution in the upload folder.
- Assets only enqueue on pages that actually contain the shortcode
  (checked via `has_shortcode()`), so the rest of the site isn't affected.
  If you embed the shortcode inside an Elementor template part where
  `has_shortcode()` can't see it, use the `ttq_force_enqueue_assets` filter.

## What still needs your input before going live

- **Product photography**: `image` is an empty string by default for both
  products in `TTQ_Admin::default_settings()` — add real photo URLs from
  **Quote Settings**, or the card falls back to a placeholder icon.
- **Tick icon in the success animation**: I used a simple generic SVG tick
  shape since I don't have your brand's actual icon asset — happy to swap
  it for the real one if you send it over.
- **Email "from" address / branding**: `TTQ_Email` uses `wp_mail()` defaults;
  wire up your SMTP plugin as usual, and adjust the HTML templates in
  `templates/emails/` to match your brand.
