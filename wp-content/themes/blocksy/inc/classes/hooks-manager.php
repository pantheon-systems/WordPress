<?php

namespace Blocksy;

class WpHooksManager {
	private $tokens = [];

	public function redirect_callbacks($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'token' => '',
				'source' => [],
				'destination' => ''
			]
		);

		blocksy_assert_args($args, ['token', 'source', 'destination']);

		global $wp_filter;

		if (! isset($wp_filter[$args['destination']])) {
			$wp_filter[$args['destination']] = new \WP_Hook();
		}

		foreach ($args['source'] as $source_id) {
			if (! isset($wp_filter[$source_id])) {
				continue;
			}

			$this->tokens[$args['token']][$source_id] = $wp_filter[$source_id];
		}

		$this->tokens[$args['token']][$args['destination']] = $wp_filter[$args['destination']]->callbacks;

		foreach ($args['source'] as $source_id) {
			if (! isset($wp_filter[$source_id])) {
				continue;
			}

			$source_callbacks = $wp_filter[$source_id]->callbacks;

			foreach ($source_callbacks as $priority => $callbacks) {
				if (! isset($wp_filter[$args['destination']]->callbacks[$priority])) {
					$wp_filter[$args['destination']]->callbacks[$priority] = [];
				}

				$wp_filter[$args['destination']]->callbacks[$priority] = array_merge(
					$wp_filter[$args['destination']]->callbacks[$priority],
					$callbacks
				);
			}

			$this->tokens[$args['token']][$source_id] = $wp_filter[$source_id];
			unset($wp_filter[$source_id]);
		}
	}

	// For now callback rolling is not needed, but it may be needed eventually
	/*
	public function rollback_callbacks($args = []) {
		$args = wp_parse_args(
			$args,
			[
				'token' => '',
				'source' => '',
				'destination' => ''
			]
		);

		blocksy_assert_args($args, ['token', 'source', 'destination']);

		if (
			! isset($this->tokens[$args['token']])
			||
			! isset($this->tokens[$args['token']][$args['destination']])
		) {
			return;
		}

		global $wp_filter;

		foreach ($args['source'] as $source_id) {
			if (! isset($this->tokens[$args['token']][$source_id])) {
				continue;
			}

			$wp_filter[$source_id] = $this->tokens[$args['token']][$source_id];
		}

		$wp_filter[$args['destination']]->callbacks = $this->tokens[$args['token']][$args['destination']];
	}
	 */
}

