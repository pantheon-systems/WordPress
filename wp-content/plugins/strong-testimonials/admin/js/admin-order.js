/**
 *  Admin list order
 *  Strong Testimonials
 */

// set cell widths to prevent shrinkage
function setCellWidths() {
  jQuery('td, th', 'table.posts').each(function () {
    var cell = jQuery(this);
    cell.width(cell.width());
  });
};

// reset cell widths
function resetCellWidths(reset) {
  jQuery('td, th', 'table.posts').each(function () {
    var cell = jQuery(this);
    cell.width('');
  }).promise().done(setCellWidths);
};

// Returns a function, that, as long as it continues to be invoked, will not
// be triggered. The function will be called after it stops being called for
// N milliseconds. If `immediate` is passed, trigger the function on the
// leading edge, instead of the trailing.
// Thanks http://davidwalsh.name/javascript-debounce-function
function debounce(func, wait, immediate) {
  var timeout;
  return function () {
    var context = this, args = arguments;
    var later = function () {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
};

// window resize listener
var myEfficientFn = debounce(resetCellWidths, 100);
window.addEventListener('resize', myEfficientFn);


jQuery(document).ready(function ($) {

  $.fn.strongSort = function (options) {

    var $this = this;
    var $handles = $(options.handles);

    var toggleReorder = function () {
      $this.sortable("enable");
      $handles.show();
    }

    this.sortable({
      disabled: true, // initially disabled
      items: 'tr',
      axis: 'y',
      handle: options.handles,
      forcePlaceholderSize: true,
      placeholder: "sortable-placeholder",
      start: function (e, ui) {
        // set height of placeholder to match current dragged element
        ui.placeholder.height(ui.helper.height());
        ui.helper.css("cursor", "move");
      },
      helper: function (e, ui) {
        var $originals = ui.children();
        var $helper = ui.clone();
        $helper.children().each(function (index) {
          // set helper cell sizes to match the original sizes
          $(this).width($originals.eq(index).width());
        });
        return $helper;
      },
      update: function (e, ui) {
        ui.item.find(".column-handle").addClass("refresh");
        $.post(ajaxurl, {
            action: 'update-menu-order',
            order: $('#the-list').sortable('serialize'),
          },
          function (data) {
            // update menu order shown
            var $orders = $(".menu-order");
            var obj = JSON.parse(data);
            var orderArray = $.map(obj, function (val, i) {
              $orders.eq(i).html(val);
            });
          })
          .done(function () {
            // update zebra striping
            $("#the-list").find("tr:even").removeClass("alternate");
            ui.item.effect('highlight', {}, 2000);
            ui.item.find(".column-handle").removeClass("refresh");
          })

      }
    });

    toggleReorder();

    return this;
  }

  // Init

  setCellWidths();

  $("#screen-meta").on("change", ".metabox-prefs", resetCellWidths);

  $("#the-list").strongSort({
    handles: 'td.column-handle'
  });

  $("td.column-handle").hover(
    function () {
      $(this).closest("tr").addClass("reorder-hover");
    },
    function () {
      $(this).closest("tr").removeClass("reorder-hover");
    }
  );

});
