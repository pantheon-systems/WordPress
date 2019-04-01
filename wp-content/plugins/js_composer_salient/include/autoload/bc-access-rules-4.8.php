<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
// Part BC: Post types
// =========================

function vc_bc_access_rule_48_post_type_get_state( $state ) {
	if ( null === $state ) {
		$content_types = vc_settings()->get( 'content_types' );
		if ( empty( $content_types ) ) {
			$state = true;
		} else {
			$state = 'custom';
		}
	}

	return $state;
}

function vc_bc_access_rule_48_post_type_rule( $value, $role, $rule ) {
	if ( ! $role ) {
		return $value;
	}
	global $vc_bc_access_rule_48_editor_post_types;
	$part = vc_role_access()->who( $role->name )->part( 'post_types' );
	if ( ! isset( $part->getRole()->capabilities[ $part->getStateKey() ] ) ) {
		if ( is_null( $vc_bc_access_rule_48_editor_post_types ) ) {
			$pt_array = vc_settings()->get( 'content_types' );
			$vc_bc_access_rule_48_editor_post_types = $pt_array ? $pt_array : vc_default_editor_post_types();
		}

		return in_array( $rule, $vc_bc_access_rule_48_editor_post_types );
	}

	return $value;
}

// Part BC: shortcodes
// =========================

function vc_bc_access_rule_48_shortcodes_get_state( $state, $role ) {
	if ( ! $role ) {
		return $state;
	}
	if ( null === $state ) {
		$group_access_settings = vc_settings()->get( 'groups_access_rules' );
		if ( ! isset( $group_access_settings[ $role->name ]['shortcodes'] ) ) {
			$state = true;
		} else {
			$state = 'custom';
		}
	}

	return $state;
}

function vc_bc_access_rule_48_shortcodes_rule( $value, $role, $rule ) {
	if ( ! $role ) {
		return $value;
	}
	if ( ! vc_bc_access_get_shortcodes_state_is_set( $role ) ) {
		if ( preg_match( '/_edit$/', $rule ) ) {
			return false;
		}
		$group_access_settings = vc_settings()->get( 'groups_access_rules' );
		if ( isset( $group_access_settings[ $role->name ]['shortcodes'] ) && ! empty( $group_access_settings[ $role->name ]['shortcodes'] ) ) {
			$rule = preg_replace( '/_all$/', '', $rule );

			return 'vc_row' === $rule || ( isset( $group_access_settings[ $role->name ]['shortcodes'][ $rule ] ) && 1 === (int) $group_access_settings[ $role->name ]['shortcodes'][ $rule ] );
		} else {
			return true;
		}
	}

	return $value;
}

/**
 * Check is state set
 *
 * @param $role
 *
 * @return bool
 */
function vc_bc_access_get_shortcodes_state_is_set( $role ) {
	if ( ! $role ) {
		return false;
	}
	$part = vc_role_access()->who( $role->name )->part( 'shortcodes' );

	return isset( $part->getRole()->capabilities[ $part->getStateKey() ] );
}

// Part BC: backened editor
// ===========================
function vc_bc_access_rule_48_backend_editor_get_state( $state, $role ) {
	if ( ! $role ) {
		return $state;
	}
	if ( null === $state ) {
		$group_access_settings = vc_settings()->get( 'groups_access_rules' );
		if ( ! isset( $group_access_settings[ $role->name ]['show'] ) || 'all' === $group_access_settings[ $role->name ]['show'] ) {
			$state = true;
		} elseif ( 'no' === $group_access_settings[ $role->name ]['show'] ) {
			$state = false;
		} else {
			$state = 'default';
		}
	}

	return $state;
}

function vc_bc_access_rule_48_frontend_editor_get_state( $state, $role ) {
	if ( ! $role ) {
		return $state;
	}
	if ( null === $state ) {
		$group_access_settings = vc_settings()->get( 'groups_access_rules' );

		if ( isset( $group_access_settings[ $role->name ]['show'] ) && 'no' === $group_access_settings[ $role->name ]['show'] ) {
			$state = false;
		} else {
			$state = true;
		}
	}

	return $state;
}

function vc_bc_access_rule_48_backend_editor_can_disabled_ce_editor_rule( $value, $role ) {
	if ( ! $role ) {
		return $value;
	}
	$part = vc_role_access()->who( $role->name )->part( 'backend_editor' );
	if ( ! isset( $part->getRole()->capabilities[ $part->getStateKey() ] ) ) {
		$group_access_settings = vc_settings()->get( 'groups_access_rules' );

		return isset( $group_access_settings[ $role->name ]['show'] ) && 'only' === $group_access_settings[ $role->name ]['show'];
	}

	return $value;
}

function vc_bc_access_rule_48_backend_editor_add_cap_disabled_ce_editor( $role ) {
	if ( ! $role ) {
		return $role;
	}
	$part = vc_role_access()->who( $role->name )->part( 'backend_editor' );
	if ( ! isset( $part->getRole()->capabilities[ $part->getStateKey() ] ) ) {
		$group_access_settings = vc_settings()->get( 'groups_access_rules' );
		if ( isset( $group_access_settings[ $role->name ]['show'] ) && 'only' === $group_access_settings[ $role->name ]['show'] ) {
			$role->capabilities[ $part->getStateKey() . '/disabled_ce_editor' ] = true;
		}
	}

	return $role;
}

function vc_bc_access_rule_48() {
	add_filter( 'vc_role_access_with_post_types_get_state', 'vc_bc_access_rule_48_post_type_get_state' );
	add_filter( 'vc_role_access_with_post_types_can', 'vc_bc_access_rule_48_post_type_rule', 10, 3 );

	add_filter( 'vc_role_access_with_shortcodes_get_state', 'vc_bc_access_rule_48_shortcodes_get_state', 10, 3 );
	add_filter( 'vc_role_access_with_shortcodes_can', 'vc_bc_access_rule_48_shortcodes_rule', 10, 3 );

	add_filter( 'vc_role_access_with_backend_editor_get_state', 'vc_bc_access_rule_48_backend_editor_get_state', 10, 3 );
	add_filter( 'vc_role_access_with_backend_editor_can_disabled_ce_editor', 'vc_bc_access_rule_48_backend_editor_can_disabled_ce_editor_rule', 10, 2 );

	add_filter( 'vc_role_access_with_frontend_editor_get_state', 'vc_bc_access_rule_48_frontend_editor_get_state', 10, 3 );

	add_filter( 'vc_role_access_all_caps_role', 'vc_bc_access_rule_48_backend_editor_add_cap_disabled_ce_editor' );
}

// BC function for shortcode
add_action( 'vc_before_init', 'vc_bc_access_rule_48' );
