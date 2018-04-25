/**
 * Strong Testimonials Custom Fields Editor
 */

// Function to get the Max value in Array
Array.max = function (array) {
  return Math.max.apply(Math, array);
};

// Convert "A String" to "a_string"
function sanitizeName(label) {
  return label.trim().replace(/\W/g, " ").replace(/\s+/g, "_").toLowerCase();
}

(function ($) {

  /**
   * If open, scroll field into view.
   * If closed, scroll all the way up.
   *
   * @returns {jQuery}
   */
  $.fn.scrollUp = function () {
    var containerOffset;
    this.each(function () {
      containerOffset = 0;
      if ($(this).hasClass("open")) {
        containerOffset = parseInt($(this).offset().top) - 72;
      }
      $("html, body").animate({scrollTop: containerOffset}, 800);
    });

    return this;
  };

  /**
   * Replace the field type selector with its value. Better than readonly.
   *
   * @returns {jQuery}
   */
  $.fn.replaceSelect = function () {
    this.each(function () {
      if ($(this).hasClass("open")) {
        $(this).find("select.field-type").each(function (index, el) {
          $(el).replaceWith(el.value);
        });
      }
    });

    return this;
  }

  /**
   * Initialize
   */

  var catCount = 0;
  $.get(ajaxurl, {
      'action': 'wpmtst_get_cat_count',
      'security': wpmtstAdmin.ajax_nonce
    },
    function (response) {
      catCount = parseInt(response);
    });


  var $theForm = $("#wpmtst-custom-fields-form");
  var $fieldList = $("#custom-field-list");

  formPreview();
  toggleCategoryFields();


  /**
   * Events
   */

  $theForm.on("change", "input", function () {
    dismissNotice();
    formPreview();
  });

  // check all field names
  $theForm.submit(function (event) {
    $("input.field-name").each(function (index) {
      if ('name' === $(this).val() || 'date' === $(this).val()) {
        $(this).focus().parent().find('.field-name-help.important').addClass('error');
        var $parent = $(this).closest("li");
        if (!$parent.hasClass("open")) {
          $parent.find("a.field").click();
        }
        event.preventDefault();
      } else {
        $(this).parent().find('.field-name-help').removeClass('error');
      }
    });
  });

  // Prevent single click on handle from opening accordion
  $fieldList.on("click", "span.handle", function () {
    event.stopImmediatePropagation();
    event.preventDefault();
  });

  // Sortable
  $fieldList.sortable({
    placeholder: "sortable-placeholder",
    forcePlaceholderSize: true,
    handle: ".handle",
    cursor: "move",
    update: function (event, ui) {
      dismissNotice();
      formPreview();
    }
  });

  // open/close
  $fieldList.on("click", "span.link", function () {
    toggleField($(this).closest("li"));
    return false;
  });

  // validate field label
  $fieldList.on("change blur", "input.field-label", function () {
    var newLabel = $(this).val().trim();
    $(this).val(newLabel);

    var $parent = $(this).closest("li");
    var fieldIndex = $parent.index();

    // fill in blank label
    if (!$(this).val()) {
      $(this).val(wpmtstAdmin.newField);
    }

    // update parent list item
    $parent.find("a.field").html(newLabel);

    // fill in blank field name
    var $fieldName = $parent.find("input.field-name");
    if (!$fieldName.val()) {
      $fieldName.val(getUniqueName(newLabel, fieldIndex));
    }
  });

  // validate field name
  $fieldList.on("change", "input.field-name", function () {
    var $parent = $(this).closest("li");
    var fieldIndex = $parent.index();
    var fieldName = $(this).val();

    if (fieldName) {
      $(this).val(getUniqueName(fieldName, fieldIndex));
    } else {
      var fieldLabel = $(this).closest(".field-table").find(".field-label").val();
      $(this).val(getUniqueName(fieldLabel, fieldIndex));
      return;
    }

    if ('name' === $(this).val() || 'date' === $(this).val()) {
      $(this).focus().parent().find('.field-name-help.important').addClass('error');
    } else {
      $(this).parent().find('.field-name-help').removeClass('error');
    }
  });

  // delete field
  $fieldList.on("click", ".delete-field", function () {
    $(this).blur();
    dismissNotice();
    var thisField = $(this).closest("li");
    var thisLabel = thisField.find(".field").text();
    if (confirm('Delete "' + thisLabel + '"?')) {
      thisField.fadeOut(function () {
        $.when(thisField.remove()).then(function () {
          formPreview();
          toggleCategoryFields();
          $("#add-field, #submit").removeAttr("disabled");
        })
      });
    }
  });

  // close field
  $fieldList.on("click", "span.close-field a", function () {
    toggleField($(this).closest("li"));
    return false;
  });

  // Add new field
  $("#add-field").click(function () {
    dismissNotice();
    var keys = $fieldList.find("li").map(function () {
      var key_id = $(this).attr("id");
      return key_id.substr(key_id.lastIndexOf("-") + 1);
    }).get();
    var nextKey = Array.max(keys) + 1;

    var data = {
      'action': 'wpmtst_add_field',
      'nextKey': nextKey,
      'fieldClass': null,
      'fieldType': null,
      'security': wpmtstAdmin.ajax_nonce
    };
    $.get(ajaxurl, data, function (response) {
      $("#add-field, #submit").attr("disabled", "disabled");

      // create list item
      var $li = $('<li id="field-' + nextKey + '" data-status="new">').append(response);

      // append to list
      $.when($fieldList.append($li)).then(function () {
        formPreview();
        togglePostFields();
        toggleCategoryFields();

        // hide "Close" link until Type is selected
        $li.find("span.close-field").hide();

        // click it to open
        $li.find("span.link").click();
      });
    });
  });

  // Field type change
  $fieldList.on("change", ".field-type", function (e) {
    console.log(e.target);
    var fieldType = $(this).val();
    console.log(fieldType);
    var $table = $(this).closest("table");
    var $parent = $(this).closest('li');

    if ($parent.data('status') !== 'new') {
      $table.find(".field-secondary, .field-admin-table").remove();
    }

    if ('none' === fieldType) {
      // Hide "Close" link
      $parent.find("span.close-field").hide();

      $("#add-field, #submit").attr("disabled", "disabled");

      return;
    }

    var key_id = $parent.attr("id");
    var key = key_id.substr(key_id.lastIndexOf("-") + 1);

    var $fieldLabel = $parent.find('input.field-label');
    var $fieldName = $parent.find('input.field-name');

    // get type of field from its optgroup
    var fieldOption = $(this).find("option[value='" + fieldType + "']");
    var fieldClass = fieldOption.closest("optgroup").attr("class");

    switch (fieldClass) {
      case 'post':
        // Force values if selecting a Post field.
        if (fieldType === 'post_title') {
          $fieldLabel.val('Testimonial Title');
          $fieldName.val('post_title').attr('disabled', 'disabled');
        }
        else if (fieldType === 'featured_image') {
          $fieldLabel.val('Photo');
          $fieldName.val('featured_image').attr('disabled', 'disabled');
        }
        else if (fieldType === 'post_content') {
          $fieldLabel.val('Testimonial');
          $fieldName.val('post_content').attr('disabled', 'disabled');
        }
        // move value to hidden input
        $fieldName.after('<input type="hidden" name="' + $fieldName.attr("name") + '" value="' + $fieldName.val() + '" />');
        // hide help message
        $parent.find(".field-name-help").hide();
        break;
      case 'optional':
        if ('category' === fieldType.split('-')[0]) {
          $fieldName.val('category').attr('disabled', 'disabled');
          // move value to hidden input
          $fieldName.after('<input type="hidden" name="' + $fieldName.attr("name") + '" value="' + $fieldName.val() + '" />');
          // hide help message
          $parent.find(".field-name-help").hide();
        }
        var forceName = fieldOption.data('force-name');
        if (forceName) {
          $fieldName.val(forceName).attr('disabled', 'disabled');
          // move value to hidden input
          $fieldName.after('<input type="hidden" name="' + $fieldName.attr("name") + '" value="' + $fieldName.val() + '" />');
          // hide help message
          $parent.find(".field-name-help").hide();
        }
        $fieldLabel.val(wpmtstAdmin.newField).focus().select();
        break;
      default:
        $fieldLabel.val(wpmtstAdmin.newField).focus().select();
        $fieldName.val('').removeAttr('disabled');
        $parent.find(".field-name-help").show();
    }


    // secondary form fields
    var data1 = {
      'action': 'wpmtst_add_field_2',
      'nextKey': key,
      'fieldClass': fieldClass,
      'fieldType': fieldType,
      'security': wpmtstAdmin.ajax_nonce
    };

    var ajax1 = $.get(ajaxurl, data1, function (response) {
      $table.append(response);
    });


    // admin-table field
    var data2 = {
      'action': 'wpmtst_add_field_4',
      'nextKey': key,
      'fieldClass': fieldClass,
      'fieldType': fieldType,
      'security': wpmtstAdmin.ajax_nonce
    };

    var ajax2 = ajax1.then(function () {
      return $.get(ajaxurl, data2, function (response) {
        $table.append(response);
      });
    });


    // hidden inputs
    var data3 = {
      'action': 'wpmtst_add_field_3',
      'nextKey': key,
      'fieldClass': fieldClass,
      'fieldType': fieldType,
      'security': wpmtstAdmin.ajax_nonce
    };

    var ajax3 = ajax2.then(function () {
      return $.get(ajaxurl, data3, function (response) {
        $table.parent().append(response);
      });
    });

    ajax3.done(function () {

      formPreview();
      $("#add-field, #submit").removeAttr("disabled");

      // Successfully added so show "Close" link
      $("span.close-field").show();

      $parent
      // Reset temporary status
        .removeData("status").removeAttr("data-status")
      // update parent list item
        .find(".custom-field-header a.field").html($fieldLabel.val()).end()
      // update hidden [record_type] input
        .find('input[name$="[record_type]"]').val(fieldClass);

    });

  });

  // Restore defaults
  $("#restore-defaults").click(function () {
    return confirm("Restore the default fields?");
  });

  /**
   * Functions
   */

  // Preview
  function formPreview() {
    var formFields = $theForm.find("[name^='fields']");
    if (!formFields.length) return;

    var data = {
      'action': 'wpmtst_get_form_preview',
      'fields': formFields.serialize()
    };
    $.post(ajaxurl, data, function (response) {
      var newDiv = $("<div></div>").hide().html(response)
      $("#fields-editor-preview")
        .children().first()
        .after(newDiv)
        .fadeOut(300, function () {
          newDiv.show();
          $(this).remove();
        });
    });
  }

  /*
   * Disable any Post fields already in use.
   *
   * Doing this client-side so a Post field can be added
   * but not saved before adding more fields;
   * i.e. add multiple fields of either type without risk
   * of duplicating single Post fields before clicking "Save".
   */
  function togglePostFields() {
    $fieldList.find('input[name$="[record_type]"]').each(function () {
      var $parent = $(this).closest("li");
      var value = $(this).val();
      if ("post" === value) {
        var name = $parent.find(".field-name").val();
        $fieldList.find("select.field-type.new").find('option[value="' + name + '"]').attr("disabled", "disabled");
      }
    });
  }

  // Only allow one category field
  function toggleCategoryFields() {
    var categoryInUse = false;

    $fieldList.find('input[name$="[record_type]"]').each(function () {
      var $parent = $(this).closest("li");
      var value = $(this).val();
      if ('optional' === value) {
        var fieldType = $parent.find('input[name$="[input_type]"]').val();
        if (!categoryInUse) {
          categoryInUse = ( 'category' === fieldType.split('-')[0] );
        }
      }
    });

    var $options = $fieldList.find('option[value^="category"]');
    if (categoryInUse) {
      $options.each(function () {
        var text = $(this).text();
        $(this)
          .attr("disabled", "disabled")
          .text(text + ' ' + wpmtstAdmin.inUse)
          .data('origText', text);
      });
    }
    else if (0 === catCount) {
      $options.each(function () {
        var text = $(this).text();
        $(this)
          .attr("disabled", "disabled")
          .text(text + ' ' + wpmtstAdmin.noneFound)
          .data('origText', text);
      });
    } else {
      $options.each(function () {
        $(this)
          .removeAttr("disabled")
          .text($(this).data('origText'));
      });
    }
  }

  // Actions on opening/closing the field
  function toggleField($field) {
    $field.replaceSelect()
      .toggleClass("open")
      .scrollUp()
      .find("span.link")
      .toggleClass("open")
      .end()
      .find(".custom-field")
      .toggleClass("open")
      .slideToggle()
      .find(".first-field")
      .focus();
  }

  // Build a unique name
  function getUniqueName(fieldName, fieldIndex) {
    fieldName = sanitizeName(fieldName);

    // Get names of *other* fields
    var names = $theForm.find("input.field-name").not(":eq(" + fieldIndex + ")").map(function () {
      return this.value;
    }).get();

    names = names.filter(function (x) {
      return (x !== (undefined || ''));
    });

    var uniqueName = fieldName;
    var i = 2;

    while ($.inArray(uniqueName, names) >= 1) {
      uniqueName = fieldName + '_' + i++;
    }
    return uniqueName;
  }

  // Dismis the "Fields saved" notice.
  function dismissNotice() {
    $("#message").find(".notice-dismiss").click();
  }

})(jQuery);
