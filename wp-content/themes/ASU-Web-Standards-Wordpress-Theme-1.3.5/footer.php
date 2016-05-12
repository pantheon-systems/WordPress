<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package asu-wordpress-web-standards
 */
?>
  </div><!-- #page -->
</div><!-- #page-wrapper -->

<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @author Global Insititue of Sustainability
 * @author Ivan Montiel
 *
 * @package asu-wordpress-web-standards
 */

if ( is_array( get_option( 'wordpress_asu_theme_options' ) ) ) {
  $cOptions = get_option( 'wordpress_asu_theme_options' );
}
?>
  <div class="footer">
    <div class="big-foot">
      <div class="container">
        <div class="row">
          <div class="col-md-4 col-sm-12 space-bot-md">
            <?php
            //  =============================
            //  = Logo                      =
            //  =============================
            // Do we have a logo?
            $logo = '<a class="footer-logo-link" href="%3$s"><img class="footer-logo" src="%1$s" alt="%2$s"/></a><br>';
            if ( isset( $cOptions ) &&
              array_key_exists( 'logo', $cOptions ) &&
              $cOptions['logo'] !== '' ) {
              echo wp_kses( sprintf( $logo, $cOptions['logo'], get_bloginfo( 'name' ) . ' Logo', home_url( '/' ) ), wp_kses_allowed_html( 'post' ) );
            } else {
              echo '<h2>' .wp_kses( get_bloginfo( 'description' ), wp_kses_allowed_html( 'post' ) ) . '</h2>';
            }
            ?>

            <?php
            //  =============================
            //  = Campus Address            =
            //  =============================
            // Do we have an address?
            if ( isset( $cOptions ) &&
                 array_key_exists( 'campus_address', $cOptions ) &&
                 $cOptions['campus_address'] !== '' ) {
              $campus_address_option = $cOptions['campus_address'];

              echo '<address>';
              switch ( $campus_address_option ) {
                case 'Tempe':
                  echo 'Arizona State University - Tempe campus<br/>1151 S. Forest Ave.<br/>Tempe, AZ 85287 USA';
                  break;
                case 'Polytechnic':
                  echo 'Arizona State University - Polytechnic campus<br/>Power Road and Williams Field Road<br/>7001 E. Williams Field Road<br/>Mesa, AZ 85212';
                  break;
                case 'Downtown Phoenix':
                  echo 'Arizona State University - Downtown Phoenix<br/>411 N. Central, Suite 520<br/>Phoenix, AZ 85004';
                  break;
                case 'West':
                  echo 'Arizona State University - West campus<br/>4701 West Thunderbird Road<br/>PO Box 37100<br/>Phoenix, AZ 85069-7100';
                  break;
                case 'Research Park':
                  echo 'Arizona State University - Research Park<br/>8750 S Science Dr<br/>Tempe, AZ 85284';
                  break;
                case 'Skysong':
                  echo 'Arizona State University - SkySong<br/>1475 N. Scottsdale Rd, Suite 200<br/>Scottsdale, Arizona 85257-3538';
                  break;
                case 'Lake Havasu':
                  echo 'Arizona State University - Lake Havasu<br/>100 University Way<br/>Lake Havasu City, AZ 86403';
                  break;
              }

              echo '</address><br/>';
            }
            ?>
            <address>
              <?php
              //  =============================
              //  = School Address            =
              //  =============================
              // Do we have an address?
              if ( isset( $cOptions ) &&
                     array_key_exists( 'address', $cOptions ) &&
                     $cOptions['address'] !== '' ) {
                echo wp_kses( nl2br( $cOptions['address'] ), wp_kses_allowed_html( 'post' ) );
              }
              ?><br/>
              <?php
              //  =============================
              //  = Phone                     =
              //  =============================
              $phone = 'Phone: <a class="phone-link" href="tel:%1$s" id="phone-link-in-footer">%1$s</a><br>';

              // Do we have a phone number?
              if ( isset( $cOptions ) &&
                     array_key_exists( 'phone', $cOptions ) &&
                     $cOptions['phone'] !== '' ) {
                echo wp_kses( sprintf( $phone, $cOptions['phone'] ), wp_kses_allowed_html( 'post' ) );
              }
              ?>
              <?php
              //  =============================
              //  = Fax                       =
              //  =============================
              //$fax = 'Fax: <a class="phone-link" href="fax:%1$s">%1$s</a><br>';
              $fax = 'Fax: %1$s<br>';

              // Do we have a fax number?
              if ( isset( $cOptions ) &&
                     array_key_exists( 'fax', $cOptions ) &&
                     $cOptions['fax'] !== '' ) {
                echo wp_kses( sprintf( $fax, $cOptions['fax'] ), wp_kses_allowed_html( 'post' ) );
              }
              ?>
            </address>
            <?php
            //  =============================
            //  = Contact Us Email or URL   =
            //  =============================
            $contactURL = '<p><a class="contact-link" href="%1$s%2$s%3$s" id="contact-us-link-in-footer">Contact Us</a></p>';

              // Do we have a contact?
            if ( isset( $cOptions ) &&
                  array_key_exists( 'contact', $cOptions ) &&
                  $cOptions['contact'] !== '' ) {
              $type       = '';
              $contact    = $cOptions['contact'];
              $additional = '';

              if ( filter_var( $contact, FILTER_VALIDATE_EMAIL ) ) {
                $type = 'mailto:';

                //  =============================
                //  = Contact Us Email Subject  =
                //  =============================

                // Do we have a subject line?
                if ( array_key_exists( 'contact_subject', $cOptions ) &&
                     $cOptions['contact_subject'] !== '' ) {
                  $additional .= '&subject=' . rawurlencode( $cOptions['contact_subject'] );
                }

                //  =============================
                //  = Contact Us Email Body     =
                //  =============================

                // Do we have a body?
                if ( array_key_exists( 'contact_body', $cOptions ) &&
                     $cOptions['contact_body'] !== '' ) {
                  $additional .= '&body=' . rawurlencode( $cOptions['contact_body'] );
                }

                // Fix the additional part
                if ( strlen( $additional ) > 0 ) {
                  $additional = substr_replace( $additional, '?', 0, 1 );
                }
              }

              echo wp_kses( sprintf( $contactURL, $type, $contact, $additional ), wp_kses_allowed_html( 'post' ) );
            }

            ?>
            <ul class="social-media">
              <?php
              //  =============================
              //  = Facebook                  =
              //  =============================
              $fb = '<li><a href="%1$s" title="Facebook" id="facebook-link-in-footer"><i class="fa fa-facebook-square" aria-hidden="true"></i><span class="sr-only">Facebook</span></a></li>';

              // Do we have a facebook?
              if ( isset( $cOptions ) &&
                     array_key_exists( 'facebook', $cOptions ) &&
                     $cOptions['facebook'] !== '' ) {
                echo wp_kses( sprintf( $fb, $cOptions['facebook'] ), wp_kses_allowed_html( 'post' ) );
              }
              ?>
              <?php
              //  =============================
              //  = Twitter                   =
              //  =============================
              $twitter = '<li><a href="%1$s" title="Twitter" id="twitter-link-in-footer"><i class="fa fa-twitter-square" aria-hidden="true"></i><span class="sr-only">Twitter</span></a></li>';

              // Do we have a twitter?
              if ( isset( $cOptions ) &&
                     array_key_exists( 'twitter', $cOptions ) &&
                     $cOptions['twitter'] !== '' ) {
                echo wp_kses( sprintf( $twitter, $cOptions['twitter'] ), wp_kses_allowed_html( 'post' ) );
              }
              ?>
              <?php
              //  =============================
              //  = Google+                   =
              //  =============================
              $googlePlus = '<li><a href="%1$s" title="Google+" id="google_plus-link-in-footer"><i class="fa fa-google-plus-square" aria-hidden="true"></i><span class="sr-only">Google Plus</span></a></li>';

              // Do we have a google+?
              if ( isset( $cOptions ) &&
                     array_key_exists( 'google_plus', $cOptions ) &&
                     $cOptions['google_plus'] !== '' ) {
                echo wp_kses( sprintf( $googlePlus, $cOptions['google_plus'] ), wp_kses_allowed_html( 'post' ) );
              }

              //  =============================
              //  = LinkedIn                  =
              //  =============================
              $linkedIn = '<li><a href="%1$s" title="LinkedIn" id="linkedin-link-in-footer"><i class="fa fa-linkedin-square" aria-hidden="true"></i><span class="sr-only">LinkedIn</span></a></li>';

              // Do we have a linkedin?
              if ( isset( $cOptions ) &&
                     array_key_exists( 'linkedin', $cOptions ) &&
                     $cOptions['linkedin'] !== '' ) {
                echo wp_kses( sprintf( $linkedIn, $cOptions['linkedin'] ), wp_kses_allowed_html( 'post' ) );
              }

              //  =============================
              //  = Youtube                   =
              //  =============================
              $youtube = '<li><a href="%1$s" title="Youtube" id="youtube-link-in-footer"><i class="fa fa-youtube-square" aria-hidden="true"></i><span class="sr-only">Youtube</span></a></li>';

              // Do we have a youtube?
              if ( isset( $cOptions ) &&
                     array_key_exists( 'youtube', $cOptions ) &&
                     $cOptions['youtube'] !== '' ) {
                echo wp_kses( sprintf( $youtube, $cOptions['youtube'] ), wp_kses_allowed_html( 'post' ) );
              }

              //  =============================
              //  = Vimeo                     =
              //  =============================
              $vimeo = '<li><a href="%1$s" title="Vimeo" id="vimeo-link-in-footer"><i class="fa fa-vimeo-square" aria-hidden="true"></i><span class="sr-only">Vimeo</span></a></li>';

              // Do we have a vimeo?
              if ( isset( $cOptions ) &&
                     array_key_exists( 'vimeo', $cOptions ) &&
                     $cOptions['vimeo'] !== '' ) {
                echo wp_kses( sprintf( $vimeo, $cOptions['vimeo'] ), wp_kses_allowed_html( 'post' ) );
              }

              //  =============================
              //  = Instagram                 =
              //  =============================
              $instagram = '<li><a href="%1$s" title="Instagram" id="instagram-link-in-footer"><i class="fa fa-instagram" aria-hidden="true"></i><span class="sr-only">Instagram</span></a></li>';

              // Do we have a instagram?
              if ( isset( $cOptions ) &&
                     array_key_exists( 'instagram', $cOptions ) &&
                     $cOptions['instagram'] !== '' ) {
                echo wp_kses( sprintf( $instagram, $cOptions['instagram'] ), wp_kses_allowed_html( 'post' ) );
              }

              //  =============================
              //  = Flickr                    =
              //  =============================
              $flickr = '<li><a href="%1$s" title="Flickr" id="flickr-link-in-footer"><i class="fa fa-flickr" aria-hidden="true"></i><span class="sr-only">Flickr</span></a></li>';

              // Do we have a flickr?
              if ( isset( $cOptions ) &&
                     array_key_exists( 'flickr', $cOptions ) &&
                     $cOptions['flickr'] !== '' ) {
                echo wp_kses( sprintf( $flickr, $cOptions['flickr'] ), wp_kses_allowed_html( 'post' ) );
              }

              //  =============================
              //  = Pinterest                 =
              //  =============================
              $pinterest = '<li><a href="%1$s" title="Pinterest" id="pinterest-link-in-footer"><i class="fa fa-pinterest-square" aria-hidden="true"></i><span class="sr-only">Pinterest</span></a></li>';

              // Do we have a pinterest?
              if ( isset( $cOptions ) &&
                     array_key_exists( 'pinterest', $cOptions ) &&
                     $cOptions['pinterest'] !== '' ) {
                echo wp_kses( sprintf( $pinterest, $cOptions['pinterest'] ), wp_kses_allowed_html( 'post' ) );
              }

              //  =============================
              //  = RSS                       =
              //  =============================
              $rss = '<li><a href="%1$s" title="RSS"  id="rss-link-in-footer"><i class="fa fa-rss" aria-hidden="true"></i><span class="sr-only">RSS</span></a></li>';

              // Do we have a instagram?
              if ( isset( $cOptions ) &&
                     array_key_exists( 'rss', $cOptions ) &&
                     $cOptions['rss'] !== '' ) {
                echo wp_kses( sprintf( $rss, $cOptions['rss'] ), wp_kses_allowed_html( 'post' ) );
              }
              ?>
            </ul>
            <?php
            //  =============================
            //  = Contribute URL            =
            //  =============================
            $contribute = '<a type="button" class="btn btn-primary" href="%s"  id="contribute-button-in-footer">Contribute</a>';

            // Do we have a contribute?
            if ( isset( $cOptions ) &&
                   array_key_exists( 'contribute', $cOptions ) &&
                   $cOptions['contribute'] !== '' ) {
              echo wp_kses( sprintf( $contribute, $cOptions['contribute'] ), wp_kses_allowed_html( 'post' ) );
            }
            ?>

          </div>


          <?php
          wp_nav_menu(
              array(
                'menu'              => 'secondary',
                'theme_location'    => 'secondary',
                'depth'             => 2,
                'container'         => '',
                'walker'            => new WP_Bootstrap_Footer_Navwalker(),
              )
          );
          ?>
        </div><!-- /.row -->
      </div><!-- /.container -->
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <?php dynamic_sidebar( 'footer' ); ?>
          </div>
        </div>
      </div>
    </div><!-- /.big-foot -->
    <div id="innovation-bar">
      <div class="container">
        <div class="row">
          <div class="col-md-10 space-top-sm space-bot-sm">
            <a href="http://yourfuture.asu.edu/rankings" target="_blank" id="asu-is-number-1-for-innovation">ASU is #1 in the U.S. for Innovation</a>
          </div>
          <div class="hidden-sm hidden-xs col-md-2 innovation-footer-image-wrapper">
             <a href="http://yourfuture.asu.edu/rankings" target="_blank" id="best-colleges-us-news-bage-icon">
              <img src="<?php echo get_template_directory_uri() ?>/assets/asu-web-standards/img/footer/best-colleges-us-news-badge.png" alt="Best Colleges U.S. News Most Innovative 2016">
            </a>
          </div>
        </div>
      </div>
    </div><!-- /#innovation-bar -->
    <div class="little-foot">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="little-foot__right">
              <ul class="little-foot-nav">
                <li><a href="http://www.asu.edu/copyright/" id="copyright-trademark-legal-footer">Copyright &amp; Trademark</a></li>
                <li><a href="http://www.asu.edu/accessibility/" id="accessibility-legal-footer">Accessibility</a></li>
                <li><a href="http://www.asu.edu/privacy/" id="privacy-legal-footer">Privacy</a></li>
                <li><a href="http://www.asu.edu/asujobs" id="jobs-legal-footer">Jobs</a></li>
                <li><a href="https://cfo.asu.edu/emergency" id="emergency-legal-footer">Emergency</a></li>
                <li><a href="https://contact.asu.edu/" id="contact-asu-legal-footer">Contact ASU</a></li>
              </ul>
            </div>
          </div>
        </div><!-- /.row -->
      </div><!-- /.container -->
    </div><!-- /.little-foot -->

  </div><!-- /.footer -->
  <!-- End Footer -->

  <?php wp_footer(); ?>
</body>
</html>
