<?php
/*
Plugin Name:  Styles: TwentyFourteen
Plugin URI:   http://wpdefault.com/styles-twentyfourteen-plugin/
Description:  Adds fonts and colors Customize options to the <a href="http://wordpress.org/themes/twentyfourteen" target="_blank">Twenty Fourteen default theme</a> using the <a href="http://wordpress.org/plugins/styles/" target="_blank">Styles plugin</a>.
Version:      1.0.2
Author:       Zulfikar Nore
Author URI:   http://www.wpstrapcode.com

Require:      Styles 1.1.7
Styles Class: Styles_Child_Theme
*/

/**
 * Original plugin is Styles: TwentyThirteen
 * Copyright (c) 2013 Brainstorm Media. All rights reserved.
 *
 * Styles: TwentyFourteen in the derivative format Copyright (c) 2013 WP Strap Code/ZGani AKA Zulfikar Nore
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

if ( !class_exists( 'Styles_Child_Notices' ) ) {
    include dirname( __FILE__ ) . '/classes/styles-child-notices/styles-child-notices.php';
}