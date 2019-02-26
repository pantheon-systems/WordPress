<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @package General
 */

require_once dirname(__FILE__) . '/XmlImportToken.php';
require_once dirname(__FILE__) . '/XmlImportException.php';

/**
 * Used to scan string into a list of tokens
 */
final class XmlImportTemplateScanner
{
  /**
   * Language keywords
   *
   * @var array
   */
  private $keywords = array(
    'IF',
    'ELSEIF',
    'ELSE',
    'ENDIF',
    'FOREACH',
    'ENDFOREACH',
    'WITH',
    'ENDWITH',
	  'MATH',
    'SPINTAX'
  );

  /**
   * Parsing text
   */
  const STATE_TEXT = 'STATE_TEXT';

  /**
   * Parsing XPath
   */
  const STATE_XPATH = 'STATE_XPATH';

  /**
   * Parsing Language
   */
  const STATE_LANG = 'STATE_LANG';

  /**
   * Whether it is lang block start
   *
   * @var bool
   */
  private $isLangBegin = false;

  private $previous_ch = false;

  /**
   * Current parsing state
   *
   * @var string
   */
  private $currentState = XmlImportTemplateScanner::STATE_TEXT;

  /**
   * Scans template from XmlImportReaderInterface and returns the list of tokens
   *
   * @param XmlImportReaderInterface $input
   * @return array
   */
  public function scan(XmlImportReaderInterface $input)
  {
    $results = array();
    
    while (($ch = $input->peek()) !== false)
    {
      switch ($this->currentState)
      {
        case XmlImportTemplateScanner::STATE_TEXT:

          if ($ch == '[')
          {                        
            $this->previous_ch = '[';
            $this->currentState = XmlImportTemplateScanner::STATE_LANG;
            $this->isLangBegin = true;
            //omit [
            $input->read();
          }
          elseif ($ch == '{')
          {
            $this->currentState = XmlImportTemplateScanner::STATE_XPATH;
            //omit {
            $input->read();
          }
          else
          {
            $results[] = $this->scanText($input);
          }
          break;
        case XmlImportTemplateScanner::STATE_XPATH:
          $results = array_merge($results, $this->scanXPath($input, false));
          break;
        case XmlImportTemplateScanner::STATE_LANG:
          $ch = $input->peek();
		 
          if (preg_match('/\s/', $ch))
          {
            //omit space
            $input->read();
          }
          elseif (preg_match('/[_a-z]/i', $ch))
          {
            $result = $this->scanName($input);
            if (is_array($result))
              $results = array_merge($results, $result);
            else
              $results[] = $result;
          }		  
          elseif (preg_match('/(\d)/', $ch))
          {
            $result = $this->scanNumber($input);
            if (is_array($result))
              $results = array_merge($results, $result);
            else
              $results[] = $result;
          }		      
          elseif ($ch == '"')
          {
            //omit "
            $input->read();
            $result = $this->scanString($input);
            if (is_array($result))
              $results = array_merge($results, $result);
            else
              $results[] = $result;
          }
          elseif ($ch == '{')
          {
            $input->read();
            $result = $this->scanXPath($input);
            if (is_array($result))
              $results = array_merge($results, $result);
            else
              $results[] = $result;
          }
          elseif ($ch == '(')
          {
            $this->isLangBegin = false;            
            $input->read();
            $results[] = new XmlImportToken(XmlImportToken::KIND_OPEN);
          }
          elseif ($ch == ')')
          {
            $this->isLangBegin = false;
            $input->read();
            $results[] = new XmlImportToken(XmlImportToken::KIND_CLOSE);
          }
          elseif ($ch == ',')
          {
            $this->isLangBegin = false;
            $input->read();
            $results[] = new XmlImportToken(XmlImportToken::KIND_COMMA);
          }
          elseif ($ch == "]")
          {
            $this->isLangBegin = false;
            $this->currentState = XmlImportTemplateScanner::STATE_TEXT;
            //omit ]
            $input->read();
          }
          elseif ($ch == "|" || $ch == "+" || $ch == "-" || $ch == "*" || $ch == "/")
          {            
            $results[] = $this->scanText($input);           
          }
          else{
            if ($ch == "'"){
              throw new XmlImportException("Unexpected symbol ' - When using shortcodes/PHP functions, use double quotes \", not single quotes '");
            }
            else{
              throw new XmlImportException("Unexpected symbol '$ch'");
            }
          }            
		
          break;
      }      
    }
	
    return $results;
  }

  /**
   * Scans text
   *
   * @param XmlImportReaderInterface $input
   * @return XmlImportToken
   */
  private function scanText($input)
  {
    $accum = $input->read();    
    while (($ch = $input->peek()) !== false)
    {            
      if ($ch == '{' && $accum[strlen($accum) - 1] != "\\")
      {
        $this->currentState = XmlImportTemplateScanner::STATE_XPATH;
        //omit {
        $input->read();
        break;
      }
      elseif ($ch == '[' && $accum[strlen($accum) - 1] != "\\")
      {                   
        
        $this->currentState = XmlImportTemplateScanner::STATE_LANG;
        $this->isLangBegin = true;
        //omit [
        $input->read();
        break;
      }
      elseif ($accum == '/' && $this->previous_ch == "["){        
        $accum = "[" . $accum . $input->read();
        //$this->previous_ch = false;
      }
      else
        $accum .= $input->read();
      $this->previous_ch = $ch;
    }
    $accum = str_replace(array("\\[", "\\{"), array('[', '{'), $accum);
    return new XmlImportToken(XmlImportToken::KIND_TEXT, $accum);
  }    

  /**
   * Scans XPath
   *
   * @param XmlImportReaderInterface $input
   * @param bool $insideLang
   * @return XmlImportToken
   */
  private function scanXPath($input, $insideLang = true)
  {
    $accum = '';
    while(($ch = $input->peek()) !== false)
    {
      if ($ch == '}' && (strlen($accum) == 0 || $accum[strlen($accum) - 1] != "\\"))
      {
        //skip }
        $input->read();
        $accum = str_replace("\\}", '}', $accum);
        if ($insideLang)
        {
          if ($this->isLangBegin)
          {
            return array(new XmlImportToken(XmlImportToken::KIND_PRINT), new XmlImportToken(XmlImportToken::KIND_XPATH, $accum));
          }
          else
            return new XmlImportToken(XmlImportToken::KIND_XPATH, $accum);
        }
        else
        {
          $this->currentState = XmlImportTemplateScanner::STATE_TEXT;

          return array(new XmlImportToken(XmlImportToken::KIND_PRINT), new XmlImportToken(XmlImportToken::KIND_XPATH, $accum));
        }
      }
      else
        $accum .= $input->read();
    }
    throw new XmlImportException('Unexpected end of XPath expression \'' . $accum . '\'');
  }

  /**
   * Scans name
   *
   * @param XmlImportReaderInterface $input
   * @return XmlImportToken
   */
  private function scanName(XmlImportReaderInterface $input)
  {
    $accum = $input->read();  

    $is_function = false;
    while (preg_match('%[/_a-z0-9=\s\-"]%i', $input->peek(), $matches))
    {                         
        $accum .= $input->read();
        if ($input->peek() === false)
          throw new XmlImportException("Unexpected end of function or keyword name \"$accum\"");
    }      

    $ch = $input->peek();

    if ($ch == "(") $is_function = true;
    
    if (in_array(strtoupper(trim($accum)), $this->keywords))
    {
      return new XmlImportToken(strtoupper($accum));
    }
    else
    {
      
      if (strpos($accum, "=") !== false or (shortcode_exists($accum) and !$is_function) or ! $is_function) {                
        $this->isLangBegin = false;
        return new XmlImportToken(XmlImportToken::KIND_TEXT, '[' . trim(trim($accum, "["), "]") . ']');            
              
      } 

      if ($this->isLangBegin)
      {
        $this->isLangBegin = false;                        
        if ( function_exists($accum) or in_array($accum, array('array')))
          return array(new XmlImportToken(XmlImportToken::KIND_PRINT), new XmlImportToken(XmlImportToken::KIND_FUNCTION, $accum));        
        else          
          throw new XmlImportException("Call to undefined function \"$accum\"");
        
      }
      else{
        if ( function_exists($accum) or in_array($accum, array('array')))
          return new XmlImportToken(XmlImportToken::KIND_FUNCTION, $accum);              
        else          
          throw new XmlImportException("Call to undefined function \"$accum\"");
      }
    }
  }

  /**
   * Scans string literal
   *
   * @param XmlImportReaderInterface $input
   * @return XmlImportToken
   */
  private function scanString(XmlImportReaderInterface $input)
  {
    $accum = '';
    while(($ch = $input->peek()) !== false)
    {
      if ($ch == '"' && (strlen($accum) == 0 || $accum[strlen($accum) - 1] != "\\"))
      {

        //skip "
        $input->read();
        $accum = str_replace("\\\"", '"', $accum);
        if ($this->isLangBegin)
        {
          $this->isLangBegin = false;
          return array(new XmlImportToken(XmlImportToken::KIND_PRINT), new XmlImportToken(XmlImportToken::KIND_STRING, $accum));
        }
        else
        {
          return new XmlImportToken(XmlImportToken::KIND_STRING, $accum);
        }
      }
      else
        $accum .= $input->read();
    }
    throw new XmlImportException('Unexpected end of string literal "' . $accum . '"');
  }

  /**
   * Scans number
   *
   * @param XmlImportReaderInterface $input
   * @return XmlImportToken
   */
  private function scanNumber(XmlImportReaderInterface $input)
  {
    $isInt = true;
    $accum = $this->scanInt($input);
    if ($input->peek() == '.')
    {
      $isInt = false;
      $accum .= $input->read();
      $accum .= $this->scanNumberFrac($input);
    }
    if (strtolower($input->peek()) == 'e' )
    {
      $isInt = false;
      $accum .= $input->read();
      $accum .= $this->scanInt($input);
    }
    if ($isInt)
    {
      if ($this->isLangBegin)
      {
        $this->isLangBegin = false;
        return array(new XmlImportToken(XmlImportToken::KIND_PRINT), new XmlImportToken(XmlImportToken::KIND_INT, intval($accum)));
      }
      else
      {
        return new XmlImportToken(XmlImportToken::KIND_INT, intval($accum));
      }
    }
    else
    {
      if ($this->isLangBegin)
      {
        $this->isLangBegin = false;
        return array(new XmlImportToken(XmlImportToken::KIND_PRINT), new XmlImportToken(XmlImportToken::KIND_FLOAT, floatval($accum)));
      }
      else
      {
        return new XmlImportToken(XmlImportToken::KIND_FLOAT, floatval($accum));
      }
    }
  }

  /**
   * Scans integer number
   *
   * @param XmlImportReaderInterface $input
   * @return string
   */
  private function scanInt(XmlImportReaderInterface $input)
  {
    if (preg_match('/(\d)/', $input->peek()))
    {
      $accum = $input->read();
      if ($accum == '-' && !preg_match('/\d/', $input->peek()))
        throw new XmlImportException("Expected digit after a minus");
      while (preg_match('/\d/', $input->peek()))
      {
        $accum .= $input->read();
      }
      return $accum;
    }
    else
      throw new XmlImportException("digit or '-' expected in a number");
  }

  /**
   * Scans fraction part of a number
   *
   * @param XmlImportReaderInterface $input
   * @return string
   */
  private function scanNumberFrac(XmlImportReaderInterface $input)
  {
    $accum = '';
    while (preg_match('/\d/', $input->peek()))
    {
      $accum .= $input->read();
    }
    if (strlen($accum) == 0)
      throw new XmlImportException("Digits are expected after a '.'");
    return $accum;
  }
}