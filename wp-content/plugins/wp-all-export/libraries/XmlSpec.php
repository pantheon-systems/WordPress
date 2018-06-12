<?php

require_once dirname(__FILE__) . '/XmlGoogleMerchants.php';

/**
 * Class XmlSpec
 */
final class XmlSpec
{
  /**
   * @var bool
   */
  public $xml = false;

  /**
   * XmlSpec constructor.
   * @param $spec
   */
  public function __construct( $spec, $export_id = false )
  {
      $this->xml = class_exists($spec) ? new $spec( $export_id ) : false;
  }


}