<?php
$add_the_view = __( 'Add the view to a page or sidebar using its unique shortcode or the Strong Testimonials widget.', 'strong-testimonials' );
?>
<h2><?php _e( 'Let\'s Get Started', 'strong-testimonials' ); ?></h2>
<p class="lead-description"><?php _e( 'This plugin is different than others you may have tried.', 'strong-testimonials' ); ?></p>

<div class="feature-section one-col">
    <p><?php printf( __( 'Testimonials are like posts. They can be viewed individually. For example, a testimonial with the title "Highly recommended!" is viewable at <a href="#" class="nogo">%1$s</a>.', 'strong-testimonials' ), home_url( 'testimonial/highly-recommended' ) ); ?></p>

    <script>
      document.querySelector(".nogo").addEventListener("click", function (e) {
        e.preventDefault();
        this.blur();
      });
    </script>

    <p><?php _e( 'The more common approach is to display a group of testimonials. Among the many options, you can select which testimonials to include, what fields to show, and the layout.', 'strong-testimonials' ); ?></p>

    <p><?php _e( 'The plugin provides a tool for configuring those displays: <strong>Views</strong>.', 'strong-testimonials' ); ?>
        <?php _e( 'A view is simply a group of settings with an easy-to-use editor.', 'strong-testimonials' ); ?>
        <?php _e( 'You can create <em>unlimited</em> views.', 'strong-testimonials' ); ?>
        <?php _e( 'Each view has its own simple shortcode.', 'strong-testimonials' ); ?>
    </p>
</div>

<div class="feature-section two-col">
    <div class="col">
        <h3><?php _e('How to Add the Form', 'strong-testimonials' ); ?></h3>
        <p>1. <?php printf( __( '<a href="%s">Check the custom fields</a>. The default set of fields are designed to suit most situations. Add or remove fields as you see fit.', 'strong-testimonials' ), admin_url( 'edit.php?post_type=wpm-testimonial&page=testimonial-fields' ) ); ?>
        </p>
        <p>2. <?php printf( __( 'Create a <a href="%s">view</a>.', 'strong-testimonials' ), admin_url( 'edit.php?post_type=wpm-testimonial&page=testimonial-views' ) ); ?>
	        <?php _e( 'Select <strong>Form</strong> mode.', 'strong-testimonials' ); ?>
        </p>
        <p>3. <?php echo $add_the_view; ?></p>
    </div>

    <div class="col">
        <h3><?php _e( 'How to Display Your Testimonials', 'strong-testimonials' ); ?></h3>
        <p>1. <?php printf( __( '<a href="%s">Enter your testimonials</a> if necessary. The plugin will not read existing testimonials from another plugin or theme. It will not import testimonials.', 'strong-testimonials' ), admin_url( 'edit.php?post_type=wpm-testimonial' ) ); ?></p>
        <p>2. <?php printf( __( 'Create a <a href="%s">view</a>.', 'strong-testimonials' ), admin_url( 'edit.php?post_type=wpm-testimonial&page=testimonial-views' ) ); ?>
	        <?php _e( 'Select <strong>Display</strong> mode.', 'strong-testimonials' ); ?>
        </p>
        <p>3. <?php echo $add_the_view; ?></p>
    </div>

    <div class="col">
        <h3><?php _e( 'How to Add a Slideshow', 'strong-testimonials' ); ?></h3>
        <p>1. <?php printf( __( '<a href="%s">Enter your testimonials</a> if necessary. The plugin will not read existing testimonials from another plugin or theme. It will not import testimonials.', 'strong-testimonials' ), admin_url( 'edit.php?post_type=wpm-testimonial' ) ); ?></p>
        <p>2. <?php printf( __( 'Create a <a href="%s">view</a>.', 'strong-testimonials' ), admin_url( 'edit.php?post_type=wpm-testimonial&page=testimonial-views' ) ); ?>
	        <?php _e( 'Select <strong>Slideshow</strong> mode.', 'strong-testimonials' ); ?>
        </p>
        <p>3. <?php echo $add_the_view; ?></p>
    </div>

    <div class="col">
        <h3><?php _e( 'How to Display the Number of Testimonials', 'strong-testimonials' ); ?></h3>
        <p><?php printf( __( 'Use the %s shortcode.', 'strong-testimonials' ), '<code>&#91;testimonial_count&#93;</code>' ); ?>
			<?php _e( 'For example:', 'strong-testimonials' ); ?></p>
        <p><span class="code"><?php printf( __( 'Read some of our %s testimonials!', 'strong-testimonials' ), '&#91;testimonial_count&#93;' ); ?></span></p>
        <p><?php printf( __( 'To count for a specific category, add the %s attribute with the category slug.', 'strong-testimonials' ), '<code>category</code>' ); ?>
			<?php _e( 'For example:', 'strong-testimonials' ); ?></p>
        <p><span class="code"><?php printf( __( 'Here\'s what %s local clients say', 'strong-testimonials' ), '&#91;testimonial_count category="local"&#93;' ); ?></span></p>
    </div>

    <div class="col">
        <h3><?php _e( 'How to Translate', 'strong-testimonials' ); ?></h3>
        <p><?php _e( 'Strong Testimonials is compatible with WPML, Polylang and WP Globus.', 'strong-testimonials' ); ?></p>
        <p><?php _e( 'In WPML and Polylang, domains are added to the <strong>String Translation</strong> pages. Those domains encompass the form fields, the form messages, the notification email, and the "Read more" link text in your views. They are updated automatically when any of those settings change.', 'strong-testimonials' ); ?></p>
    </div>
</div>
