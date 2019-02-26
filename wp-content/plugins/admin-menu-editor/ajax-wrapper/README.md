# AJAX Action Wrapper

**Warning: Work in progress.** Not intended for public consumption. There is no documentation.

This helper library makes it easier to handle AJAX requests in WordPress plugins.

### Goals
- Automate common, boring stuff.
  - [x] Automatically pass the `admin-ajax.php` URL and nonce to JS.
  - [x] Define required parameters.
  - [x] Define optional parameters with default values.  
  - [x] Automatically remove "magic quotes" that WordPress adds to `$_GET`, `$_POST` and `$_REQUEST`.
  - [x] Encode return values as JSON.
- Security should be the default.
  - [x] Generate and verify nonces. Nonce verification is on by default, but can be disabled.
  - [x] Check capabilities.
  - [x] Verify that all required parameters are set.
  - [x] Validate parameter values.
  - [x] Set the required HTTP method.
- Resilience.
  - [ ] Lenient response parsing to work around bugs in other plugins. For example, deal with extraneous whitespace and PHP notices in AJAX responses.
  - [ ] Multiple versions of the library can coexist on the same site.

