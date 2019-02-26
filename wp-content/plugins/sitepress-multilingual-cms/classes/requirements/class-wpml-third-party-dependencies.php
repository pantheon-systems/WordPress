<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Third_Party_Dependencies {
	private $integrations;
	private $requirements;

	/**
	 * WPML_Third_Party_Dependencies constructor.
	 *
	 * @param WPML_Integrations $integrations
	 * @param WPML_Requirements $requirements
	 */
	public function __construct( WPML_Integrations $integrations, WPML_Requirements $requirements ) {
		$this->integrations = $integrations;
		$this->requirements = $requirements;
	}

	public function get_issues( $scope = null ) {
		$issues = array(
			'causes'       => array(),
			'requirements' => array(),
		);

		$components = $this->get_components( $scope );
		foreach ( (array) $components as $slug => $component_data ) {
			$issue = $this->get_issue( $component_data, $slug );
			if ( $issue ) {
				$issues['causes'][] = $issue['cause'];

				foreach ( $issue['requirements'] as $requirement ) {
					$issues['requirements'][] = $requirement;
				}
			}
		}

		sort( $issues['causes'] );
		sort( $issues['requirements'] );

		$issues['causes']       = array_unique( $issues['causes'], SORT_REGULAR );
		$issues['requirements'] = array_unique( $issues['requirements'], SORT_REGULAR );

		if ( ! $issues || ! $issues['causes'] || ! $issues['requirements'] ) {
			return array();
		}

		return $issues;
	}

	private function get_components( $scope ) {
		$components = $this->integrations->get_results();

		foreach ( $components as $index => $component ) {
			if (
				WPML_Integrations::SCOPE_WP_CORE === $component['type'] && WPML_Integrations::SCOPE_WP_CORE !== $scope ||
				WPML_Integrations::SCOPE_WP_CORE !== $component['type'] && WPML_Integrations::SCOPE_WP_CORE === $scope
			) {
				unset( $components[ $index ] );
			}
		}

		return $components;
	}

	private function get_issue( $component_data, $slug ) {
		$requirements = $this->requirements->get_requirements( $component_data['type'], $slug );
		if ( ! $requirements ) {
			return null;
		}

		return array(
			'cause'        => $component_data,
			'requirements' => $requirements,
		);
	}
}
