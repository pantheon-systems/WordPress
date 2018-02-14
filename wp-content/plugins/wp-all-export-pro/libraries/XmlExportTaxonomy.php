<?php

if (!class_exists('XmlExportTaxonomy')) {
    final class XmlExportTaxonomy
    {
        private $init_fields = array(
            array(
                'label' => 'term_id',
                'name' => 'Term ID',
                'type' => 'term_id'
            ),
            array(
                'label' => 'term_name',
                'name' => 'Term Name',
                'type' => 'term_name'
            ),
            array(
                'label' => 'term_slug',
                'name' => 'Term Slug',
                'type' => 'term_slug'
            )
        );

        private $default_fields = array(
            array(
                'label' => 'term_id',
                'name' => 'Term ID',
                'type' => 'term_id'
            ),
            array(
                'label' => 'term_name',
                'name' => 'Term Name',
                'type' => 'term_name'
            ),
            array(
                'label' => 'term_slug',
                'name' => 'Term Slug',
                'type' => 'term_slug'
            ),
            array(
                'label' => 'term_description',
                'name' => 'Description',
                'type' => 'term_description'
            ),
            array(
                'label' => 'term_parent_id',
                'name' => 'Parent ID',
                'type' => 'term_parent_id'
            ),
            array(
                'label' => 'term_parent_name',
                'name' => 'Parent Name',
                'type' => 'term_parent_name'
            ),
            array(
                'label' => 'term_parent_slug',
                'name' => 'Parent Slug',
                'type' => 'term_parent_slug'
            ),
            array(
                'label' => 'term_posts_count',
                'name' => 'Count',
                'type' => 'term_posts_count'
            ),
        );

        private $advanced_fields = array();

        public static $is_active = true;

        public function __construct()
        {

            if (XmlExportEngine::$exportOptions['export_type'] == 'specific' and !in_array('taxonomies', XmlExportEngine::$post_types) or XmlExportEngine::$exportOptions['export_type'] == 'advanced') {
                self::$is_active = false;
                return;
            }

            add_filter("wp_all_export_available_sections", array(&$this, "filter_available_sections"), 10, 1);
            add_filter("wp_all_export_init_fields", array(&$this, "filter_init_fields"), 10, 1);
            add_filter("wp_all_export_default_fields", array(&$this, "filter_default_fields"), 10, 1);
            add_filter("wp_all_export_other_fields", array(&$this, "filter_other_fields"), 10, 1);
        }

        // [FILTERS]

        /**
         *
         * Filter Init Fields
         *
         */
        public function filter_init_fields($init_fields)
        {
            return $this->init_fields;
        }

        /**
         *
         * Filter Default Fields
         *
         */
        public function filter_default_fields($default_fields)
        {
            return $this->default_fields;
        }

        /**
         *
         * Filter Other Fields
         *
         */
        public function filter_other_fields($other_fields)
        {
            return $this->advanced_fields;
        }

        /**
         *
         * Filter Sections in Available Data
         *
         */
        public function filter_available_sections($sections)
        {

            unset($sections['media']['additional']['attachments']);
            unset($sections['cats']);
            unset($sections['other']);

            $sections['cf']['title'] = __("Term Meta", "wp_all_export_plugin");

            return $sections;
        }

        // [\FILTERS]

        public function init(& $existing_meta_keys = array())
        {
            if (!self::$is_active) return;

            if (!empty(XmlExportEngine::$exportQuery)) {
                $terms = XmlExportEngine::$exportQuery->get_terms();
            }

            if (!empty($terms)) {
                foreach ($terms as $term) {
                    $term_meta = get_term_meta($term->term_id, '');
                    if (!empty($term_meta)) {
                        foreach ($term_meta as $record_meta_key => $record_meta_value) {
                            if (!in_array($record_meta_key, $existing_meta_keys)) {
                                $to_add = true;
                                foreach ($this->default_fields as $default_value) {
                                    if ($record_meta_key == $default_value['name'] || $record_meta_key == $default_value['type']) {
                                        $to_add = false;
                                        break;
                                    }
                                }
                                if ($to_add) {
                                    foreach ($this->advanced_fields as $advanced_value) {
                                        if ($record_meta_key == $advanced_value['name'] || $record_meta_key == $advanced_value['type']) {
                                            $to_add = false;
                                            break;
                                        }
                                    }
                                }
                                if ($to_add) $existing_meta_keys[] = $record_meta_key;
                            }
                        }
                    }
                }
            }
        }

        public static function prepare_data($term, $exportOptions, $xmlWriter = false, &$acfs, $implode_delimiter, $preview)
        {
            $article = array();

            // associate exported comment with import
            if (wp_all_export_is_compatible() and isset($exportOptions['is_generate_import']) and $exportOptions['is_generate_import'] and $exportOptions['import_id']) {
                $postRecord = new PMXI_Post_Record();
                $postRecord->clear();
                $postRecord->getBy(array(
                    'post_id' => $term->term_id,
                    'import_id' => $exportOptions['import_id'],
                ));

                if ($postRecord->isEmpty()) {
                    $postRecord->set(array(
                        'post_id' => $term->term_id,
                        'import_id' => $exportOptions['import_id'],
                        'unique_key' => $term->term_id
                    ))->save();
                }
                unset($postRecord);
            }

            $is_xml_export = false;

            if (!empty($xmlWriter) and $exportOptions['export_to'] == 'xml' and !in_array($exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants'))) {
                $is_xml_export = true;
            }

            foreach ($exportOptions['ids'] as $ID => $value) {
                $fieldName = apply_filters('wp_all_export_field_name', wp_all_export_parse_field_name($exportOptions['cc_name'][$ID]), XmlExportEngine::$exportID);
                $fieldValue = $exportOptions['cc_value'][$ID];
                $fieldLabel = $exportOptions['cc_label'][$ID];
                $fieldSql = $exportOptions['cc_sql'][$ID];
                $fieldPhp = $exportOptions['cc_php'][$ID];
                $fieldCode = $exportOptions['cc_code'][$ID];
                $fieldType = $exportOptions['cc_type'][$ID];
                $fieldOptions = $exportOptions['cc_options'][$ID];

                if (empty($fieldName) or empty($fieldType) or !is_numeric($ID)) continue;

                $element_name = (!empty($fieldName)) ? $fieldName : 'untitled_' . $ID;

                $element_name_ns = '';

                if ($is_xml_export) {
                    $element_name = (!empty($fieldName)) ? preg_replace('/[^a-z0-9_:-]/i', '', $fieldName) : 'untitled_' . $ID;

                    if (strpos($element_name, ":") !== false) {
                        $element_name_parts = explode(":", $element_name);
                        $element_name_ns = (empty($element_name_parts[0])) ? '' : $element_name_parts[0];
                        $element_name = (empty($element_name_parts[1])) ? 'untitled_' . $ID : preg_replace('/[^a-z0-9_-]/i', '', $element_name_parts[1]);
                    }
                }

                $fieldSnipped = (!empty($fieldPhp) and !empty($fieldCode)) ? $fieldCode : false;

                if ($exportOptions['cc_combine_multiple_fields'][$ID]) {

                    $combineMultipleFieldsValue = $exportOptions['cc_combine_multiple_fields_value'][$ID];

                    $combineMultipleFieldsValue = stripslashes($combineMultipleFieldsValue);
                    $snippetParser = new \Wpae\App\Service\SnippetParser();
                    $snippets = $snippetParser->parseSnippets($combineMultipleFieldsValue);
                    $engine = new XmlExportEngine(XmlExportEngine::$exportOptions);
                    $engine->init_available_data();
                    $engine->init_additional_data();
                    $snippets = $engine->get_fields_options($snippets);

                    $articleData = self::prepare_data($term, $snippets, $xmlWriter = false, $acfs, $implode_delimiter, $preview);

                    $functions = $snippetParser->parseFunctions($combineMultipleFieldsValue);
                    $combineMultipleFieldsValue = \Wpae\App\Service\CombineFields::prepareMultipleFieldsValue($functions, $combineMultipleFieldsValue, $articleData);

                    if($preview) {
                        $combineMultipleFieldsValue = trim(preg_replace('~[\r\n]+~', ' ', htmlspecialchars($combineMultipleFieldsValue)));
                    }

                    wp_all_export_write_article($article, $element_name, pmxe_filter($combineMultipleFieldsValue, $fieldSnipped));

                } else {
                    switch ($fieldType) {
                        case 'term_id':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_term_id', pmxe_filter($term->term_id, $fieldSnipped), $term->term_id));
                            break;
                        case 'term_name':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_term_name', pmxe_filter($term->name, $fieldSnipped), $term->term_id));
                            break;
                        case 'term_slug':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_term_slug', pmxe_filter($term->slug, $fieldSnipped), $term->term_id));
                            break;
                        case 'term_description':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_term_description', pmxe_filter($term->description, $fieldSnipped), $term->term_id));
                            break;
                        case 'term_parent_id':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_term_parent', pmxe_filter($term->parent, $fieldSnipped), $term->term_id));
                            break;
                        case 'term_parent_name':
                            $term_parent_name = '';
                            if ($term->parent) {
                                $parent_term = get_term($term->parent, $term->taxonomy);
                                if ($parent_term) {
                                    $term_parent_name = $parent_term->name;
                                }
                            }
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_term_parent', pmxe_filter($term_parent_name, $fieldSnipped), $term->term_id));
                            break;
                        case 'term_parent_slug':
                            $term_parent_slug = '';
                            if ($term->parent) {
                                $parent_term = get_term($term->parent, $term->taxonomy);
                                if ($parent_term) {
                                    $term_parent_slug = $parent_term->slug;
                                }
                            }
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_term_parent', pmxe_filter($term_parent_slug, $fieldSnipped), $term->term_id));
                            break;
                        case 'term_posts_count':
                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_term_count', pmxe_filter($term->count, $fieldSnipped), $term->term_id));
                            break;
                        // Media Images
                        case 'media':
                        case 'image_id':
                        case 'image_url':
                        case 'image_filename':
                        case 'image_path':
                        case 'image_title':
                        case 'image_caption':
                        case 'image_description':
                        case 'image_alt':

                            $field_options = json_decode($fieldOptions, true);

                            XmlExportMediaGallery::getInstance($term->term_id);

                            $images_data = XmlExportMediaGallery::get_images($fieldType, $field_options);

                            $images_separator = empty($field_options['image_separator']) ? $implode_delimiter : $field_options['image_separator'];

                            wp_all_export_write_article($article, $element_name, apply_filters('pmxe_' . $fieldType, pmxe_filter(implode($images_separator, $images_data), $fieldSnipped), $term->term_id));

                            break;
                        case 'cf':
                            if (!empty($fieldValue)) {
                                $cur_meta_values = get_term_meta($term->term_id, $fieldValue);
                                if (!empty($cur_meta_values) and is_array($cur_meta_values)) {
                                    $val = "";
                                    foreach ($cur_meta_values as $key => $cur_meta_value) {
                                        if (empty($val)) {
                                            $val = apply_filters('pmxe_custom_field', pmxe_filter(maybe_serialize($cur_meta_value), $fieldSnipped), $fieldValue, $term->term_id);
                                        } else {
                                            $val = apply_filters('pmxe_custom_field', pmxe_filter($val . $implode_delimiter . maybe_serialize($cur_meta_value), $fieldSnipped), $fieldValue, $term->term_id);
                                        }
                                    }
                                    wp_all_export_write_article($article, $element_name, $val);
                                }

                                if (empty($cur_meta_values)) {
                                    if (empty($article[$element_name])) {
                                        wp_all_export_write_article($article, $element_name, apply_filters('pmxe_custom_field', pmxe_filter('', $fieldSnipped), $fieldValue, $term->term_id));
                                    }
                                }
                            }
                            break;
                        case 'acf':

                            if (!empty($fieldLabel) and class_exists('acf')) {
                                global $acf;

                                $field_options = unserialize($fieldOptions);

                                if (!$is_xml_export) {
                                    switch ($field_options['type']) {
                                        case 'textarea':
                                        case 'oembed':
                                        case 'wysiwyg':
                                        case 'wp_wysiwyg':
                                        case 'date_time_picker':
                                        case 'date_picker':

                                            $field_value = get_field($fieldLabel, $term->taxonomy . "_" . $term->term_id, false);

                                            break;

                                        default:

                                            $field_value = get_field($fieldLabel, $term->taxonomy . "_" . $term->term_id);

                                            break;
                                    }
                                } else {
                                    $field_value = get_field($fieldLabel, $term->taxonomy . "_" . $term->term_id);
                                }

                                XmlExportACF::export_acf_field(
                                    $field_value,
                                    $exportOptions,
                                    $ID,
                                    $term->taxonomy . "_" . $term->term_id,
                                    $article,
                                    $xmlWriter,
                                    $acfs,
                                    $element_name,
                                    $element_name_ns,
                                    $fieldSnipped,
                                    $field_options['group_id'],
                                    $preview
                                );

                            }

                            break;
                        case 'sql':

                            if (!empty($fieldSql)) {
                                global $wpdb;
                                $val = $wpdb->get_var($wpdb->prepare(stripcslashes(str_replace("%%ID%%", "%d", $fieldSql)), $term->term_id));
                                if (!empty($fieldPhp) and !empty($fieldCode)) {
                                    // if shortcode defined
                                    if (strpos($fieldCode, '[') === 0) {
                                        $val = do_shortcode(str_replace("%%VALUE%%", $val, $fieldCode));
                                    } else {
                                        $val = eval('return ' . stripcslashes(str_replace("%%VALUE%%", $val, $fieldCode)) . ';');
                                    }
                                }
                                wp_all_export_write_article($article, $element_name, apply_filters('pmxe_sql_field', $val, $element_name, $term->term_id));
                            }
                            break;
                        default:
                            # code...
                            break;
                    }
                }

                if ($is_xml_export and isset($article[$element_name])) {
                    $element_name_in_file = XmlCsvExport::_get_valid_header_name($element_name);

                    $xmlWriter = apply_filters('wp_all_export_add_before_element', $xmlWriter, $element_name_in_file, XmlExportEngine::$exportID, $term->term_id);

                    $xmlWriter->beginElement($element_name_ns, $element_name_in_file, null);
                    $xmlWriter->writeData($article[$element_name], $element_name_in_file);
                    $xmlWriter->closeElement();

                    $xmlWriter = apply_filters('wp_all_export_add_after_element', $xmlWriter, $element_name_in_file, XmlExportEngine::$exportID, $term->term_id);
                }
            }
            return $article;
        }

        public static function prepare_import_template($exportOptions, &$templateOptions, $element_name, $ID)
        {

            $options = $exportOptions;

            $element_type = $options['cc_type'][$ID];

            $is_xml_template = $options['export_to'] == 'xml';

            $implode_delimiter = XmlExportEngine::$implode;

            switch ($element_type) {
                // Export Taxonomy Terms
                case 'term_id':
                    $templateOptions['unique_key'] = '{' . $element_name . '[1]}';
                    $templateOptions['tmp_unique_key'] = '{' . $element_name . '[1]}';
                    break;
                case 'term_name':
                    $templateOptions['title'] = '{' . $element_name . '[1]}';
                    $templateOptions['is_update_title'] = 1;
                    break;
                case 'term_slug':
                    $templateOptions['taxonomy_slug'] = 'xpath';
                    $templateOptions['taxonomy_slug_xpath'] = '{' . $element_name . '[1]}';
                    $templateOptions['is_update_slug'] = 1;
                    break;
                case 'term_description':
                    $templateOptions['content'] = '{' . $element_name . '[1]}';
                    $templateOptions['is_update_content'] = 1;
                    break;
                case 'term_parent_slug':
                    $templateOptions['taxonomy_parent'] = '{' . $element_name . '[1]}';
                    $templateOptions['is_update_parent'] = 1;
                    break;

            }
        }

        /**
         * __get function.
         *
         * @access public
         * @param mixed $key
         * @return mixed
         */
        public function __get($key)
        {
            return $this->get($key);
        }

        /**
         * Get a session variable
         *
         * @param string $key
         * @param  mixed $default used if the session variable isn't set
         * @return mixed value of session variable
         */
        public function get($key, $default = null)
        {
            return isset($this->{$key}) ? $this->{$key} : $default;
        }
    }
}