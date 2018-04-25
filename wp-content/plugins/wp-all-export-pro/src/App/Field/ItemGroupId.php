<?php

namespace Wpae\App\Field;


class ItemGroupId extends Field
{
    const SECTION = 'detailedInformation';

    const SET_THE_GROUP_AUTOMATICALLY = 'automatically';

    public function getValue($snippetData)
    {
        $detailedInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if($detailedInformationData['setTheGroupId'] == self::SET_THE_GROUP_AUTOMATICALLY) {

            if($this->entry->post_type == 'product_variation') {
                $product = get_post($this->entry->post_parent);
            } else {
                $product = $this->entry;
            }

            if($product){
                $groupItemId = substr(md5($product->ID.$product->post_title), 0, 16);
            } else {
                $groupItemId = '';
            }

        }
        else if($detailedInformationData['setTheGroupId'] == self::CUSTOM_VALUE_TEXT) {
            $groupItemId = $this->replaceSnippetsInValue($detailedInformationData['setTheGroupIdCV'], $snippetData);
        } else {
            throw new \Exception('Unknown field value '.$detailedInformationData['setTheGroupId'] . 'for field '.$this->getFieldName());
        }

        return $groupItemId;
    }

    public function getFieldName()
    {
        return 'item_group_id';
    }
}