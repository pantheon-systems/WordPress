# Configuration

FAIR is designed to operate without user configuration, and with minimal changes to user workflows. Some configuration is provided for specific parts of FAIR for users and for hosts.


## Central repository mirror URL

By default, plugin and theme data for the central repository (`wordpress.org`) is provided from the AspirePress mirror at api.aspirecloud.net.

For hosts which prefer to use their own mirror for plugin and theme data, the `FAIR_DEFAULT_REPO_DOMAIN` constant can be set to a different domain. This domain must mirror the APIs available at `api.wordpress.org/plugins/` and `api.wordpress.org/themes/` (and sub-APIs thereof):

```php
define( 'FAIR_DEFAULT_REPO_DOMAIN', 'api.myhost.example.com' );
```

This can also be configured to use the original WordPress.org domain by setting the constant to api.wordpress.org:

```php
define( 'FAIR_DEFAULT_REPO_DOMAIN', 'api.wordpress.org' );
```

This constant only affects the default repository used for plugin and theme data. Using a different mirror (including WordPress.org) for code distribution does not affect any other APIs (such as the events API or Gravatar).
