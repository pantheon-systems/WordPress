<?php

namespace Wpae\App\Field;


class SizeSystem extends Field
{
    const SECTION = 'detailedInformation';

    public function getValue($snippetData)
    {
        $detailedInformationData = $this->feed->getSectionFeedData(self::SECTION);

        return $detailedInformationData['sizeSystem'];
    }

    public function getFieldName()
    {
        return 'size_system';
    }
}