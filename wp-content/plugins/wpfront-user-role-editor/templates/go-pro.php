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
 * Template for WPFront User Role Editor Go Pro
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

<div class="wrap go-pro">
    <h2>
        <?php
        if ($this->product === NULL)
            echo $this->__('WPFront User Role Editor Pro');
        else
            echo $this->__($this->product);
        ?>
    </h2>

    <?php if ($this->error !== NULL) { ?>
        <div class="error below-h2">
            <p>
                <?php echo $this->error; ?>
            </p>
        </div>
    <?php } ?>

    <?php if ($this->need_license) { ?>
        <div class="license-container">
            <form id="license-form" method="POST">
                <?php $this->main->create_nonce(); ?>
                <table class="form-table">
                    <tbody>
                        <tr class="form-required">
                            <th scope="row">
                                <label for="license_key">
                                    <?php echo $this->__('License Key'); ?>
                                </label>
                            </th>
                            <td>
                                <input name="license_key" type="text" id="license_key" class="regular-text" value="<?php echo $this->license_key; ?>" aria-required="true" <?php echo $this->has_license ? 'disabled' : ''; ?> />
                                <?php if (!$this->has_license) { ?>
                                    <input type="submit" name="activate" class="button-secondary" value="<?php echo $this->__('Activate'); ?>" />
                                <?php } else { ?>
                                    <input type="submit" name="deactivate" class="button-secondary" value="<?php echo $this->__('Deactivate'); ?>" />
                                    <input type="button" name="recheck" class="button-secondary" value="<?php echo $this->__('Recheck'); ?>" />
                                <?php } ?>
                            </td>
                        </tr>
                        <?php if ($this->has_license) { ?>
                            <tr>
                                <th scope="row">
                                    <?php echo $this->__('License Status'); ?>
                                </th>
                                <td class="<?php echo $this->license_status; ?>">
                                    <?php
                                    switch ($this->license_status) {
                                        case 'valid':
                                            echo $this->__('Valid');
                                            break;
                                        case 'expired':
                                            echo $this->__('Expired');
                                            break;
                                        case 'invalid':
                                            echo $this->__('Invalid');
                                            if(!empty($this->license_home_url)) {
                                                echo ' [' . $this->license_home_url . ']';
                                            }
                                            break;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <?php echo $this->__('License Expires'); ?>
                                </th>
                                <td class="<?php echo $this->license_expired ? 'expired' : ''; ?>">
                                    <?php
                                    echo $this->license_expires;

                                    if ($this->license_status != 'invalid' && !empty($this->renew_url)) {
                                        echo sprintf('<a class="add-new-h2 post-title-action" href="%s" target="_blank">%s</a>', $this->renew_url, $this->__('Renew'));
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </form>
        </div>

        <script type="text/javascript">
            (function ($) {
                var noblock = false;
                
                function formSubmit() {
                    noblock = true;
                    var $form = $('#license-form');
                    $form.find("input").prop('disabled', false);
                    $form.find("input").removeAttr('disabled');
                    $form.find("input[type='submit']").click();
                    setTimeout(function () {
                        $form.find("input[type='submit']").click();
                    }, 500);
                }
                
                $('#license-form').submit(function () {
                    setTimeout(formSubmit, 30000);
                    
                    if (noblock)
                        return;

                    var data = {
                        "action": "wpfront_user_role_editor_license_functions"
                    };

                    $(this).find("input").prop('disabled', true).each(function () {
                        var $input = $(this);
                        data[$input.attr('name')] = $input.val();
                    });

                    $.post(ajaxurl, data, function (response) {
                        if (response)
                            window.location.replace(window.location.href);
                        else {
                            formSubmit();
                        }
                    }, 'json');

                    return false;
                }).find('input[name="recheck"]').click(function () {
                    $(this).prev().attr("name", "recheck").click();
                });
            })(jQuery);
        </script>
    <?php } ?>

    <div>
        <p></p>
        <?php echo $this->pro_html; ?>
    </div>

</div>