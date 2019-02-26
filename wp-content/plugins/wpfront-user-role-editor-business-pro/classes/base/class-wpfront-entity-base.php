<?php

/*
  WPFront Plugins Entity Base
  Copyright (C) 2013, WPFront.com
  Website: wpfront.com
  Contact: syam@wpfront.com

  WPFront Plugins are distributed under the GNU General Public License, Version 3,
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

require_once(plugin_dir_path(__FILE__) . "class-wpfront-static.php");

if (!class_exists('WPFront_Entity_Base')) {

    /**
     * Plugin entity framework base class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2013 WPFront.com
     */
    abstract class WPFront_Entity_Base {

        private static $DB_STATUS = array();
        protected static $UNINSTALL = FALSE;
        private $slug;
        private $version;
        private $options;
        private $tablename;
        private $func_data = array();
        protected $db_data;

        public function __construct($slug, $version, $options, $tablename) {
            $this->slug = $slug;
            $this->version = $version;
            $this->options = $options;
            $this->tablename = $tablename;

            if (!self::$UNINSTALL) {
                $this->prepare_db_data();
                $this->db_delta();
            }
        }

        private function sql_type($type) {
            switch ($type) {
                case 'bit':
                    return 'tinyint(1)';
                default:
                    return $type;
            }
        }

        private function prepare_db_data() {
            $obj = (OBJECT) array(
                        'key' => 'id',
                        'type' => 'bigint(20)',
                        'sql' => 'id bigint(20) NOT NULL AUTO_INCREMENT',
                        'data' => 0
            );
            $this->db_data = array(
                'id' => $obj
            );
            $this->func_data["get_id"] = (OBJECT) array(
                        'type' => 'get',
                        'data' => $obj
            );
            $this->func_data["get_by_id"] = (OBJECT) array(
                        'data' => $obj
            );

            foreach ($this->_db_data() as $value) {
                $key = $value->name;
                $obj = (OBJECT) array(
                            'key' => $key,
                            'type' => strtolower($value->type),
                            'default' => (isset($value->default) ? $value->default : NULL)
                );
                $obj->sql = "$key {$this->sql_type($obj->type)} " . ($obj->default === NULL ? "DEFAULT NULL" : "DEFAULT $obj->default NOT NULL");
                $obj->data = $obj->default;
                $this->db_data[$key] = $obj;

                $this->func_data["get_$key"] = (OBJECT) array(
                            'data' => $obj
                );
                $this->func_data["set_$key"] = (OBJECT) array(
                            'data' => $obj
                );

                $this->func_data["get_by_$key"] = (OBJECT) array(
                            'data' => $obj
                );
                $this->func_data["get_all_by_$key"] = (OBJECT) array(
                            'data' => $obj
                );
                $this->func_data["delete_by_$key"] = (OBJECT) array(
                            'data' => $obj
                );
            }
        }

        public function table_name() {
            global $wpdb;

            return $wpdb->prefix . 'wpfront_' . $this->tablename;
        }

        private function db_delta() {
            $table_name = $this->table_name();
            if (isset(self::$DB_STATUS[$table_name]))
                return;

            self::$DB_STATUS[$table_name] = TRUE;

            $option_key = $this->get_db_version_option_name();
            $db_version = get_option($option_key);
            if ($db_version === FALSE)
                $db_version = '0.0';

            $table_key = $table_name . '-db-version';

            if (version_compare($db_version, $this->version, '>=')) {
                $db_version = $this->options->get_option($table_key);
                if ($db_version === NULL)
                    $db_version = '0.0';

                if (version_compare($db_version, $this->version, '>='))
                    return;
            }

            global $wpdb;

            $sql = "CREATE TABLE $table_name (";

            foreach ($this->db_data as $key => $value) {
                $sql .= "$value->sql, \n";
            }

            $sql .= "UNIQUE KEY id (id)\n);";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            dbDelta($sql);

            update_option($option_key, $this->version);
            $this->options->update_option($table_key, $this->version);
        }

        abstract protected function _db_data();

        protected function db_data_field($name, $type) {
            return (OBJECT) array(
                        'name' => $name,
                        'type' => $type
            );
        }

        public function __call($name, array $args) {
            if (!array_key_exists($name, $this->func_data)) {
                throw new Exception("$name function not found");
            }

            return call_user_func_array(array($this, preg_replace('/_' . $this->func_data[$name]->data->key . '$/', '', $name)), array($this->func_data[$name]->data, $args));
        }

        private function format($data) {
            $format = array(
                'int' => '%d',
                'bigint' => '%d',
                'varchar' => '%s',
                'longtext' => '%s',
                'tinytext' => '%s',
                'datetime' => '%s',
                'bit' => '%d'
            );

            foreach ($format as $key => $value) {
                if (strpos($data->type, $key) === 0) {
                    return $value;
                }
            }

            throw new Exception("$data->type format not specified");
        }

        private function convert($data) {
            switch ($data->type) {
                case 'bit':
                    return (BOOL) $data->data;
                case 'int':
                case 'bigint':
                    return intval($data->data);
                default:
                    return strval($data->data);
            }
        }

        private function get($data) {
            return $this->convert($data);
        }

        private function set($data, $args) {
            $data->data = $args[0];
        }

        protected function get_object($result) {
            $class = get_class($this);
            $class = new $class();

            foreach ($class->db_data as $key => $value) {
                $value->data = $result[$key];
            }

            return $class;
        }

        private function get_by($data, $args) {
            global $wpdb;

            $table_name = $this->table_name();

            $result = $wpdb->get_row(
                    $wpdb->prepare(
                            "SELECT * FROM $table_name "
                            . "WHERE $data->key = " . $this->format($data), $args[0]
                    ), ARRAY_A
            );

            if ($result === NULL)
                return NULL;

            return $this->get_object($result);
        }

        private function get_all_by($data, $args) {
            global $wpdb;

            $table_name = $this->table_name();

            $result = $wpdb->get_results(
                    $wpdb->prepare(
                            "SELECT * FROM $table_name "
                            . "WHERE $data->key = " . $this->format($data), $args[0]
                    ), ARRAY_A
            );

            $data = array();
            $class = get_class($this);

            foreach ($result as $row) {
                $data[] = $this->get_object($row);
            }

            return $data;
        }

        protected function get_all($orderby = array(), $page_index = -1, $per_page = -1, $search = '') {
            global $wpdb;

            $table_name = $this->table_name();

            $sql = "SELECT * FROM $table_name";
            
            if(!empty($search)) {
                $sql .= " WHERE $search";
            }

            if (!empty($orderby)) {
                $sql .= ' ORDER BY ';
                foreach ($orderby as $key => $value) {
                    $sql .= $key;
                    if ($value)
                        $sql .= ' DESC';
                    $sql .= ', ';
                }
            }
            $sql = trim($sql, ", ");
            
            if($page_index > -1) {
                $start = $page_index * $per_page;
                
                $sql .= " LIMIT $start, $per_page";
            }

            $result = $wpdb->get_results($sql, ARRAY_A);

            $data = array();
            $class = get_class($this);

            foreach ($result as $row) {
                $data[] = $this->get_object($row);
            }

            return $data;
        }

        protected function count($where = '') {
            global $wpdb;

            $table_name = $this->table_name();

            $sql = "SELECT COUNT(*) FROM $table_name";
            
            if(!empty($where)) {
                $sql .= " WHERE $where";
            }

            $result = $wpdb->get_var($sql);

            return intval($result);
        }

        public function add() {
            $values = array();
            $format = array();

            foreach ($this->db_data as $key => $value) {
                if ($key === 'id')
                    continue;
                $values[$key] = $value->data;
                $format[] = $this->format($value);
            }

            global $wpdb;

            $wpdb->insert($this->table_name(), $values, $format);
            $this->db_data['id']->data = $wpdb->insert_id;

            return $wpdb->insert_id;
        }

        public function update() {
            $values = array();
            $format = array();

            foreach ($this->db_data as $key => $value) {
                if ($key === 'id')
                    continue;
                $values[$key] = $value->data;
                $format[] = $this->format($value);
            }

            global $wpdb;

            $wpdb->update($this->table_name(), $values, array('id' => $this->db_data['id']->data), $format, array('%d'));
            return $this->db_data['id']->data;
        }

        public function save() {
            if ($this->db_data['id']->data === 0)
                return $this->add();
            else
                return $this->update();
        }

        public function delete() {
            if ($this->db_data['id']->data === 0)
                return;

            global $wpdb;
            $wpdb->delete($this->table_name(), array('id' => $this->db_data['id']->data), array('%d'));
        }

        public function delete_by($data) {
            global $wpdb;
            $wpdb->delete($this->table_name(), array($data->key => $data->data), $this->format($data));
        }

        private function get_db_version_option_name() {
            return $this->slug . '-db-version';
        }

        protected function uninstall_action() {
            delete_option($this->get_db_version_option_name());

            global $wpdb;
            $table_name = $this->table_name();

            $sql = "DROP TABLE IF EXISTS $table_name;";
            $wpdb->query($sql);
        }

    }

}
