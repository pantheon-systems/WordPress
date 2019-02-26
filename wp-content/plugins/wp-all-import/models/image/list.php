<?php

/**
 * Class PMXI_Image_List
 */
class PMXI_Image_List extends PMXI_Model_List {

    /**
     * PMXI_Image_List constructor.
     */
    public function __construct() {
		parent::__construct();
		$this->setTable(PMXI_Plugin::getInstance()->getTablePrefix() . 'images');
	}

    /**
     * @param $url
     * @return array|bool|null|\WP_Post
     */
    public function getExistingImageByUrl($url) {
        $args = array(
            'image_url' => $url,
        );
        return $this->getExistingImage($args);
    }

    /**
     * @param $image
     * @return array|bool|null|\WP_Post
     */
    public function getExistingImageByFilename($image) {
        $args = array(
            'image_filename' => $image,
        );
        return $this->getExistingImage($args);
    }

    /**
     * @param $args
     * @return array|bool|null|\WP_Post
     */
    public function getExistingImage($args){
        $attid = false;
        foreach($this->getBy($args)->convertRecords() as $imageRecord) {
            if ( ! $imageRecord->isEmpty() ) {
                $attid = $imageRecord->attachment_id;
                break;
            }
        }
        return $attid ? get_post($attid) : false;
    }
}