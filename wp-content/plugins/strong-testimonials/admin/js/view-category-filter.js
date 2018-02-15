/**
 * View Category Filter
 *
 * Adapted from the excellent:
 *
 * Plugin Name:       Post Category Filter
 * Plugin URI:        http://www.jahvi.com
 * Description:       Filter post categories and taxonomies live in the WordPress admin area
 * Version:           1.2.4
 * Author:            Javier Villanueva
 * Author URI:        http://www.jahvi.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

(function ($) {

  'use strict';

  var $categoryDivs = $('.view-category-list-panel');

  $(function () {

    $(".fc-search-wrap").show();

    $categoryDivs.on('keyup search', '.fc-search-field', function (event) {

      var searchTerm = event.target.value,
        $listItems = $(this).closest(".view-category-list-panel").find('.view-category-list li');

      if ($.trim(searchTerm)) {

        $listItems.hide().filter(function () {
          return $(this).text().toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1;
        }).show();

      } else {

        $listItems.show();

      }

    });

  });

}(jQuery));
