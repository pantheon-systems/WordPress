<?php if ( ! defined( 'ABSPATH' ) || ! class_exists( 'NF_Abstracts_Action' ) ) exit;

/**
 * The Affiliate_WP_Ninja_Forms_Add_Referral class.
 *
 * This class adds a Ninja Forms 3.0+ 'Action',
 * by extending the NF_Abstracts_Action class.
 *
 * @since 1.8.6
 * @link  ninja-forms/includes/Abstracts/Action.php
 */
final class Affiliate_WP_Ninja_Forms_Add_Referral extends NF_Abstracts_Action {
    /**
     * @var   string
     * @since 1.8.6
     */
    protected $_name  = 'affiliatewp_add_referral';

    /**
     * @var   array
     * @since 1.8.6
     */
    protected $_tags = array( 'affiliate', 'affiliatewp', 'referral' );

    /**
     * @var   string
     * @since 1.8.6
     */
    protected $_timing = 'late';

    /**
     * @var   string
     * @since 1.8.6
     */
    protected $_priority = '10';

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();

        $options = array();
        foreach( affiliate_wp()->referrals->types_registry->get_types() as $type_id => $type ) {
            $options[] = array(
                'label' => $type['label'],
                'value' => $type_id
            );
        }

        $this->_nicename = __( 'Add Referral', 'affiliate-wp' );

        $this->_settings[ 'affiliatewp_total' ] = array(
            'name'           => 'affiliatewp_total',
            'label'          => __( 'Total Field', 'affiliate-wp' ),
            'type'           => 'textbox',
            'width'          => 'full',
            'value'          => '',
            'group'          => 'primary',
            'use_merge_tags' => array(
                'exclude' => array(
                    'post',
                    'user',
                    'system'
                )
            )
        );

        $this->_settings[ 'affiliatewp_email' ] = array(
            'name'           => 'affiliatewp_email',
            'label'          => __( 'Customer Email', 'affiliate-wp' ),
            'type'           => 'textbox',
            'width'          => 'full',
            'value'          => '',
            'group'          => 'primary',
            'use_merge_tags' => array(
                'exclude' => array(
                    'user'
                )
            )
        );

        $this->_settings[ 'affiliatewp_description' ] = array(
            'name'           => 'affiliatewp_description',
            'label'          => __( 'Description', 'affiliate-wp' ),
            'type'           => 'textbox',
            'width'          => 'full',
            'value'          => '',
            'group'          => 'primary',
            'use_merge_tags' => array(
                'exclude' => array(
                    'post',
                    'user',
                    'system'
                )
            )
        );

        $this->_settings[ 'affiliatewp_referral_type' ] = array(
            'name'           => 'affiliatewp_referral_type',
            'label'          => __( 'Referral Type', 'affiliate-wp' ),
            'type'           => 'select',
            'options'        => $options,
            'width'          => 'full',
            'group'          => 'primary',
        );
    }

    /**
     * Public save method
     *
     * @since  1.8.6
     *
     * @param  array  $action_settings  [description]
     *
     */
    public function save( $action_settings ) {
    }

    /**
     * Processes the AffiliateWP referral.
     *
     * @since  1.8.6
     *
     * @param  array   $action_settings Form action settings.
     * @param  int     $form_id         The form ID.
     * @param  mixed   $data            Form data.
     *
     * @return mixed   $data            Form data.
     *
     */
    public function process( $action_settings, $form_id, $data ) {

        if( isset( $data['settings']['is_preview'] ) && $data['settings']['is_preview'] ){
            return $data;
        }

        if ( ! isset( $data[ 'actions' ][ 'save' ][ 'sub_id' ] ) ) {
            $sub = Ninja_Forms()->form( $form_id )->sub()->get();

            $hidden_field_types = apply_filters( 'nf_sub_hidden_field_types', array() );

            foreach( $data['fields'] as $field ){

                if( in_array( $field[ 'type' ], array_values( $hidden_field_types ) ) ) {
                    $data['actions']['save']['hidden'][] = $field['type'];
                    continue;
                }

                $sub->update_field_value( $field['id'], $field['value'] );
            }

            if( isset( $data[ 'extra' ] ) ) {
                $sub->update_extra_values( $data['extra'] );
            }

            /**
             * Fires when saving a Ninja Forms submission in Ninja Forms 3.0 or greater.
             *
             * @param int $sub->get_id() The Ninja Forms form submission ID.
             */
            do_action( 'nf_save_sub', $sub->get_id() );

            /**
             * Fires when saving a Ninja Forms submission in Ninja Forms versions lower than 3.0.
             *
             * @param int $sub->get_id() The Ninja Forms form submission ID.
             */
            do_action( 'ninja_forms_save_sub', $sub->get_id() );

            $sub->save();

            $data[ 'actions' ][ 'save' ][ 'sub_id' ] = $sub->get_id();

        }

        $total          = $this->get_total( $action_settings );
        $reference      = $data[ 'actions' ][ 'save' ][ 'sub_id' ];
        $description    = $this->get_description( $action_settings, $data );
        $customer_email = $this->get_customer_email( $action_settings );
        $type           = $this->get_type( $action_settings );
        $args           = $data[ 'extra' ][ 'affiliatewp' ] = compact( 'total', 'reference', 'description', 'customer_email', 'type' );

        /**
         * Fires when adding a referral via Ninja Forms.
         *
         * @param array $args Referral arguments.
         */
        do_action( 'nf_affiliatewp_add_referral', $args );

        return $data;
    }


    /**
     * Get the total of the form.
     *
     * @since  1.8.6
     *
     * @param  array  $action_settings  The form action settings.
     *
     * @return int                      The total amount of the form.
     */
    private function get_total( $action_settings ) {
        $total = 0;
        if( isset( $action_settings[ 'affiliatewp_total' ] ) ) {
            $total = $action_settings[ 'affiliatewp_total' ];
        }
        return $total;
    }

    /**
     * Get the referral reference.
     *
     * @since  1.8.6
     *
     * @param  mixed   $data       Form data.
     *
     * @return string  $reference  The referral reference.
     */
    private function get_reference( $data ) {
        $reference = '';
        if( isset( $data[ 'actions' ][ 'save' ][ 'id' ] ) ) {
            $reference = $data[ 'actions' ][ 'save' ][ 'id' ];
        }

        return $reference;
    }

    /**
     * Get the referral description.
     *
     * @since  1.8.6
     *
     * @param  array   $action_settings  The form action settings.
     * @param  mixed   $data             Form data.
     *
     * @return string  $description      The referral description. Will first check for
     *                                   an AffiliateWP custom description. Uses the form
     *                                   title if no custom description is defined.
     */
    private function get_description( $action_settings, $data ) {

        $description = '';

        $products    = $this->get_products( $data );

        if( ! empty( $products ) ) {
            $description = $products;
        }elseif( ! empty( $action_settings[ 'affiliatewp_description' ] ) ) {
            $description = $action_settings[ 'affiliatewp_description' ];
        } elseif( ! empty( $data[ 'settings' ][ 'title' ] ) ) {
            $description = $data[ 'settings' ][ 'title' ];
        }

        return $description;
    }

    /**
     * Get the customer email.
     *
     * @since  1.8.6
     *
     * @param  array  $action_settings The settings for this action.
     *
     * @return string $email           The customer email.
     */
    private function get_customer_email( $action_settings ) {
        $email = 0;
        if( isset( $action_settings[ 'affiliatewp_email' ] ) ) {
            $email = $action_settings[ 'affiliatewp_email' ];
        }
        return $email;
    }

    /**
     * Get the referral type.
     *
     * @since  2.2
     *
     * @param  array  $action_settings The settings for this action.
     *
     * @return string $type The referral type.
     */
    private function get_type( $action_settings ) {
        $type = 'sale';;
        if( isset( $action_settings[ 'affiliatewp_referral_type' ] ) ) {
            $type = $action_settings[ 'affiliatewp_referral_type' ];
        }
        return $type;
    }

    /**
     * Get the referral products.
     *
     * @since  2.1.16
     *
     * @param  mixed   $data             Form data.
     *
     * @return string  $products         The form products if the form has one or more product
     *                                   fields.
     */
    private function get_products( $data ) {
        $products = '';

        if( ! empty( $data['extra']['product_fields'] ) ) {

            $product_labels = array();

            foreach( $data['fields'] as $field ) {

                if( 'product' == $field[ 'type' ] ) {
                    $product_labels[] = $field['label'];
                    continue;
                }

            }

            $products = implode( ', ', $product_labels );

        }

        return $products;
    }
}
