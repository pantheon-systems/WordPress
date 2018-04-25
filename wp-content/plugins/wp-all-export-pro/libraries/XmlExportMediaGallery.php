<?php

final class XmlExportMediaGallery
{
	/**
	 * Singletone instance
	 * @var XmlExportMediaGallery
	 */
	protected static $instance;

	/**
	 * Return singletone instance
	 * @return XmlExportMediaGallery
	 */
	static public function getInstance( $pid ) {		
		if ( self::$instance == NULL or self::$pid != $pid ) {
			self::$instance = new self( $pid );
		}			
		return self::$instance;
	}

	public static $pid 		     = false;	

	public static $attachments    = array();	
	public static $images        = array();
	public static $images_ids    = array();

	public static $featured_image = false;

	private function __construct( $pid )
	{			
		self::$pid 		      = $pid;				
	}	

	public static function init( $type = 'attachments', $options = false )
	{
		self::$attachments    = array();
		self::$images         = array();
		self::$images_ids     = array();	
		self::$featured_image = false;	

		switch ($type) 
		{
			case 'attachments':					

				$attachments = get_posts( array(
					'post_type' => 'attachment',
					'posts_per_page' => -1,
					'post_parent' => self::$pid,
				) );

				if ( ! empty($attachments)):

					foreach ($attachments as $attachment) 
					{
						if ( ! wp_attachment_is_image( $attachment->ID ) ) 
						{
							self::$attachments[]    = $attachment;							
						}																
					}

				endif;

				break;
			
			case 'images':

				// prepare featured image data
				if ( empty(self::$featured_image) )
				{
					$_featured_image_id = self::get_meta(self::$pid, '_thumbnail_id', true);

                    if (empty($_featured_image_id)){
                        $_featured_image_id = self::get_meta(self::$pid, 'thumbnail_id', true);
                    }

					if ( ! empty($_featured_image_id) )
					{
						$_featured_image = get_post($_featured_image_id);
						
						if ($_featured_image)
						{							
							self::$featured_image = $_featured_image;															
						}				
					}					
				}

				if ( ! empty(self::$featured_image) and ( empty($options) or ! empty($options['is_export_featured']) ) and ! in_array(self::$featured_image->ID, self::$images_ids)) 
				{
					self::$images_ids[] = self::$featured_image->ID;
					self::$images[]     = self::$featured_image;
				}

				// prepare attached images data
				if ( empty($options) or ! empty($options['is_export_attached']) )
				{

					$_gallery = self::get_meta(self::$pid, '_product_image_gallery', true);

					if ( ! empty($_gallery))
					{
						$gallery = explode(',', $_gallery);

						if ( ! empty($gallery) and is_array($gallery))
						{
							foreach ($gallery as $aid) 
							{
								if ( ! empty($aid) and ! in_array($aid, self::$images_ids) and ( empty(self::$featured_image) or self::$featured_image->ID != $aid ) )
								{
									$_image = get_post($aid);
									if ($_image)
									{
										self::$images_ids[] = $aid;
										self::$images[]     = $_image;
									}
								} 
							}
						}
					}
					
					$images = get_posts( array(
						'post_type' => 'attachment',
						'posts_per_page' => -1,
						'post_parent' => self::$pid,
					) );

					if ( ! empty($images)):

						foreach ($images as $image) 
						{
							if ( wp_attachment_is_image( $image->ID ) and ! in_array($image->ID, self::$images_ids) and ( empty(self::$featured_image) or self::$featured_image->ID != $image->ID ) )
							{
								self::$images[]     = $image;	
								self::$images_ids[] = $image->ID;						
							}																
						}

					endif;										
				}				

				break;

			default:
				# code...
				break;
		}
	}

	public static function get_attachments (  $field = 'attachment_url' )
	{
		self::init('attachments');

		$data = array();

		if ( ! empty(self::$attachments) )
		{
			foreach (self::$attachments as $attachment) 
			{
				$v = self::get_media( str_replace("attachment_", "", $field), $attachment );

				$data[] = $v;
			}
		}

        return $data;
	}

	public static function get_images(  $field = 'image_url', $options = false )
	{
		self::init('images', $options);

		$data = array();

		if ( ! empty(self::$images) )
		{
			foreach (self::$images as $image) 
			{
				$v = self::get_media( str_replace("image_", "", $field), $image );

				$data[] = $v;
			}
		}

		return $data;
	}

	private static function get_media( $field = 'url', $attachment = false )
	{
		if ( empty($attachment)) return false;

		switch ($field) 
		{
			case 'media':
			case 'attachments':
			case 'url':
				return wp_get_attachment_url( $attachment->ID );
				break;
			case 'filename':
				return basename(wp_get_attachment_url( $attachment->ID ));
				break;
			case 'path':
				return get_attached_file( $attachment->ID );
				break;
			case 'id':
				return $attachment->ID;
				break;
			case 'title':
				return $attachment->post_title;
				break;
			case 'caption':
				return $attachment->post_excerpt;
				break;
			case 'description':
				return $attachment->post_content;
				break;
			case 'alt':
				return self::get_meta($attachment->ID, '_wp_attachment_image_alt', true);
				break;
			
			default:
				# code...
				break;
		}

		return false;
	}

	public static $is_include_feature_meta = null;
	public static $is_include_gallery_meta = null;

	public static function prepare_import_template( $exportOptions, &$templateOptions, $element_name, $ID)
	{
		$options = $exportOptions;

		$is_xml_template = $options['export_to'] == 'xml';

		$implode_delimiter = XmlExportEngine::$implode;

		$element_type = $options['cc_type'][$ID];

		if ( is_null(self::$is_include_feature_meta) || is_null(self::$is_include_gallery_meta)) {
			self::$is_include_feature_meta = false;
			self::$is_include_gallery_meta = false;

			foreach ($options['ids'] as $elID => $value) 
			{
				if ( 'image_url' == $options['cc_type'][$elID] ) {
					$field_options = json_decode($options['cc_options'][$elID], true);
					if ( ! empty($field_options['is_export_featured']) )  self::$is_include_feature_meta = true;
					if ( ! empty($field_options['is_export_attached']) )  self::$is_include_gallery_meta = true;
				}
			}
		}		

		switch ($element_type) 
		{
			case 'media':					
			case 'image_url':				
				$field_options = json_decode($options['cc_options'][$ID], true);
				$templateOptions['is_update_images'] = 1;
				$templateOptions['update_images_logic'] = 'add_new';
				$templateOptions['download_featured_delim'] = (empty($field_options['image_separator'])) ? "|" : $field_options['image_separator'];
				if ( empty($templateOptions['download_featured_image'])) {
					$templateOptions['download_featured_image'] = '{'. $element_name .'[1]}';
				}
				else {
					$templateOptions['download_featured_image'] .= $templateOptions['download_featured_delim'] . '{'. $element_name .'[1]}';
				}
				break;
			case 'image_title':
				$field_options = json_decode($options['cc_options'][$ID], true);
				if ( (int) $field_options['is_export_featured'] == (int) self::$is_include_feature_meta && (int) $field_options['is_export_attached'] == (int) self::$is_include_gallery_meta )
				{					
					$templateOptions['set_image_meta_title'] = 1;				
					$templateOptions['image_meta_title_delim'] = (empty($field_options['image_separator'])) ? "|" : $field_options['image_separator'];
					if ( empty($templateOptions['image_meta_title'])) {
						$templateOptions['image_meta_title'] = '{'. $element_name .'[1]}';
					}
					else {
						$templateOptions['image_meta_title'] .= $templateOptions['image_meta_title_delim'] . '{'. $element_name .'[1]}';
					}
				}				
				break;
			case 'image_caption':
				$field_options = json_decode($options['cc_options'][$ID], true);
				if ( (int) $field_options['is_export_featured'] == (int) self::$is_include_feature_meta && (int) $field_options['is_export_attached'] == (int) self::$is_include_gallery_meta )
				{
					$templateOptions['set_image_meta_caption'] = 1;				
					$templateOptions['image_meta_caption_delim'] = (empty($field_options['image_separator'])) ? "|" : $field_options['image_separator'];
					if ( empty($templateOptions['image_meta_caption'])) {
						$templateOptions['image_meta_caption'] = '{'. $element_name .'[1]}';
					}
					else {
						$templateOptions['image_meta_caption'] .= $templateOptions['image_meta_caption_delim'] . '{'. $element_name .'[1]}';
					}
				}
				break;
			case 'image_description':
				$field_options = json_decode($options['cc_options'][$ID], true);
				if ( (int) $field_options['is_export_featured'] == (int) self::$is_include_feature_meta && (int) $field_options['is_export_attached'] == (int) self::$is_include_gallery_meta )
				{
					$templateOptions['set_image_meta_description'] = 1;				
					$templateOptions['image_meta_description_delim'] = (empty($field_options['image_separator'])) ? "|" : $field_options['image_separator'];
					if ( empty($templateOptions['image_meta_description'])) {
						$templateOptions['image_meta_description'] = '{'. $element_name .'[1]}';
					}
					else {
						$templateOptions['image_meta_description'] .= $templateOptions['image_meta_description_delim'] . '{'. $element_name .'[1]}';
					}
				}
				break;
			case 'image_alt':
				$field_options = json_decode($options['cc_options'][$ID], true);
				if ( (int) $field_options['is_export_featured'] == (int) self::$is_include_feature_meta && (int) $field_options['is_export_attached'] == (int) self::$is_include_gallery_meta )
				{
					$templateOptions['set_image_meta_alt'] = 1;			
					$templateOptions['image_meta_alt_delim'] = (empty($field_options['image_separator'])) ? "|" : $field_options['image_separator'];
					if ( empty($templateOptions['image_meta_alt'])) {
						$templateOptions['image_meta_alt'] = '{'. $element_name .'[1]}';
					}
					else {
						$templateOptions['image_meta_alt'] .= $templateOptions['image_meta_alt_delim'] . '{'. $element_name .'[1]}';
					}
				}
				break;

			case 'attachments':					
			case 'attachment_url':				
				$templateOptions['atch_delim'] = '|';
				$templateOptions['is_update_attachments'] = 1;
				if ( empty($templateOptions['attachments'])) {
					$templateOptions['attachments'] = '{'. $element_name .'[1]}';
				}
				else {
					$templateOptions['attachments'] .= $templateOptions['atch_delim'] . '{'. $element_name .'[1]}';
				}
				break;
		}

	}

	public static function get_meta($pid, $key){
	    if (XmlExportTaxonomy::$is_active){
            return get_term_meta($pid, $key, true);
        }
        if (XmlExportUser::$is_active){
            return get_user_meta($pid, $key, true);
        }
	    return get_post_meta($pid, $key, true);
    }
}