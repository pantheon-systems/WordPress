/*
jQuery Multiselect
by Matt Gibbs
2015-03-31
*/
(function($) {

    function MultiSelect(el, options) {
        $(el).hide();

        var html = '<div><input type="checkbox" class="select-all" /> Select all</div>';
        $(el).find('optgroup').each(function(idx, optgroup) {
            html += '<div class="optgroup">';
            html += '<div class="optgroup-label"><input type="checkbox" /> ' + $(optgroup).attr('label');
            html += '</div>';

            $(optgroup).find('option').each(function(idx, option) {
                var val = $(option).val();
                var checked = $(option).is(':checked') ? ' checked="checked"' : '';
                html += '<div class="opt"><input type="checkbox" value="' + val + '"' + checked + ' /> ' + $(option).html() + '</div>';
            });

            html += '</div>';
        });

        $(el).after('<div class="multiselect">' + html + '</div>');
    }

    $(document).on('click', '.multiselect input[type="checkbox"]', function() {
        var $this = $(this);
        var $parent = $this.closest('.multiselect');
        var is_checked = $this.is(':checked');

        // The "Select all" box is checked
        if ($this.hasClass('select-all')) {
            $parent.find('input[type="checkbox"]').prop('checked', is_checked);
        }

        // A group is checked
        else if (0 < $this.closest('.optgroup-label').length) {
            $this.closest('.optgroup').find('input[type="checkbox"]').prop('checked', is_checked);
        }

        // Update the main select box
        var selected = [];
        $parent.find('.opt input:checked').each(function() {
            selected.push($(this).val());
        });

        var $select_box = $this.closest('.bundle-select-wrapper').find('.bundle-select');
        $select_box.find('option').each(function() {
            var is_selected = (-1 < $.inArray($(this).val(), selected));
            $(this).prop('selected', is_selected);
        });
    });

    $.fn.multiSelect = function() {
        this.each(function() {
            var $this = this;
            var data = new MultiSelect($this, {});
        });
    }

})(jQuery);