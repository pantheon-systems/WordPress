/**
 * Strong Testimonials admin
 *
 * @namespace jQuery
 */

// Function to get the Max value in Array
Array.max = function (array) {
  return Math.max.apply(Math, array);
};

jQuery(document).ready(function ($) {

  // Convert "A String" to "a_string"
  function convertLabel (label) {
    return label.replace(/\s+/g, '_').replace(/\W/g, '').toLowerCase();
  }

  // Remove invalid characters
  function removeSpaces (word) {
    //return word.replace(/\s+/g, "_");
    return word.replace(/[^\w\s(?!\-)]/gi, '');
  }

  $.fn.showInlineBlock = function () {
    return this.css('display', 'inline-block');
  };

  /**
   * ----------------------------------------
   * General events
   * ----------------------------------------
   */

  // Add protocol if missing
  // Thanks http://stackoverflow.com/a/36429927/51600
  $('input[type=url]').change(function () {
    if (this.value.length && !/^https*:\/\//.test(this.value)) {
      this.value = 'http://' + this.value;
    }
  });

  $('ul.ui-tabs-nav li a').click(function () {
    $(this).blur();
  });

  $('.focus-next-field').change(function (e) {
    if ($(e.target).is(':checked')) {
      $(e.target).parent().next().find('input').focus().select();
    }
  });

  // toggle screenshots
  $('#toggle-screen-options').add('#screenshot-screen-options').click(function (e) {
    $(this).blur();
    $('#screenshot-screen-options').slideToggle();
  });

  // toggle screenshots
  $('#toggle-help').click(function (e) {
    $(this).toggleClass('closed open').blur();
    $('#help-section').slideToggle();
  });

  /**
   * ----------------------------------------
   * View List Table
   * ----------------------------------------
   */

  /**
   * Save sort order
   */
  $('table.wpm-testimonial_page_testimonial-views th.manage-column').on('click', function (e) {
    var columnName = $(this).attr('id');
    // get the opposite class
    var columnOrder = $(this).hasClass('asc') ? 'desc' : $(this).hasClass('desc') ? 'asc' : '';
    var data = {
      'action': 'wpmtst_save_view_list_order',
      'name': columnName,
      'order': columnOrder
    };
    $.get(ajaxurl, data, function (response) {
    });
  });

  /**
   * Sticky views
   */
  $('table.wpm-testimonial_page_testimonial-views').on('click', '.stickit', function (e) {
    var icon = $(this);
    icon.closest('.wp-list-table-wrap').find('.overlay').fadeIn(200);
    icon.blur().toggleClass('stuck');
    var id = $(this).closest('tr').find('td.id').html();
    var data = {
      'action': 'wpmtst_save_view_sticky',
      'id': id
    };
    $.get(ajaxurl, data, function (response) {
      if (response) {
        window.location.reload();
      }
    });
  });

});
