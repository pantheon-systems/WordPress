<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @package AST
 */

require_once dirname(__FILE__) . '/XmlImportAstExpression.php';

/**
 * Represents a literal node
 *
 * @abstract
 */
abstract class XmlImportAstLiteral extends XmlImportAstExpression
{
  /**
   * Vsalue of a node
   *
   * @var mixed
   */
  private $value;

  /**
   * Creates new instance of a token
   *
   * @param mixed $value
   */
  public function __construct($value)
  {
    $this->value = $value;
  }

  /**
   * Gets a value of a node
   *
   * @return mixed
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * String representation of an literal
   *
   * @return string
   */
  public function __toString()
  {
    return get_class($this) . ': "' . $this->getValue() . "\"";
  }
}
