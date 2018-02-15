<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @package General
 */

require_once dirname(__FILE__) . '/ast/XmlImportAstSequence.php';
require_once dirname(__FILE__) . '/ast/XmlImportAstPrint.php';
require_once dirname(__FILE__) . '/ast/XmlImportAstText.php';
require_once dirname(__FILE__) . '/ast/XmlImportAstWith.php';
require_once dirname(__FILE__) . '/ast/XmlImportAstForeach.php';
require_once dirname(__FILE__) . '/ast/XmlImportAstIf.php';
require_once dirname(__FILE__) . '/ast/XmlImportAstMath.php';
require_once dirname(__FILE__) . '/ast/XmlImportAstSpintax.php';

require_once dirname(__FILE__) . '/ast/XmlImportAstXPath.php';
require_once dirname(__FILE__) . '/ast/XmlImportAstString.php';
require_once dirname(__FILE__) . '/ast/XmlImportAstInteger.php';
require_once dirname(__FILE__) . '/ast/XmlImportAstFloat.php';
require_once dirname(__FILE__) . '/ast/XmlImportAstFunction.php';

/**
 * Parses a list of nodes into AST (Abstract Syntax Tree)
 */
class XmlImportTemplateParser
{
  /**
   * List of tokens
   *
   * @var array
   */
  private $tokens;

  /**
   * Current index
   *
   * @var int
   */
  private $index = -1;

  /**
   * Stack that stores possible block endings
   *
   * @var array
   */
  private $clauseStack = array();

  /**
   * Stack of sequences
   *
   * @var array
   */
  private $sequenceStack = array();

  /**
   * Whether else subclause is allowed
   *
   * @var bool
   */
  private $elseAllowed = false;

  /**
   * Creates new instance
   *
   * @param array $tokens
   */
  public function __construct(array $tokens)
  {
    $this->tokens = $tokens;        
  }

  /**
   * Parses the list of tokens into AST tree
   *
   * @return XmlImportAstSequence
   */
  public function parse()
  {
    $result = $this->parseSequence();

    if (count($this->clauseStack) > 0)
      throw new XmlImportException("Unexpected end of template.");
    return $result;
  }

  /**
   * Parses sequence
   *
   * @return XmlImportAstSequence
   */
  private function parseSequence()
  {    
    if (($this->index + 1) == count($this->tokens))
      throw new XmlImportException("Reached end of template but statement sequence expected");
    $sequence = new XmlImportAstSequence();
    array_push($this->sequenceStack, $sequence);	
    if (count($this->clauseStack) == 0)
    {
      while (($this->index + 1) < count($this->tokens))
      {		
        $sequence->addStatement($this->parseStatement());
      }
    }
    else
    {
      while (($this->index + 1) < count($this->tokens))
      {
        if ($this->tokens[$this->index + 1]->getKind() == $this->clauseStack[count($this->clauseStack) - 1])
        {
          $this->index++;
          array_pop($this->clauseStack);
          break;
        }
        $statement = $this->parseStatement();
        if (is_null($statement)){
          array_pop($this->sequenceStack);
          return $sequence;
        }
        $sequence->addStatement($statement);
      }
    }    
    array_pop($this->sequenceStack);
	
    return $sequence;
  }

  /**
   * Parses statement
   *
   * @return XmlImportAstText
   */
  private function parseStatement()
  {
    if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_TEXT)
    {
      return new XmlImportAstText($this->tokens[++$this->index]->getValue());
    }
    elseif ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_PRINT)
    {
      $this->index++;
      return new XmlImportAstPrint($this->parseExpression());
    }
    elseif ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_WITH)
    {
      return $this->parseWith();
    }
    elseif ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_FOREACH)
    {
      return $this->parseForeach();
    }
  	elseif($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_MATH)
  	{				  	  	 
        return new XmlImportAstPrint($this->parseExpression());
  	}
    elseif($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_SPINTAX)
    {                
        return new XmlImportAstPrint($this->parseExpression());
    }
    elseif ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_IF)
    {
      return $this->parseIf();
    }
    elseif($this->clauseStack[count($this->clauseStack) - 1] == XmlImportToken::KIND_ENDIF &&
      in_array($this->tokens[$this->index + 1]->getKind(), array(XmlImportToken::KIND_ELSE, XmlImportToken::KIND_ELSEIF)))
    {      
      if ($this->elseAllowed)
      {
        if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_ELSE)
          $this->elseAllowed = false;
      }
      else
      {
        throw new XmlImportException("ELSEIF or ELSE is not allowed again after ELSE");
      }
      return null;
    }
    else
      throw new XmlImportException ("Unexpected token {$this->tokens[$this->index + 1]->getKind()}, statement was expected.");
  }

  /**
   * Parses expression
   *
   * @return XmlImportAstXPath
   */
  private function parseExpression()
  {
    if ($this->index + 1 == count($this->tokens))
      throw new XmlImportException("Reached end of template but expression was expected");
    if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_FUNCTION)
    {
      return $this->parseFunction();
    }
  	elseif($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_MATH)
  	{
  		return $this->parseMath();
  	}
    elseif($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_SPINTAX)
    {
      return $this->parseSpintax();
    }
    elseif ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_XPATH)
    {                        
      $xpath = new XmlImportAstXPath($this->tokens[++$this->index]->getValue());      
      $this->sequenceStack[count($this->sequenceStack) - 1]->addVariable($xpath);
      return $xpath;
    }
    elseif ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_STRING || $this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_OPERATION)
    {
      return new XmlImportAstString($this->tokens[++$this->index]->getValue());
    }
    elseif ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_INT)
    {
      return new XmlImportAstInteger($this->tokens[++$this->index]->getValue());
    }
    elseif ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_FLOAT)
    {
      return new XmlImportAstFloat($this->tokens[++$this->index]->getValue());
    }
    else
      throw new XmlImportException("Unexpected token " . $this->tokens[$this->index + 1]->getKind());
  }

  /**
   * Parses function
   *
   * @return XmlImportAstFunction
   */
  private function parseFunction()
  {
    $function = new XmlImportAstFunction($this->tokens[++$this->index]->getValue());    

    if ($this->tokens[$this->index + 1]->getKind() != XmlImportToken::KIND_OPEN)
      throw new XmlImportException ("Open brace expected instead of " . $this->tokens[$this->index + 1]->getKind());
    $this->index++;
    if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_CLOSE)
    {
      $this->index++;
      return $function;
    }
    else
    {
      while ($this->index < count($this->tokens) - 2)
      {
        $function->addArgument($this->parseExpression());
        if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_CLOSE)
        {
          $this->index++;
          return $function;
          break;
        }
        elseif ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_COMMA)
          $this->index++;
        else
          throw new XmlImportException("Comma or closing brace expected instead of " . $this->tokens[$this->index + 1]->getKind());
      }
      throw new XmlImportException("Unexpected end of {$function->getName()} function argument list");
    }
  }
  
  /**
   * Parses function
   *
   * @return XmlImportAstFunction
   */
  private function parseMath()
  {
    $math = new XmlImportAstMath($this->tokens[++$this->index]->getValue());
    if ($this->tokens[$this->index + 1]->getKind() != XmlImportToken::KIND_OPEN)
      throw new XmlImportException ("Open brace expected instead of " . $this->tokens[$this->index + 1]->getKind());
    $this->index++;
    if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_CLOSE)
    {
      $this->index++;
      return $math;
    }
    else
    {
      while ($this->index < count($this->tokens) - 2)
      {
        $math->addArgument($this->parseExpression());
        if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_CLOSE)
        {
          $this->index++;
          return $math;
          break;
        }
        elseif ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_COMMA)
          $this->index++;
        else
          throw new XmlImportException("Comma or closing brace expected instead of " . $this->tokens[$this->index + 1]->getKind());
      }
      throw new XmlImportException("Unexpected end of MATH argument list");
    }
  }

  /**
   * Parses function
   *
   * @return XmlImportSpintaxFunction
   */
  private function parseSpintax()
  {
    $spintax = new XmlImportAstSpintax($this->tokens[++$this->index]->getValue());
    if ($this->tokens[$this->index + 1]->getKind() != XmlImportToken::KIND_OPEN)
      throw new XmlImportException ("Open brace expected instead of " . $this->tokens[$this->index + 1]->getKind());
    $this->index++;
    if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_CLOSE)
    {
      $this->index++;
      return $spintax;
    }
    else
    {      
      while ($this->index < count($this->tokens) - 2)
      {        
        $spintax->addArgument($this->parseExpression());
        if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_CLOSE)
        {
          $this->index++;
          return $spintax;
          break;
        }
        elseif ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_COMMA)
          $this->index++;        
        else
          throw new XmlImportException("Comma or closing brace expected instead of " . $this->tokens[$this->index + 1]->getKind());
      }
      throw new XmlImportException("Unexpected end of {$function->getName()} function argument list");
    }
  }

  /**
   * Parses clause that uses XPath and returns XPath
   *
   * @return XmlImportAstXPath
   */
  private function parseXPathDependant()
  {
    $this->index++;

    if ($this->index + 1 == count($this->tokens))
      throw new XmlImportException("Reached end of template but expression was expected");
      
    if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_OPEN)
      $this->index++;
    else
      throw new XmlImportException("Open brace expected instead of " . $this->tokens[$this->index + 1]->getKind());
    if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_XPATH)
    {
      $xpath = new XmlImportAstXPath($this->tokens[++$this->index]->getValue());
      $this->sequenceStack[count($this->sequenceStack) - 1]->addVariable($xpath);      
    }
    else
      throw new XmlImportException("XPath expression expected instead of " . $this->tokens[$this->index + 1]->getKind());
    if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_CLOSE)
      $this->index++;
    else
      throw new XmlImportException("Close brace expected instead of " . $this->tokens[$this->index + 1]->getKind());
    return $xpath;
  }

  /**
   * Parses WITH clause
   *
   * @return XmlImportAstWith
   */
  private function parseWith()
  {
    $xpath = $this->parseXPathDependant();
    //store sequence exit
    array_push($this->clauseStack, XmlImportToken::KIND_ENDWITH);
    return new XmlImportAstWith($xpath, $this->parseSequence());
  }

  /**
   * Parses FOREACH clause
   *
   * @return XmlImportAstForeach
   */
  private function parseForeach()
  {
    $xpath = $this->parseXPathDependant();
    
    array_push($this->clauseStack, XmlImportToken::KIND_ENDFOREACH);
    return new XmlImportAstForeach($xpath, $this->parseSequence());
  }  
  
  /**
   * Parses IF clause
   *
   * @return XmlImportAstIf
   */
  private function parseIf()
  {
    $this->index++;
    $this->elseAllowed = true;
    array_push($this->clauseStack, XmlImportToken::KIND_ENDIF);

    if ($this->index + 1 == count($this->tokens))
      throw new XmlImportException("Reached end of template but expression was expected");
      
    if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_OPEN)
      $this->index++;
    else
      throw new XmlImportException("Open brace expected instead of " . $this->tokens[$this->index + 1]->getKind());

    $if = new XmlImportAstIf($this->parseExpression());
    if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_CLOSE)
      $this->index++;
    else
      throw new XmlImportException("Close brace expected instead of " . $this->tokens[$this->index + 1]->getKind());            
    $if->addIfBody($this->parseSequence());

    if ($this->index + 1 != count($this->tokens))
    {
      while ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_ELSEIF)
      {
        $this->index++;
        if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_OPEN)
          $this->index++;
        else
          throw new XmlImportException("Open brace expected instead of " . $this->tokens[$this->index + 1]->getKind());
        $condition = $this->parseExpression();
        if ($this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_CLOSE)
          $this->index++;
        else
          throw new XmlImportException("Close brace expected instead of " . $this->tokens[$this->index + 1]->getKind());

        $elseif = new XmlImportAstElseif($condition, $this->parseSequence());
        $if->addElseif($elseif);
        if ($this->index + 1 == count($this->tokens))
          break;
      }     
      if ($this->index + 1 < count($this->tokens) && $this->tokens[$this->index + 1]->getKind() == XmlImportToken::KIND_ELSE)
      {
        $this->index++;            
        $if->addElseBody($this->parseSequence());
      }
    }	
    
    return $if;
  }
}