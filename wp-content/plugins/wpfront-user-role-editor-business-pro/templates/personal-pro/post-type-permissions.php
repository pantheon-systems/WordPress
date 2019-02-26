<?php
if (!defined('ABSPATH')) {
    exit();
}
?>

<style type="text/css">
    table.wpfront-user-role-editor-post-type-permission th {
        font-weight: bold;
    }

    table.wpfront-user-role-editor-post-type-permission th,
    table.wpfront-user-role-editor-post-type-permission td {
        text-align: center;
        padding: 3px 15px 3px 0px;

    }

    table.wpfront-user-role-editor-post-type-permission th.role {
        text-align: left;
    }
</style>
<div>
    <label>
        <input type="checkbox" name="<?php echo self::$META_DATA_KEY . '-enable-role-permissions'; ?>" <?php echo $this->enable_permissions ? 'checked' : ''; ?> /><?php echo $this->__('Enable Role Permissions'); ?>
    </label>
</div>
<table class="wpfront-user-role-editor-post-type-permission">
    <thead>
        <tr>
            <th class="role"></th>
            <th class="role-select"></th>
            <th class="read"><?php echo $this->__('Read'); ?></th>
            <th class="edit"><?php echo $this->__('Edit'); ?></th>
            <th class="delete"><?php echo $this->__('Delete'); ?></th>
        </tr>
        <tr>
            <th class="role"></th>
            <td class="role-select"><input type="checkbox" class="row-select col-select" /></td>
            <td class="read col1"><input type="checkbox" class="col-select" /></td>
            <td class="edit col2"><input type="checkbox" class="col-select" /></td>
            <td class="delete col3"><input type="checkbox" class="col-select" /></td>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($this->roles as $key => $value) {
            $row_select_disabled = $value[1][1] && $value[2][1] && $value[3][1];
            ?>
            <tr>
                <th scope="row" class="role"><?php echo $value[0]; ?></th>
                <td class="role-select"><input type="checkbox" class="row-select <?php echo $row_select_disabled ? 'disabled' : ''; ?>" <?php echo $row_select_disabled ? 'disabled' : ''; ?> /></td>
                <td class="read col1"><input type="checkbox" class="cell <?php echo $value[1][1] ? 'disabled' : ''; ?>" name="<?php echo self::$META_DATA_KEY . '[' . $key . '][read]'; ?>" <?php echo $value[1][0] ? 'checked' : ''; ?> <?php echo $value[1][1] ? 'disabled' : ''; ?> /></td>
                <td class="edit col2"><input type="checkbox" class="cell <?php echo $value[2][1] ? 'disabled' : ''; ?>" name="<?php echo self::$META_DATA_KEY . '[' . $key . '][edit]'; ?>" <?php echo $value[2][0] ? 'checked' : ''; ?> <?php echo $value[2][1] ? 'disabled' : ''; ?> /></td>
                <td class="delete col3"><input type="checkbox" class="cell <?php echo $value[3][1] ? 'disabled' : ''; ?>" name="<?php echo self::$META_DATA_KEY . '[' . $key . '][delete]'; ?>" <?php echo $value[3][0] ? 'checked' : ''; ?> <?php echo $value[3][1] ? 'disabled' : ''; ?> /></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

<script type="text/javascript">

    (function($) {
        function checked($this) {
            if ($this.hasClass('row-select')) {
                var chks = [];
                $this.closest('tr').find('input:enabled').prop('checked', $this.prop('checked')).each(function() {
                    if ($this.is($(this)))
                        return;

                    chks.push(this);
                });
                $(chks).each(function() {
                    checked($(this));
                });
            }

            if ($this.hasClass('col-select')) {
                var i = $this.parent().index();
                var chks = [];
                $this.closest('table').find('tbody tr').each(function() {
                    var $e = $(this).children(':eq(' + i + ')').children(':enabled').prop('checked', $this.prop('checked'));

                    chks.push($e[0]);
                });
                $(chks).each(function() {
                    checked($(this));
                });
            }

            if ($this.hasClass('cell')) {
                var $chks = $this.closest('tr').find('input.cell:enabled');
                $this.closest('tr').find('input.row-select').prop('checked', $chks.filter(':checked').length == $chks.length);

                var i = $this.parent().index();
                var chks = [];
                $this.closest('tbody').children().each(function() {
                    var chk = $(this).children(':eq(' + i + ')').children();
                    if (chk.is(':enabled'))
                        chks.push(chk[0]);
                });
                $this.closest('table').children('thead').find('td input.col-select:eq(0)').closest('tr').children(':eq(' + i + ')').children().prop('checked', chks.length == $(chks).filter(':checked').length);

                var chks = $this.closest('table').children('tbody').find('td input.cell:not(:disabled)');
                $this.closest('table').children('thead').find('input.row-select.col-select').prop('checked', chks.length == chks.filter(':checked').length);
            }
        }

        $('table.wpfront-user-role-editor-post-type-permission input').click(function() {
            checked($(this));
        }).filter('.cell').each(function() {
            checked($(this));
        });

        $('input[name="<?php echo self::$META_DATA_KEY . '-enable-role-permissions'; ?>"]').click(function() {
            $('table.wpfront-user-role-editor-post-type-permission input:not(.disabled)').prop('disabled', !$(this).prop('checked'));
        }).triggerHandler('click');

    })(jQuery);

</script>