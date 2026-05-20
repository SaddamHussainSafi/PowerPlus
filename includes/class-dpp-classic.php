<?php
/**
 * Classic Editor mode feature.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_DPP_Classic {

	/**
	 * Option key.
	 *
	 * @var string
	 */
	private $option_key = 'pkwt_dpp_classic_settings';

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'use_block_editor_for_post_type', array( $this, 'filter_use_block_editor_for_post_type' ), 10, 2 );
		add_filter( 'use_block_editor_for_post', array( $this, 'filter_use_block_editor_for_post' ), 10, 2 );
		add_filter( 'gutenberg_use_widgets_block_editor', array( $this, 'filter_widgets_block_editor' ) );
		add_filter( 'use_widgets_block_editor', array( $this, 'filter_widgets_block_editor' ) );
		add_action( 'admin_menu', array( $this, 'maybe_disable_fse_menu' ), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'cleanup_admin_block_assets' ), 99 );
		add_filter( 'should_load_remote_block_patterns', array( $this, 'filter_remote_patterns' ) );
		add_action( 'init', array( $this, 'maybe_unregister_block_patterns' ), 100 );
		add_filter( 'block_directory_enabled', array( $this, 'filter_block_directory_enabled' ) );
		add_filter( 'block_editor_settings_all', array( $this, 'filter_block_editor_settings' ), 10, 2 );
		add_filter( 'tiny_mce_before_init', array( $this, 'filter_tinymce_settings' ) );
		add_filter( 'wp_default_editor', array( $this, 'filter_default_editor_tab' ) );
		add_filter( 'user_can_richedit', array( $this, 'filter_user_can_richedit' ) );
		add_action( 'show_user_profile', array( $this, 'render_user_preference_field' ) );
		add_action( 'edit_user_profile', array( $this, 'render_user_preference_field' ) );
		add_action( 'personal_options_update', array( $this, 'save_user_preference_field' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_user_preference_field' ) );
		add_action( 'admin_notices', array( $this, 'maybe_render_editor_notice' ) );
	}

	/**
	 * Register option.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting( 'pkwt_dpp_settings_group', $this->option_key, array( $this, 'sanitize_settings' ) );
	}

	/**
	 * Default settings.
	 *
	 * @return array<string,mixed>
	 */
	private function defaults(): array {
		return array(
			'dpp_classic_enabled'             => 0,
			'dpp_classic_post_types'          => $this->get_all_public_post_types(),
			'dpp_classic_scope'               => 'all',
			'dpp_classic_allow_user_choice'   => 0,
			'dpp_classic_allow_admin_bypass'  => 1,
			'dpp_classic_remove_block_css'    => 1,
			'dpp_classic_remove_block_js'     => 1,
			'dpp_classic_disable_fse'         => 1,
			'dpp_classic_disable_widgets'     => 1,
			'dpp_classic_remove_patterns'     => 0,
			'dpp_classic_remove_block_dir'    => 1,
			'dpp_classic_toolbar_style'       => 'full',
			'dpp_classic_kitchen_sink'        => 1,
			'dpp_classic_default_editor_tab'  => 'visual',
			'dpp_classic_show_notice'         => 0,
			'dpp_classic_notice_text'         => __( 'Classic Editor is active. Contact your admin for help.', 'powerplus-toolkit' ),
			'dpp_classic_enabled_at'          => 0,
		);
	}

	/**
	 * Get settings.
	 *
	 * @return array<string,mixed>
	 */
	public function get_settings(): array {
		$saved = get_option( $this->option_key, array() );
		$data  = wp_parse_args( is_array( $saved ) ? $saved : array(), $this->defaults() );
		if ( empty( $data['dpp_classic_post_types'] ) || ! is_array( $data['dpp_classic_post_types'] ) ) {
			$data['dpp_classic_post_types'] = $this->get_all_public_post_types();
		}
		return $data;
	}

	/**
	 * Sanitize settings.
	 *
	 * @param mixed $settings Raw settings.
	 * @return array<string,mixed>
	 */
	public function sanitize_settings( $settings ): array {
		if ( ! is_array( $settings ) ) {
			return $this->get_settings();
		}
		$current  = $this->get_settings();
		$output   = $this->defaults();

		$bool_keys = array(
			'dpp_classic_enabled',
			'dpp_classic_allow_user_choice',
			'dpp_classic_allow_admin_bypass',
			'dpp_classic_remove_block_css',
			'dpp_classic_remove_block_js',
			'dpp_classic_disable_fse',
			'dpp_classic_disable_widgets',
			'dpp_classic_remove_patterns',
			'dpp_classic_remove_block_dir',
			'dpp_classic_kitchen_sink',
			'dpp_classic_show_notice',
		);
		foreach ( $bool_keys as $key ) {
			$output[ $key ] = empty( $settings[ $key ] ) ? 0 : 1;
		}

		$scope = isset( $settings['dpp_classic_scope'] ) ? sanitize_key( (string) $settings['dpp_classic_scope'] ) : 'all';
		$output['dpp_classic_scope'] = in_array( $scope, array( 'all', 'new' ), true ) ? $scope : 'all';

		$toolbar = isset( $settings['dpp_classic_toolbar_style'] ) ? sanitize_key( (string) $settings['dpp_classic_toolbar_style'] ) : 'full';
		$output['dpp_classic_toolbar_style'] = in_array( $toolbar, array( 'full', 'basic', 'minimal' ), true ) ? $toolbar : 'full';

		$tab = isset( $settings['dpp_classic_default_editor_tab'] ) ? sanitize_key( (string) $settings['dpp_classic_default_editor_tab'] ) : 'visual';
		$output['dpp_classic_default_editor_tab'] = in_array( $tab, array( 'visual', 'html' ), true ) ? $tab : 'visual';

		$output['dpp_classic_notice_text'] = isset( $settings['dpp_classic_notice_text'] ) ? sanitize_text_field( wp_unslash( $settings['dpp_classic_notice_text'] ) ) : $output['dpp_classic_notice_text'];

		$post_types = array();
		if ( isset( $settings['dpp_classic_post_types'] ) && is_array( $settings['dpp_classic_post_types'] ) ) {
			foreach ( $settings['dpp_classic_post_types'] as $post_type ) {
				$post_types[] = sanitize_key( (string) $post_type );
			}
		}
		$output['dpp_classic_post_types'] = ! empty( $post_types ) ? array_values( array_unique( $post_types ) ) : $this->get_all_public_post_types();

		$enabled_now  = ! empty( $output['dpp_classic_enabled'] );
		$enabled_prev = ! empty( $current['dpp_classic_enabled'] );
		if ( $enabled_now && ! $enabled_prev ) {
			$output['dpp_classic_enabled_at'] = time();
		} else {
			$output['dpp_classic_enabled_at'] = isset( $current['dpp_classic_enabled_at'] ) ? absint( $current['dpp_classic_enabled_at'] ) : 0;
		}

		return $output;
	}

	/**
	 * Filter block editor by post type.
	 *
	 * @param bool   $use_block_editor Use block editor.
	 * @param string $post_type Post type.
	 * @return bool
	 */
	public function filter_use_block_editor_for_post_type( bool $use_block_editor, string $post_type ): bool {
		if ( ! $this->is_enabled() ) {
			return $use_block_editor;
		}
		if ( $this->should_allow_bypass_for_current_user() ) {
			return $use_block_editor;
		}
		return $this->is_classic_enabled_for_post_type( $post_type ) ? false : $use_block_editor;
	}

	/**
	 * Filter block editor by post.
	 *
	 * @param bool          $use_block_editor Use block editor.
	 * @param int|\WP_Post  $post Post object.
	 * @return bool
	 */
	public function filter_use_block_editor_for_post( bool $use_block_editor, $post ): bool {
		if ( ! $this->is_enabled() ) {
			return $use_block_editor;
		}

		if ( $this->should_allow_bypass_for_current_user() ) {
			return $use_block_editor;
		}

		$preference = $this->get_user_editor_preference();
		if ( 'block' === $preference ) {
			return true;
		}
		if ( 'classic' === $preference ) {
			return false;
		}

		$post_obj = $post instanceof \WP_Post ? $post : get_post( (int) $post );
		if ( ! $post_obj ) {
			return $use_block_editor;
		}

		if ( ! $this->is_classic_enabled_for_post_type( $post_obj->post_type ) ) {
			return $use_block_editor;
		}

		$settings = $this->get_settings();
		if ( 'new' === $settings['dpp_classic_scope'] ) {
			$enabled_at = absint( $settings['dpp_classic_enabled_at'] );
			$post_time  = strtotime( (string) $post_obj->post_date_gmt );
			if ( $enabled_at > 0 && $post_time > 0 && $post_time < $enabled_at ) {
				return $use_block_editor;
			}
		}

		return false;
	}

	/**
	 * Widget block editor filter.
	 *
	 * @param bool $use_widgets_block_editor Value.
	 * @return bool
	 */
	public function filter_widgets_block_editor( bool $use_widgets_block_editor ): bool {
		if ( ! $this->is_enabled() ) {
			return $use_widgets_block_editor;
		}
		$settings = $this->get_settings();
		if ( empty( $settings['dpp_classic_disable_widgets'] ) ) {
			return $use_widgets_block_editor;
		}
		return false;
	}

	/**
	 * Disable full site editor menu.
	 *
	 * @return void
	 */
	public function maybe_disable_fse_menu(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}
		$settings = $this->get_settings();
		if ( empty( $settings['dpp_classic_disable_fse'] ) ) {
			return;
		}
		remove_submenu_page( 'themes.php', 'site-editor.php' );
	}

	/**
	 * Remove block assets in admin.
	 *
	 * @return void
	 */
	public function cleanup_admin_block_assets(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}

		// Never strip block scripts on the Elementor editor screen — Elementor
		// depends on wp-editor, wp-blocks, etc. and removing them breaks the editor.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only query arg check for editor detection.
		if ( isset( $_GET['action'] ) && 'elementor' === sanitize_key( wp_unslash( $_GET['action'] ) ) ) {
			return;
		}

		$settings = $this->get_settings();

		if ( ! empty( $settings['dpp_classic_remove_block_css'] ) ) {
			foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'global-styles' ) as $handle ) {
				wp_dequeue_style( $handle );
			}
		}
		if ( ! empty( $settings['dpp_classic_remove_block_js'] ) ) {
			foreach ( array( 'wp-edit-post', 'wp-editor', 'wp-edit-widgets', 'wp-customize-widgets', 'wp-blocks' ) as $handle ) {
				wp_dequeue_script( $handle );
			}
		}
	}

	/**
	 * Disable remote patterns.
	 *
	 * @param bool $should_load Value.
	 * @return bool
	 */
	public function filter_remote_patterns( bool $should_load ): bool {
		if ( ! $this->is_enabled() ) {
			return $should_load;
		}
		$settings = $this->get_settings();
		return empty( $settings['dpp_classic_remove_patterns'] ) ? $should_load : false;
	}

	/**
	 * Disable block directory.
	 *
	 * @param bool $enabled Value.
	 * @return bool
	 */
	public function filter_block_directory_enabled( bool $enabled ): bool {
		if ( ! $this->is_enabled() ) {
			return $enabled;
		}
		$settings = $this->get_settings();
		return empty( $settings['dpp_classic_remove_block_dir'] ) ? $enabled : false;
	}

	/**
	 * Unregister all block patterns.
	 *
	 * @return void
	 */
	public function maybe_unregister_block_patterns(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}
		$settings = $this->get_settings();
		if ( empty( $settings['dpp_classic_remove_patterns'] ) || ! class_exists( 'WP_Block_Patterns_Registry' ) ) {
			return;
		}
		$registry = \WP_Block_Patterns_Registry::get_instance();
		$patterns = $registry->get_all_registered();
		foreach ( $patterns as $name => $pattern ) {
			$registry->unregister( (string) $name );
		}
	}

	/**
	 * Adjust block editor settings.
	 *
	 * @param array<string,mixed> $settings Block editor settings.
	 * @return array<string,mixed>
	 */
	public function filter_block_editor_settings( array $settings ): array {
		if ( ! $this->is_enabled() ) {
			return $settings;
		}
		$cfg = $this->get_settings();
		if ( ! empty( $cfg['dpp_classic_remove_block_dir'] ) ) {
			$settings['enableOpenverseMediaCategory'] = false;
			$settings['__experimentalBlockDirectory'] = false;
		}
		return $settings;
	}

	/**
	 * Adjust TinyMCE toolbar.
	 *
	 * @param array<string,mixed> $init Init.
	 * @return array<string,mixed>
	 */
	public function filter_tinymce_settings( array $init ): array {
		if ( ! $this->is_enabled() ) {
			return $init;
		}
		$settings = $this->get_settings();
		$style    = (string) $settings['dpp_classic_toolbar_style'];
		if ( 'basic' === $style ) {
			$init['toolbar1'] = 'bold,italic,link,bullist,numlist,undo,redo';
			$init['toolbar2'] = '';
		} elseif ( 'minimal' === $style ) {
			$init['toolbar1'] = '';
			$init['toolbar2'] = '';
		}
		$init['wordpress_adv_hidden'] = empty( $settings['dpp_classic_kitchen_sink'] ) ? '1' : '0';
		return $init;
	}

	/**
	 * Default editor tab.
	 *
	 * @param string $editor Editor.
	 * @return string
	 */
	public function filter_default_editor_tab( string $editor ): string {
		if ( ! $this->is_enabled() ) {
			return $editor;
		}
		$settings = $this->get_settings();
		return 'html' === $settings['dpp_classic_default_editor_tab'] ? 'html' : 'tinymce';
	}

	/**
	 * Disable rich edit for minimal mode.
	 *
	 * @param bool $can_richedit Current value.
	 * @return bool
	 */
	public function filter_user_can_richedit( bool $can_richedit ): bool {
		if ( ! $this->is_enabled() ) {
			return $can_richedit;
		}
		$settings = $this->get_settings();
		if ( 'minimal' === $settings['dpp_classic_toolbar_style'] ) {
			return false;
		}
		return $can_richedit;
	}

	/**
	 * Show user profile preference field.
	 *
	 * @param \WP_User $user User.
	 * @return void
	 */
	public function render_user_preference_field( \WP_User $user ): void {
		if ( ! $this->is_enabled() ) {
			return;
		}
		$settings = $this->get_settings();
		if ( empty( $settings['dpp_classic_allow_user_choice'] ) ) {
			return;
		}
		$current = get_user_meta( $user->ID, 'dpp_editor_preference', true );
		$current = in_array( $current, array( 'classic', 'block' ), true ) ? $current : '';
		?>
		<h2><?php esc_html_e( 'Editor Preference', 'powerplus-toolkit' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th><label for="dpp_editor_preference"><?php esc_html_e( 'Preferred editor', 'powerplus-toolkit' ); ?></label></th>
				<td>
					<select name="dpp_editor_preference" id="dpp_editor_preference">
						<option value="" <?php selected( $current, '' ); ?>><?php esc_html_e( 'Use site default', 'powerplus-toolkit' ); ?></option>
						<option value="classic" <?php selected( $current, 'classic' ); ?>><?php esc_html_e( 'Classic', 'powerplus-toolkit' ); ?></option>
						<option value="block" <?php selected( $current, 'block' ); ?>><?php esc_html_e( 'Block', 'powerplus-toolkit' ); ?></option>
					</select>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save user preference.
	 *
	 * @param int $user_id User ID.
	 * @return void
	 */
	public function save_user_preference_field( int $user_id ): void {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Core profile form nonce validation.
		$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'update-user_' . $user_id ) ) {
			return;
		}
		$settings = $this->get_settings();
		if ( empty( $settings['dpp_classic_allow_user_choice'] ) ) {
			delete_user_meta( $user_id, 'dpp_editor_preference' );
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Profile nonce verified above.
		$pref = isset( $_POST['dpp_editor_preference'] ) ? sanitize_key( wp_unslash( $_POST['dpp_editor_preference'] ) ) : '';
		if ( ! in_array( $pref, array( 'classic', 'block' ), true ) ) {
			delete_user_meta( $user_id, 'dpp_editor_preference' );
			return;
		}
		update_user_meta( $user_id, 'dpp_editor_preference', $pref );
	}

	/**
	 * Show classic notice on edit screens.
	 *
	 * @return void
	 */
	public function maybe_render_editor_notice(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}
		$settings = $this->get_settings();
		if ( empty( $settings['dpp_classic_show_notice'] ) ) {
			return;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'post' !== $screen->base ) {
			return;
		}
		$text = ! empty( $settings['dpp_classic_notice_text'] ) ? (string) $settings['dpp_classic_notice_text'] : __( 'Classic Editor is active. Contact your admin for help.', 'powerplus-toolkit' );
		echo '<div class="notice notice-info is-dismissible"><p>' . esc_html( $text ) . '</p></div>';
	}

	/**
	 * Get status report rows.
	 *
	 * @return array<int,array<string,string>>
	 */
	public function get_status_rows(): array {
		$settings = $this->get_settings();
		$rows     = array();
			foreach ( $this->get_all_public_post_types() as $post_type ) {
				$rows[] = array(
					'status' => $this->is_classic_enabled_for_post_type( $post_type ) ? 'ok' : 'warn',
					/* translators: %s: post type slug. */
					'label'  => sprintf( __( 'Gutenberg disabled for %s', 'powerplus-toolkit' ), $post_type ),
				);
			}
		$rows[] = array(
			'status' => ! empty( $settings['dpp_classic_remove_block_css'] ) ? 'ok' : 'warn',
			'label'  => __( 'Block editor CSS removed', 'powerplus-toolkit' ),
		);
		$rows[] = array(
			'status' => ! empty( $settings['dpp_classic_remove_block_js'] ) ? 'ok' : 'warn',
			'label'  => __( 'Block editor JS removed', 'powerplus-toolkit' ),
		);
		$rows[] = array(
			'status' => ! empty( $settings['dpp_classic_disable_widgets'] ) ? 'ok' : 'warn',
			'label'  => __( 'Widget editor restored to classic', 'powerplus-toolkit' ),
		);
		$rows[] = array(
			'status' => ! empty( $settings['dpp_classic_disable_fse'] ) ? 'ok' : 'warn',
			'label'  => __( 'Full site editor disabled', 'powerplus-toolkit' ),
		);

			$user_query = new \WP_User_Query(
				array(
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Required count for plugin-owned user preference meta.
					'meta_key'     => 'dpp_editor_preference',
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Required count for plugin-owned user preference meta.
					'meta_value'   => 'block',
					'count_total'  => true,
					'fields'       => 'ID',
				'number'       => 1,
			)
		);
			$count = (int) $user_query->get_total();
			$rows[] = array(
				'status' => $count > 0 ? 'warn' : 'ok',
				/* translators: %d: number of users with block editor preference. */
				'label'  => sprintf( __( '%d users have personal editor preference set to Block', 'powerplus-toolkit' ), $count ),
			);
		return $rows;
	}

	/**
	 * Is master toggle enabled.
	 *
	 * @return bool
	 */
	private function is_enabled(): bool {
		$settings = $this->get_settings();
		return ! empty( $settings['dpp_classic_enabled'] );
	}

	/**
	 * Get user preference.
	 *
	 * @return string
	 */
	private function get_user_editor_preference(): string {
		$settings = $this->get_settings();
		if ( empty( $settings['dpp_classic_allow_user_choice'] ) || ! is_user_logged_in() ) {
			return '';
		}
		$pref = (string) get_user_meta( get_current_user_id(), 'dpp_editor_preference', true );
		return in_array( $pref, array( 'classic', 'block' ), true ) ? $pref : '';
	}

	/**
	 * Admin bypass check.
	 *
	 * @return bool
	 */
	private function should_allow_bypass_for_current_user(): bool {
		$settings = $this->get_settings();
		return ! empty( $settings['dpp_classic_allow_admin_bypass'] ) && current_user_can( 'manage_options' );
	}

	/**
	 * Is classic enabled for one post type.
	 *
	 * @param string $post_type Post type.
	 * @return bool
	 */
	private function is_classic_enabled_for_post_type( string $post_type ): bool {
		$settings   = $this->get_settings();
		$post_types = isset( $settings['dpp_classic_post_types'] ) && is_array( $settings['dpp_classic_post_types'] ) ? $settings['dpp_classic_post_types'] : $this->get_all_public_post_types();
		return in_array( $post_type, $post_types, true );
	}

	/**
	 * Get all public post types.
	 *
	 * @return array<int,string>
	 */
	private function get_all_public_post_types(): array {
		return array_values(
			get_post_types(
				array(
					'public' => true,
					'show_ui'=> true,
				),
				'names'
			)
		);
	}
}
