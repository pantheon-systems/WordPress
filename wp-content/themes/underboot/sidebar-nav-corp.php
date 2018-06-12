<?php
/**
 * The sidebar containing the Main Navigation.
 *
 *
 * @package qiigo\'s_theme
 */
 
 // get_site_url does not have the trailing slash
 // corporate menu is different; the location path is not included because there isn't one. had to remove the double slash in the URL.
 ?>
                	<ul class="menu" >
                           <li><a href="<?php echo get_site_url(); ?>" title="Home page for 360&deg; Painting">Corporate Home</a></li>

                         <?php
						 
						 
							//get the value of Qiigo Variables CPT button1, supposed to be about -----------------------------------------------------------
								$aboutnav = do_shortcode('[bng_location id="button_1_url"]');
								
							//is the services (button1) variable not empty?
							if($aboutnav !== '') {	
                                //get the URL of the about page 	
                                     $aboutnav = site_url ('/'.$aboutnav);  //removed locationpath so as to not have a double slash in the URL
                                //get the ID of the about page
                                    $aboutpostid = url_to_postid( $aboutnav );                                
                                //check if ID has children
                                    $aboutancestor_id=$aboutpostid;
                                    $aboutdescendants = get_pages(array('child_of' => $aboutancestor_id));
									//if no children, just show the normal li
									if (empty($aboutdescendants)) {	?>			
										<li><a href="<?php echo get_site_url(); ?>/<?php echo do_shortcode( '[bng_location id="button_1_url"]' );?>" title="About">About 360&deg;</a></li>			
									<?php }else 
									//if has children
									{	
											$aboutincl = "";	
											foreach ($aboutdescendants as $aboutpage) {
											if (($aboutpage->post_parent == $aboutancestor_id) ||
											   ($aboutpage->post_parent == $aboutpost->post_parent) ||
											   ($aboutpage->post_parent == $aboutpost->ID))
											{
											  $aboutincl .= $aboutpage->ID . ",";
											}
											}?>
                                            	<li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">About 360&deg;<span class="caret"></span></a>    
                                                <ul class="dropdown-menu">
                                                <?php wp_list_pages(array("child_of" => $aboutancestor_id, "include" => $aboutincl, "link_before" => "", "title_li" => "", "sort_column" => "menu_order"));?>
                                                </ul>              
                                            </li>			
                               <?php }?>
  						<?php }?>
                         









						<?php
							//get the value of Qiigo Variables CPT button3, supposed to be Residential -----------------------------------------------------------
								$residentialnav = do_shortcode('[bng_location id="customvar3"]');
								
							//is the residential (var3) variable not empty?
							if($residentialnav !== '') {	 
                                //get the URL of the residential page 	
                                    $residentialnav = site_url ('/'.$residentialnav);
                                //get the ID of the residential page
                                    $residentialpostid = url_to_postid( $residentialnav );                                
                                //check if ID has children
                                    $residentialancestor_id=$residentialpostid;
                                    $residentialdescendants = get_pages(array('child_of' => $residentialancestor_id));
									//if no children, just show the normal li
									if (empty($residentialdescendants)) {	?>			
										<li><a href="<?php echo get_site_url(); ?>/<?php echo do_shortcode( '[bng_location id="customvar3"]' );?>" title="Residential painting and fence and deck staining">Residential</a></li>			
									<?php }else 
									//if has children
									{	
											$residentialincl = "";	
											foreach ($residentialdescendants as $residentialpage) {
											if (($residentialpage->post_parent == $residentialancestor_id) ||
											   ($residentialpage->post_parent == $residentialpost->post_parent) ||
											   ($residentialpage->post_parent == $residentialpost->ID))
											{
											  $residentialincl .= $residentialpage->ID . ",";
											}
											}?>
                                            	<li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Residential<span class="caret"></span></a>    
                                                <ul class="dropdown-menu">
                                                <?php wp_list_pages(array("child_of" => $residentialancestor_id, "include" => $residentialincl, "link_before" => "", "title_li" => "", "sort_column" => "menu_order"));?>
                                                </ul>              
                                            </li>			
                               <?php }?>
  						<?php }?>
                        
                        
                        
                        
                        
                        
                        <?php
							//get the value of Qiigo Variables CPT button3, supposed to be commercial -----------------------------------------------------------
								$commercialnav = do_shortcode('[bng_location id="customvar4"]');
								
							//is the commercial (var4) variable not empty?
							if($commercialnav !== '') {	 
                                //get the URL of the commercial page 	
                                    $commercialnav = site_url ('/'.$commercialnav);
                                //get the ID of the commercial page
                                    $commercialpostid = url_to_postid( $commercialnav );                                
                                //check if ID has children
                                    $commercialancestor_id=$commercialpostid;
                                    $commercialdescendants = get_pages(array('child_of' => $commercialancestor_id));
									//if no children, just show the normal li
									if (empty($commercialdescendants)) {	?>			
										<li><a href="<?php echo get_site_url(); ?>/<?php echo do_shortcode( '[bng_location id="customvar4"]' );?>" title="Commercial painting and fence and deck staining">Commercial</a></li>			
									<?php }else 
									//if has children
									{	
											$commercialincl = "";	
											foreach ($commercialdescendants as $commercialpage) {
											if (($commercialpage->post_parent == $commercialancestor_id) ||
											   ($commercialpage->post_parent == $commercialpost->post_parent) ||
											   ($commercialpage->post_parent == $commercialpost->ID))
											{
											  $commercialincl .= $commercialpage->ID . ",";
											}
											}?>
                                            	<li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Commercial<span class="caret"></span></a>    
                                                <ul class="dropdown-menu">
                                                <?php wp_list_pages(array("child_of" => $commercialancestor_id, "include" => $commercialincl, "link_before" => "", "title_li" => "", "sort_column" => "menu_order"));?>
                                                </ul>              
                                            </li>			
                               <?php }?>
  						<?php }?>
                        
                        
                        
                        








						 <?php
							//get the value of Qiigo Variables CPT button5, supposed to be Service Areas Page -----------------------------------------------------------
								$serviceareanav = do_shortcode('[bng_location id="customvar5"]');
								
							//is the service areas page (var5) variable not empty?
							if($serviceareanav !== '') {	 
                                //get the URL of the Service Areas page 	
                                    $serviceareanav = site_url ('/'.$serviceareanav);
                                
								//get the ID of the Service Areas page
									 
									$serviceareapostid = url_to_postid( $serviceareanav );  
										  
									//echo "<h1>".$serviceareapostid."</h1>";  
									                       
                                //check if ID has children
                                    $serviceareaancestor_id=$serviceareapostid;
                                    $serviceareadescendants = get_pages(array('child_of' => $serviceareaancestor_id));
									//if no children, just show the normal li
									if (empty($serviceareadescendants)) {	?>			
										<li><a href="<?php echo get_site_url(); ?>/<?php echo do_shortcode( '[bng_location id="customvar5"]' );?>" title="Service areas for painting services">Service Areas</a></li>			
									<?php }else 
									//if has children
									{	
											$serviceareaincl = "";	
											foreach ($serviceareadescendants as $serviceareapage) {
											if (($serviceareapage->post_parent == $serviceareaancestor_id) ||
											   ($serviceareapage->post_parent == $serviceareapost->post_parent) ||
											   ($serviceareapage->post_parent == $serviceareapost->ID))
											{
											  $serviceareaincl .= $serviceareapage->ID . ",";
											}
											}?>
                                            	<li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Service Areas<span class="caret"></span></a>    
                                                <ul class="dropdown-menu">
                                                <?php wp_list_pages(array("child_of" => $serviceareaancestor_id, "include" => $serviceareaincl, "link_before" => "", "title_li" => "", "sort_column" => "menu_order"));?>
                                                </ul>              
                                            </li>			
                               <?php }?>
  						<?php }?>
                        
                        
                        
                        
                        
                        
                        
                         
 
                        
	
                        <?php $blognav = do_shortcode('[bng_location id="customvar2"]');
                            if($blognav !== '') {?>
                          	<li><a href="<?php echo get_site_url(); ?>/<?php echo do_shortcode( '[bng_location id="customvar2"]' );?>" title="Blog at 360° Painting">Blog</a> </li> 
                       <?php }
                        ?>    
                        
            			 <?php $contactnav = do_shortcode('[bng_location id="button_5_url"]');
                            if($contactnav !== '') {?>
                          	<li><a href="<?php echo get_site_url(); ?>/<?php echo do_shortcode( '[bng_location id="button_5_url"]' );?>" title="Contact Us">Contact Us</a></li>
                       <?php } ?>	
                       
                       
                       
                       
                       
                       
                       <?php
							//get the value of Qiigo Variables CPT button3, supposed to be MediaRoom -----------------------------------------------------------
								$medianav = do_shortcode('[bng_location id="button_3_url"]');
								
							//is the media (button3) variable not empty?
							if($medianav !== '') {	 
                                //get the URL of the media page 	
                                    $mediaurl = site_url ('/'.$medianav);
                                //get the ID of the media page
                                    $mediapostid = url_to_postid( $medianav );                                
                                ?>
                                                       
                                <li style="padding-left:50px;"><a href="<?php echo get_site_url(); ?>/<?php echo do_shortcode( '[bng_location id="button_3_url"]' );?>" title="Media Room" style="color:#cccccc;">Media Room</a></li>            		
                               
  						<?php }?>
                        
                        
                        
                        
                        <?php
							//get the value of Qiigo Variables CPT button2, supposed to be services -----------------------------------------------------------
								$servicesnav = do_shortcode('[bng_location id="button_2_url"]');
								
							//is the services (button2) variable not empty?
							if($servicesnav !== '') {	 
                                //get the URL of the services page 	
                                    $servicesurl = site_url ('/'.$servicesnav);
                                //get the ID of the services page
                                    $servicespostid = url_to_postid( $servicesnav );                                
                                //check if ID has children
                                    $servicesancestor_id=$servicespostid;
                                    $servicesdescendants = get_pages(array('child_of' => $servicesancestor_id));
									//if no children, just show the normal li
									if (empty($servicesdescendants)) {	?>			
										<li><a href="<?php echo get_site_url(); ?>/<?php echo do_shortcode( '[bng_location id="button_2_url"]' );?>" title="Services">Services</a></li>			
									<?php }else 
									//if has children
									{	
											$servicesincl = "";	
											foreach ($servicesdescendants as $servicespage) {
											if (($servicespage->post_parent == $servicesancestor_id) ||
											   ($servicespage->post_parent == $servicespost->post_parent) ||
											   ($servicespage->post_parent == $servicespost->ID))
											{
											  $servicesincl .= $servicespage->ID . ",";
											}
											}?>
                                            	<li role="presentation" class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" style="color:#cccccc;">Services<span class="caret"></span></a>    
                                                <ul class="dropdown-menu">
                                                <?php wp_list_pages(array("child_of" => $servicesancestor_id, "include" => $servicesincl, "link_before" => "", "title_li" => "", "sort_column" => "menu_order"));?>
                                                <li><a href="#">Tester1</a></li><li><a href="#">Tester2</a></li><li><a href="#">Tester3</a></li><li><a href="#">Tester4</a></li><li><a href="#">Tester5</a></li><li><a href="#">Tester6</a></li><li><a href="#">Tester7</a></li><li><a href="#">Tester8</a></li>
                                                </ul>              
                                            </li>			
                               <?php }?>
  						<?php }?>


                       
 
                       
                    </ul>						