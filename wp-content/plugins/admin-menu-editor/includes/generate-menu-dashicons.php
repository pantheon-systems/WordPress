<?php
/**
 * This utility script generates menu icons metadata based on the Dashicons icon font included in WordPress.
 */

if ( !defined('ABSPATH') ) {
	die('No direct script access');
}
if ( !constant('WP_DEBUG') || !current_user_can('edit_plugins') ) {
	echo "Permission denied. You need the edit_plugins cap to run this script and WP_DEBUG must be enabled.";
	return;
}

require_once dirname(__FILE__) . '/PHP-CSS-Parser/autoloader.php';
$dashiconsStylesheet = ABSPATH . WPINC . '/css/dashicons.css';

$icons = array();

$allDashiconDefinitions = '';

$ignoreIcons = array('dashboard', 'editor-bold', 'editor-italic');
$ignoreIcons = array_flip($ignoreIcons);

$parser = new Sabberworm\CSS\Parser(file_get_contents($dashiconsStylesheet));
$cssDocument = $parser->parse();

$blocks = $cssDocument->getAllDeclarationBlocks();
foreach($blocks as $block) {
	/** @var Sabberworm\CSS\RuleSet\DeclarationBlock $block */

	//We want the ".dashicons-*:before" selectors.
	$selectors = $block->getSelectors();
	foreach($selectors as $selector) {
		/** @var Sabberworm\CSS\Property\Selector $selector */

		if ( preg_match('/\.dashicons-(?P<name>[\w\-]+):before/', $selector->getSelector(), $matches) ) {
			$name = $matches['name'];
			$char = null;

			//The arrow icons aren't really suitable as menu icons.
			if ( preg_match('/^(arrow)-/', $name) ) {
				break;
			}

			//Some icons are duplicates of the "admin-" icons or just wouldn't look very good in a menu.
			if ( array_key_exists($name, $ignoreIcons) ) {
				break;
			}

			$rules = $block->getRules('content'); //Expect something like "content: '\f123'".
			foreach($rules as $rule) {
				/** @var Sabberworm\CSS\Rule\Rule $rule */
				$value = $rule->getValue();
				if ($value instanceof Sabberworm\CSS\Value\CSSString) {
					//The parser defaults to UTF-8. Convert the char to a hexadecimal escape code
					//so we don't have to worry about our CSS charset.
					$char = ltrim(bin2hex(iconv('UTF-8', 'UCS-4', $value->getString())), '0');
					$icons[$name] = '\\' . $char;
				}
			}

			if (isset($char) && ($name !== 'before')) {
				$allDashiconDefinitions .= sprintf(
					'%s { content: "\%s" !important; }',
					implode(', ', $selectors),
					$char
				) . "\n";
			}

			break;
		}
	}
}

$dashiconComment = sprintf(
	"/*\nThis file was automatically generated from /wp-includes/css/dashicons.css.\nLast update: %s\n*/",
	date('c')
);
file_put_contents(
	dirname(__FILE__) . '/../css/_dashicons.scss',
	$dashiconComment . "\n" . $allDashiconDefinitions
);

?>
<div class="wrap">
<h2>Dashicons to Menu Icons</h2>
<style type="text/css" scoped="scoped">
	.ame-debug-dashicon {
		display: inline-block;
		margin: 2px;
		min-width: 180px;
	}
</style>
<?php

ksort($icons);
$arrayDefinition = "array(\n";
$currentLine = "\t";

foreach($icons as $name => $character) {
	//Output each icon for visual verification.
	printf(
		'<div class="ame-debug-dashicon"><div class="dashicons dashicons-%1$s"></div> %1$s</div>',
		$name
	);

	//Wrap the array definition at about 80 characters for legibility.
	$item = "'" . $name . "', ";
	if ( strlen($currentLine . $item) > 80 ) {
		$arrayDefinition .= $currentLine . "\n";
		$currentLine = "\t";
	}

	$currentLine .= $item;
}

if (strlen($currentLine) > 1) {
	$arrayDefinition .= $currentLine . "\n";
}
$arrayDefinition .= ')';

echo '<div class="clear"></div><br>';
echo '<textarea cols="100" rows="20">', esc_textarea($arrayDefinition), '</textarea>';

echo '</div>';