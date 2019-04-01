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
 * Template for WPFront User Role Editor Restore Role
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

<div class="wrap role-restore">
    <h2 id="restore-role">
        <?php echo $this->__('Restore Role'); ?>
    </h2>

    <table class="form-table">
        <tbody>
            <?php foreach ($this->roles as $key => $value) {
                ?>
                <tr class="form-field">
                    <th scope="row">
                        <?php echo $value; ?>
                    </th>
                    <td>
                        <button class="button button-primary restore-role" value="<?php echo $key; ?>"><?php echo $this->__('Restore'); ?></button>
                        <div class="restore-role-button-container">
                            <button class="button restore-role-cancel" value="<?php echo $key; ?>"><?php echo $this->__('Cancel'); ?></button>
                            <button class="button restore-role-confirm" value="<?php echo $key; ?>"><?php echo $this->__('Confirm'); ?></button>
                        </div>
                        <div class="restore-role-loader">
                            <img src="<?php echo $this->image_url() . 'loading.gif'; ?>" />
                        </div>
                        <div class="restore-role-success">
                            <button class="button button" disabled="true">
                                <i class="fa fa-check fa-1"></i>
                                <?php echo $this->__('Restored'); ?>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php }
            ?>

        </tbody>
    </table>
</div>

<script type="text/javascript">
    (function($) {
        $('button.restore-role').click(function() {
            $(this).hide().next().show();
        });

        $('button.restore-role-cancel').click(function() {
            $(this).parent().hide().prev().show();
        });

        $('button.restore-role-confirm').click(function() {
            $('button.restore-role-confirm').prop('disabled', true);

            var _this = $(this).parent().hide().next().show();

            var data = {
                "action": "wpfront_user_role_editor_restore_role",
                "role": $(this).val(),
                "referer": <?php echo json_encode(esc_html($_SERVER['REQUEST_URI'])); ?>,
                "nonce": <?php echo json_encode(wp_create_nonce(esc_html($_SERVER['REQUEST_URI']))); ?>
            };

<?php
if ($this->multisite) {
    ?>
                data["multisite"] = true;
    <?php
}
?>

            var response_process = function(response) {
                if (typeof response === 'undefined' || response == null) {
                    response = {'result': false, 'message': <?php echo json_encode($this->__('Unexpected error / Timed out')); ?>};
                }
                _this.hide();
                if (response.result)
                    _this.next().show();
                else
                    _this.next().text(response.message).css('color', 'Red').show();

                $('button.restore-role-confirm').prop('disabled', false);
            };

            $.post(ajaxurl, data, response_process, 'json').fail(function() {
                response_process();
            });
        });
    })(jQuery);
</script>