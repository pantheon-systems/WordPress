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

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront_User_Role_Editor_Attachment_Permissions')) {

    /**
     * Attachment Permissions
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Attachment_Permissions extends WPFront_User_Role_Editor_Controller_Base {

        private static $attachment_capabilities = array('edit_attachments', 'delete_attachments', 'read_others_attachments', 'edit_others_attachments', 'delete_others_attachments');

        public function __construct($main) {
            parent::__construct($main);

            $media = WPFront_User_Role_Editor::$STANDARD_CAPABILITIES['Media'];
            foreach (self::$attachment_capabilities as $value) {
                $media[$value] = $media['upload_files'];
            }
            WPFront_User_Role_Editor::$STANDARD_CAPABILITIES['Media'] = $media;

            add_filter('user_has_cap', array($this, 'user_has_cap'), 10, 3);
            add_filter('posts_where', array($this, 'posts_where'), 10, 2);

            add_action('admin_init', array($this, 'admin_init'));
        }

        public function user_has_cap($allcaps, $caps, $args) {
            return $allcaps;
        }

        public function posts_where($where, $query) {
            return $where;
        }

        public function admin_init() {
            $option_key = 'attachment_capabilities_processed';
            $entity = new WPFront_User_Role_Editor_Entity_Options();

            $processed = $entity->get_option($option_key);
            if (!empty($processed))
                return;

            global $wp_roles;

            foreach ($wp_roles->role_objects as $key => $role) {
                if ($role->has_cap('upload_files')) {
                    foreach (self::$attachment_capabilities as $cap) {
                        $role->add_cap($cap);
                    }
                }
            }

            $entity->update_option($option_key, TRUE);
        }

    }

}

