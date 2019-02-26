<?php

$meatGeneral = array('thumb-size'	=> array(	'label'		=> __('Images thumb size', $this->plugin_meta['shortname']),
																								'desc'		=> __('Enter integer value for thumb size for images', $this->plugin_meta['shortname']),
																								'id'			=> $this->plugin_meta['shortname'].'_thumb_size',
																								'type'			=> 'text',
																								'default'		=> '75',
																								'help'			=> __('e.g: <strong>100</strong>', $this->plugin_meta['shortname'])
																								),
					'post-redirect'		=>  array(	'label'		=> __('Redirect?', $this->plugin_meta['shortname']),
																								'desc'		=> __('Type redirect url after form submitted, leave blank for alert on same page', $this->plugin_meta['shortname']),
																								'id'			=> $this->plugin_meta['shortname'].'_redirect_url',
																								'type'			=> 'text',
																								'default'		=> '',
																								),
					'filetype-error'	=> array('label'		=> __('File type not supported message', $this->plugin_meta['shortname']),
							'desc'		=> __('This message will be shown invalid file type is selected', $this->plugin_meta['shortname']),
							'id'			=> $this->plugin_meta['shortname'].'_filetype_error',
							'type'			=> 'textarea',
							'default'		=> '',
							'help'			=> ''),
		
					'save-forms'		=>  array(	'label'		=> __('Do you want to save forms?', $this->plugin_meta['shortname']),
							'desc'		=> __('Do you want to save every form as Custom post type: <code>nm-forms</code>?', $this->plugin_meta['shortname']),
							'id'			=> $this->plugin_meta['shortname'].'_save_forms',
							'type'			=> 'checkbox',
							'default'		=> '',
							'options'		=> array('yes'	=> 'Yes', 'No'	=> 'No'),
					),
					);
					

$meatAddon = array('pro-feature'	=> array(	'desc'		=> $proFeatures,
		'type'		=> 'file',
		'id'		=> 'get-addon.php',
),);
$this -> the_options = array('general-settings'	=> array(	'name'		=> __('Basic Setting', $this->plugin_meta['shortname']),
														'type'	=> 'tab',
														'desc'	=> __('Set options as per your need', $this->plugin_meta['shortname']),
														'meat'	=> $meatGeneral,
														
													),
		'addon'	=> array(	'name'		=> __('Awesome Photo Editing', $this->plugin_meta['shortname']),
				'type'	=> 'tab',
				'desc'	=> __('Following features are only available in PRO version', $this->plugin_meta['shortname']),
				'meat'	=> $meatAddon,
				'class'	=> 'pro',
		
		),
	
					);

//print_r($repo_options);