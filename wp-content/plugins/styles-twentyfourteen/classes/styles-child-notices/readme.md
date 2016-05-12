# Setup

This class is meant to be included in Styles child-plugins.

For the class to work, child plugins must have a WordPress plugin header with `Require` set, like this:

    /*
    Plugin Name: Styles: TwentyEleven
    Plugin URI: http://stylesplugin.com
    
    Require: Styles 1.0.7
    Styles Class: Styles_Child_Theme
    */    

The version number is optional.

The child-plugin must also include the class. For example:

    if ( !class_exists( 'Styles_Child_Notices' ) ) {
    	include dirname( __FILE__ ) . '/classes/styles-child-notices/styles-child-notices.php';
    }

Once installed `Styles_Child_Notices` offers these features:

* Check all plugins for "Require" header.
* If "Require" is set, display notices if the named plugin is not installed or activated.
* Provide links for installation from wordpress.org or activation in WordPress.
* Check and display notice for required version number is one is specified.
* Specifically for `Styles`, display notices in `customize.php` in addition to the WordPress Admin.

# Including with Git subtree

Excerpt from http://go.brain.st/15ExgfV
http://blogs.atlassian.com/2013/05/alternatives-to-git-submodule-git-subtree/

## Adding the sub-project as a remote

Adding the subtree as a remote allows us to refer to it in shorter form:

    git remote add -f styles-child-notices git@github.com:stylesplugin/styles-child-notices.git

Now we can add the subtree (as before), but now we can refer to the remote in short form:

    git subtree add --prefix classes/styles-child-notices styles-child-notices master --squash

The command to update the sub-project at a later date becomes:

    git fetch styles-child-notices master
    git subtree pull --prefix classes/styles-child-notices styles-child-notices master --squash

## Contributing back to upstream

We can freely commit our fixes to the sub-project in our local working directory now.

When it’s time to contribute back to the upstream project we need to fork the project and add it as another remote:

    git remote add pdclark-child-notices git@github.com:stylesplugin/pdclark-child-notices.git

Now we can use the subtree push command like the following:

    git subtree push --prefix=classes/styles-child-notices/ pdclark-child-notices master

    git push using:  styles-child-notices master
    Counting objects: 5, done.
    Delta compression using up to 4 threads.
    Compressing objects: 100% (3/3), done.
    Writing objects: 100% (3/3), 308 bytes, done.
    Total 3 (delta 2), reused 0 (delta 0)
    To git@github.com:stylesplugin/styles-child-notices.git
      02199ea..dcacd4b  dcacd4b -} master

After this we’re ready and we can open a pull-request to the maintainer of the package.