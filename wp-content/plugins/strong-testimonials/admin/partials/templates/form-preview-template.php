<?php
/**
 * Template Name: Default Form
 * Description: The default form template.
 */
?>
<div class="strong-view strong-form">

	<div id="wpmtst-form">

        <div class="strong-form-inner">

	        <?php wpmtst_field_required_notice(); ?>

            <form enctype="multipart/form-data" autocomplete="off">

                <?php wpmtst_all_form_fields( $new_fields ); ?>

                <?php wpmtst_form_submit_button( true ); ?>

            </form>

        </div>

	</div>

</div>