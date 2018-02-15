<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @package AST
 */

require_once dirname(__FILE__) . '/XmlImportAstStatement.php';

/**
 * Represents statement sequence
 */
class XmlImportAstSequence extends XmlImportAstStatement
{

  /**
   * Sequence instance number
   *
   * @var int
   */
  private static $sequenceInstanceNumber = 0;

  /**
   * Current sequence number
   *
   * @var int
   */
  private $sequenceNumber;

  /**
   * Current variable number
   *
   * @var int 
   */
  private $varNumber = 0;
  
  /**
   * List of statements
   *
   * @var array
   */
  private $statements = array();

  /**
   * Variable definitions
   *
   * @var array
   */
  private $variableDefinitions = array();

  /**
   * Variables
   *
   * @var array
   */
  private $variables = array();

  /**
   * Creates new instance
   */
  public function __construct()
  {
    $this->sequenceNumber = self::$sequenceInstanceNumber++;
  }

  /**
   * Adds statement to a sequence
   *
   * @param XmlImportAstStatement $statement
   */
  public function addStatement(XmlImportAstStatement $statement)
  {
    $this->statements[] = $statement;
  }

  /**
   * Gets the list of statements
   *
   * @return array
   */
  public function getStatements()
  {
    return $this->statements;
  }

  /**
   * Adds variable to a sequence
   *
   * @param XmlImportAstXPath $xpath
   */
  public function addVariable(XmlImportAstXPath $xpath)
  {
    if (!array_key_exists($xpath->getValue(), $this->variables))
    {
      $var = '$v' . $this->sequenceNumber . '_' . $this->varNumber++;
      $value =     $escapedValue = strtr($xpath->getValue(), array(
    	"\n" => "\\n",
    	"\t" => "\\t",
    	"\r" => "\\r",
    	"$" => "\\$",
    	"\"" => "\\\"",
    	"\\" => "\\\\",
      ));
      $result = $var . ' = {{XML}}->xpath("' . $value . '");';
      $this->variables[$xpath->getValue()] = $var;
      $this->variableDefinitions[] = $result;
    }
  }

  /**
   * Gets variable definitions
   *
   * @return array
   */
  public function getVariableDefinitions()
  {
    return $this->variableDefinitions;
  }

  /**
   * Gets variables
   *
   * @return array
   */
  public function getVariables()
  {
    return $this->variables;
  }

  /**
   * Returns the number of current instance
   *
   * @return int
   */
  public function getSequenceNumber()
  {
    return $this->sequenceNumber;
  }

  /**
   * String representation of a sequence node
   *
   * @return string
   */
  public function __toString()
  {
    $result = "--> begin " . get_class($this) . "\n";
    foreach ($this->getStatements() as $statement)
    {
      $array = explode("\n", $statement);
      for ($i = 0; $i < count($array); $i++)
      {
        $array[$i] = '  ' . $array[$i];
      }
      $result .= implode("\n", $array) . "\n";
    }

    $result .= "--> end " . get_class($this);

    return $result;
  }
}