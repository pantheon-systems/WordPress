<?php

/**
 * Class XmlGoogleMerchants
 */
final class XmlGoogleMerchants
{
  /**
   * @var bool
   */
  private $export_id = false;
  /**
   * @var array
   */
  private $add_data  = array();

  /**
   * @var array
   *
   * https://support.google.com/merchants/answer/160589?hl=en
   *
   */
  private $required_fields = array(
    array(
      'name'  => 'g:id',
      'type'  => 'ID',
//      'label' => 'id'
    ),
    array(
      'name'  => 'title',
      'type'  => 'Title',
//      'label' => 'title'
    ),
    array(
      'name'  => 'link',
      'type'  => 'Permalink',
//      'label' => 'permalink'
    ),
    array(
      'name'  => 'description',
      'type'  => 'Content',
//      'label' => 'content'
    ),
    array(
      'name'  => 'g:image_link',
      'type'  => 'Image Url',
//      'options' => '{"is_export_featured":true,"is_export_attached":false,"image_separator":"|"}'
    ),
    array(
      'name'  => 'g:price',
      'type'  => 'Regular Price',
    ),
//    array(
//      'name'  => 'g:condition',
//      'type'  => 'woo',
//      'label' => '_regular_price'
//    )
  );

  /**
   * XmlGoogleMerchants constructor.
   * @param $id
   * @param array $additional_data
   */
  public function __construct($id, $additional_data = array())
  {
      $this->export_id = $id;
      $this->add_data  = $additional_data;

      if ( ! empty($this->export_id))
      {
          add_filter('wp_all_export_xml_header', array( &$this, 'wpae_xml_header'), 10, 2);
          add_filter('wp_all_export_additional_data', array( &$this, 'wpae_additional_data'), 10, 3);
          add_filter('wp_all_export_xml_footer', array( &$this, 'wpae_xml_footer'), 10, 2);
          add_filter('wp_all_export_main_xml_tag', array( &$this, 'wpae_main_xml_tag'), 10, 2);
          add_filter('wp_all_export_record_xml_tag', array( &$this, 'wpae_record_xml_tag'), 10, 2);
      }
  }

  /**
   * @param $header
   * @param $export_id
   * @return string
   */
  public function wpae_xml_header($header, $export_id)
  {
      if ( $export_id == $this->export_id )
      {
          $header .= "\n<rss version=\"2.0\" xmlns:g=\"http://base.google.com/ns/1.0\">";
      }
      return $header;
  }

  /**
   * @param $add_data
   * @param $options
   * @param $export_id
   * @return array
   */
  public function wpae_additional_data($add_data, $options, $export_id)
  {
      if ( $export_id == $this->export_id && ! empty($this->add_data))
      {
          $add_data = array_merge($add_data, $this->add_data);
      }
      return $add_data;
  }

  /**
   * @param $footer
   * @param $export_id
   * @return string
   */
  public function wpae_xml_footer($footer, $export_id)
  {
      if ( $export_id == $this->export_id )
      {
          $footer = "</rss>";
      }
      return $footer;
  }

  /**
   * @param $tag
   * @param $export_id
   * @return string
   */
  public function wpae_main_xml_tag($tag, $export_id )
  {
      return ( $export_id == $this->export_id ) ? 'channel' : $tag;
  }

  /**
   * @param $tag
   * @param $export_id
   * @return string
   */
  public function wpae_record_xml_tag($tag, $export_id )
  {
      return ( $export_id == $this->export_id ) ? 'item' : $tag;
  }

  /**
   *
   */
  public function get_required_fields()
  {
      $xml_template = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<rss version=\"2.0\" xmlns:g=\"http://base.google.com/ns/1.0\">\n";
        $xml_template .= "\t<chanel>";
            $xml_template .= "\n\t\t<!-- BEGIN LOOP -->";
            $xml_template .= "\n\t\t<item>";
            foreach ($this->required_fields as $field){
                $xml_template .= "\n\t\t\t<" . $field['name'] . ">{" . $field['type'] . "}</" . $field['name'] . ">";
            }
            $xml_template .= "\n\t\t</item>";
            $xml_template .= "\n\t\t<!-- END LOOP -->";
        $xml_template .= "\n\t</chanel>";
      $xml_template .= "\n</rss>";
      return $xml_template;
  }
}