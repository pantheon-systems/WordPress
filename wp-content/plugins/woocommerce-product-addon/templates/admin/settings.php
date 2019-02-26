<?php 
/*
 * this file rendering admin options for the plugin
* options are defined in admin/admin-options.php
*/

$this -> load_template('admin/options.php');
//$this -> pa($this -> the_options);

//$this -> pa($this -> plugin_settings);

//this function rendering notice if any issue during installing plugin
//showNotice();

$sendUpdate = '';
?>


<h2>
	<?php echo $this->plugin_meta['name']?>
</h2>
<div id="tab-container" class="tab-container">
	<ul class='etabs'>
		<?php foreach($this -> the_options as $id => $option){
			
			?>

		<li class='tab'><a href="#<?php echo $id?>"><?php echo $option['name']?>
		</a></li>

		<?php }?>
	</ul>


	<?php foreach($this -> the_options as $id => $options){
		
		// reseting the update data array
		
		?>

	<div id="<?php echo $id?>" class="general-settings">
		<p>
			<?php echo $options['desc']?>
		</p>


		<ul>
			<?php foreach($options['meat'] as $key => $data){
			
				$sendUpdate[$data['id']] = array('type'	=> $data['type']);
				
				?>

			<li id="<?php echo $key?>" class="plugin-field-set">			
			<?php switch($data['type']){
					
				case 'text':
					?>
				<ul>
					<li><h4><?php echo $data['desc']?> </h4>
					<label for="<?php echo $data['id']?>"><?php echo $data['label']?> 
					<input type="text" name="<?php echo $data['id']?>" id="<?php echo $data['id']?>" value="<?php echo stripcslashes($this ->plugin_settings[ $data['id'] ])?>" class="regular-text">
					</label><br />
					<em><?php echo $data['help']?> </em> 
					</li>
				</ul> <?php 
				break;
				
				
				case 'textarea':
					$ta_val = stripcslashes($this ->plugin_settings[ $data['id'] ]);
					?>
								<ul>
									<li><h4><?php echo $data['desc']?> </h4>
									<label for="<?php echo $data['id']?>"><?php echo $data['label']?></label><br /> 
									<textarea cols="45" rows="6" name="<?php echo $data['id']?>" id="<?php echo $data['id']?>"><?php echo $ta_val?></textarea>
									<br />
									<em><?php echo $data['help']?> </em> 
									</li>
								</ul> 
				<?php 
				break;

				case 'checkbox':?>
				<ul>
					<li>
					<h4><?php echo $data['desc']?> </h4>
					
					<?php foreach($data['options'] as $k => $label){?>
					
						<label for="<?php echo $data['id'].'-'.$k?>"> <input type="checkbox" name="<?php echo $data['id']?>" id="<?php echo $data['id'].'-'.$k?>" value="<?php echo $k?>"> <?php echo $label?>
						</label>
					<?php }?>
					
					<br />
					<em><?php echo $data['help']?> </em> 
					</li>
					<!-- setting value -->
					<script>
					setChecked('<?php echo $data['id']?>', '<?php echo json_encode($this ->plugin_settings[ $data['id'] ])?>');
					</script>
				</ul>
				
								
				<?php break;
				
				
				case 'radio':?>
								<ul>
									<li>
									<h4><?php echo $data['desc']?> </h4>
									
									<?php foreach($data['options'] as $k => $label){?>
									
										<label for="<?php echo $data['id'].'-'.$k?>"> <input type="radio" name="<?php echo $data['id']?>" id="<?php echo $data['id'].'-'.$k?>" value="<?php echo $k?>"> <?php echo $label?>
										</label>
									<?php }?>
									
									<br />
									<em><?php echo $data['help']?> </em> 
									</li>
									<script>
									setCheckedRadio('<?php echo $data['id']?>', '<?php echo $this ->plugin_settings[ $data['id'] ]?>');
									</script>
								</ul>
								
												
				<?php break;
				
				case 'select':?>
								<ul>
									<li>
									<h4><?php echo $data['desc']?> </h4>
									
										<label for="<?php echo $data['id']?>"><?php echo $data['label']?> 										 
										<select name="<?php echo $data['id']?>" id="<?php echo $data['id']?>">
											<option value=""><?php echo $data['default']?></option>
											
											<?php foreach($data['options'] as $k => $label){
												
													$selected = ($k == $this ->plugin_settings[ $data['id'] ]) ? 'selected = "selected"' : '';
													
													echo '<option value="'.$k.'" '.$selected.'>'.$label.'</option>';
											}
												?>
											
										</select> 
										</label>
									
									<br />
									<em><?php echo $data['help']?> </em>
									</li>
								</ul>
								
								<?php break;
								
			case 'para':?>
											<ul>
												<li>
												<h4><?php echo $data['desc']?> </h4>
												
												<br />
												<em><?php echo $data['help']?> </em>
												</li>
											</ul>
											
											<?php break;
			
		case 'file':?>
													<ul>
														<li>
														<?php 
														$file = $this->plugin_meta['path'] .'/templates/admin/'.$data['id'];
														if(file_exists($file))
															include $file;
														else 	
															echo 'file not exists '.$file;
														?> 
														</li>
													</ul>
													
													<?php break;

			} ?></li>
			<?php }
			
			?>
		</ul>
		
		
	</div>

	<?php 
	}
	?>
	
	<p><button class="button button-primary" onclick=updateOptions('<?php echo json_encode($sendUpdate)?>')><?php _e('Setting settings', "ppom")?></button></p>
</div>
