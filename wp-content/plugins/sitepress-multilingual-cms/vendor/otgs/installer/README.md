# OTGS WP Installer

OTGS WP Installer is a library that allows you to install and upgrade plugins and themes developed by OnTheGoSystems.

## Installation

First, add OTGS WP Installer as a dependency with [Composer](http://getcomposer.org):

```bash
composer require --dev otgs/installer:dev-master
```

Make sure that your bootstrap file is loading the composer autoloader:

```php
require_once 'vendor/autoload.php';
```

Then, load the OTGS WP Installer bootstrap. Before the `plugins_loaded` action add:

```php
include 'vendor/otgs/installer/loader.php';
```

If you're not using composer to install this library, just unpack the archive anywhere inside the plugin or theme folder and then include the bootstrap file and mentioned in the paragraph above.

Optionally, you can specify parameters to configure showing a dedicated UI under `Plugins -> Install New` or to load specific repositories.
By default, all repositories configrede in `repositories.xml` will be loaded:
* wpml - [WPML.org](http://wpml.org)
* toolset - [WP-Types.com](http://wp-types.com)

```php
WP_Installer_Setup( $wp_installer_instance,  
    array(
        'plugins_install_tab'   => '1',   // optional, default value: 0
        'repositories_include'  => array( 'wpml' ) // optional, default to empty (show all)
    )
); 
```

After `init`, configure display the OTGS WP Installer UI like in the example below:

```php 
WP_Installer_Show_Products( 
    array( 
        'template'         => 'compact', //required
        'product_name'     => 'WPML', 
        'box_title'        => 'Multilingual Avada', 
        'name'             => 'Avada', //name of theme/plugin
        'box_description'  => 'Avada theme is fully compatible with WPML - the WordPress Multilingual plugin. WPML lets 
                                      you add languages to your existing sites and includes advanced translation management.', 
        'repository'       => 'wpml', // required
        'package'          => 'multilingual-cms', // required
        'product'          => 'multilingual-cms' // required
    ) 
);
```

* `template` two options available: default and compact. Default will be the same GUI as on the Plugins -> Install new page while compact is a smaller version that can be fit in a different already existing screen
* `repository` only one product of a specific product package from a specific repository can be shown
* `package` only one product of a specific product package from a specific repository can be shown
* `product` only one product of a specific product package from a specific repository can be shown

