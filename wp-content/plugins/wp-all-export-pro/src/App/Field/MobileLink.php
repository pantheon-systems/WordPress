<?php

namespace Wpae\App\Field;


class MobileLink extends Field
{
    const SECTION = 'basicInformation';

    public function getValue($snippetData)
    {
        $basicInformationData = $this->feed->getSectionFeedData(self::SECTION);

        if(!isset($basicInformationData['mobileLink'])) {
            return '';
        }

        return $this->replaceSnippetsInValue($basicInformationData['mobileLink'], $snippetData);

    }

    public function getFieldName()
    {
        return 'mobile_link';
    }
}