<?php

include __DIR__ . "/XmlStreamReader/autoload.php";

use Prewk\XmlStringStreamer;
use Prewk\XmlStringStreamer\Parser;
use Prewk\XmlStringStreamer\Stream;

/**
 * Chunk
 * 
 * Reads a large file in as chunks for easier parsing.
 * 
 * 
 * @package default
 * @author Max Tsiplyakov
 */
class PMXI_Chunk {
  /**
   * options
   *
   * @var array Contains all major options
   * @access public
   */
  public $options = array(
    'path' => './',       // string The path to check for $file in
    'element' => '',      // string The XML element to return    
    'type' => 'upload',
    'encoding' => 'UTF-8',
    'pointer' => 1,
    'chunkSize' => 1024,
    'filter' => true,
    'get_cloud' => false    
  );
  
  /**
   * file
   *
   * @var string The filename being read
   * @access public
   */
  public $file = '';
  /**
   * pointer
   *
   * @var integer The current position the file is being read from
   * @access public
   */
  public $reader;  
  public $cloud = array();      
  public $loop = 1;  
  public $is_404 = false;
  public $parser_type = false;
    
  /**
   * handle
   *
   * @var resource The fopen() resource
   * @access private
   */
  private $handle = null;
  /**
   * reading
   *
   * @var boolean Whether the script is currently reading the file
   * @access private
   */  
  
  /**
   * __construct
   * 
   * Builds the Chunk object
   *
   * @param string $file The filename to work with
   * @param array $options The options with which to parse the file
   * @author Dom Hastings
   * @access public
   */
  public function __construct($file, $options = array(), $parser_type = false) {
    
    // merge the options together
    $this->options = array_merge($this->options, (is_array($options) ? $options : array()));                       

    $this->options['chunkSize'] *= PMXI_Plugin::getInstance()->getOption('chunk_size');      

    // set the filename
    $this->file = $file;   

    $this->parser_type = empty($parser_type) ? 'xmlreader' : $parser_type;

    $sleep = apply_filters( 'wp_all_import_shard_delay', 0 );
    usleep($sleep);

    $is_html = false;
    $f = @fopen($file, "rb");
    while (!@feof($f)) {
      $chunk = @fread($f, 1024);         
      if (strpos($chunk, "<!DOCTYPE") === 0) $is_html = true;
      break;      
    }  
    @fclose($f);    

    if ($is_html)
    {
      $path = $this->get_file_path();

      $this->is_404 = true;

      $this->reader = new XMLReader();            
      @$this->reader->open($path);
      @$this->reader->setParserProperty(XMLReader::VALIDATE, false);
      return;
    }

    $input  = new PMXI_Input();
    $import_id = $input->get('id', 0);
    if ( empty($import_id)) $import_id = $input->get('import_id', 0);

    if ( PMXI_Plugin::getInstance()->getOption('force_stream_reader') )
    {
      $this->parser_type = 'xmlstreamer';
    }
    else
    {
      if ( ! empty($import_id) )
      {
        $this->parser_type = empty($parser_type) ? 'xmlreader' : $parser_type;
        $import = new PMXI_Import_Record();
        $import->getById($import_id);
        if ( ! $import->isEmpty() ){
          $this->parser_type = empty($import->options['xml_reader_engine']) ? 'xmlreader' : 'xmlstreamer';
        }
      }    
      else
      {
        $this->parser_type = empty($parser_type) ? get_option('wpai_parser_type', 'xmlreader') : $parser_type;
      }
    }        

    if (empty($this->options['element']) or $this->options['get_cloud'])
    {      
      $path = $this->get_file_path();

      if ( $this->parser_type == 'xmlreader' ) 
      {
        $reader = new XMLReader();
        $reader->open($path);
        $reader->setParserProperty(XMLReader::VALIDATE, false);
        while ( @$reader->read() ) {
           switch ($reader->nodeType) {
             case (XMLREADER::ELEMENT):                    
                $localName = str_replace("_colon_", ":", $reader->localName);     
                if (array_key_exists(str_replace(":", "_", $localName), $this->cloud))
                  $this->cloud[str_replace(":", "_", $localName)]++;
                else
                  $this->cloud[str_replace(":", "_", $localName)] = 1;                                     
                break;                            
              default:

                break;
           }
        }
        unset($reader);   
      }      
      else 
      {   
        $CHUNK_SIZE = 1024;
        $streamProvider = new Prewk\XmlStringStreamer\Stream\File($path, $CHUNK_SIZE);
        $parseroptions = array(            
            "extractContainer" => false, // Required option
        );
        // Works like an XmlReader, and walks the XML tree node by node. Captures by node depth setting.
        $parser = new Parser\StringWalker($parseroptions);
        // Create the streamer
        $streamer = new XmlStringStreamer($parser, $streamProvider);        
        while ($node = $streamer->getNode()) {        
            // $simpleXmlNode = simplexml_load_string($node);
            // echo (string)$simpleXmlNode->firstName;            
        }

        $this->cloud = $parser->cloud;

      }

      if ( ! empty($this->cloud) and empty($this->options['element']) ){
        
        arsort($this->cloud);           

        $main_elements = array('node', 'product', 'job', 'deal', 'entry', 'item', 'property', 'listing', 'hotel', 'record', 'article', 'post', 'book', 'item_0');

        foreach ($this->cloud as $element_name => $value) {          
          if ( in_array(strtolower($element_name), $main_elements) ){
            $this->options['element'] = $element_name;
            break;    
          }
        }
        
        if (empty($this->options['element'])){                
          foreach ($this->cloud as $el => $count) {                        
              $this->options['element'] = $el;
              break;            
          }          
        }

        $this->options['element'] = apply_filters('wp_all_import_root_element', $this->options['element'], $import_id, $this->cloud);
      }
    }

    $path = $this->get_file_path();                                 

    if ( $this->parser_type == 'xmlreader' ) 
    {
      $this->reader = new XMLReader();            
      @$this->reader->open($path);
      @$this->reader->setParserProperty(XMLReader::VALIDATE, false);
    }
    else
    {      
      $parseroptions = array(
          "uniqueNode" => $this->options['element']
      );
      $CHUNK_SIZE = 1024;
      $streamProvider = new Prewk\XmlStringStreamer\Stream\File($path, $CHUNK_SIZE);
      $parser = new Parser\UniqueNode($parseroptions);
      $this->reader = new XmlStringStreamer($parser, $streamProvider);
    }
  }  

  function get_file_path()
  {
    $is_enabled_stream_filter = apply_filters('wp_all_import_is_enabled_stream_filter', true);
    if ( function_exists('stream_filter_register') and $this->options['filter'] and $is_enabled_stream_filter and $this->parser_type == 'xmlreader' )
    {
        stream_filter_register('preprocessxml', 'preprocessXml_filter');
        if (defined('HHVM_VERSION'))
           $path = $this->file;
        else
           $path = 'php://filter/read=preprocessxml/resource=' . $this->file;
    }
    else $path = $this->file;

    return $path;
  }

  /**
   * __destruct
   * 
   * Cleans up
   *
   * @return void
   * @author Dom Hastings
   * @access public
   */
  public function __destruct() {
    // close the file resource
    unset($this->reader);
  }
  
  /**
   * read
   * 
   * Reads the first available occurence of the XML element $this->options['element']
   *
   * @return string The XML string from $this->file
   * @author Dom Hastings
   * @access public
   */
  public function read($debug = false) {

    // trim it
    $element = trim($this->options['element']);
                  
    $xml = '';    
    
    if ( $this->parser_type == 'xmlreader' ) 
    {
      try { 
        while ( @$this->reader->read() ) {        
            switch ($this->reader->nodeType) {
             case (XMLREADER::ELEMENT):            
              
                $localName = str_replace("_colon_", ":", $this->reader->localName);     

                if ( strtolower(str_replace(":", "_", $localName)) == strtolower($element) ) {

                    if ($this->loop < $this->options['pointer']){
                      $this->loop++;                              
                      continue;
                    }                
                    
                    $xml = @$this->reader->readOuterXML();                  

                    break(2);                                
                }            
                break;
              default:
                // code ...
                break;
            }               
        }
      } catch (XmlImportException $e) {
        $xml = false;      
      }
    }  
    else
    {    
      $is_preprocess_enabled = apply_filters('is_xml_preprocess_enabled', true);    

      while ($xml = $this->reader->getNode()) {

          if ($this->loop < $this->options['pointer']){
            $this->loop++;                              
            continue;
          }          

          if ($is_preprocess_enabled)
          {
            // the & symbol is not valid in XML, so replace it with temporary word _ampersand_
            $xml = str_replace("&", "_ampersand_", $xml);
            $xml = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', str_replace(":", "_colon_", $xml));        
          }

          break;       
      }
    }

    return ( ! empty($xml) ) ? self::removeColonsFromRSS(preg_replace('%xmlns.*=\s*([\'"&quot;]).*\1%sU', '', $xml)) : false;

  }  

  public static function removeColonsFromRSS($feed) {
        
        $feed = str_replace("_colon_", ":", $feed);
        
        // pull out colons from start tags
        // (<\w+):(\w+>)
        $pattern = '/(<\w+):([\w+|\.|-]+[ |>]{1})/i';
        $replacement = '$1_$2';
        $feed = preg_replace($pattern, $replacement, $feed);
        // pull out colons from end tags
        // (<\/\w+):(\w+>)
        $pattern = '/(<\/\w+):([\w+|\.|-]+>)/i';
        $replacement = '$1_$2';
        $feed = preg_replace($pattern, $replacement, $feed);

        $is_replace_colons = apply_filters('wp_all_import_replace_colons_in_attribute_names', true);
        if ( $is_replace_colons ) {
            // pull out colons from attributes
            $pattern = '/(\s+\w+):(\w+[=]{1})/i';
            $replacement = '$1_$2';
            $feed = preg_replace($pattern, $replacement, $feed);
        }
        // pull colons from single element 
        // (<\w+):(\w+\/>)
        $pattern = '/(<\w+):([\w+|\.|-]+\/>)/i';
        $replacement = '$1_$2';
        $feed = preg_replace($pattern, $replacement, $feed);

        $is_preprocess_enabled = apply_filters('is_xml_preprocess_enabled', true);
        if ($is_preprocess_enabled)
        {
          // replace temporary word _ampersand_ back to & symbol
          $feed = str_replace("_ampersand_", "&", $feed);
        }

        // replace all standalone & symbols ( which is not in htmlentities e.q. &nbsp; and not wrapped in CDATA section ) to &amp;
        PMXI_Import_Record::preprocessXml($feed); 

        return $feed;

  }

}

class preprocessXml_filter extends php_user_filter {    

    function filter($in, $out, &$consumed, $closing)
    {
      while ($bucket = stream_bucket_make_writeable($in)) {        
        $is_preprocess_enabled = apply_filters('is_xml_preprocess_enabled', true);
        if ($is_preprocess_enabled)
        {
          // the & symbol is not valid in XML, so replace it with temporary word _ampersand_
          $bucket->data = str_replace("&", "_ampersand_", $bucket->data);
          $cleanXML = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $this->replace_colons($bucket->data));
          if ($cleanXML == NULL && preg_last_error() == PREG_BAD_UTF8_ERROR){
              $cleanXML = preg_replace('/[^\x09\x0a\x0d\x20-\xFF]+/', ' ', $this->replace_colons($bucket->data));
          }
          if ($cleanXML == NULL && preg_last_error() == PREG_BAD_UTF8_ERROR){
              if (function_exists('mb_ereg_replace')){
                  mb_regex_encoding('UTF-8');
                  $cleanXML = mb_ereg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $this->replace_colons($bucket->data));
              }
          }
          $bucket->data = empty($cleanXML) ? $this->replace_colons($bucket->data) : $cleanXML;
        }
        $consumed += $bucket->datalen;
        stream_bucket_append($out, $bucket);
      }      
      return PSFS_PASS_ON;
    }

    function replace_colons($data)
    {
      return str_replace(":", "_colon_", $data);
    }

}
