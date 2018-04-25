<?php

namespace Wpae\App\Field;


class Title extends Field
{
    const SECTION = 'basicInformation';

    public function getValue($snippetData)
    {
        $basicInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if($basicInformationData['itemTitle'] == 'productTitle') {
            if($this->entry->post_type == 'product_variation' && $basicInformationData['useParentTitleForVariableProducts']) {
                return get_post($this->entry->post_parent)->post_title;
            }
            return $this->entry->post_title;
        } else if($basicInformationData['itemTitle'] == self::CUSTOM_VALUE_TEXT) {
            return $this->replaceSnippetsInValue($basicInformationData['itemTitleCV'], $snippetData);
        } else {
            throw new \Exception('Unknown field value');
        }
    }

    public function getFieldName()
    {
        return 'title';
    }


}