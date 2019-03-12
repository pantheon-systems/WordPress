<?php
/*
  WPFront User Role Editor Plugin
  Copyright (C) 2014, WPFront.com
  Website: wpfront.com
  Contact: syam@wpfront.com

  WPFront User Role Editor Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Template for WPFront User Role Editor Add Remove Capability
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */
?>

<?php
if (!defined('ABSPATH')) {
    exit();
}
?>

<div class="wrap add-remove-capability">
    <h2>
        <?php echo $this->__('Add/Remove Capability'); ?>
    </h2>
    <?php
    if (!empty($this->message)) {
        ?>
        <div class="updated">
            <p><?php echo $this->message; ?></p>
        </div>
        <?php
    }
    ?>

    <?php printf('<p>%s</p>', $this->__('Add/Remove a capability to/from roles within this site.')); ?>

    <form method="post" class="validate">
        <?php $this->main->create_nonce(); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="action_type">
                            <?php echo $this->__('Action'); ?>
                        </label>
                    </th>
                    <td>
                        <fieldset>
                            <label><input name="action_type" type="radio" value="add" <?php echo $this->action === 'add' ? 'checked' : ''; ?> /><?php echo $this->__('Add Capability'); ?></label>
                            <br />
                            <label><input name="action_type" type="radio" value="remove" <?php echo $this->action === 'remove' ? 'checked' : ''; ?> /><?php echo $this->__('Remove Capability'); ?></label>
                        </fieldset>
                    </td>
                </tr>
                <tr class="form-required <?php echo $this->capability === NULL ? 'form-invalid' : ''; ?>">
                    <th scope="row">
                        <label for="capability">
                            <?php echo $this->__('Capability'); ?> <span class="description">(<?php echo $this->__('required'); ?>)</span>
                        </label>
                    </th>
                    <td>
                        <input class="regular-text" name="capability" type="text" id="capability" value="<?php echo $this->capability; ?>" aria-required="true"  />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="roles_type">
                            <?php echo $this->__('Roles'); ?>
                        </label>
                    </th>
                    <td>
                        <fieldset>
                            <label><input name="roles_type" type="radio" value="all" <?php echo $this->roles_type === 'all' ? 'checked' : ''; ?> /><?php echo $this->__('All Roles'); ?></label>
                            <br />
                            <label><input name="roles_type" type="radio" value="selected" <?php echo $this->roles_type === 'selected' ? 'checked' : ''; ?> /><?php echo $this->__('Selected Roles'); ?></label>
                            <div class="<?php echo $this->roles_type === 'all' ? 'hidden' : ''; ?>">
                                <?php
                                $roles = $this->get_roles();

                                foreach ($roles as $key => $value) {
                                    ?>
                                    <label><input type="checkbox" name="selected-roles[<?php echo $key; ?>]" <?php echo array_key_exists($key, $this->roles) ? 'checked' : ''; ?> /><?php echo $value; ?></label>
                                    <br />
                                    <?php
                                }
                                ?>
                            </div>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" id="add-remove-capability" name="add-remove-capability" class="button button-primary" value="<?php echo $this->action === 'add' ? $this->__('Add Capability') : $this->__('Remove Capability'); ?>" />
        </p>
    </form>
    <?php $this->footer(); ?>
</div>

<script type="text/javascript">

    (function ($) {

        var $container = $('div.wrap.add-remove-capability');

        $container.find('input[name="action_type"]').change(function () {
            if ($(this).val() == 'add') {
                $("#add-remove-capability").val("<?php echo $this->__('Add Capability'); ?>");
            } else {
                $("#add-remove-capability").val("<?php echo $this->__('Remove Capability'); ?>");
            }
        });

        $container.find('input[name="roles_type"]').change(function () {
            if ($(this).val() == 'all') {
                $(this).closest('fieldset').find('div').addClass('hidden');
            } else {
                $(this).closest('fieldset').find('div').removeClass('hidden');
            }
        });

    })(jQuery);

</script>