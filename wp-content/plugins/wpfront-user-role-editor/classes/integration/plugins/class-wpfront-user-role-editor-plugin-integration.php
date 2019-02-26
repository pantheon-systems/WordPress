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

if (!class_exists('WPFront_User_Role_Editor_Plugin_Integration')) {

    /**
     * Base class of Plugin Integration
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    abstract class WPFront_User_Role_Editor_Plugin_Integration {

        const ADMINISTRATOR_ROLE_KEY = WPFront_User_Role_Editor::ADMINISTRATOR_ROLE_KEY;
        
        private $initialized = FALSE;

        protected abstract function init($params);

        protected abstract function translate_capability($capability);

        public function __construct($slug) {
            add_action("wpfront_user_role_editor_{$slug}_init", array($this, '_init'));

            add_filter("wpfront_user_role_editor_{$slug}_integration_ready", array($this, '_integration_ready'));
            add_filter("wpfront_user_role_editor_{$slug}_translate_capability", array($this, '_translate_capability'));
        }

        public function _init($params) {
            $this->initialized = TRUE;
            $this->init($params);
        }

        public function _integration_ready() {
            return TRUE;
        }

        public function _translate_capability($capability) {
            if (!$this->initialized)
                return $capability;

            return $this->translate_capability($capability);
        }

    }

}

require_once(plugin_dir_path(__FILE__) . "duplicator/class-wpfront-user-role-editor-plugin-duplicator.php");

