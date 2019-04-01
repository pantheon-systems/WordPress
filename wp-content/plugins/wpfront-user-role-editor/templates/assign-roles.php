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
 * Template for WPFront User Role Editor List Roles
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */
?>

<?php
if (!defined('ABSPATH')) {
    exit();
}

$this->main->verify_nonce();
?>

<div class="wrap assign-roles">
    <h2 id="assign-roles">
        <?php echo $this->__('Assign Roles'); ?>
    </h2>

    <?php
    if ($this->result != NULL) {
        if ($this->result->success) {
            ?>
            <div class="updated">
                <p><?php echo $this->result->message; ?></p>
            </div>
        <?php } else { ?>

            <div class="error">
                <p><?php echo $this->result->message; ?></p>
            </div>
            <?php
        }
    }
    ?>

    <form method="POST">
        <?php $this->main->create_nonce(); ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <?php echo $this->__('User'); ?>
                    </th>
                    <td>
                        <select id="assign_users_list" name="assign-user">
                            <?php
                            foreach ($this->users as $user) {
                                $select = FALSE;
                                if ($this->user != NULL && $this->user->ID == $user->ID)
                                    $select = TRUE;
                                ?>
                                <option <?php echo $select ? 'selected' : ''; ?> value="<?php echo $user->ID; ?>">
                                    <?php echo $user->display_name . ' [' . $user->user_login . ']'; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <?php $this->primary_secondary_section('assign', $this->userPrimaryRole, $this->userSecondaryRoles); ?>

            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="assignroles" id="assignroles" class="button button-primary" value="<?php echo $this->__('Assign Roles'); ?>" />
        </p>
    </form>

    <h2 id="migrate-users">
        <?php echo $this->__('Migrate Users'); ?>
    </h2>

    <form method="POST">
        <?php $this->main->create_nonce(); ?>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <?php echo $this->__('From Primary Role'); ?>
                    </th>
                    <td>
                        <select id="migrate_from_role" name="migrate-from-primary-role">
                            <?php
                            foreach ($this->primary_roles as $key => $role) {
                                ?>
                                <option value="<?php echo $key; ?>" <?php echo $this->migrateFromPrimaryRole === $key ? 'selected' : ''; ?>>
                                    <?php echo $role; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <?php $this->primary_secondary_section('migrate', $this->migrateToPrimaryRole, $this->migrateToSecondaryRoles); ?>

            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="migrateroles" id="migrateroles" class="button button-primary" value="<?php echo $this->__('Migrate Users'); ?>" />
        </p>
    </form>

</div>

<script type="text/javascript">

    (function ($) {

        var page_url = '<?php echo $this->get_assign_role_url(); ?>';

        $('#assign_users_list').change(function () {
            window.location.replace(page_url + $(this).val());
        });

        $('#assign_roles_list, #migrate_roles_list').change(function () {
            var $this = $(this);
            if ($this.val() == '') {
                $this.closest('table').find('div.role-list-item input').prop('disabled', true);
                $this.closest('table').next().find('input').prop('disabled', true);
            }
            else {
                $this.closest('table').find('div.role-list-item input').prop('disabled', false);
                $this.closest('table').next().find('input').prop('disabled', false);
            }
        }).change();

    })(jQuery);

</script>