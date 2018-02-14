<?php
/**
 * @author Olexandr Zanichkovsky <olexandr.zanichkovsky@zophiatech.com>
 * @package General
 */

/**
 * Interface that allows to either peek or read symbol from class that implements it
 */
interface XmlImportReaderInterface
{
  /**
   * Peeks a symbol
   */
  public function peek();

  /**
   * Reads a symbol
   */
  public function read();
}
