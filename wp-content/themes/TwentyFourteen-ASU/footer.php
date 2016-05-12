<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>

		</div><!-- #main -->

		<footer id="colophon" class="site-footer" role="contentinfo">

			<?php get_sidebar( 'footer' ); ?>
            <div id="eng-footer">
				<!-- FULTON FOOTER --> 
				<p><a href="http://engineering.asu.edu/contacts/">contact engineering</a><br>
    			Ira A. Fulton Schools of Engineering<br /> 
        		Arizona State University | P.O. Box 9309 | Tempe, AZ 85287-9309<br /> 
        		Brickyard 6th Floor | <a href="http://www.asu.edu/map/interactive/?campus=tempe&amp;building=BYENG">link to map</a><br /> 
        		Engineering Administration: 480-965-1730</p> 
			</div><!-- end footer contact info --> 
		</footer><!-- #colophon -->
        <?php include('http://www.asu.edu/asuthemes/4.0-rsp.1/includes/footer.shtml'); ?>
	</div><!-- #page -->

	<?php wp_footer(); ?>
</body>
</html>