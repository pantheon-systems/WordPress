<?php
/**
 * Chunk
 * 
 * Reads a large file in as chunks for easier parsing.
 * 
 * 
 * @package default
 * @author Max Tsiplyakov
 */
class PMXE_Chunk {
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
  public function __construct($file, $options = array()) {
    
    // merge the options together
    $this->options = array_merge($this->options, (is_array($options) ? $options : array()));                       

    $this->options['chunkSize'] *= 32;      

    // set the filename
    $this->file = $file;   

    $is_html = false;
    $f = @fopen($file, "rb");       
    while (!@feof($f)) {
      $chunk = @fread($f, 1024);         
      if (strpos($chunk, "<!DOCTYPE") === 0) $is_html = true;
      break;      
    }  
    @fclose($f);

    if ($is_html) return;

    if (empty($this->options['element']) or $this->options['get_cloud'])
    {      
      if (function_exists('stream_filter_register') and $this->options['filter']){
        stream_filter_register('preprocessxml', 'wpae_preprocessXml_filter');
        $path = 'php://filter/read=preprocessxml/resource=' . $this->file;   
      }
      else $path = $this->file;

      $reader = new XMLReader();
      $reader->open($path);
      $reader->setParserProperty(XMLReader::VALIDATE, false);
      while ( @$reader->read()) {
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
     
      if ( ! empty($this->cloud) and empty($this->options['element']) ){
        
        arsort($this->cloud);           

        $main_elements = array('node', 'product', 'job', 'deal', 'entry', 'item', 'property', 'listing', 'hotel', 'record', 'article', 'post', 'book');

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
      }
    }                           

    if (function_exists('stream_filter_register') and $this->options['filter']){
      stream_filter_register('preprocessxml', 'wpae_preprocessXml_filter');
      $path = 'php://filter/read=preprocessxml/resource=' . $this->file;        
    }
    else $path = $this->file;

    $this->reader = new XMLReader();        
    @$this->reader->open($path);
    @$this->reader->setParserProperty(XMLReader::VALIDATE, false);
    

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
    
    return ( ! empty($xml) ) ? $this->removeColonsFromRSS(preg_replace('%xmlns.*=\s*([\'"&quot;]).*\1%sU', '', $xml)) : false;

  }  

  function removeColonsFromRSS($feed) {
                  
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
        // pull out colons from attributes
        $pattern = '/(\s+\w+):(\w+[=]{1})/i';
        $replacement = '$1_$2';
        $feed = preg_replace($pattern, $replacement, $feed);
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

        return $feed;

  }

}

class wpae_preprocessXml_filter extends php_user_filter {    

    function filter($in, $out, &$consumed, $closing)
    {
      while ($bucket = stream_bucket_make_writeable($in)) {        
        $is_preprocess_enabled = apply_filters('is_xml_preprocess_enabled', true);
        if ($is_preprocess_enabled)
        {
          // the & symbol is not valid in XML, so replace it with temporary word _ampersand_
          $bucket->data = str_replace("&", "_ampersand_", $bucket->data);
          $bucket->data = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $this->replace_colons($bucket->data));               
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
