# WordPress

This is a WordPress repository configured to run on the [Pantheon platform](https://pantheon.io).

Pantheon is website platform optimized and configured to run high performance sites with an amazing developer workflow. There is built-in support for features such as Varnish, Redis, Apache Solr, New Relic, Nginx, PHP-FPM, MySQL, PhantomJS and more. 

## Getting Started

### 1. Spin-up a site

If you do not yet have a Pantheon account, you can create one for free. Once you've verified your email address, you will be able to add sites from your dashboard. Choose "WordPress" to use this distribution.

### 2. Load up the site

When the spin-up process is complete, you will be redirected to the site's dashboard. Click on the link under the site's name to access the Dev environment.

![alt](http://i.imgur.com/2wjCj9j.png?1, '')

### 3. Run the WordPress installer

How about the WordPress database config screen? No need to worry about database connection information as that is taken care of in the background. The only step that you need to complete is the site information and the installation process will be complete.

We will post more information about how this works but we recommend developers take a look at `wp-config.php` to get an understanding.

![alt](http://i.imgur.com/4EOcqYN.png, '')

If you would like to keep a separate set of configuration for local development, you can use a file called `wp-config-local.php`, which is already in our .gitignore file.

### 4. Enjoy!

![alt](http://i.imgur.com/fzIeQBP.png, '')

## Branches

The `default` branch of this repository is where PRs are merged, and has [CI](https://github.com/pantheon-systems/WordPress/tree/default/.circleci) that copies `default` to `master` after removing the CI directories. This allows customers to clone from `master` and implement their own CI without needing to worry about potential merge conflicts.

## Custom Upstreams

If you are using this repository as a starting point for a custom upstream, be sure to review the [documentation](https://pantheon.io/docs/create-custom-upstream#pull-in-core-from-pantheons-upstream) and pull the core files from the `master` branch.
