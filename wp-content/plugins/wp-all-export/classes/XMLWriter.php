<?php

// Handle eval errors that cause the script to finish
$wpaeErrorHandler = new WpaePhpInterpreterErrorHandler();
register_shutdown_function(array($wpaeErrorHandler, 'handle'));

/**
 * Class PMXE_XMLWriter
 */
class PMXE_XMLWriter extends XMLWriter
{
    /**
     * @var array
     */
    public $articles = array();


    /**
     * @param array $articles
     */
    public function writeArticle($articles = array())
    {

        $article = array_shift($articles);

        if (!empty($articles)) {

            $keys = array();
            foreach ($articles as $a) {

                foreach ($a as $key => $value) {

                    if (!isset($article[$key])) {
                        $article[$key] = array($value);
                    } else {
                        if (is_array($article[$key])){
                            array_push($article[$key], $value);
                        }
                        else{
                            $article[$key] = array($article[$key], $value);
                        }
                    }

                    if (!in_array($key, $keys)) $keys[] = $key;
                }
            }
        }

        if (!empty($article)) {
            foreach ($article as $key => $value) {
                if (!is_array($value) && strpos($value, '#delimiter#') !== FALSE) {
                    $article[$key] = explode('#delimiter#', $value);
                }
            }
        }

        $this->articles[] = $article;
    }

    /**
     * @param $ns
     * @param $element
     * @param $uri
     * @param $value
     * @return bool
     */
    public function putElement($ns, $element, $uri, $value)
    {
        if (in_array(XmlExportEngine::$exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants'))) return true;

        if (empty($ns)) {
            return $this->writeElement($element, $value);
        } else {
            return $this->writeElementNS($ns, $element, $uri, $value);
        }
    }

    /**
     * @param $ns
     * @param $element
     * @param $uri
     * @return bool
     */
    public function beginElement($ns, $element, $uri)
    {
        if (in_array(XmlExportEngine::$exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants'))) return true;

        if (empty($ns)) {
            return $this->startElement($element);
        } else {
            return $this->startElementNS($ns, $element, $uri);
        }
    }

    /**
     * @return bool
     */
    public function closeElement()
    {

        if (in_array(XmlExportEngine::$exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants'))) return true;

        return $this->endElement();
    }

    /**
     * @param $value
     * @param $element_name
     *
     * @return bool
     */
    public function writeData($value, $element_name)
    {
        if (in_array(XmlExportEngine::$exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants'))) return true;

        $cdataStrategyFactory = new CdataStrategyFactory();

        if (!isset(XmlExportEngine::$exportOptions['custom_xml_cdata_logic'])) {
            XmlExportEngine::$exportOptions['custom_xml_cdata_logic'] = 'auto';
        }
        $cdataStrategy = $cdataStrategyFactory->create_strategy(XmlExportEngine::$exportOptions['custom_xml_cdata_logic']);
        $is_wrap_into_cdata = $cdataStrategy->should_cdata_be_applied($value);

        $wrap_value_into_cdata = apply_filters('wp_all_export_is_wrap_value_into_cdata', $is_wrap_into_cdata, $value, $element_name);

        if ($wrap_value_into_cdata === false) {
            $this->writeRaw($value);
        } else {
            if (XmlExportEngine::$is_preview && XmlExportEngine::$exportOptions['show_cdata_in_preview']) {
                $this->text('CDATABEGIN' . $value . 'CDATACLOSE');
            } else if (XmlExportEngine::$is_preview && !XmlExportEngine::$exportOptions['show_cdata_in_preview']) {
                return $this->text($value);
            } else {
                $this->writeCdata($value);
            }
        }
    }

    /**
     * @return mixed|string
     */
    public function wpae_flush()
    {
        if (!in_array(XmlExportEngine::$exportOptions['xml_template_type'], array('custom', 'XmlGoogleMerchants'))) return $this->flush(true);

        $xml = '';
        foreach ($this->articles as $article) {

            $founded_values = array_keys($article);
            $node_tpl = XmlExportEngine::$exportOptions['custom_xml_template_loop'];

            // clean up XPaths for not found values
            preg_match_all("%(\{[^\}\{]*\})%", $node_tpl, $matches);
            $xpaths = array_unique($matches[0]);

            if (!empty($xpaths)) {
                foreach ($xpaths as $xpath) {
                    if (!in_array(preg_replace("%[\{\}]%", "", $xpath), $founded_values)) {
                        $node_tpl = str_replace($xpath, "", $node_tpl);
                    }
                }
            }

            foreach ($article as $key => $value) {
                switch ($key) {
                    case 'id':
                        $node_tpl = str_replace('{'.$key.'}', '{' . $value . '}', $node_tpl);
                        break;
                    default:
                        // replace [ and ]
                        $v = str_replace(']', 'CLOSEBRAKET', str_replace('[', 'OPENBRAKET', $value));
                        // replace { and }
                        $v = str_replace('}', 'CLOSECURVE', str_replace('{', 'OPENCURVE', $v));
                        // replace ( and )
                        $v = str_replace(')', 'CLOSECIRCLE', str_replace('(', 'OPENCIRCLE', $v));

                        $originalValue = $v;

                        if (is_array($v)) {
                            foreach($v as &$val) {
                                $val = str_replace("\"","**DOUBLEQUOT**",$val);
                                $val = str_replace("'","**SINGLEQUOT**",$val);
                            }

                            $delimiter = uniqid();
                            $node_tpl = preg_replace('%\[(.*)\{'.$key.'\}([^\[]*)\]%', "[$1explode('" . $delimiter . "', '" . implode($delimiter, $v) . "')$2]", $node_tpl);
                            $v = "[explode('" . $delimiter . "', '" . implode($delimiter, $v) . "')]";
                        } else {
                            $v = '{' . $v . '}';
                        }

                        $arrayTypes = array(
                            'Product Tags', 'Tags', 'Product Categories', 'Categories', 'Image URL', 'Image Filename', 'Image Path', 'Image ID', 'Image Title', 'Image Caption', 'Image Description', 'Image Alt Text', 'Product Type', 'Categories'
                        );

                        // We have an empty array, which is transformed into {}
                        if(in_array($key, $arrayTypes) && $v == "{}") {
                            $delimiter = uniqid();
                            $node_tpl = preg_replace('%\[(.*)\{'.$key.'\}([^\[]*)\]%', "[$1explode('" . $delimiter . "', '" . implode($delimiter, array()) . "')$2]", $node_tpl);
                            $v = "[explode('" . $delimiter . "', '" . implode($delimiter, array()) . "')]";
                        }

                        // We have an array with just one value (Which is transformed into a string)
                        if(in_array($key, $arrayTypes) && count($originalValue) == 1) {
                            $delimiter = uniqid();
                            $node_tpl = preg_replace('%\[(.*)\{'.$key.'\}([^\[]*)\]%', "[$1explode('" . $delimiter . "', '" . implode($delimiter, array($originalValue)) . "')$2]", $node_tpl);
                            $v = "[explode('" . $delimiter . "', '" . implode($delimiter, array($originalValue)) . "')]";
                        }
                        
                        $node_tpl = str_replace('{' . $key . '}', $v, $node_tpl);

                        break;
                }
            }

            $xml .= $node_tpl;

        }

        $this->articles = array();
        $wpaeString = new WpaeString();
        $xmlPrepreocesor = new WpaeXmlProcessor($wpaeString);
        return $xmlPrepreocesor->process($xml);
    }

    public static function getIndentationCount($content, $str)
    {
        $lines = explode("\r", $content);
        foreach ($lines as $lineNumber => $line) {
            if (strpos($line, $str) !== false) {
                return substr_count($line, "\t");
            }
        }
        
        return -1;
    }

    public static function indentTag($tag, $indentationCount, $index)
    {
        if($index == 0) {
            $indentationString = "";
        } else {
            $indentationString = str_repeat("\t", $indentationCount);
        }

        return $indentationString . $tag;
    }

    /**
     * @param string $xml
     * @return mixed|string
     *
     * @throws WpaeInvalidStringException
     * @throws WpaeMethodNotFoundException
     */
    public static function preprocess_xml($xml = '')
    {
        $xml = str_replace('<![CDATA[', 'DOMCdataSection', $xml);

        preg_match_all("%(\[[^\]\[]*\])%", $xml, $matches);
        $snipets = empty($matches) ? array() : array_unique($matches[0]);
        $simple_snipets = array();
        preg_match_all("%(\{[^\}\{]*\})%", $xml, $matches);
        $xpaths = array_unique($matches[0]);

        if (!empty($xpaths)) {
            foreach ($xpaths as $xpath) {
                if (!in_array($xpath, $snipets)) $simple_snipets[] = $xpath;
            }
        }

        if (!empty($snipets)) {
            foreach ($snipets as $snipet) {
                // if function is found
                if (preg_match("%\w+\(.*\)%", $snipet)) {

                    $filtered = trim(trim(trim($snipet, "]"), "["));
                    $filtered = preg_replace("%[\{\}]%", "\"", $filtered);
                    $filtered = str_replace('CLOSEBRAKET', ']', str_replace('OPENBRAKET', '[', $filtered));
                    $filtered = str_replace('CLOSECURVE', '}', str_replace('OPENCURVE', '{', $filtered));
                    $filtered = str_replace('CLOSECIRCLE', ')', str_replace('OPENCIRCLE', '(', $filtered));

                    $functionName = self::sanitizeFunctionName($filtered);

                    $numberOfSingleQuotes = substr_count($filtered, "'");
                    $numberOfDoubleQuotes = substr_count($filtered, "\"");

                    if ($numberOfSingleQuotes % 2 || $numberOfDoubleQuotes % 2) {
                        throw new WpaeInvalidStringException($functionName);
                    }

                    if (!function_exists($functionName)) {
                        throw new WpaeMethodNotFoundException($functionName);
                    }

                    $values = eval("return " . $filtered . ";");

                    $v = '';
                    if (is_array($values)) {
                        $tag = false;

                        preg_match_all("%(<[\w]+[\s|>]{1})" . preg_quote($snipet) . "%", $xml, $matches);

                        if (!empty($matches[1])) {
                            $tag = array_shift($matches[1]);
                        }
                        if (empty($tag)) $tag = "<item>";

                        $indentationCount = self::getIndentationCount($xml, $tag);

                        $i = 0;
                        foreach ($values as $number => $value) {
                            $v .= self::indentTag($tag . self::maybe_cdata($value) . str_replace("<", "</", $tag) . "\n", $indentationCount, $i);
                            $i++;
                        }

                        $xml = str_replace($tag . $snipet . str_replace("<", "</", $tag), $v, $xml);
                    } else {
                        $xml = str_replace($snipet, self::maybe_cdata($values), $xml);
                    }
                }
            }
        }

        if (!empty($simple_snipets)) {
            foreach ($simple_snipets as $snipet) {
                $filtered = preg_replace("%[\{\}]%", "", $snipet);

                $is_attribute = false;

                //Encode data in attributes
                if (strpos($xml, "\"$snipet\"") !== false || strpos($xml, "'$snipet'") !== false) {
                    $is_attribute = true;
                    $filtered = str_replace('&amp;', '&', $filtered);
                    $filtered = str_replace('&', '&amp;', $filtered);
                    $filtered = str_replace('\'', '&#x27;', $filtered);
                    $filtered = str_replace('"', '&quot;', $filtered);
                    $filtered = str_replace('<', '&lt;', $filtered);
                    $filtered = str_replace('>', '&gt;', $filtered);
                }

                $filtered = str_replace('CLOSEBRAKET', ']', str_replace('OPENBRAKET', '[', $filtered));
                $filtered = str_replace('CLOSECURVE', '}', str_replace('OPENCURVE', '{', $filtered));
                $filtered = str_replace('CLOSECIRCLE', ')', str_replace('OPENCIRCLE', '(', $filtered));

                if ($is_attribute) {
                    $xml = str_replace($snipet, $filtered, $xml);
                } else {
                    $xml = str_replace($snipet, self::maybe_cdata($filtered), $xml);
                }
            }
        }

        $xml = str_replace('DOMCdataSection', '<![CDATA[', $xml);

        return $xml;
    }

    /**
     * @param $v
     * @return string
     */
    public static function maybe_cdata($v)
    {

        if (XmlExportEngine::$is_preview) {
            $v = str_replace('&amp;', '&', $v);
            $v = htmlspecialchars($v);
        }

        if (XmlExportEngine::$is_preview && !XmlExportEngine::$exportOptions['show_cdata_in_preview']) {
            return $v;
        }

        $cdataStrategyFactory = new CdataStrategyFactory();

        if (!isset(XmlExportEngine::$exportOptions['custom_xml_cdata_logic'])) {
            XmlExportEngine::$exportOptions['custom_xml_cdata_logic'] = 'auto';
        }

        $cdataStrategy = $cdataStrategyFactory->create_strategy(XmlExportEngine::$exportOptions['custom_xml_cdata_logic']);
        $is_wrap_into_cdata = $cdataStrategy->should_cdata_be_applied($v);

        if ($is_wrap_into_cdata === false) {
            return $v;
        } else {
            if (XmlExportEngine::$is_preview && XmlExportEngine::$exportOptions['show_cdata_in_preview']) {
                return 'CDATABEGIN' . $v . 'CDATACLOSE';
            } else {
                return "<![CDATA[" . $v . "]]>";
            }
        }
    }

    /**
     * @param $filtered
     * @return mixed
     */
    private static function sanitizeFunctionName($filtered)
    {
        $functionName = preg_replace('/"[^"]+"/', '', $filtered);
        $functionName = preg_replace('/\'[^\']+\'/', '', $functionName);

        $firstSingleQuote = strpos($functionName, '\'');
        $firstDoubleQuote = strpos($functionName, '"');

        if ($firstDoubleQuote < $firstSingleQuote && $firstDoubleQuote != 0) {
            $functionName = explode('"', $functionName);
            $functionName = $functionName[0];
        } else if ($firstSingleQuote != 0) {
            $functionName = explode('\'', $functionName);
            $functionName = $functionName[0];
        }
        $functionName = str_replace(array('(', ')', ',', ' ', '\'', '"'), '', $functionName);

        return $functionName;
    }
} 