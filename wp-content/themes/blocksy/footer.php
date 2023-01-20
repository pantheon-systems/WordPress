<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Blocksy
 */

blocksy_after_current_template();
do_action('blocksy:content:bottom');

?>
	</main>

	<?php
		do_action('blocksy:content:after');
		do_action('blocksy:footer:before');

		blocksy_output_footer();

		do_action('blocksy:footer:after');
	?>
</div>

<?php wp_footer(); ?>

</body>
</html>
