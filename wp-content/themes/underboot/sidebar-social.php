
<?php
/**
 * The sidebar containing the social code.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package qiigo\'s_theme
 */

						 $fbsocial = do_shortcode('[bng_location id="facebook_url"]');
                                if($fbsocial !== '') {?>    
                                    <a id="facebook" target="_blank" href="<?php echo do_shortcode( '[bng_location id="facebook_url"]');?>" title="Facebook"></a>
                        <?php }else {?>
									<a id="facebook" target="_blank" href="https://www.facebook.com/360Painting" title="Facebook"></a>	
						<?php }?>	
						
                        <?php $twittersocial = do_shortcode('[bng_location id="twitter_url"]');
                                if($twittersocial !== '') {?>
                                    <a id="twitter" target="_blank" href="<?php echo do_shortcode( '[bng_location id="twitter_url"]');?>" title="Twitter"></a>         
                        <?php }else {?>
                        			<a id="twitter" target="_blank" href="https://twitter.com/360Painting" title="Twitter"></a> 
                        <?php }?>
                        <?php $googlesocial = do_shortcode('[bng_location id="google_url"]');
                                if($googlesocial !== '') {?>
                                    <a id="google" target="_blank" href="<?php echo do_shortcode( '[bng_location id="google_url"]');?>" title="Google"></a>          
                        <?php }?>
                        <?php $youtubesocial = do_shortcode('[bng_location id="youtube_url"]');
                                if($youtubesocial !== '') {?>
                                    <a id="youtube" target="_blank" href="<?php echo do_shortcode( '[bng_location id="youtube_url"]');?>" title="YouTube"></a>          
                        <?php }?>
                        <?php $pinterestsocial = do_shortcode('[bng_location id="pinterest_url"]');
                                if($pinterestsocial !== '') {?>
                                    <a id="pinterest" target="_blank" href="<?php echo do_shortcode( '[bng_location id="pinterest_url"]');?>" title="Pinterest"></a>            
                        <?php }else {?>
                        			<a id="pinterest" target="_blank" href="https://www.pinterest.com/360painting0554/" title="Pintrest"></a>    
                        <?php }?>
                        <?php $angiessocial = do_shortcode('[bng_location id="angieslist_url"]');
                                if($angiessocial !== '') {?>
                                    <a id="angieslist" target="_blank" href="<?php echo do_shortcode( '[bng_location id="angieslist_url"]');?>" title="Angies List"></a>      
                        <?php }else {?>
                        			<a id="angieslist" target="_blank" href="https://www.angieslist.com/" title="Angies List"></a>    
                        <?php }?>
                        
                        <?php $linkedinsocial = do_shortcode('[bng_location id="customvar10"]');
                                if($linkedinsocial !== '') {?>
                                    <a id="linkedin" target="_blank" href="<?php echo do_shortcode( '[bng_location id="customvar10"]');?>" title="LinkedIn"></a>        
                        <?php }else {?>
                        			<a id="linkedin" target="_blank" href="https://www.linkedin.com/company/360-painting/" title="LinkedIn"></a>    
                        <?php }?>
                        <?php $instagramsocial = do_shortcode('[bng_location id="instagram_url"]');
                                if($instagramsocial !== '') {?>
                                    <a id="instagram" target="_blank" href="<?php echo do_shortcode( '[bng_location id="instagram_url"]');?>" title="Instagram"></a>        
                        <?php }else {?>
                        			<a id="instagram" target="_blank" href="https://www.instagram.com/360_painting/?hl=en" title="Instagram"></a>    
                        <?php }?>
                        
                        <?php //remove logo sn 11/3/17: https://projects.zoho.com/portal/qiigo#buginfo/103629000002592245/103629000014615457 ?>
                       <?php //$homeadvisorsocial = do_shortcode('[bng_location id="customvar9"]');
                                //if($homeadvisorsocial !== '') {?>
                                    <!--<a id="homeadvisor" target="_blank" href="<?php //echo do_shortcode( '[bng_location id="customvar9_url"]');?>" title="HomeAdvisor"></a>  -->      
                        <?php //}else {?>
                        			<!--<a id="homeadvisor" target="_blank" href="https://www.homeadvisor.com/threesixtypainting/" title="HomeAdvisor"></a>  -->  
                        <?php //}?>