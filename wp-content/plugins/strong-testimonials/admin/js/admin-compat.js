/**
 * Compatibility settings tab
 */

;(function ($) {

  var currentSettings = {}
  var quick = 200

  // Store current setting(s)
  function saveCurrentSettings () {
    $('[data-radio-group]').each(function (index, el) {
      var radioGroup = $(this).data('radioGroup')
      currentSettings[radioGroup] = {
        value: $(this).find(':checked').val(),
        forced: false
      }
    })
  }

  // Update display based on current selections
  function updateDisplay () {
    // matchMethodSetting()
    highlightRadioLabel()
    toggle()
  }

  // Toggle dependent inputs
  function toggle () {
    $('[data-group]').each(function (index, el) {
      var group = $(this).data('group')
      var $sub = $("[data-sub='" + group + "']")
      if ($(this).is(':checked')) {
        $sub.fadeIn()
      } else {
        $sub.fadeOut(quick)
      }
    })
  }

  // Update available options --- not currently used
  function matchMethodSetting () {
    if ($('#prerender-current').is(':checked')) {
      saveCurrentSettings()
      $('#method-none').prop('checked', true)
      $('#method-universal').prop('disabled', true)
      $('#method-observer').prop('disabled', true)
      $('#method-event').prop('disabled', true)
      $('#method-script').prop('disabled', true)
      currentSettings['method'].forced = true
    } else {
      if (currentSettings['method'].forced) {
        $('#method-' + currentSettings['method'].value).prop('checked', true)
        $('#method-universal').prop('disabled', false)
        $('#method-observer').prop('disabled', false)
        $('#method-event').prop('disabled', false)
        $('#method-script').prop('disabled', false)
        currentSettings['method'].forced = false
      }
    }
  }

  // UI
  function highlightRadioLabel () {
    $('input:radio:checked').closest('label').addClass('current')
    $('input:radio:not(:checked)').closest('label').removeClass('current')
  }

  // Number spinner
  function initSpinner () {
    $("input[type='number']").each(function () {
      $(this).number();
    });
  }

  // Presets
  function setScenario1() {
    $('#page-loading-general').click()
    $('#prerender-all').click().prop('checked', true)
    $('#method-universal').click().prop('checked', true)
  }

  function setScenarioDefault() {
    $('#prerender-current').click()
    $('#method-none').click()
  }

  // Listen for change
  $('.form-table').on('change', function (e) {
    updateDisplay()
    var currentType = $("input[name='wpmtst_compat_options[page_loading]']:checked").val()
    switch (currentType) {
      case 'general':
        setScenario1()
        break;
      case 'advanced':
        break;
      default:
        setScenarioDefault()
    }
  })

  // Listen for presets
  $('#set-scenario-1').click(function(e) {
    $(this).blur()
    setScenario1()
    e.preventDefault()
  })

  // Start
  saveCurrentSettings()
  updateDisplay()
  initSpinner()

})(jQuery)
