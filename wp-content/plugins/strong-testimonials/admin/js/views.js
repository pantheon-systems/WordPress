/**
 * Strong Testimonials - Views
 */

// Function to get the Max value in Array
Array.max = function (array) {
  return Math.max.apply(Math, array);
};

/**
 * jQuery alterClass plugin
 *
 * Remove element classes with wildcard matching. Optionally add classes:
 *   $( '#foo' ).alterClass( 'foo-* bar-*', 'foobar' )
 *
 * https://gist.github.com/peteboere/1517285
 *
 * Copyright (c) 2011 Pete Boere (the-echoplex.net)
 * Free under terms of the MIT license: http://www.opensource.org/licenses/mit-license.php
 */
(function ($) {
  $.fn.alterClass = function (removals, additions) {

    var self = this;

    if (removals.indexOf('*') === -1) {
      // Use native jQuery methods if there is no wildcard matching
      self.removeClass(removals);
      return !additions ? self : self.addClass(additions);
    }

    var patt = new RegExp('\\s' +
      removals.replace(/\*/g, '[A-Za-z0-9-_]+').split(' ').join('\\s|\\s') +
      '\\s', 'g');

    self.each(function (i, it) {
      var cn = ' ' + it.className + ' ';
      while (patt.test(cn)) {
        cn = cn.replace(patt, ' ');
      }
      it.className = $.trim(cn);
    });

    return !additions ? self : self.addClass(additions);
  };
})(jQuery);

/**
 * Special handling after toggling certain options.
 */
(function ($) {
  // custom handling
  $.fn.afterToggle = function () {

    // Category selector
    var $categoryDivs = $('.view-category-list-panel');
    // Set initial width to compensate for narrowed box due to checkbox being hidden first
    // and to prevent horizontal jumpiness as filter is applied.
    if (!$categoryDivs.hasClass('fixed')) {
      $categoryDivs.width($categoryDivs.outerWidth(true)).addClass('fixed');
    }

    // Slideshow controls
    var $controls = $('#view-slideshow_controls_type');
    var $pager = $('#view-slideshow_pager_type');
    var controlsValue = $controls.val();
    var pagerValue = $pager.val();

    if ('full' === controlsValue) {
      $('.then_has-pager').fadeOut();
      $pager.val('none');
      $('option[value="text"]', '#view-slideshow_controls_style').prop('disabled', false);
    }
    else if ('sides' === controlsValue) {
      $('.then_has-pager').fadeIn();
      var $style = $('#view-slideshow_controls_style');
      if ('text' === $style.val()) {
        $('option:first', $style).prop('selected', true);
      }
      $('option[value="text"]', $style).prop('disabled', true);
    }
    else {
      $('.then_has-pager').fadeIn();
      $('option[value="text"]', '#view-slideshow_controls_style').prop('disabled', false);
    }

    if ('none' === pagerValue && ('none' === controlsValue || 'sides' === controlsValue)) {
      $('.then_has-position').fadeOut();
    }
    else {
      $('.then_has-position').fadeIn();
    }

    // If no navigation, must start automatically.
    if ('none' === pagerValue && 'none' === controlsValue) {
      $('#view-auto_start').val('1').prop('checked', true);
    }

    return this;
  };
}(jQuery));

/**
 * Masonry in the Layout section.
 */
jQuery(document).ready(function ($) {
  // Masonry
  $('.view-layout-masonry .example-container')
    .find('.box')
    .width(jQuery('.grid-sizer').width())
    .end()
    .masonry();

  // Category select width
  $.fn.afterToggle();
});

(function ($) {
  'use strict';

  // the shortcode code
  $('#view-shortcode').on('focus', function () {
    $(this).select();
  });

  $('.expand-cats').on('click', function () {
    // TODO i18n
    var $categoryDivs = $('.view-category-list-panel');
    if ($categoryDivs.hasClass('tall-panel')) {
      $categoryDivs.addClass('short-panel').removeClass('tall-panel');
      $(this).val('expand list');
    } else {
      $categoryDivs.removeClass('short-panel').addClass('tall-panel');
      $(this).val('collapse list');
    }
    $(this).blur();
  });

  // Masonry example
  var masonryExample = $('.view-layout-masonry .example-container');
  masonryExample.find('.box').width($('.grid-sizer').width()).end().masonry({
    columnWidth: '.grid-sizer',
    gutter: 10,
    itemSelector: '.box',
    percentPosition: true
  });

  // Column count selector
  var columnCount = $('#view-column-count');
  var columnCountChange = function () {
    var col = columnCount.val();
    $('.example-container').alterClass('col-*', 'col-' + col);
    masonryExample.find('.box').width($('.grid-sizer').width()).end().masonry();
  };

  columnCountChange();
  columnCount.on('change', columnCountChange);
  $('input[name=\'view[data][layout]\']').on('change', function () {
    if ('masonry' === $(this).val()) {
      setTimeout(columnCountChange, 200);
    }
  });

  // Color pickers
  var myOptions = {
    // you can declare a default color here, or in the data-default-color attribute on the input
    //defaultColor: '#FFFFFF',
    // a callback to fire whenever the color changes to a valid color
    change: function (event, ui) {
      setTimeout(function () {
        updateBackgroundPreview();
      }, 250);
    },
    // a callback to fire when the input is emptied or an invalid color
    clear: function (event, ui) {
      setTimeout(function () {
        updateBackgroundPreview();
      }, 250);
    },
    // hide the color picker controls on load
    //hide: true,
    // show a group of common colors beneath the square
    // or, supply an array of colors to customize further
    palettes: true
  };
  $('.wp-color-picker-field').wpColorPicker(myOptions);

  /**
   * Restore defaults
   */
  // TODO i18n
  $('#restore-defaults').click(function () {
    return confirm('Restore the default settings?');
  });

  /**
   * -----------------
   * Dependent options
   * -----------------
   */

  /**
   * Special handling
   * TODO Use a technique similar to if-then for adding/removing classes
   */

  var viewContent = $('#view-content');
  var viewContentChange = function () {
    var thisValue = viewContent.val();
    viewContent.closest('td').find('.highlight2').each(function (index, el) {
      if ('excerpt' === thisValue) {
        $(el).addClass('highlight-on');
      } else {
        $(el).removeClass('highlight-on');
      }
    });
  };
  viewContentChange();
  viewContent.on('change', viewContentChange);

  /**
   * Plugin: Show/Hide parts based on current Mode
   */
  $.fn.updateScreen = function (mode, speed) {
    speed = speed || 400;
    if (!mode)
      return;

    var modeDesc = $('.mode-description');
    modeDesc.html('');
    $('.then_' + mode).fadeIn(speed);
    $('.then_not_' + mode).fadeOut(speed);

    /**
     * Special handling
     */
    switch (mode) {
      case 'form':
        // hack
        setTimeout(formTemplateDescriptions, 500);
        break;
      case 'slideshow':
        break;
      case 'display':
        // update single/multiple selector ONLY
        $.fn.selectPerOption($('#view-single_or_multiple'));
        break;
      case 'single_template':
        break;
      default:
    }

    /**
     * Update description
     *
     * @since 2.22.0
     */
    var data = {
      'action': 'wpmtst_view_get_mode_description',
      'mode': mode
    };
    $.get(ajaxurl, data, function (response) {
      if (response) {
        modeDesc.html(response);
      }
    });

    return this;
  };

  /**
   * Plugin: Toggle dependent options for checkboxes.
   *
   * Show/hide other option groups when checkbox is "on".
   * Single value
   */
  $.fn.toggleOption = function (el, speed) {
    speed = speed || 400;
    var option = $(el).attr('id').split('-').pop();
    var checked = $(el).prop('checked');
    var deps = '.then_' + option;
    var indeps = '.then_not_' + option;
    if (checked) {
      $(deps).fadeIn(speed);
      $(indeps).fadeOut(speed);
    }
    else {
      $(deps).fadeOut(speed);
      $(indeps).fadeIn(speed);
    }
    return this;
  };

  /**
   * Plugin: Toggle dependent options for checkboxes.
   *
   * Show/hide other option groups when checkbox is "on".
   * Multiple values
   *
   * @since 1.20.0
   */
  $.fn.selectPerOption = function (el, speed) {
    speed = speed || 400;
    var fast = 0;
    //var option = $(el).attr("id").split("-").pop();
    var currentValue = $(el).val();
    var deps = '.then_' + currentValue;
    var depsFast = deps + '.fast';
    var indeps = '.then_not_' + currentValue;
    var indepsFast = indeps + '.fast';
    if (currentValue) {

      $(depsFast).not('.then_not_' + currentMode).fadeIn(fast);
      $(deps).not('.fast, .then_not_' + currentMode).fadeIn(speed);

      $(indepsFast).fadeOut(fast);
      $(indeps).not('.fast').fadeOut(speed);

    } else {

      $(indepsFast).fadeIn(fast);
      $(indeps).not('.fast').fadeIn(speed);

      $(depsFast).fadeOut(fast);
      $(deps).not('.fast').fadeOut(speed);

    }
    return this;
  };

  /**
   * Plugin: Toggle dependent options for selects.
   *
   * Show/hide other option groups when one and only one *specific* option is selected.
   * class="if select"
   * ~TRIP~
   */
  $.fn.selectOption = function (el, speed) {
    speed = speed || 400;
    var currentValue = $(el).val();
    var tripValue = $(el).find('.trip').val();
    var option = $(el).attr('id').split('-').pop();
    var deps = '.then_' + option;
    if (currentValue === tripValue) {
      $(deps).fadeIn(speed);
    }
    else {
      $(deps).fadeOut(speed);
    }
    return this;
  };

  /**
   * Plugin: Toggle dependent options for selects.
   *
   * Show/hide other option groups when one and only one *specific* option is selected.
   * class="if selectnot"
   * ~TRIP~
   */
  $.fn.selectNotOption = function (el, speed) {
    speed = speed || 400;
    var currentValue = $(el).val();
    var tripValue = $(el).find('.trip').val();
    var option = $(el).attr('id').split('-').pop();
    var deps = '.then_' + option;
    if (currentValue === tripValue) {
      $(deps).fadeOut(speed);
    }
    else {
      $(deps).fadeIn(speed);
    }
    return this;
  };

  /**
   * Plugin: Toggle dependent options for selects.
   *
   * Show/hide other option groups when any *non-empty (initial)* option is selected.
   * class="if selectany"
   */
  $.fn.selectAnyOption = function (el, speed) {
    speed = speed || 400;
    var currentValue = $(el).val();
    var option = $(el).attr('id').split('-').pop();
    var deps = '.then_' + option + '.then_' + currentValue;
    var indeps = '.then_not_' + option + '.then_' + currentValue;
    if (currentValue) {
      $(deps).fadeIn(speed);
      $(indeps).fadeOut(speed);
    }
    else {
      $(deps).fadeOut(speed);
      $(indeps).fadeIn(speed);
    }
    return this;
  };

  /**
   * Plugin: Toggle dependent options.
   *
   * Show/hide other option groups depending on value (1:1 relationshsip).
   * Using both option and value, which is different method than other functions.
   * class="if selectgroup"
   *
   * @since 1.20.0
   */
  $.fn.selectGroupOption = function (el) {
    var speed = 400,
      fastOut = 0,
      fastIn = 100;
    var option = $(el).attr('id').split('-').pop();
    var currentValue = $(el).val();
    var deps = '.then_' + option + '.then_' + currentValue;
    var depsFast = deps + '.fast';
    var indeps = '.then_' + option + '.then_not_' + currentValue;
    var indepsFast = indeps + '.fast';
    if (currentValue) {
      $(depsFast).fadeIn(fastIn);
      $(deps).not('.fast').fadeIn(speed);
      $(indepsFast).fadeOut(fastOut);
      $(indeps).not('.fast').fadeOut(speed);
    }
    else {
      $(indepsFast).fadeIn(fastIn);
      $(indeps).not('.fast').fadeIn(speed);
      $(depsFast).fadeOut(fastOut);
      $(deps).not('.fast').fadeOut(speed);
    }
    return this;
  };

  /**
   * Initial state
   */
  var $mode = $('#view-mode');
  var currentMode = $mode.find('input:checked').val();
  $mode.find('input:checked').closest('label').addClass('checked');
  $.fn.updateScreen(currentMode);

  /**
   * Mode listener
   */
  $mode.find('input').on('change', function () {
    currentMode = $(this).val();
    $mode.find('input').not(':checked').closest('label').removeClass('checked');
    $mode.find('input:checked').closest('label').addClass('checked');
    $.fn.updateScreen(currentMode);

    // Force default template since we have more than one group of templates.
    $('input[type=radio][name=\'view[data][template]\'][value=\'default\']').prop('checked', true);
    templateRadios.change();
    $('input[type=radio][name=\'view[data][form-template]\'][value=\'default-form\']').prop('checked', true);
    // formTemplateRadios.change();
    layoutRadios.change();
    backgroundRadios.change();
  });

  /**
   * Initial state & Change listeners
   */
  function initialize () {
    $('.if.toggle').each(function (index, el) {
      $.fn.toggleOption(this);
      $(this).on('change', function () {
        $.fn.toggleOption(this);
      });
    });

    $('.if.select').each(function (index, el) {
      $.fn.selectOption(this);
      $(this).on('change', function () {
        $.fn.selectOption(this);
      });
    });

    $('.if.selectnot').each(function (index, el) {
      $.fn.selectNotOption(this);
      $(this).on('change', function () {
        $.fn.selectNotOption(this).afterToggle();
      });
    });

    $('.if.selectany').each(function (index, el) {
      $.fn.selectAnyOption(this);
      $(this).on('change', function () {
        $.fn.selectAnyOption(this);
      });
    });

    $('.if.selectper').each(function (index, el) {
      $.fn.selectPerOption(this);
      $(this).on('change', function () {
        $.fn.selectPerOption(this).afterToggle();
      });
    });

    $('.if.selectgroup').each(function (index, el) {
      $.fn.selectGroupOption(this);
      $(this).on('change', function () {
        $.fn.selectGroupOption(this);
      });
    });

    $('.field-name select').each(function () {
      var $el = $(this);
      var $elParent = $el.closest('.field3');
      var fieldValue = $el.val();
      var fieldType = $el.find('option:selected').data('type');
      var key = $elParent.data('key');
      var typeSelectParent = $elParent.find('td.field-type');
      var typeSelect = typeSelectParent.find('select');

      if (fieldValue === 'post_date') {
        typeSelect.prop('disabled', true);
        typeSelect.parent().append('<input type="hidden" class="save-type" name="view[data][client_section][' + key + '][save-type]" value="date">');
      }
      else if (fieldValue === 'submit_date') {
        typeSelect.prop('disabled', true);
        typeSelect.parent().append('<input type="hidden" class="save-type" name="view[data][client_section][' + key + '][save-type]" value="date">');
      }
      else if (fieldValue === 'category') {
        typeSelect.prop('disabled', true);
        typeSelect.parent().append('<input type="hidden" class="save-type" name="view[data][client_section][' + key + '][save-type]" value="category">');
      }
      else if (fieldType === 'rating') { /* --- type! --- */
        typeSelect.prop('disabled', true);
        typeSelectParent.append('<input type="hidden" class="save-type" name="view[data][client_section][' + key + '][save-type]" value="rating">');
      }
      else {
        $(typeSelect).prop('disabled', false);
        $(typeSelect).parent().find('input.save-type').remove();
      }
    });

  }

  initialize();

  /**
   * Link field text change listener
   */
  function textChangeListener () {
    $('select[id^="view-fieldtext"]').on('change', function () {
      if ($(this).val() === 'custom') {
        var key = $(this).closest('.field3').data('key');
        $('#view-fieldtext' + key + '-custom').focus();
      }
    });
  }

  textChangeListener();

  /**
   * Template change listener
   */
    // TODO Use ID
  var templateRadios = $('input[type=radio][name=\'view[data][template]\']');

  function templateDescriptions () {
    var templateRadioOff, templateRadioOn, template;

    templateRadioOff = templateRadios.filter(':not(:checked)');
    templateRadioOff.closest('li').removeClass('current-selection').find('.options').hide();

    templateRadioOn = templateRadios.filter(':checked');
    template = templateRadioOn.val();
    templateRadioOn.closest('li').addClass('current-selection').find('.options').show();

    // Check for forced options
    if (template) {
      $('input.forced').removeProp('disabled').removeClass('forced');
      var data = {
        'action': 'wpmtst_force_check',
        'template': template
      };
      $.get(ajaxurl, data, function (response) {
        if (response) {
          var $el = $('#' + response);
          $el.prop('checked', true).change();
          var inputName = $el.prop('name');
          $('input[name=\'' + inputName + '\']').prop('disabled', true).addClass('forced');
        }
      });

      // Special handling
      if ('unstyled' === template) {
        $('input[name=\'view[data][background][type]\']').prop('disabled', true);
        $('input[name=\'view[data][font-color][type]\']').prop('disabled', true);
      } else {
        $('input[name=\'view[data][background][type]\']').prop('disabled', false);
        $('input[name=\'view[data][font-color][type]\']').prop('disabled', false);
      }

      // Special handling for Lucid add-on until I can incorporate a template group config file
      if ('lucid' === template.substr(0, 5) || 'single_template' === currentMode) {
        $('.then_lucid').show();
      } else {
        $('.then_lucid').hide();
      }
    }
  }

  templateDescriptions();

  templateRadios.on('change', templateDescriptions);

  /**
   * Form template change listener
   */
  var formTemplateRadios = $('input[type=radio][name=\'view[data][form-template]\']');

  function formTemplateDescriptions () {
    var formTemplateRadioOff, formTemplateRadioOn, formTemplate;

    formTemplateRadioOff = formTemplateRadios.filter(':not(:checked)');
    formTemplateRadioOff.closest('li').removeClass('current-selection').find('.options').hide();

    formTemplateRadioOn = formTemplateRadios.filter(':checked');
    formTemplate = formTemplateRadioOn.val();
    formTemplateRadioOn.closest('li').addClass('current-selection').find('.options').show();
  }

  formTemplateDescriptions();

  formTemplateRadios.on('change', formTemplateDescriptions);

  /**
   * Layout change listener
   */
    // TODO Use ID instead.
  var layoutRadios = $('input[type=radio][name=\'view[data][layout]\']');

  function layoutDescriptions () {
    var layoutRadioOff, layoutRadioOn, layout;

    layoutRadioOff = layoutRadios.filter(':not(:checked)');
    layoutRadioOff.closest('li').removeClass('current-selection').find('.options').hide();

    layoutRadioOn = layoutRadios.filter(':checked');
    layout = layoutRadioOn.attr('id');
    layoutRadioOn.closest('li').addClass('current-selection').find('.options').show();

    $('.layout-description, .layout-example').hide();
    $('.' + layout).show();

    // Special handling

    if ('view-layout-normal' === layout)
      $('#column-count-wrapper').fadeOut();
    else
      $('#column-count-wrapper').fadeIn();

    if ('view-layout-masonry' === layout) {
      if ($('#view-pagination').is(':checked')) {
        alert('Masonry is incompatible with pagination. Please disable pagination first.');
        $('#view-layout-normal').prop('checked', true).change();
      }
    }
  }

  layoutDescriptions();

  layoutRadios.on('change', layoutDescriptions);

  /**
   * Pagination change listener
   */
  function paginationChangeListener () {
    // Pagination is incompatible with Masonry
    // TODO DRY
    if ($(this).is(':checked') && 'masonry' === layoutRadios.filter(':checked').val()) {
      alert('Pagination is incompatible with Masonry. Please select another layout first.');
      $(this).prop('checked', false).change();
    }
  }

  $('#view-pagination').on('change', paginationChangeListener);

  /**
   * Disallow standard pagination with query limit.
   */
  var $viewQuantity = $('#view-all'),
    $viewPaginationType = $('#view-pagination_type');

  function paginationTypeChangeListener () {
    if (this.value === 'standard' && $viewQuantity.val() === '0' && $('#view-pagination').is(':checked')) {
      alert('Standard pagination is incompatible with Count.');
      $(this).val('simple').change();
    }
  }

  function quantityChangeListener () {
    if (this.value === '0' && $viewPaginationType.val() === 'standard' && $('#view-pagination').is(':checked')) {
      alert('Count is incompatible with Standard pagination.');
      $(this).val(1).change();
    }
  }

  $viewPaginationType.on('change', paginationTypeChangeListener);
  $viewQuantity.on('change', quantityChangeListener);

  /**
   * ----------------------------------------------------------------------
   * Background and Font colors
   * ----------------------------------------------------------------------
   */
  function updateBackgroundPreview () {
    var c1,
      c2,
      c3,
      background = backgroundRadios.filter(':checked').val(),
      fontColor = fontColorRadios.filter(':checked').val();

    if ('custom' === fontColor) {
      c3 = document.getElementById('fc-color').value;
      backgroundPreview.css('color', c3);
    } else {
      backgroundPreview.css('color', 'inherit');
    }
    switch (background) {
      case '':
        backgroundPreview.css('background', 'transparent');
        break;
      case 'single':
        c1 = document.getElementById('bg-color').value;
        backgroundPreview.css('background', c1);
        break;
      case 'gradient':
        c1 = document.getElementById('bg-gradient1').value;
        c2 = document.getElementById('bg-gradient2').value;
        backgroundPreview.css(constructGradientCSS(c1, c2));
        break;
      case 'preset':
        backgroundPreset(backgroundPresetSelector.val());
        break;
      default:
    }

  }

  var backgroundRadios = $('input[type=radio][name=\'view[data][background][type]\']'),
    backgroundPreview = $('#background-preview'),
    backgroundPresetSelector = $('#view-background-preset');

  /**
   * Font-color change listener
   */
    // TODO Use ID instead.
  var fontColorRadios = $('input[type=radio][name=\'view[data][font-color][type]\']');

  function fontColorDescriptions () {
    var fontColorRadioOff, fontColorRadioOn, fontColorID;

    fontColorRadioOff = fontColorRadios.filter(':not(:checked)');
    fontColorRadioOff.closest('li').removeClass('current-selection');

    fontColorRadioOn = fontColorRadios.filter(':checked');
    fontColorID = fontColorRadioOn.filter(':checked').attr('id');
    fontColorRadioOn.closest('li').addClass('current-selection');

    $('#view-font-color-info')
      .find('.font-color-description:visible')
      .hide()
      .end()
      .find('.' + fontColorID)
      .show();

    updateBackgroundPreview();
  }

  fontColorDescriptions();

  fontColorRadios.on('change', fontColorDescriptions);

  /**
   * Background change listener
   */
  function backgroundDescriptions () {
    var backgroundRadioOff, backgroundRadioOn, backgroundID;

    backgroundRadioOff = backgroundRadios.filter(':not(:checked)');
    backgroundRadioOff.closest('li').removeClass('current-selection').find('.options').hide();

    backgroundRadioOn = backgroundRadios.filter(':checked');
    backgroundID = backgroundRadioOn.filter(':checked').attr('id');
    backgroundRadioOn.closest('li').addClass('current-selection').find('.options').show();

    $('#view-background-info')
      .find('.background-description:visible')
      .hide()
      .end()
      .find('.' + backgroundID)
      .show();

    updateBackgroundPreview();
  }

  backgroundDescriptions();

  backgroundRadios.on('change', backgroundDescriptions);

  backgroundPresetSelector.on('change', function () {
    backgroundPreset($(this).val());
  });

  function backgroundPreset (preset) {
    if (!preset) {
      backgroundPreview.css('background', 'transparent');
      return;
    }

    var data = {
      'action': 'wpmtst_get_background_preset_colors',
      'key': preset,
    };
    $.get(ajaxurl, data, function (response) {
      var presetObj = JSON.parse(response);
      if (presetObj.color && presetObj.color2) {
        backgroundPreview.css(constructGradientCSS(presetObj.color, presetObj.color2));
      }
      else if (presetObj.color) {
        backgroundPreview.css('background', presetObj.color);
      }
      else {
        backgroundPreview.css('background', 'transparent');
      }
    });
  }

  function constructGradientCSS (c1, c2) {
    return {
      'background': 'linear-gradient(to bottom, ' + c1 + ' 0%, ' + c2 + ' 100%)'
    };
  }

  //$.fn.updateScreen(currentMode);

  /**
   * -------------
   * Client fields
   * -------------
   */

  /**
   * Make client fields sortable
   */

  var customFieldList = $('#custom-field-list2');

  // Prevent single click on handle from opening accordion
  customFieldList.on('click', 'span.handle', function (e) {
    e.stopImmediatePropagation();
    e.preventDefault();
  });

  // customFieldList.find(".field-properties").hide();

  customFieldList.sortable({
    placeholder: 'sortable-placeholder',
    // forcePlaceholderSize: true,
    handle: '.handle',
    cursor: 'move',
    helper: 'clone',
    start: function (e, ui) {
      ui.placeholder.height(ui.item.height());
    }
  });
  //}).disableSelection(); // <-- this breaks Firefox

  /**
   * Add client field
   */
  $('#add-field').click(function (e) {
    var keys = $('.field3').map(function () {
      return $(this).data('key');
    }).get();
    var nextKey = Array.max(keys) + 1;
    var data = {
      'action': 'wpmtst_view_add_field',
      'key': nextKey,
    };
    $.get(ajaxurl, data, function (response) {
      //customFieldList.append( response ).find("#field-"+nextKey+" span.link").click();
      $.when(customFieldList.append(response)).then(function () {
        var $newField = customFieldList.find('#field-' + nextKey);
        $newField
          .find('span.link').click().end()
          .find('.field-dep').hide().end()
          .find('.first-field').focus();
      });
    });
  });

  /**
   * Field type change listener
   */
  customFieldList.on('change', '.field-type select', function () {
    var $el = $(this);
    var $elParent = $el.closest('.field3');
    var fieldType = $el.val();
    var fieldName = $elParent.find('.field-name').find('select').val();
    // var key = $elParent.attr("id").split('-').slice(-1)[0];
    var key = $elParent.data('key');
    var data;

    switch (fieldType) {

      case 'link2':
      case 'link':
        // if changing to [link], add link fields
        data = {
          'action': 'wpmtst_view_add_field_link',
          'fieldName': fieldName,
          'fieldType': fieldType,
          'key': key,
        };
        $.get(ajaxurl, data, function (response) {
          // insert into placeholder div
          $elParent.find('.field-property-box').html(response);

          // Trigger conditional select
          var $newFieldSelect = $elParent.find('.if.selectgroup');
          $.fn.selectGroupOption($newFieldSelect);
          $newFieldSelect.on('change', function () {
            $.fn.selectGroupOption($newFieldSelect);
          });
          textChangeListener();

          // Get field name --> Get field label --> Populate link_text label
          var fieldName = $elParent.find('.field-name').find('select').val();
          var data2 = {
            'action': 'wpmtst_view_get_label',
            'name': fieldName,
          };
          $.get(ajaxurl, data2, function (response) {
            // 	var key = $elParent.attr("id").split('-').slice(-1)[0];
            $('#view-fieldtext' + key + '-label').val(response);
          });

        });
        break;

      case 'date':
        // if changing to [date], add date fields
        data = {
          'action': 'wpmtst_view_add_field_date',
          'key': key,
        };
        $.get(ajaxurl, data, function (response) {
          // insert into placeholder div
          $elParent.find('.field-property-box').html(response);
        });
        break;

      case 'text':
        // if changing to [text], remove meta fields
        $elParent.find('.field-property-box').empty();
        break;

      default:

    }
  });

  /**
   * Field name change listener.
   */
  customFieldList.on('change', '.field-name select', function () {
    var $el = $(this);
    var $elParent = $el.closest('.field3');
    var fieldType = $el.find('option:selected').data('type');
    var fieldValue = $el.val();
    var key = $elParent.data('key');
    var typeSelectParent = $elParent.find('.field-type');
    var typeSelect = typeSelectParent.find('select');
    var data;

    $elParent.not('.open').addClass('open').find('.field-properties').addClass('open').slideDown();

    if ('' === fieldValue) {
      $elParent.find('.field-description').html('');
      // Hide dependent inputs if nothing has been selected
      $elParent.find('.field-dep').hide();
    }
    else {
      // Update field label
      data = {
        'action': 'wpmtst_view_get_label',
        'name': fieldValue,
        'key': key,
      };
      $.get(ajaxurl, data, function (response) {
        if (response) {
          $elParent.find('.field-description').html(response);
        }
      });

      // Show dependent inputs
      $elParent.find('.field-dep').show();
    }

    switch (fieldValue) {
      // First, the immutables
      case 'post_date':
      case 'submit_date':
        // Disable type selector
        typeSelect.val('date').prop('disabled', true);
        typeSelectParent.append('<input type="hidden" class="save-type" name="view[data][client_section][' + key + '][save-type]" value="date">');

        // add format field
        data = {
          'action': 'wpmtst_view_add_field_date',
          'key': key,
        };
        $.get(ajaxurl, data, function (response) {
          // Insert into placeholder div. Add hidden field because we are
          // disabling the <select> so its value will not be submitted.
          $elParent.find('.field-property-box').html(response); // .find("input").focus();
          $el.parent().append('<input type="hidden" class="save-type" name="view[data][client_section][' + key + '][type]" value="date">');
        });
        break;

      case 'link2':
      case 'link':
        // Get field name --> Get field label --> Populate link_text label
        var fieldName = $elParent.find('.field-name').find('select').val();
        var data2 = {
          'action': 'wpmtst_view_get_label',
          'name': fieldName,
        };
        $.get(ajaxurl, data2, function (response) {
          var key = $elParent.attr('id').split('-').slice(-1)[0];
          $('#view-fieldtext' + key + '-label').val(response);
        });
        break;

      case 'category':
        $(typeSelect).val('category').prop('disabled', true);
        typeSelectParent.append('<input type="hidden" class="save-type" name="view[data][client_section][' + key + '][save-type]" value="category">');
        $elParent.find('.field-property-box').empty();
        break;

      default:
        // Special handling
        if ('rating' === fieldType) {
          typeSelect.val('rating').prop('disabled', true);
          typeSelectParent.append('<input type="hidden" class="save-type" name="view[data][client_section][' + key + '][save-type]" value="rating">');
          $elParent.find('.field-property-box').empty();
          break;
        }

        $(typeSelect).val('text').prop('disabled', false);
        // remove meta field
        $elParent.find('.field-property-box').empty();
        // remove the saved type that's only necessary when we disable the input (above)
        $el.parent().find('input.save-type').remove();
    }
  });

  /**
   * Delete a client field
   */
  customFieldList.on('click', 'span.delete', function (e) {
    var thisField = $(this).closest('.field2');
    var yesno = confirm('Remove this field?');
    if (yesno) {
      thisField.fadeOut(function () {
        $(this).remove();
      });
    }
    // Prevent click from expanding accordion
    e.stopImmediatePropagation();
    e.preventDefault();
  });

  customFieldList.on('click', 'span.link', function (e) {
    $(this)
      .closest('.field2')
      .toggleClass('open')
      .find('.field-properties')
      .slideToggle();
    return false;
  });

})(jQuery);

/**
 * Click to copy to keyboard
 * Thanks https://www.sitepoint.com/javascript-copy-to-clipboard/
 */
(function () {

  'use strict';

  // click events
  document.body.addEventListener('click', copy, true);

  // event handler
  function copy (e) {

    // find target element
    var
      t = e.target,
      c = t.dataset.copytarget,
      inp = (c ? document.querySelector(c) : null);

    // is element selectable?
    if (inp && inp.select) {

      // select text
      inp.select();

      try {
        // copy text
        document.execCommand('copy');
        inp.blur();

        //t.classList.add('copied');
        //setTimeout(function() { t.classList.remove('copied'); }, 1500);
        document.getElementById('copy-message').classList.add('copied');
        setTimeout(function () {
          document.getElementById('copy-message').classList.remove('copied');
        }, 2000);
      }
      catch (err) {
        alert('Sorry, please press Ctrl/Cmd+C to copy instead.');
      }

    }

  }

})();
