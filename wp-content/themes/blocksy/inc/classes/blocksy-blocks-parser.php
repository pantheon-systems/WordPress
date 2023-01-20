<?php

class Blocksy_WP_Block_Parser extends WP_Block_Parser {
	function parse($document) {
		$result = parent::parse($document);

		$current_index = 0;
		$current_heading_index = 0;

		foreach ($result as $index => $first_level_block) {
			$result[$index]['firstLevelBlock'] = true;

			if (
				! empty(trim($first_level_block['innerHTML']))
				&&
				isset($first_level_block['blockName'])
				&&
				$first_level_block['blockName']
			) {
				$result[$index]['firstLevelBlockIndex'] = $current_index++;

				if (
					strpos($first_level_block['blockName'], 'heading') !== false
					||
					strpos($first_level_block['blockName'], 'headline') !== false
					||
					in_array(
						substr(trim($first_level_block['innerHTML']), 0, 3),
						[
							'<h1', '<h2', '<h3',
							'<h4', '<h5', '<h6'
						]
					)
				) {
					$result[$index]['firstLevelHeadingIndex'] = $current_heading_index++;
				}
			}
		}

		return $result;
	}
}
