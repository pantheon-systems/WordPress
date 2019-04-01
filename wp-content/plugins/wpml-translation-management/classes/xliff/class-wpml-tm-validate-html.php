<?php

/**
 * Class WPML_TM_Validate_HTML
 */
class WPML_TM_Validate_HTML {

	/** @var string Validated html */
	private $html = '';

	/** @var array Tags currently open */
	private $tags = array();

	/** @var int Number of errors */
	private $error_count = 0;

	/**
	 * Get validated html.
	 *
	 * @return string
	 */
	public function get_html() {
		return $this->html;
	}

	/**
	 * Validate html.
	 *
	 * @param string $html HTML to process.
	 *
	 * @return int Number of errors.
	 */
	public function validate( $html ) {
		$html = $this->hide_wp_bugs( $html );
		$html = $this->hide_cdata( $html );
		$html = $this->hide_comments( $html );
		$html = $this->hide_self_closing_tags( $html );
		$html = $this->hide_scripts( $html );
		$html = $this->hide_styles( $html );

		$processed_html = '';
		$html_arr       = array(
			'processed' => $processed_html,
			'next'      => $html,
		);
		while ( '' !== $html_arr['next'] ) {
			$html_arr = $this->validate_next( $html_arr['next'] );
			if ( $html_arr ) {
				$processed_html .= $html_arr['processed'];
			}
		}

		$html = $processed_html;

		$html = $this->restore_styles( $html );
		$html = $this->restore_scripts( $html );
		$html = $this->restore_self_closing_tags( $html );
		$html = $this->restore_comments( $html );
		$html = $this->restore_wp_bugs( $html );

		$this->html = $html;

		return $this->error_count;
	}

	/**
	 * Validate first tag in html flow and return processed html and rest.
	 * In processed part broken html is replaced by wpml comment.
	 *
	 * @param string $html HTML to process.
	 *
	 * @return array|null
	 */
	private function validate_next( $html ) {
		$regs = array();

		// Get first opening or closing tag.
		$pattern = '<\s*?([a-z]+|/[a-z]+)((?:.|\s)*?)>';
		mb_eregi( $pattern, $html, $regs );

		if ( $regs ) {
			$full_tag  = $regs[0];
			$pos       = mb_strpos( $html, $full_tag );
			$next_html = mb_substr( $html, $pos + mb_strlen( $full_tag ) );

			$tag    = $regs[1];
			$result = true;
			if ( '/' === mb_substr( $tag, 0, 1 ) ) {
				$result = $this->close_tag( mb_substr( $tag, 1 ) );
			} else {
				$this->open_tag( $tag );
			}

			if ( $result ) {
				$processed_html = mb_substr( $html, 0, $pos + mb_strlen( $full_tag ) );
			} else {
				$processed_html = mb_substr( $html, 0, $pos ) . '<!-- wpml:html_fragment ' . $full_tag . ' -->';
			}

			return array(
				'processed' => $processed_html,
				'next'      => $next_html,
			);
		}

		return array(
			'processed' => $html,
			'next'      => '',
		);
	}

	/**
	 * Convert WP bugs into wpml commented bugs.
	 *
	 * @param string $html HTML to process.
	 *
	 * @return false|string
	 */
	private function hide_wp_bugs( $html ) {
		// WP bug fix for comments - in case you REALLY meant to type '< !--'
		$html = str_replace( '< !--', '<    !--', $html );
		// WP bug fix for LOVE <3 (and other situations with '<' before a number)
		$pattern = '<([0-9]{1})';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'hide_wp_bug_callback' ), $html, 'msri' );

		return $html;
	}

	/**
	 * Callback to convert WP bugs into wpml commented bugs.
	 *
	 * @param array $matches
	 *
	 * @return string
	 */
	public function hide_wp_bug_callback( $matches ) {
		return '<!-- wpml:wp_bug ' . $matches[0] . ' -->';
	}

	/**
	 * Convert wpml commented bugs to WP bugs.
	 *
	 * @param $html
	 *
	 * @return false|string
	 */
	private function restore_wp_bugs( $html ) {
		$pattern = '<!-- wpml:wp_bug (.*?) -->';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'restore_bug_callback' ), $html, 'msri' );
		$html    = str_replace( '<    !--', '< !--', $html );

		return $html;
	}

	/**
	 * Callback to convert wpml commented bugs to WP bugs.
	 *
	 * @param array $matches
	 *
	 * @return mixed
	 */
	public function restore_bug_callback( $matches ) {
		return $matches[1];
	}

	/**
	 * Convert HTML comments into wpml comments.
	 *
	 * @param string $html HTML to process.
	 *
	 * @return false|string
	 */
	private function hide_comments( $html ) {
		$pattern = '<!--(.*?)-->';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'hide_comment_callback' ), $html, 'msri' );

		$pattern = '<!((?!-- wpml:).*?)>';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'hide_declaration_callback' ), $html, 'msri' );

		return $html;
	}

	/**
	 * Callback to convert HTML comment to wpml comment.
	 *
	 * @param array $matches
	 *
	 * @return string
	 */
	public function hide_comment_callback( $matches ) {
		return '<!-- wpml:html_comment ' . base64_encode( $matches[0] ) . ' -->';
	}

	/**
	 * Callback to convert HTML declaration to wpml declaration.
	 *
	 * @param array $matches
	 *
	 * @return string
	 */
	public function hide_declaration_callback( $matches ) {
		return '<!-- wpml:html_declaration ' . base64_encode( $matches[0] ) . ' -->';
	}

	/**
	 * Convert wpml comments to HTML comments.
	 *
	 * @param string $html
	 *
	 * @return string
	 */
	private function restore_comments( $html ) {
		$pattern = '<!-- wpml:html_comment (.*?) -->';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'restore_encoded_content_callback' ), $html, 'msri' );

		$pattern = '<!-- wpml:html_declaration (.*?) -->';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'restore_encoded_content_callback' ), $html, 'msri' );

		return $html;
	}

	/**
	 * Callback to convert wpml base64 encoded content.
	 *
	 * @param array $matches
	 *
	 * @return string
	 */
	public function restore_encoded_content_callback( $matches ) {
		return base64_decode( $matches[1] );
	}

	/**
	 * Convert self-closing tags to wpml self-closing tags.
	 *
	 * @param string $html HTML to process.
	 *
	 * @return false|string
	 */
	private function hide_self_closing_tags( $html ) {
		$self_closing_tags = array(
			'area',
			'base',
			'basefont',
			'br',
			'col',
			'command',
			'embed',
			'frame',
			'hr',
			'img',
			'input',
			'isindex',
			'link',
			'meta',
			'param',
			'source',
			'track',
			'wbr',
			'command',
			'keygen',
			'menuitem',
			'path',
			'polyline',
		);
		foreach ( $self_closing_tags as $self_closing_tag ) {
			$pattern = '<\s*?' . $self_closing_tag . '((?:.|\s)*?)(>|/>)';
			$html    = mb_ereg_replace_callback( $pattern, array( $this, 'hide_sct_callback' ), $html, 'msri' );
		}

		$pattern = '<\s*?[^>]*/>';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'hide_sct_callback' ), $html, 'msri' );

		return $html;
	}

	/**
	 * Callback to convert self-closing tags to wpml self-closing tags.
	 *
	 * @param $matches
	 *
	 * @return string
	 */
	public function hide_sct_callback( $matches ) {
		return '<!-- wpml:html_self_closing_tag ' . str_replace( array( '<', '>' ), '', $matches[0] ) . ' -->';
	}

	/**
	 * Convert wpml self-closing tags to HTML self-closing tags.
	 *
	 * @param $html
	 *
	 * @return false|string
	 */
	private function restore_self_closing_tags( $html ) {
		$pattern = '<!-- wpml:html_self_closing_tag (.*?) -->';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'restore_sct_callback' ), $html, 'msri' );

		return $html;
	}

	/**
	 * Callback to convert wpml self-closing tags to self-closing tags.
	 *
	 * @param $matches
	 *
	 * @return string
	 */
	public function restore_sct_callback( $matches ) {
		return '<' . $matches[1] . '>';
	}

	/**
	 * Convert wpml comments to initial HTML.
	 *
	 * @param $html
	 *
	 * @return false|string
	 */
	public function restore_html( $html ) {
		$html = $this->restore_cdata( $html );
		$html = $this->restore_html_fragments( $html );
		return $html;
	}
	/**
	 * Convert wpml fragments to HTML fragments.
	 *
	 * @param $html
	 *
	 * @return false|string
	 */
	private function restore_html_fragments( $html ) {
		$pattern = '<!-- wpml:html_fragment (.*?) -->';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'restore_html_fragment_callback' ), $html, 'msri' );

		return $html;
	}

	/**
	 * Callback to convert wpml fragment to HTML fragment.
	 *
	 * @param $matches
	 *
	 * @return string
	 */
	public function restore_html_fragment_callback( $matches ) {
		return $matches[1];
	}

	/**
	 * Convert scripts to wpml scripts.
	 *
	 * @param string $html HTML to process.
	 *
	 * @return false|string
	 */
	private function hide_scripts( $html ) {
		$pattern = '<\s*?script\s*?>((?:.|\s)*?)</script\s*?>';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'hide_script_callback' ), $html, 'msri' );

		return $html;
	}

	/**
	 * Callback to convert script to wpml script.
	 *
	 * @param array $matches
	 *
	 * @return string
	 */
	public function hide_script_callback( $matches ) {
		return '<!-- wpml:script ' . base64_encode( $matches[0] ) . ' -->';
	}

	/**
	 * Convert wpml scripts to scripts.
	 *
	 * @param $html
	 *
	 * @return false|string
	 */
	private function restore_scripts( $html ) {
		$pattern = '<!-- wpml:script (.*?) -->';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'restore_encoded_content_callback' ), $html, 'msri' );

		return $html;
	}

	/**
	 * Convert CDATA to wpml cdata.
	 *
	 * @param string $html HTML to process.
	 *
	 * @return false|string
	 */
	private function hide_cdata( $html ) {
		$pattern = '<!\[CDATA\[((?:.|\s)*?)\]\]>';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'hide_cdata_callback' ), $html, 'msri' );

		return $html;
	}

	/**
	 * Callback to convert CDATA to wpml cdata.
	 *
	 * @param array $matches
	 *
	 * @return string
	 */
	public function hide_cdata_callback( $matches ) {
		return '<!-- wpml:cdata ' . base64_encode( $matches[0] ) . ' -->';
	}

	/**
	 * Convert wpml cdata to CDATA.
	 *
	 * @param $html
	 *
	 * @return false|string
	 */
	private function restore_cdata( $html ) {
		$pattern = '<!-- wpml:cdata (.*?) -->';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'restore_encoded_content_callback' ), $html, 'msri' );

		return $html;
	}

	/**
	 * Convert styles to wpml scripts.
	 *
	 * @param string $html HTML to process.
	 *
	 * @return false|string
	 */
	private function hide_styles( $html ) {
		$pattern = '<\s*?style\s*?>((?:.|\s)*?)</style\s*?>';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'hide_style_callback' ), $html, 'msri' );

		return $html;
	}

	/**
	 * Callback to convert style to wpml style.
	 *
	 * @param array $matches
	 *
	 * @return string
	 */
	public function hide_style_callback( $matches ) {
		return '<!-- wpml:style ' . base64_encode( $matches[0] ) . ' -->';
	}

	/**
	 * Convert wpml styles to styles.
	 *
	 * @param $html
	 *
	 * @return false|string
	 */
	private function restore_styles( $html ) {
		$pattern = '<!-- wpml:style (.*?) -->';
		$html    = mb_ereg_replace_callback( $pattern, array( $this, 'restore_encoded_content_callback' ), $html, 'msri' );

		return $html;
	}

	/**
	 * Open tag encountered in html.
	 *
	 * @param string $tag Tag name.
	 */
	private function open_tag( $tag ) {
		$tag = mb_strtolower( $tag );
		array_push( $this->tags, $tag );
	}

	/**
	 * Close tag encountered in html.
	 *
	 * @param string $tag Tag name.
	 *
	 * @return bool Closed successfully.
	 */
	private function close_tag( $tag ) {
		$tag      = mb_strtolower( $tag );
		$last_tag = end( $this->tags );
		if ( $last_tag === $tag ) {
			array_pop( $this->tags );

			return true;
		} else {
			$this->error_count ++;
			if ( in_array( $tag, $this->tags, true ) ) {
				do {
					array_pop( $this->tags );
				} while ( $this->tags && end( $this->tags ) !== $tag );
				array_pop( $this->tags );
			}

			return false;
		}
	}
}
