<?php
/**
 * Template Name: Simple Form
 * Description: A simple form template.
 */
?>
<div class="strong-view strong-form <?php wpmtst_container_class(); ?>"<?php wpmtst_container_data(); ?>>

	<div id="wpmtst-form">

        <div class="strong-form-inner">

	        <?php wpmtst_field_required_notice(); ?>

            <form <?php wpmtst_form_info(); ?>>

                <?php wpmtst_form_setup(); ?>

                <?php do_action( 'wpmtst_form_before_fields' ); ?>

                <?php wpmtst_all_form_fields(); ?>

                <?php do_action( 'wpmtst_form_after_fields' ); ?>

                <?php wpmtst_form_submit_button(); ?>

            </form>

        </div>

	</div>

</div>
