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

if (!class_exists('WPFront_User_Role_Editor_Plugin_Duplicator')) {

    /**
     * Main class of Duplicator Integration
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Plugin_Duplicator extends WPFront_User_Role_Editor_Plugin_Integration {

        private static $slug = 'duplicator';

        public function __construct() {
            parent::__construct(self::$slug);
        }

        protected function init($params) {
            $admin_role = get_role(self::ADMINISTRATOR_ROLE_KEY);
            $caps = array();
            foreach($params as $value) {
                $cap = $this->translate_capability($value);
                $caps[$cap] = TRUE;
                $admin_role->add_cap($cap);
            }

            add_role(self::$slug, 'Duplicator', $caps);
        }

        protected function translate_capability($capability) {
            return self::$slug . '_' . $capability;
        }

    }

    new WPFront_User_Role_Editor_Plugin_Duplicator();
}

