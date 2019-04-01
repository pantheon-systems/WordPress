<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<script type="text/javascript" id="vc_role_access_manager_script">
	(function ( $ ) {
		var _localCapabilities, _check, _groupAccessRules, _shortcodesPartSet, _mergedCaps;
		_localCapabilities = <?php echo json_encode( vc_user_roles_get_all() ); ?>;
		_shortcodesPartSet = <?php echo vc_bc_access_get_shortcodes_state_is_set( vc_user_access()->part( 'shortcodes' )->getRole() ) ? 'true' : 'false'; ?>;
		_groupAccessRules = <?php echo json_encode( array_merge( array( 'current_user' => wp_get_current_user()->roles ), (array) vc_settings()->get( 'groups_access_rules' ) ) ); ?>;
		_mergedCaps = <?php echo json_encode( vc_user_access()->part( 'shortcodes' )->getMergedCaps() ) ?>;
		_check = function ( part, rule, custom, not_check_state ) {
			var state, partObj, findRule;

			partObj = _.isUndefined( _localCapabilities[ part ] ) ? {} : _localCapabilities[ part ];
			rule = vc_user_access().updateMergedCaps(rule);
			if ( ! not_check_state ) {
				state = _.isUndefined( partObj.state ) ? false : partObj.state; // if we don't have state it is incorrect part
				if ( null === state ) {
					return true;
				} else if ( _.isBoolean( state ) ) {
					return state;
				}
			}

			findRule = (
				_.isUndefined( partObj.capabilities ) ||
				_.isUndefined( partObj.capabilities[ rule ] )
			) ? false : partObj.capabilities[ rule ];

			return _.isBoolean( findRule ) ? findRule : findRule === custom;
		};
		// global function
		window.vc_user_access = function () {
			return {
				editor: function ( editor ) {
					return this.partAccess( editor );
				},
				partAccess: function ( editor ) {
					return ! _.isUndefined( _localCapabilities[ editor ] ) && false !== _localCapabilities[ editor ][ 'state' ];
				},
				check: function ( part, rule, custom, not_check_state ) {
					return _check( part, rule, custom, not_check_state );
				},
				getState: function ( part ) {
					var state, partObj;

					partObj = _.isUndefined( _localCapabilities[ 'shortcodes' ] ) ? {} : _localCapabilities[ part ];
					state = _.isUndefined( partObj.state ) ? false : partObj.state;

					return state;
				},
				shortcodeAll: function ( shortcode ) {
					if ( ! _shortcodesPartSet ) {
						return this.shortcodeValidateOldMethod( shortcode );
					}
					var state = this.getState( 'shortcodes' );
					if ( state === 'edit' ) {
						return false;
					}
					return _check( 'shortcodes', shortcode + '_all' );
				},
				shortcodeEdit: function ( shortcode ) {
					if ( ! _shortcodesPartSet ) {
						return this.shortcodeValidateOldMethod( shortcode );
					}

					var state = this.getState( 'shortcodes' );
					if ( state === 'edit' ) {
						return true;
					}
					return _check( 'shortcodes', shortcode + '_all' ) || _check( 'shortcodes', shortcode + '_edit' );
				},
				shortcodeValidateOldMethod: function ( shortcode ) {
					if ( 'vc_row' === shortcode ) {
						return true;
					}
					return _.every( _groupAccessRules.current_user, function ( role ) {
						return ! (! _.isUndefined( _groupAccessRules[ role ] ) && ! _.isUndefined( _groupAccessRules[ role ][ 'shortcodes' ] ) && _.isUndefined( _groupAccessRules[ role ][ 'shortcodes' ][ shortcode ] ));
					} );
				},
				updateMergedCaps: function ( rule ) {
					if ( undefined !== _mergedCaps[ rule ] ) {
						return _mergedCaps[ rule ];
					}
					return rule;
				}
			};
		};
		<?php do_action( 'vc_acesss_manager-js' ); ?>
	})( window.jQuery );
</script>
