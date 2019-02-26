<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @package General
 */

/**
 * Represents a template token
 */
class XmlImportToken
{
  /**
   * Token Kind
   *
   * @var string
   */
  private $kind;

  /**
   * Token value
   *
   * @var mixed
   */
  private $value;

  /**
   * Token is a text
   */
  const KIND_TEXT = 'TEXT';

  /**
   * Token is a print statement
   */
  const KIND_PRINT = 'PRINT';

  /**
   * Token is a XPath literal
   */
  const KIND_XPATH = "XPATH";

  /**
   * Token is an IF keyword
   */
  const KIND_IF = "IF";

  /**
   * Token is an ENDIF keyword
   */
  const KIND_ENDIF = "ENDIF";

  /**
   * Token is an ELSEIF keyword
   */
  const KIND_ELSEIF = "ELSEIF";

  /**
   * Token is an ELSE keyword
   */
  const KIND_ELSE = "ELSE";

  /**
   * Token is a WITH keyword
   */
  const KIND_WITH = "WITH";

  /**
   * Token is an ENDWITH keyword
   */
  const KIND_ENDWITH = "ENDWITH";

  /**
   * Token is a FOREACH keyword
   */
  const KIND_FOREACH = "FOREACH";

  /**
   * Token is an ENDFOREACH keyword
   */
  const KIND_ENDFOREACH = "ENDFOREACH";

  /**
   * Token is a function name
   */
  const KIND_FUNCTION = "FUNCTION";

  /**
   * Token is a comma
   */
  const KIND_COMMA = "COMMA";

  /**
   * Token is an open brace
   */
  const KIND_OPEN = "OPEN";

  /**
   * Token is a close brace
   */
  const KIND_CLOSE = "CLOSE";

  /**
   * Token is a string literal
   */
  const KIND_STRING = "STRING";

  /**
   * Token is an integer number
   */
  const KIND_INT = "INT";

  /**
   * Token is a float number
   */
  const KIND_FLOAT = "FLOAT";
  
  /**
   * Token is a math on the price element
   */ 
  const KIND_MATH = "MATH";

  /**
   * Token is a spintax
   */ 
  const KIND_SPINTAX = "SPINTAX";
  
  /**
   * Token is a math on the price element
   */ 
  const KIND_OPERATION = "OPERATION";

  /**
   * Creates new instance of a token
   *
   * @param string $kind kind of a token
   * @param mixed $value value of a token
   */
  public function __construct($kind, $value = null)
  {
    $this->kind = $kind;
    $this->value = $value;
  }

  /**
   * Gets a kind of a token
   *
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }

  /**
   * Gets a value of a token
   *
   * @return mixed
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * String representation of a token
   *
   * @return string
   */
  public function __toString()
  {
    return '--> ' . $this->getKind() . (is_null($this->value) ? '' :  ': "' . $this->getValue() . '"');
  }
}