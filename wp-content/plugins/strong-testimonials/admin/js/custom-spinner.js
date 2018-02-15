/**
 * Custom Number Spinner
 *
 * Courtesy of
 * http://www.jqueryscript.net/form/Custom-Number-InputSpinner-Plugin-jQuery-number.html
 * with help from
 * https://stackoverflow.com/a/7343013/51600
 */
;(function ($) {
  $.fn.number = function (customOptions) {
    function round(value, precision) {
      var multiplier = Math.pow(10, precision || 0);
      return Math.round(value * multiplier) / multiplier;
    }

    var options = {
      'containerClass': 'number-style noselect',
      'minus': 'number-minus',
      'plus': 'number-plus',
      'containerTag': 'div',
      'btnTag': 'span'
    }
    options = $.extend(true, options, customOptions)

    var input = this
    input.wrap('<' + options.containerTag + ' class="' + options.containerClass + '">')

    var wrapper = input.parent()
    wrapper.prepend('<' + options.btnTag + ' class="' + options.minus + '"></' + options.btnTag + '>')
    wrapper.append('<' + options.btnTag + ' class="' + options.plus + '"></' + options.btnTag + '>')

    var minus = wrapper.find('.' + options.minus)
    var plus = wrapper.find('.' + options.plus)

    var min = round(input.attr('min'),1)
    var max = round(input.attr('max'),1)
    var step = 1
    if(input.attr('step')){
      step = +round(input.attr('step'),1)
    }

    if (+input.val() <= +min) {
      minus.addClass('disabled')
    }
    if (+input.val() >= +max) {
      plus.addClass('disabled')
    }

    minus.click(function () {
      var input = $(this).parent().find('input')
      var value = +round(input.val(),1)
      if (+value > +min) {
        input.val(round(+value - step,1))
        if (+input.val() === +min) {
          input.prev('.' + options.minus).addClass('disabled')
        }
        if (input.next('.' + options.plus).hasClass('disabled')) {
          input.next('.' + options.plus).removeClass('disabled')
        }
      } else if (!min) {
        input.val(round(+value - step,1))
      }
    })

    plus.click(function () {
      var input = $(this).parent().find('input')
      var value = +round(input.val(),2)
      if (+value < +max) {
        input.val(round(+value + step,1))
        if (+input.val() === +max) {
          input.next('.' + options.plus).addClass('disabled')
        }
        if (input.prev('.' + options.minus).hasClass('disabled')) {
          input.prev('.' + options.minus).removeClass('disabled')
        }
      } else if (!max) {
        input.val(round(+value + step,1))
      }
    })
  }
})(jQuery)
