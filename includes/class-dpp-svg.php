<?php
/**
 * SVG upload feature.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_DPP_SVG {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		// The admin-post action is always safe to register — it checks capability inside.
		add_action( 'admin_post_pkwt_svg_scan', array( $this, 'scan_existing_svgs' ) );

		// Always skip thumbnail generation for SVGs — this is safe for ALL uploads
		// because the callbacks check the MIME type before doing anything.
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'skip_svg_metadata' ), 10, 2 );
		add_filter( 'intermediate_image_sizes_advanced', array( $this, 'skip_svg_thumbnails' ), 10, 2 );

		// Upload-altering hooks are only registered when the SVG module is enabled.
		// Registering them unconditionally can corrupt normal (non-SVG) media uploads.
		if ( ! $this->is_enabled() ) {
			return;
		}

		add_filter( 'upload_mimes', array( $this, 'allow_svg_mime' ) );
		// Priority 75 so our forced type/ext wins over core's late real-MIME reset (which
		// sniffs SVG as text/plain and would otherwise block the upload).
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'fix_svg_filetype' ), 75, 5 );
		// Validate AND sanitize on the prefilter so a malicious SVG never reaches disk.
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'validate_svg_upload' ) );
		add_filter( 'wp_handle_sideload_prefilter', array( $this, 'validate_svg_upload' ) );
		add_filter( 'wp_prepare_attachment_for_js', array( $this, 'prepare_svg_preview' ), 10, 3 );
	}

	/**
	 * Skip thumbnail generation metadata for SVG attachments.
	 * WordPress image processors (GD/Imagick) cannot process SVGs and throw
	 * "server cannot process the image" errors without this bypass.
	 *
	 * @param array<string,mixed> $metadata    Generated metadata (may be empty for SVG).
	 * @param int                 $attachment_id Attachment ID.
	 * @return array<string,mixed>
	 */
	public function skip_svg_metadata( array $metadata, int $attachment_id ): array {
		if ( 'image/svg+xml' !== get_post_mime_type( $attachment_id ) ) {
			return $metadata;
		}
		// Return minimal metadata so WP stores the attachment without errors.
		$file = get_attached_file( $attachment_id );
		return array(
			'file'   => $file ? basename( $file ) : '',
			'width'  => 0,
			'height' => 0,
			'sizes'  => array(),
		);
	}

	/**
	 * Prevent WordPress from trying to generate image sub-sizes for SVGs.
	 *
	 * @param array<string,mixed>[] $sizes     Image sizes to generate.
	 * @param array<string,mixed>   $image_meta Image meta data.
	 * @return array<string,mixed>[]
	 */
	public function skip_svg_thumbnails( array $sizes, array $image_meta ): array {
		// If the original file is an SVG, skip all sub-size generation.
		$file = isset( $image_meta['file'] ) ? (string) $image_meta['file'] : '';
		if ( 'svg' === strtolower( (string) pathinfo( $file, PATHINFO_EXTENSION ) ) ) {
			return array();
		}
		return $sizes;
	}

	/**
	 * Get settings.
	 *
	 * @return array<string,mixed>
	 */
	private function get_settings(): array {
		$defaults = array(
			'dpp_svg_enabled'          => 0,
			'dpp_svg_roles'            => array( 'administrator', 'editor' ),
			'dpp_svg_preview'          => 1,
			'dpp_svg_max_size_kb'      => 512,
			'dpp_svg_strictness'       => 'standard',
			'dpp_svg_blocked_log'      => 0,
			'dpp_svg_auto_clean_days'  => 30,
		);
		$saved = get_option( 'pkwt_dpp_svg_settings', array() );
		return wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
	}

	/**
	 * Check if enabled.
	 *
	 * @return bool
	 */
	private function is_enabled(): bool {
		$settings = $this->get_settings();
		return ! empty( $settings['dpp_svg_enabled'] );
	}

	/**
	 * Check if current user can upload SVG.
	 *
	 * @return bool
	 */
	private function can_current_user_upload_svg(): bool {
		$settings = $this->get_settings();
		$roles    = isset( $settings['dpp_svg_roles'] ) && is_array( $settings['dpp_svg_roles'] ) ? $settings['dpp_svg_roles'] : array();
		$user     = wp_get_current_user();
		if ( ! $user instanceof \WP_User ) {
			return false;
		}
		foreach ( $user->roles as $role ) {
			if ( in_array( $role, $roles, true ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Allow SVG mime type.
	 *
	 * @param array<string,string> $mimes Mimes.
	 * @return array<string,string>
	 */
	public function allow_svg_mime( array $mimes ): array {
		if ( ! $this->is_enabled() || ! $this->can_current_user_upload_svg() ) {
			return $mimes;
		}
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

	/**
	 * Fix SVG filetype detection.
	 *
	 * @param array<string,mixed> $data Values.
	 * @param string              $file File.
	 * @param string              $filename Filename.
	 * @param array<string,mixed> $mimes Mimes.
	 * @param string|false        $real_mime Real mime.
	 * @return array<string,mixed>
	 */
	public function fix_svg_filetype( array $data, string $file, string $filename, $mimes, $real_mime ): array {
		if ( 'svg' === strtolower( (string) pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		}
		return $data;
	}

	/**
	 * Validate size/cap before upload.
	 *
	 * @param array<string,mixed> $file File.
	 * @return array<string,mixed>
	 */
	public function validate_svg_upload( array $file ): array {
		$filename = isset( $file['name'] ) ? (string) $file['name'] : '';
		if ( 'svg' !== strtolower( (string) pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
			return $file;
		}

		if ( ! $this->is_enabled() || ! $this->can_current_user_upload_svg() ) {
			$file['error'] = __( 'SVG uploads are disabled for your account.', 'powerplus-toolkit' );
			return $file;
		}

		$settings = $this->get_settings();
		$max_kb   = isset( $settings['dpp_svg_max_size_kb'] ) ? max( 64, absint( $settings['dpp_svg_max_size_kb'] ) ) : 512;
		$size     = isset( $file['size'] ) ? (int) $file['size'] : 0;
		if ( $size > ( $max_kb * 1024 ) ) {
			$file['error'] = sprintf(
				/* translators: %d size in KB */
				__( 'SVG exceeds maximum allowed size (%d KB).', 'powerplus-toolkit' ),
				$max_kb
			);
			return $file;
		}

		// Sanitize the temp file IN PLACE before WordPress moves it into the uploads dir,
		// so a malicious SVG is never written to a web-accessible location.
		$tmp = isset( $file['tmp_name'] ) ? (string) $file['tmp_name'] : '';
		if ( '' === $tmp || ! is_uploaded_file( $tmp ) ) {
			$file['error'] = __( 'Could not read uploaded SVG.', 'powerplus-toolkit' );
			return $file;
		}
		$contents = file_get_contents( $tmp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $contents ) {
			$file['error'] = __( 'Could not read uploaded SVG.', 'powerplus-toolkit' );
			return $file;
		}
		$result = $this->sanitize_svg_markup( $contents );
		if ( empty( $result['safe'] ) ) {
			$file['error'] = __( 'SVG content is unsafe and was blocked.', 'powerplus-toolkit' );
			return $file;
		}
		file_put_contents( $tmp, (string) $result['safe'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		$this->maybe_log_blocked( $filename, isset( $result['removed'] ) ? (array) $result['removed'] : array() );

		return $file;
	}

	/**
	 * Sanitize SVG markup server-side using a DOM-based ALLOWLIST.
	 *
	 * A regex blocklist fails open (unquoted handlers, data: URIs, CDATA, entity tricks),
	 * so this parses the document and keeps only known-safe elements/attributes, strips the
	 * DOCTYPE (XXE), removes every event handler, and enforces an href protocol allowlist.
	 *
	 * @param string $svg Raw svg.
	 * @return array<string,mixed>  array{ safe:string, removed:string[] }
	 */
	private function sanitize_svg_markup( string $svg ): array {
		$settings   = $this->get_settings();
		$strictness = isset( $settings['dpp_svg_strictness'] ) ? sanitize_key( (string) $settings['dpp_svg_strictness'] ) : 'standard';
		$removed    = array();

		// Strip UTF-8 BOM and any stray PHP tags up front.
		$svg = preg_replace( '/^\xEF\xBB\xBF/', '', (string) $svg );
		$svg = preg_replace( '/<\?(?:php|=).*?\?>/is', '', (string) $svg );

		// Reject any DOCTYPE / internal subset outright — it is the XXE vector and has no
		// legitimate use in an uploaded asset. Catch entity declarations too.
		if ( preg_match( '/<!DOCTYPE/i', $svg ) || preg_match( '/<!ENTITY/i', $svg ) ) {
			return array( 'safe' => '', 'removed' => array( 'doctype_or_entity' ) );
		}

		if ( '' === trim( $svg ) ) {
			return array( 'safe' => '', 'removed' => array( 'empty' ) );
		}

		// Disable external entity loading on libxml < 2.9 (no-op/deprecated after, where it
		// is already the default). Suppresses XXE on older stacks.
		if ( \LIBXML_VERSION < 20900 && function_exists( 'libxml_disable_entity_loader' ) ) {
			libxml_disable_entity_loader( true ); // phpcs:ignore Generic.PHP.DeprecatedFunctions.Deprecated
		}
		$prev_errors = libxml_use_internal_errors( true );

		$dom                     = new \DOMDocument();
		$dom->preserveWhiteSpace = false;
		// NEVER pass LIBXML_NOENT (that would expand entities). LIBXML_NONET blocks network.
		$loaded = $dom->loadXML( $svg, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );
		libxml_clear_errors();
		libxml_use_internal_errors( $prev_errors );

		if ( ! $loaded || ! $dom->documentElement || 'svg' !== strtolower( $dom->documentElement->nodeName ) ) {
			return array( 'safe' => '', 'removed' => array( 'unparseable' ) );
		}

		// Tag allowlist. 'standard' permits the common presentational set; 'strict'/'paranoid'
		// narrow it. Dangerous elements (script, foreignObject, etc.) are never listed.
		$base_tags = array(
			'svg', 'g', 'title', 'desc', 'defs', 'symbol', 'use', 'metadata',
			'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon',
			'text', 'tspan', 'textpath', 'clippath', 'mask', 'pattern',
			'lineargradient', 'radialgradient', 'stop', 'marker',
			'filter', 'fegaussianblur', 'feoffset', 'feblend', 'fecolormatrix',
			'femerge', 'femergenode', 'fecomposite', 'feflood', 'switch',
		);
		$narrow_tags = array(
			'svg', 'g', 'title', 'desc', 'defs', 'symbol', 'path', 'rect', 'circle',
			'ellipse', 'line', 'polyline', 'polygon', 'clippath', 'mask',
			'lineargradient', 'radialgradient', 'stop',
		);
		$allowed_tags = in_array( $strictness, array( 'strict', 'paranoid' ), true ) ? $narrow_tags : $base_tags;
		// <use> can pull in external content; only allow it in the permissive 'standard' mode
		// and only with same-document fragment refs (enforced in the attribute pass below).
		$allow_use = ( 'standard' === $strictness );

		$href_attrs   = array( 'href', 'xlink:href' );
		$walker_remove = array();

		$all = $dom->getElementsByTagName( '*' );
		// Snapshot into an array because the live NodeList mutates as we remove nodes.
		$elements = array();
		foreach ( $all as $el ) {
			$elements[] = $el;
		}

		foreach ( $elements as $el ) {
			$tag = strtolower( $el->nodeName );
			if ( ! in_array( $tag, $allowed_tags, true ) || ( 'use' === $tag && ! $allow_use ) ) {
				$walker_remove[] = $el;
				$removed[]       = 'tag:' . $tag;
				continue;
			}

			if ( ! $el->hasAttributes() ) {
				continue;
			}
			// Collect first (live NamedNodeMap mutates during removal).
			$attrs = array();
			foreach ( $el->attributes as $attr ) {
				$attrs[] = $attr;
			}
			foreach ( $attrs as $attr ) {
				$name  = strtolower( $attr->nodeName );
				$value = (string) $attr->nodeValue;

				// 1) Strip ALL event handlers (covers unquoted/obfuscated cases since the DOM
				//    already parsed them into discrete attributes).
				if ( 0 === strpos( $name, 'on' ) ) {
					$el->removeAttribute( $attr->nodeName );
					$removed[] = 'event_handler';
					continue;
				}
				// 2) Block style values that smuggle url()/expression()/javascript.
				if ( 'style' === $name && preg_match( '/url\s*\(|expression\s*\(|javascript:|@import/i', $value ) ) {
					$el->removeAttribute( $attr->nodeName );
					$removed[] = 'unsafe_style';
					continue;
				}
				// 3) href/xlink:href value allowlist: same-doc fragment, site-relative, http(s),
				//    or data:image/(png|gif|jpeg|webp). Everything else (javascript:, data:text,
				//    vbscript:, protocol-relative //host) is dropped.
				if ( in_array( $name, $href_attrs, true ) ) {
					$decoded = html_entity_decode( $value, ENT_QUOTES | ENT_HTML5 );
					$decoded = preg_replace( '/\s+/', '', $decoded ); // defeat "java\nscript:".
					$ok      = (bool) preg_match( '#^(?:\#|/(?!/)|https?:/|data:image/(?:png|gif|jpe?g|webp);base64,)#i', (string) $decoded );
					if ( 'use' === $tag ) {
						// <use> may only reference an in-document fragment.
						$ok = (bool) preg_match( '/^#/', (string) $decoded );
					}
					if ( ! $ok ) {
						$el->removeAttribute( $attr->nodeName );
						$removed[] = 'unsafe_href';
					}
				}
			}
		}

		foreach ( $walker_remove as $node ) {
			if ( $node->parentNode ) {
				$node->parentNode->removeChild( $node );
			}
		}

		$safe = (string) $dom->saveXML( $dom->documentElement );
		$safe = trim( $safe );

		return array(
			'safe'    => $safe,
			'removed' => array_values( array_unique( $removed ) ),
		);
	}

	/**
	 * Add preview metadata for SVG in media modal.
	 *
	 * @param array<string,mixed> $response Response.
	 * @param \WP_Post            $attachment Attachment.
	 * @param array<string,mixed> $meta Meta.
	 * @return array<string,mixed>
	 */
	public function prepare_svg_preview( array $response, \WP_Post $attachment, array $meta ): array {
		$settings = $this->get_settings();
		if ( empty( $settings['dpp_svg_preview'] ) ) {
			return $response;
		}
		if ( 'image/svg+xml' !== get_post_mime_type( $attachment ) ) {
			return $response;
		}
		if ( empty( $response['sizes'] ) || ! is_array( $response['sizes'] ) ) {
			$response['sizes'] = array();
		}
		$url = wp_get_attachment_url( $attachment->ID );
		if ( $url ) {
			$response['sizes']['full'] = array(
				'url'         => esc_url_raw( $url ),
				'width'       => 512,
				'height'      => 512,
				'orientation' => 'landscape',
			);
		}
		return $response;
	}

	/**
	 * Scan existing SVG files.
	 *
	 * @return void
	 */
	public function scan_existing_svgs(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerplus-toolkit' ) );
		}
		check_admin_referer( 'pkwt_svg_scan' );

		$query = new \WP_Query(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'post_mime_type' => 'image/svg+xml',
				'posts_per_page' => 200,
				'fields'         => 'ids',
			)
		);

		$results = array();
		foreach ( $query->posts as $attachment_id ) {
			$path = get_attached_file( (int) $attachment_id );
			if ( ! $path || ! file_exists( $path ) ) {
				continue;
			}
			$raw = file_get_contents( $path );
			if ( false === $raw ) {
				continue;
			}
			$scan      = $this->sanitize_svg_markup( $raw );
			$removed   = isset( $scan['removed'] ) ? (array) $scan['removed'] : array();
			$is_flagged = ! empty( $removed );
			$results[] = array(
				'file'   => basename( $path ),
				'issue'  => $is_flagged ? implode( ', ', $removed ) : __( 'No issues', 'powerplus-toolkit' ),
				'status' => $is_flagged ? 'flagged' : 'clean',
			);
		}

		set_transient( 'pkwt_svg_scan_results', $results, 10 * MINUTE_IN_SECONDS );
		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=svg-upload&pkwt_notice=svg_scanned' ) );
		exit;
	}

	/**
	 * Log blocked elements.
	 *
	 * @param string   $filename Filename.
	 * @param string[] $removed Removed markers.
	 * @return void
	 */
	private function maybe_log_blocked( string $filename, array $removed ): void {
		$settings = $this->get_settings();
		if ( empty( $settings['dpp_svg_blocked_log'] ) || empty( $removed ) ) {
			return;
		}

		$log = get_option( 'pkwt_dpp_svg_log', array() );
		if ( ! is_array( $log ) ) {
			$log = array();
		}

		$cutoff = time() - ( 30 * DAY_IN_SECONDS );
		$log    = array_values(
			array_filter(
				$log,
				static function ( $entry ) use ( $cutoff ) {
					return isset( $entry['time'] ) && (int) $entry['time'] >= $cutoff;
				}
			)
		);

		$log[] = array(
			'time'     => time(),
			'file'     => sanitize_text_field( $filename ),
			'removed'  => implode( ', ', array_map( 'sanitize_text_field', $removed ) ),
		);
		update_option( 'pkwt_dpp_svg_log', $log, false );
	}
}
