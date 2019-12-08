  ## always cache these images & static assets
  if (req.request == "GET" && req.url.ext ~ "(?i)(css|js|gif|jpg|jpeg|bmp|png|ico|img|tga|webp|wmf)") {
    remove req.http.cookie;
  } else if (req.request == "GET" && req.url.path ~ "(xmlrpc\.php|wlmanifest\.xml)") {
    remove req.http.cookie;
  }

  ### do not cache these files:
  ## never cache the admin pages, or the server-status page
  if (req.request == "GET" && (req.url.path ~ "(wp-admin|bb-admin|server-status)")) {
    set req.http.X-Pass = "1";
  } else if (req.http.X-Requested-With == "XMLHttpRequest" && req.url !~ "recent_reviews") {
  # Do not cache ajax requests except for recent reviews
    set req.http.X-Pass = "1";
  } if (req.url.qs ~ "nocache" ||
      req.url.path ~ "(control\.php|wp-comments-post\.php|wp-login\.php|bb-login\.php|bb-reset-password\.php|register\.php)") {
    set req.http.X-Pass = "1";
  }

  # Remove wordpress_test_cookie except on non-cacheable paths
  if (!req.http.X-Pass && req.http.Cookie:wordpress_test_cookie) {
    remove req.http.Cookie:wordpress_test_cookie;
  }

  if ( req.http.Cookie ) {
    ### do not cache authenticated sessions
    if (req.http.Cookie ~ "(wordpress_|PHPSESSID)") {
      set req.http.X-Pass = "1";
    } else if (!req.http.X-Pass) {
      # Cleans up cookies by removing everything except vendor_region, PHPSESSID and themetype2
      set req.http.Cookie = ";" req.http.Cookie;
      set req.http.Cookie = regsuball(req.http.Cookie, "; +", ";");
      set req.http.Cookie = regsuball(req.http.Cookie, ";(vendor_region|PHPSESSID|themetype2|.*woocommerce.*)=", "; \1=");
      set req.http.Cookie = regsuball(req.http.Cookie, ";[^ ][^;]*", "");
      set req.http.Cookie = regsuball(req.http.Cookie, "^[; ]+|[; ]+$", "");

      if (req.http.Cookie == "") {
        remove req.http.Cookie;
      }
    }
  }
