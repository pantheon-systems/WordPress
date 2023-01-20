<?php

$options = [

	blocksy_rand_md5() => [
		'title' => __( 'General', 'blocksy-companion' ),
		'type' => 'tab',
		'options' => [
			'product_review_entity' => [
				'label' => __( 'Review Entity', 'blocksy-companion' ),
				'type' => 'ct-select',
				'value' => 'Thing',
				'view' => 'text',
				'design' => 'inline',
				'choices' => blocksy_ordered_keys(
					[
						'Thing' => __( 'Default', 'blocksy-companion' ),
						'Product' => __( 'Product', 'blocksy-companion' ),
						'Book' => __( 'Book', 'blocksy-companion' ),
						// 'Course' => __( 'Course', 'blocksy-companion' ),
						'CreativeWorkSeason' => __( 'Creative Work Season', 'blocksy-companion' ),
						'CreativeWorkSeries' => __( 'Creative Work Series', 'blocksy-companion' ),
						'Episode' => __( 'Episode', 'blocksy-companion' ),
						// 'Event' => __( 'Event', 'blocksy-companion' ),
						'Game' => __( 'Game', 'blocksy-companion' ),
						// 'HowTo' => __( 'How To', 'blocksy-companion' ),
						'LocalBusiness' => __( 'Local Business', 'blocksy-companion' ),
						'MediaObject' => __( 'Media Object', 'blocksy-companion' ),
						'Movie' => __( 'Movie', 'blocksy-companion' ),
						'MusicPlaylist' => __( 'Music Playlist', 'blocksy-companion' ),
						'MusicRecording' => __( 'Music Recording', 'blocksy-companion' ),
						'Organization' => __( 'Organization', 'blocksy-companion' ),
						// 'Recipe' => __( 'Recipe', 'blocksy-companion' ),
						// 'SoftwareApplication' => __( 'Software Application', 'blocksy-companion' ),
					]
				),
				'desc' => sprintf(
					__(
						'More info about review entity and how to choose the right one can be found %shere%s.',
						'blocksy-companion'
					),
					'<a href="https://developers.google.com/search/blog/2019/09/making-review-rich-results-more-helpful" target="_blank">',
					'</a>'
				),
			],

			blocksy_rand_md5() => [
				'type' => 'ct-condition',
				'condition' => ['product_review_entity' => 'Product'],
				'options' => [

					blocksy_rand_md5() => [
						'type' => 'ct-group',
						'label' => ' ',
						'wrapperAttr' => ['data-design' => 'inline'],
						'options' => [

							blocksy_rand_md5() => [
								'type' => 'ct-notification',
								'text' => __( 'Please note that some of this information (price, sku, brand) won\'t be displayed on the front-end. It is solely used for Google\'s Schema.org markup.', 'blocksy-companion' ),
								'attr' => ['data-type' => 'background:yellow']
							],

						],
					],

					'product_entity_price' => [
						'type' => 'text',
						'label' => __('Product Price', 'blocksy-companion'),
						'design' => 'inline',
						'value' => '',
					],

					'product_entity_sku' => [
						'type' => 'text',
						'label' => __('Product SKU', 'blocksy-companion'),
						'design' => 'inline',
						'value' => '',
					],

					'product_entity_brand' => [
						'type' => 'text',
						'label' => __('Product Brand', 'blocksy-companion'),
						'design' => 'inline',
						'value' => '',
					],
				]
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'gallery' => [
				'type' => 'ct-multi-image-uploader',
				'label' => __('Gallery', 'blocksy-companion'),
				'design' => 'inline',
				'value' => []
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'product_button_label' => [
				'type' => 'text',
				'label' => __('Affiliate Button Label', 'blocksy-companion'),
				'design' => 'inline',
				'value' => __('Buy Now', 'blocksy-companion')
			],

			'product_link' => [
				'type' => 'text',
				'label' => __('Affiliate Link', 'blocksy-companion'),
				'design' => 'inline',
				'value' => '#'
			],

			'product_link_target' => [
				'label' => __( 'Open Link In New Tab', 'blocksy-companion' ),
				'type'  => 'ct-switch',
				'value' => 'no',
			],

			'product_link_sponsored' => [
				'label' => __( 'Sponsored Attribute', 'blocksy-companion' ),
				'type'  => 'ct-switch',
				'value' => 'no',
			],

			/*
			'product_button_icon' => [
				'type' => 'icon-picker',
				'label' => __('Button Icon', 'blocksy-companion'),
				'design' => 'inline',
				'value' => [
					'icon' => 'fas fa-shopping-cart'
				]
			],
			 */

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'product_read_content_button_label' => [
				'type' => 'text',
				'label' => __('Read More Button Label', 'blocksy-companion'),
				'design' => 'inline',
				'value' => __('Read More', 'blocksy-companion')
			],

			/*
			'product_read_content_button_icon' => [
				'type' => 'icon-picker',
				'label' => __('Button Icon', 'blocksy-companion'),
				'design' => 'inline',
				'value' => [
					'icon' => 'fas fa-arrow-down'
				]
			],
			 */

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'product_description' => [
				'type' => 'wp-editor',
				'label' => __('Short Description', 'blocksy-companion'),
				'value' => '',
				'design' => 'inline',
			],

		],
	],

	blocksy_rand_md5() => [
		'title' => __( 'Rating', 'blocksy-companion' ),
		'type' => 'tab',
		'options' => [

			'scores' => [
				'type' => 'ct-addable-box',
				'label' => __('Scores', 'blocksy-companion'),
				'design' => 'inline',
				'preview-template' => '<%= label %> (<%= score === 1 ? "1 star" : score + " stars" %>)',

				'inner-options' => [
					'label' => [
						'type' => 'text',
						'value' => 'Default'
					],

					'score' => [
						'type' => 'ct-number',
						'value' => 5,
						'step' => 0.1,
						'min' => 1,
						'max' => 5
					]
				],

				'value' => [
					/*
					[
						'label' => 'Features',
						'score' => 5
					],

					[
						'label' => 'Quality',
						'score' => 5
					]
					 */
				]
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'product_specs' => [
				'type' => 'ct-addable-box',
				'label' => __('Product specs', 'blocksy-companion'),
				'design' => 'inline',
				'preview-template' => '<%= label %>',

				'inner-options' => [
					'label' => [
						'type' => 'text',
						'value' => 'Default'
					],

					'value' => [
						'type' => 'text',
						'value' => ''
					]
				],

				'value' => []
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'product_pros' => [
				'type' => 'ct-addable-box',
				'label' => __('Pros', 'blocksy-companion'),
				'design' => 'inline',
				'preview-template' => '<%= label %>',

				'inner-options' => [
					'label' => [
						'type' => 'text',
						'value' => 'Default'
					],
				],

				'value' => []
			],

			blocksy_rand_md5() => [
				'type' => 'ct-divider',
			],

			'product_cons' => [
				'type' => 'ct-addable-box',
				'label' => __('Cons', 'blocksy-companion'),
				'design' => 'inline',
				'preview-template' => '<%= label %>',

				'inner-options' => [
					'label' => [
						'type' => 'text',
						'value' => 'Default'
					],
				],

				'value' => []
			],

		],
	],

	// blocksy_rand_md5() => [
	// 	'title' => __( 'Design', 'blocksy-companion' ),
	// 	'type' => 'tab',
	// 	'options' => [

	// 	],
	// ],
];

