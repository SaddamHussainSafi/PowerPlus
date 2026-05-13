<?php
/**
 * DPP admin UI and settings.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_DPP_Admin {

	/**
	 * Settings option key.
	 *
	 * @var string
	 */
	private $option_key = 'pkwt_dpp_settings';

	/**
	 * SVG option key.
	 *
	 * @var string
	 */
	private $svg_option_key = 'pkwt_dpp_svg_settings';

	/**
	 * Ghost option key.
	 *
	 * @var string
	 */
	private $ghost_option_key = 'pkwt_dpp_ghost_settings';

	/**
	 * Get settings.
	 *
	 * @return array<string,mixed>
	 */
	public function get_settings(): array {
		$defaults = array(
			'enabled'                 => 1,
			'enabled_post_types'      => array(),
			'title_suffix'            => '(Copy)',
			'copy_author'             => 'current',
			'enable_elementor_button' => 1,
			'enable_row_action'       => 1,
		);
		$settings = get_option( $this->option_key, array() );

		return wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'option_page_capability_pkwt_dpp_settings_group', array( $this, 'settings_group_capability' ) );
		add_action( 'admin_post_pkwt_apply_preset', array( $this, 'handle_apply_preset' ) );
		add_action( 'admin_notices', array( $this, 'render_duplicate_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'script_loader_tag', array( $this, 'defer_module_scripts' ), 10, 3 );
	}

	/**
	 * Register settings page.
	 *
	 * @return void
	 */
	public function register_settings_page(): void {
		add_options_page(
			esc_html__( 'Duplicate Post', 'powerkit-powerful-tools-for-your-website' ),
			esc_html__( 'Duplicate Post', 'powerkit-powerful-tools-for-your-website' ),
			'manage_options',
			'pkwt-dpp-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register setting.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting( 'pkwt_dpp_settings_group', $this->option_key, array( $this, 'sanitize_settings' ) );
		register_setting( 'pkwt_dpp_settings_group', $this->svg_option_key, array( $this, 'sanitize_svg_settings' ) );
		register_setting( 'pkwt_dpp_settings_group', $this->ghost_option_key, array( $this, 'sanitize_ghost_settings' ) );
	}

	/**
	 * Dynamic capability for DPP settings group.
	 *
	 * @param string $default_cap Default capability.
	 *
	 * @return string
	 */
	public function settings_group_capability( string $default_cap ): string {
		$settings = get_option( 'pkwt_settings', array() );
		if ( ! empty( $settings['admin_test_mode'] ) ) {
			return 'manage_options';
		}
		if ( current_user_can( 'manage_options' ) ) {
			return 'manage_options';
		}
		$allowed = isset( $settings['access_roles'] ) && is_array( $settings['access_roles'] ) ? $settings['access_roles'] : array( 'administrator' );
		$user    = wp_get_current_user();
		if ( $user && ! empty( $user->roles ) ) {
			foreach ( $user->roles as $role ) {
				if ( in_array( $role, $allowed, true ) ) {
					return 'read';
				}
			}
		}
		return $default_cap;
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array<string,mixed> $settings Settings.
	 *
	 * @return array<string,mixed>
	 */
	public function sanitize_settings( $settings ): array {
		if ( ! is_array( $settings ) ) {
			return $this->get_settings();
		}
		$output = $this->get_settings();

		$output['enabled']                 = empty( $settings['enabled'] ) ? 0 : 1;
		$output['title_suffix']            = isset( $settings['title_suffix'] ) ? sanitize_text_field( wp_unslash( $settings['title_suffix'] ) ) : '(Copy)';
		$output['copy_author']             = isset( $settings['copy_author'] ) && in_array( $settings['copy_author'], array( 'current', 'original' ), true ) ? sanitize_key( $settings['copy_author'] ) : 'current';
		$output['enable_elementor_button'] = empty( $settings['enable_elementor_button'] ) ? 0 : 1;
		$output['enable_row_action']       = empty( $settings['enable_row_action'] ) ? 0 : 1;

		$post_types = array();
		if ( isset( $settings['enabled_post_types'] ) && is_array( $settings['enabled_post_types'] ) ) {
			foreach ( $settings['enabled_post_types'] as $post_type ) {
				$post_types[] = sanitize_key( (string) $post_type );
			}
		}
		$output['enabled_post_types'] = ! empty( $post_types ) ? array_values( array_unique( $post_types ) ) : array();

		return $output;
	}

	/**
	 * Get SVG settings.
	 *
	 * @return array<string,mixed>
	 */
	public function get_svg_settings(): array {
		$defaults = array(
			'dpp_svg_enabled'      => 0,
			'dpp_svg_roles'        => array( 'administrator', 'editor' ),
			'dpp_svg_preview'      => 1,
			'dpp_svg_max_size_kb'  => 512,
			'dpp_svg_strictness'   => 'standard',
			'dpp_svg_blocked_log'  => 0,
		);
		$settings = get_option( $this->svg_option_key, array() );
		return wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );
	}

	/**
	 * Sanitize SVG settings.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,mixed>
	 */
	public function sanitize_svg_settings( $settings ): array {
		if ( ! is_array( $settings ) ) {
			return $this->get_svg_settings();
		}
		$output                         = $this->get_svg_settings();
		$output['dpp_svg_enabled']      = empty( $settings['dpp_svg_enabled'] ) ? 0 : 1;
		$output['dpp_svg_preview']      = empty( $settings['dpp_svg_preview'] ) ? 0 : 1;
		$output['dpp_svg_blocked_log']  = empty( $settings['dpp_svg_blocked_log'] ) ? 0 : 1;
		$output['dpp_svg_max_size_kb']  = isset( $settings['dpp_svg_max_size_kb'] ) ? max( 64, min( 4096, absint( $settings['dpp_svg_max_size_kb'] ) ) ) : 512;
		$output['dpp_svg_strictness']   = isset( $settings['dpp_svg_strictness'] ) ? sanitize_key( (string) $settings['dpp_svg_strictness'] ) : 'standard';
		if ( ! in_array( $output['dpp_svg_strictness'], array( 'standard', 'strict', 'paranoid' ), true ) ) {
			$output['dpp_svg_strictness'] = 'standard';
		}
		$roles = array();
		if ( isset( $settings['dpp_svg_roles'] ) && is_array( $settings['dpp_svg_roles'] ) ) {
			foreach ( $settings['dpp_svg_roles'] as $role ) {
				$roles[] = sanitize_key( (string) $role );
			}
		}
		$output['dpp_svg_roles'] = ! empty( $roles ) ? array_values( array_unique( $roles ) ) : array( 'administrator', 'editor' );
		return $output;
	}

	/**
	 * Get Ghost settings.
	 *
	 * @return array<string,mixed>
	 */
	public function get_ghost_settings(): array {
		$defaults = array(
			'dpp_ghost_enabled'                 => 0,
			'dpp_ghost_custom_cms_name'         => 'Nebula Runtime',
			'dpp_ghost_remove_generator'        => 1,
			'dpp_ghost_strip_version_urls'      => 1,
			'dpp_ghost_remove_rsd'              => 1,
			'dpp_ghost_remove_wlw'              => 1,
			'dpp_ghost_remove_shortlink'        => 1,
			'dpp_ghost_remove_feed_links'       => 1,
			'dpp_ghost_remove_emoji'            => 1,
			'dpp_ghost_remove_oembed'           => 1,
			'dpp_ghost_remove_rest_link'        => 1,
			'dpp_ghost_disable_author_archives' => 1,
			'dpp_ghost_disable_xmlrpc'          => 1,
			'dpp_ghost_hide_rest_users'         => 1,
			'dpp_ghost_rest_prefix'             => '',
			'dpp_ghost_alias_content'           => 'assets',
			'dpp_ghost_alias_includes'          => 'core',
			'dpp_ghost_alias_themes'            => 'designs',
			'dpp_ghost_alias_plugins'           => 'extensions',
			'dpp_ghost_mask_plugin_names'       => 0,
			'dpp_ghost_plugin_name_map'         => array(),
			'dpp_ghost_block_probes'            => 1,
			'dpp_ghost_auto_test'               => 0,
			'dpp_ghost_auto_test_threshold'     => 8,
		);
		$settings = get_option( $this->ghost_option_key, array() );
		return wp_parse_args( is_array( $settings ) ? $settings : array(), $defaults );
	}

	/**
	 * Sanitize Ghost settings.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,mixed>
	 */
	public function sanitize_ghost_settings( $settings ): array {
		if ( ! is_array( $settings ) ) {
			return $this->get_ghost_settings();
		}
		$output = $this->get_ghost_settings();
		$bool_keys = array(
			'dpp_ghost_enabled',
			'dpp_ghost_remove_generator',
			'dpp_ghost_strip_version_urls',
			'dpp_ghost_remove_rsd',
			'dpp_ghost_remove_wlw',
			'dpp_ghost_remove_shortlink',
			'dpp_ghost_remove_feed_links',
			'dpp_ghost_remove_emoji',
			'dpp_ghost_remove_oembed',
			'dpp_ghost_remove_rest_link',
			'dpp_ghost_disable_author_archives',
			'dpp_ghost_disable_xmlrpc',
			'dpp_ghost_hide_rest_users',
			'dpp_ghost_mask_plugin_names',
			'dpp_ghost_block_probes',
			'dpp_ghost_auto_test',
		);
		foreach ( $bool_keys as $key ) {
			$output[ $key ] = empty( $settings[ $key ] ) ? 0 : 1;
		}
		$output['dpp_ghost_custom_cms_name']     = isset( $settings['dpp_ghost_custom_cms_name'] ) ? sanitize_text_field( wp_unslash( $settings['dpp_ghost_custom_cms_name'] ) ) : '';
		$output['dpp_ghost_rest_prefix']         = isset( $settings['dpp_ghost_rest_prefix'] ) ? sanitize_title( wp_unslash( $settings['dpp_ghost_rest_prefix'] ) ) : '';
		$output['dpp_ghost_alias_content']       = isset( $settings['dpp_ghost_alias_content'] ) ? sanitize_title( wp_unslash( $settings['dpp_ghost_alias_content'] ) ) : 'assets';
		$output['dpp_ghost_alias_includes']      = isset( $settings['dpp_ghost_alias_includes'] ) ? sanitize_title( wp_unslash( $settings['dpp_ghost_alias_includes'] ) ) : 'core';
		$output['dpp_ghost_alias_themes']        = isset( $settings['dpp_ghost_alias_themes'] ) ? sanitize_title( wp_unslash( $settings['dpp_ghost_alias_themes'] ) ) : 'designs';
		$output['dpp_ghost_alias_plugins']       = isset( $settings['dpp_ghost_alias_plugins'] ) ? sanitize_title( wp_unslash( $settings['dpp_ghost_alias_plugins'] ) ) : 'extensions';
		$output['dpp_ghost_auto_test_threshold'] = isset( $settings['dpp_ghost_auto_test_threshold'] ) ? max( 1, min( 11, absint( $settings['dpp_ghost_auto_test_threshold'] ) ) ) : 8;
		$plugin_name_map                         = array();
		if ( isset( $settings['dpp_ghost_plugin_name_map'] ) && is_array( $settings['dpp_ghost_plugin_name_map'] ) ) {
			foreach ( $settings['dpp_ghost_plugin_name_map'] as $plugin_file => $plugin_label ) {
				$key = sanitize_text_field( (string) $plugin_file );
				$val = sanitize_text_field( (string) wp_unslash( $plugin_label ) );
				if ( '' !== $key && '' !== $val ) {
					$plugin_name_map[ $key ] = $val;
				}
			}
		}
		$output['dpp_ghost_plugin_name_map'] = $plugin_name_map;
		foreach ( array( 'dpp_ghost_alias_content', 'dpp_ghost_alias_includes', 'dpp_ghost_alias_themes', 'dpp_ghost_alias_plugins' ) as $alias_key ) {
			if ( '' === (string) $output[ $alias_key ] ) {
				$output[ $alias_key ] = 'x' . substr( md5( $alias_key . home_url() ), 0, 6 );
			}
		}
		return $output;
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$tab         = $this->get_query_arg_key( 'tab', 'duplicate' );
		$tab         = in_array( $tab, array( 'duplicate', 'svg-upload', 'ghost-mode' ), true ) ? $tab : 'duplicate';
		$settings    = $this->get_settings();
		$svg         = $this->get_svg_settings();
		$ghost       = $this->get_ghost_settings();
		$post_types  = get_post_types( array( 'show_ui' => true ), 'objects' );
		$wp_roles    = wp_roles();
		$all_roles   = $wp_roles ? $wp_roles->roles : array();
		$scan_results = get_transient( 'pkwt_svg_scan_results' );
		$svg_log     = get_option( 'pkwt_dpp_svg_log', array() );
		$ghost_last  = get_option( 'pkwt_dpp_ghost_last_test', array() );
		$notice      = $this->get_query_arg_key( 'pkwt_notice' );
		?>
		<div class="wrap dpp-feature-panel">
			<h1><?php esc_html_e( 'Duplicate Page & Post Settings', 'powerkit-powerful-tools-for-your-website' ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab <?php echo 'duplicate' === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=pkwt-settings&tab=duplicate' ) ); ?>"><?php esc_html_e( 'Duplicate', 'powerkit-powerful-tools-for-your-website' ); ?></a>
				<a class="nav-tab <?php echo 'svg-upload' === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=pkwt-settings&tab=svg-upload' ) ); ?>"><?php esc_html_e( 'SVG Upload', 'powerkit-powerful-tools-for-your-website' ); ?></a>
				<a class="nav-tab <?php echo 'ghost-mode' === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=pkwt-settings&tab=ghost-mode' ) ); ?>"><?php esc_html_e( 'Ghost Mode', 'powerkit-powerful-tools-for-your-website' ); ?></a>
			</h2>

			<?php if ( 'svg_scanned' === $notice ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'SVG scan completed.', 'powerkit-powerful-tools-for-your-website' ); ?></p></div>
			<?php elseif ( 'ghost_tested' === $notice ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Ghost mode detection test completed.', 'powerkit-powerful-tools-for-your-website' ); ?></p></div>
			<?php endif; ?>

			<?php if ( 'duplicate' === $tab ) : ?>
			<form method="post" action="options.php">
				<?php settings_fields( 'pkwt_dpp_settings_group' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable post duplicator', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="pkwt_dpp_settings[enabled]" value="1" <?php checked( ! empty( $settings['enabled'] ) ); ?> />
								<?php esc_html_e( 'Turn Duplicate button on/off globally', 'powerkit-powerful-tools-for-your-website' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable post types', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td>
							<p class="description"><?php esc_html_e( 'If none are checked, duplication is allowed for all post types shown in admin.', 'powerkit-powerful-tools-for-your-website' ); ?></p>
							<?php foreach ( $post_types as $post_type ) : ?>
								<label style="display:block;margin-bottom:4px;">
									<input type="checkbox" name="pkwt_dpp_settings[enabled_post_types][]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( in_array( $post_type->name, $settings['enabled_post_types'], true ) ); ?> />
									<?php echo esc_html( $post_type->labels->name ); ?>
								</label>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Title suffix', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td><input class="regular-text" type="text" name="pkwt_dpp_settings[title_suffix]" value="<?php echo esc_attr( (string) $settings['title_suffix'] ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Copy author', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td>
							<select name="pkwt_dpp_settings[copy_author]">
								<option value="current" <?php selected( $settings['copy_author'], 'current' ); ?>><?php esc_html_e( 'Current logged-in user', 'powerkit-powerful-tools-for-your-website' ); ?></option>
								<option value="original" <?php selected( $settings['copy_author'], 'original' ); ?>><?php esc_html_e( 'Original author', 'powerkit-powerful-tools-for-your-website' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable row action link', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td><input type="checkbox" name="pkwt_dpp_settings[enable_row_action]" value="1" <?php checked( ! empty( $settings['enable_row_action'] ) ); ?> /></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Elementor editor button', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td><input type="checkbox" name="pkwt_dpp_settings[enable_elementor_button]" value="1" <?php checked( ! empty( $settings['enable_elementor_button'] ) ); ?> /></td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
			<?php elseif ( 'svg-upload' === $tab ) : ?>
			<form method="post" action="options.php">
				<?php settings_fields( 'pkwt_dpp_settings_group' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable SVG uploads', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td><label><input type="checkbox" name="pkwt_dpp_svg_settings[dpp_svg_enabled]" value="1" <?php checked( ! empty( $svg['dpp_svg_enabled'] ) ); ?> /> <?php esc_html_e( 'Safely allow SVG upload with sanitization', 'powerkit-powerful-tools-for-your-website' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Who can upload SVGs', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td>
							<fieldset <?php disabled( empty( $svg['dpp_svg_enabled'] ) ); ?>>
								<?php foreach ( $all_roles as $role_key => $role_data ) : ?>
									<label style="display:block;margin-bottom:4px;"><input type="checkbox" name="pkwt_dpp_svg_settings[dpp_svg_roles][]" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( in_array( $role_key, $svg['dpp_svg_roles'], true ) ); ?> /> <?php echo esc_html( $role_data['name'] ); ?></label>
								<?php endforeach; ?>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Show SVG preview in Media Library', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td><input type="checkbox" name="pkwt_dpp_svg_settings[dpp_svg_preview]" value="1" <?php checked( ! empty( $svg['dpp_svg_preview'] ) ); ?> <?php disabled( empty( $svg['dpp_svg_enabled'] ) ); ?> /></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Max SVG file size (KB)', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td><input type="number" min="64" max="4096" name="pkwt_dpp_svg_settings[dpp_svg_max_size_kb]" value="<?php echo esc_attr( (string) $svg['dpp_svg_max_size_kb'] ); ?>" <?php disabled( empty( $svg['dpp_svg_enabled'] ) ); ?> /></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Sanitization strictness', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td>
							<fieldset <?php disabled( empty( $svg['dpp_svg_enabled'] ) ); ?>>
								<label style="display:block;"><input type="radio" name="pkwt_dpp_svg_settings[dpp_svg_strictness]" value="standard" <?php checked( $svg['dpp_svg_strictness'], 'standard' ); ?> /> <?php esc_html_e( 'Standard', 'powerkit-powerful-tools-for-your-website' ); ?></label>
								<label style="display:block;"><input type="radio" name="pkwt_dpp_svg_settings[dpp_svg_strictness]" value="strict" <?php checked( $svg['dpp_svg_strictness'], 'strict' ); ?> /> <?php esc_html_e( 'Strict', 'powerkit-powerful-tools-for-your-website' ); ?></label>
								<label style="display:block;"><input type="radio" name="pkwt_dpp_svg_settings[dpp_svg_strictness]" value="paranoid" <?php checked( $svg['dpp_svg_strictness'], 'paranoid' ); ?> /> <?php esc_html_e( 'Paranoid', 'powerkit-powerful-tools-for-your-website' ); ?></label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Blocked elements log', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td><input type="checkbox" name="pkwt_dpp_svg_settings[dpp_svg_blocked_log]" value="1" <?php checked( ! empty( $svg['dpp_svg_blocked_log'] ) ); ?> <?php disabled( empty( $svg['dpp_svg_enabled'] ) ); ?> /></td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:12px;">
				<?php wp_nonce_field( 'pkwt_svg_scan' ); ?>
				<input type="hidden" name="action" value="pkwt_svg_scan" />
				<button type="submit" class="button"><?php esc_html_e( 'Scan Media Library for Unsafe SVGs', 'powerkit-powerful-tools-for-your-website' ); ?></button>
			</form>

			<?php if ( ! empty( $scan_results ) && is_array( $scan_results ) ) : ?>
				<h2><?php esc_html_e( 'Scan Results', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<table class="widefat striped">
					<thead><tr><th><?php esc_html_e( 'File', 'powerkit-powerful-tools-for-your-website' ); ?></th><th><?php esc_html_e( 'Issue', 'powerkit-powerful-tools-for-your-website' ); ?></th><th><?php esc_html_e( 'Status', 'powerkit-powerful-tools-for-your-website' ); ?></th></tr></thead>
					<tbody>
					<?php foreach ( $scan_results as $row ) : ?>
						<tr>
							<td><?php echo esc_html( isset( $row['file'] ) ? (string) $row['file'] : '' ); ?></td>
							<td><?php echo esc_html( isset( $row['issue'] ) ? (string) $row['issue'] : '' ); ?></td>
							<td><?php echo esc_html( isset( $row['status'] ) ? (string) $row['status'] : '' ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ( ! empty( $svg_log ) && is_array( $svg_log ) ) : ?>
				<h2><?php esc_html_e( 'Sanitization Log', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<table class="widefat striped">
					<thead><tr><th><?php esc_html_e( 'Time', 'powerkit-powerful-tools-for-your-website' ); ?></th><th><?php esc_html_e( 'File', 'powerkit-powerful-tools-for-your-website' ); ?></th><th><?php esc_html_e( 'Removed', 'powerkit-powerful-tools-for-your-website' ); ?></th></tr></thead>
					<tbody>
					<?php foreach ( array_reverse( $svg_log ) as $entry ) : ?>
						<tr>
							<td><?php echo esc_html( isset( $entry['time'] ) ? wp_date( 'Y-m-d H:i', (int) $entry['time'] ) : '' ); ?></td>
							<td><?php echo esc_html( isset( $entry['file'] ) ? (string) $entry['file'] : '' ); ?></td>
							<td><?php echo esc_html( isset( $entry['removed'] ) ? (string) $entry['removed'] : '' ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
			<?php else : ?>
			<form method="post" action="options.php">
				<?php settings_fields( 'pkwt_dpp_settings_group' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Ghost Mode', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td><label><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_enabled]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_enabled'] ) ); ?> /> <?php esc_html_e( 'Hide common WordPress fingerprints', 'powerkit-powerful-tools-for-your-website' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Custom CMS Name', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td><input class="regular-text" type="text" name="pkwt_dpp_ghost_settings[dpp_ghost_custom_cms_name]" value="<?php echo esc_attr( (string) $ghost['dpp_ghost_custom_cms_name'] ); ?>" <?php disabled( empty( $ghost['dpp_ghost_enabled'] ) ); ?> /></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Source Code Signals', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td>
							<fieldset <?php disabled( empty( $ghost['dpp_ghost_enabled'] ) ); ?>>
								<label style="display:block;"><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_generator]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_generator'] ) ); ?> /> <?php esc_html_e( 'Remove generator meta tag', 'powerkit-powerful-tools-for-your-website' ); ?></label>
								<label style="display:block;"><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_strip_version_urls]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_strip_version_urls'] ) ); ?> /> <?php esc_html_e( 'Remove WordPress version from asset URLs', 'powerkit-powerful-tools-for-your-website' ); ?></label>
								<label style="display:block;"><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_rsd]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_rsd'] ) ); ?> /> <?php esc_html_e( 'Remove RSD link', 'powerkit-powerful-tools-for-your-website' ); ?></label>
								<label style="display:block;"><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_wlw]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_wlw'] ) ); ?> /> <?php esc_html_e( 'Remove WLW manifest link', 'powerkit-powerful-tools-for-your-website' ); ?></label>
								<label style="display:block;"><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_shortlink]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_shortlink'] ) ); ?> /> <?php esc_html_e( 'Remove shortlink', 'powerkit-powerful-tools-for-your-website' ); ?></label>
								<label style="display:block;"><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_feed_links]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_feed_links'] ) ); ?> /> <?php esc_html_e( 'Remove feed links', 'powerkit-powerful-tools-for-your-website' ); ?></label>
								<label style="display:block;"><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_emoji]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_emoji'] ) ); ?> /> <?php esc_html_e( 'Remove emoji scripts', 'powerkit-powerful-tools-for-your-website' ); ?></label>
								<label style="display:block;"><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_oembed]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_oembed'] ) ); ?> /> <?php esc_html_e( 'Remove oEmbed links', 'powerkit-powerful-tools-for-your-website' ); ?></label>
								<label style="display:block;"><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_remove_rest_link]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_remove_rest_link'] ) ); ?> /> <?php esc_html_e( 'Remove REST API link', 'powerkit-powerful-tools-for-your-website' ); ?></label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'API & Endpoint Signals', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td>
							<fieldset <?php disabled( empty( $ghost['dpp_ghost_enabled'] ) ); ?>>
								<label style="display:block;"><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_disable_author_archives]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_disable_author_archives'] ) ); ?> /> <?php esc_html_e( 'Disable author archive URLs', 'powerkit-powerful-tools-for-your-website' ); ?></label>
								<label style="display:block;"><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_disable_xmlrpc]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_disable_xmlrpc'] ) ); ?> /> <?php esc_html_e( 'Disable XML-RPC', 'powerkit-powerful-tools-for-your-website' ); ?></label>
								<label style="display:block;"><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_hide_rest_users]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_hide_rest_users'] ) ); ?> /> <?php esc_html_e( 'Hide REST API users endpoint', 'powerkit-powerful-tools-for-your-website' ); ?></label>
								<label style="display:block;"><?php esc_html_e( 'Custom REST base path', 'powerkit-powerful-tools-for-your-website' ); ?> <input type="text" name="pkwt_dpp_ghost_settings[dpp_ghost_rest_prefix]" value="<?php echo esc_attr( (string) $ghost['dpp_ghost_rest_prefix'] ); ?>" <?php disabled( empty( $ghost['dpp_ghost_enabled'] ) ); ?> /></label>
								<label style="display:block;"><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_block_probes]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_block_probes'] ) ); ?> /> <?php esc_html_e( 'Block common WordPress probe requests', 'powerkit-powerful-tools-for-your-website' ); ?></label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Auto-test weekly', 'powerkit-powerful-tools-for-your-website' ); ?></th>
						<td>
							<label><input type="checkbox" name="pkwt_dpp_ghost_settings[dpp_ghost_auto_test]" value="1" <?php checked( ! empty( $ghost['dpp_ghost_auto_test'] ) ); ?> <?php disabled( empty( $ghost['dpp_ghost_enabled'] ) ); ?> /> <?php esc_html_e( 'Email admin if score drops below threshold', 'powerkit-powerful-tools-for-your-website' ); ?></label>
							<br />
							<label><?php esc_html_e( 'Threshold', 'powerkit-powerful-tools-for-your-website' ); ?> <input type="number" min="1" max="11" name="pkwt_dpp_ghost_settings[dpp_ghost_auto_test_threshold]" value="<?php echo esc_attr( (string) $ghost['dpp_ghost_auto_test_threshold'] ); ?>" <?php disabled( empty( $ghost['dpp_ghost_enabled'] ) ); ?> /></label>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:12px;">
				<?php wp_nonce_field( 'dpp_ghost_test' ); ?>
				<input type="hidden" name="action" value="dpp_ghost_test" />
				<button type="submit" class="button"><?php esc_html_e( 'Test My Site Now', 'powerkit-powerful-tools-for-your-website' ); ?></button>
			</form>
			<?php if ( ! empty( $ghost_last ) && is_array( $ghost_last ) ) : ?>
				<h2><?php esc_html_e( 'Detection Test Results', 'powerkit-powerful-tools-for-your-website' ); ?></h2>
				<p><?php echo esc_html( sprintf( 'Ghost Score: %1$d / %2$d', isset( $ghost_last['score'] ) ? (int) $ghost_last['score'] : 0, isset( $ghost_last['total'] ) ? (int) $ghost_last['total'] : 0 ) ); ?></p>
				<p><?php echo esc_html( sprintf( 'Last test: %s', isset( $ghost_last['time'] ) ? wp_date( 'Y-m-d H:i', (int) $ghost_last['time'] ) : '-' ) ); ?></p>
			<?php endif; ?>
			<div class="notice notice-info inline"><p><?php esc_html_e( 'Ghost Mode reduces many common WordPress fingerprints, but no solution can make WordPress 100% undetectable in every scenario.', 'powerkit-powerful-tools-for-your-website' ); ?></p></div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Add admin success notice.
	 *
	 * @return void
	 */
	public function render_duplicate_notice(): void {
		if ( ! is_admin() ) {
			return;
		}

		if ( '' === $this->get_query_arg_key( 'dpp_duplicated' ) || $this->get_query_arg_int( 'dpp_new_post_id' ) <= 0 ) {
			return;
		}

		$new_post_id = $this->get_query_arg_int( 'dpp_new_post_id' );
		if ( $new_post_id <= 0 ) {
			return;
		}

		$edit_link = get_edit_post_link( $new_post_id, '' );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Post duplicated. Edit the copy:', 'powerkit-powerful-tools-for-your-website' ) . ' <a href="' . esc_url( (string) $edit_link ) . '">' . esc_html__( 'Open draft', 'powerkit-powerful-tools-for-your-website' ) . '</a></p></div>';
	}

	/**
	 * Apply recommended presets for modules.
	 *
	 * @return void
	 */
	public function handle_apply_preset(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerkit-powerful-tools-for-your-website' ) );
		}
		check_admin_referer( 'pkwt_apply_preset' );

		$module = $this->get_query_arg_key( 'module' );
		$tab    = 'overview';

		switch ( $module ) {
			case 'duplicate':
				$tab = 'duplicate';
				update_option(
					'pkwt_dpp_settings',
					array(
						'enabled'                 => 1,
						'enabled_post_types'      => array(),
						'title_suffix'            => '(Copy)',
						'copy_author'             => 'current',
						'enable_elementor_button' => 1,
						'enable_row_action'       => 1,
					)
				);
				break;
			case 'svg':
				$tab = 'svg-upload';
				update_option(
					'pkwt_dpp_svg_settings',
					array(
						'dpp_svg_enabled'      => 1,
						'dpp_svg_roles'        => array( 'administrator', 'editor' ),
						'dpp_svg_preview'      => 1,
						'dpp_svg_max_size_kb'  => 512,
						'dpp_svg_strictness'   => 'strict',
						'dpp_svg_blocked_log'  => 1,
					)
				);
				break;
			case 'ghost':
				$tab = 'ghost-mode';
				update_option(
					'pkwt_dpp_ghost_settings',
					array(
						'dpp_ghost_enabled'                 => 1,
						'dpp_ghost_custom_cms_name'         => 'Nebula Runtime',
						'dpp_ghost_remove_generator'        => 1,
						'dpp_ghost_strip_version_urls'      => 1,
						'dpp_ghost_remove_rsd'              => 1,
						'dpp_ghost_remove_wlw'              => 1,
						'dpp_ghost_remove_shortlink'        => 1,
						'dpp_ghost_remove_feed_links'       => 1,
						'dpp_ghost_remove_emoji'            => 1,
						'dpp_ghost_remove_oembed'           => 1,
						'dpp_ghost_remove_rest_link'        => 1,
						'dpp_ghost_disable_author_archives' => 1,
						'dpp_ghost_disable_xmlrpc'          => 1,
						'dpp_ghost_hide_rest_users'         => 1,
						'dpp_ghost_rest_prefix'             => '',
						'dpp_ghost_alias_content'           => 'assets',
						'dpp_ghost_alias_includes'          => 'core',
						'dpp_ghost_alias_themes'            => 'designs',
						'dpp_ghost_alias_plugins'           => 'extensions',
						'dpp_ghost_mask_plugin_names'       => 1,
						'dpp_ghost_plugin_name_map'         => array(),
						'dpp_ghost_block_probes'            => 1,
						'dpp_ghost_auto_test'               => 0,
						'dpp_ghost_auto_test_threshold'     => 8,
					)
				);
				break;
			case 'classic':
				$tab = 'classic-editor';
				$classic = new Class_PKWT_DPP_Classic();
				$cfg     = $classic->get_settings();
				$cfg['dpp_classic_enabled']            = 1;
				$cfg['dpp_classic_scope']              = 'all';
				$cfg['dpp_classic_allow_user_choice']  = 0;
				$cfg['dpp_classic_allow_admin_bypass'] = 1;
				$cfg['dpp_classic_remove_block_css']   = 1;
				$cfg['dpp_classic_remove_block_js']    = 1;
				$cfg['dpp_classic_disable_fse']        = 1;
				$cfg['dpp_classic_disable_widgets']    = 1;
				$cfg['dpp_classic_remove_patterns']    = 0;
				$cfg['dpp_classic_remove_block_dir']   = 1;
				$cfg['dpp_classic_toolbar_style']      = 'full';
				$cfg['dpp_classic_kitchen_sink']       = 1;
				$cfg['dpp_classic_default_editor_tab'] = 'visual';
				$cfg['dpp_classic_show_notice']        = 0;
				update_option( 'pkwt_dpp_classic_settings', $cfg );
				break;
			default:
				wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=overview&pkwt_notice=preset_error' ) );
				exit;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings&tab=' . $tab . '&pkwt_notice=preset_applied' ) );
		exit;
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return void
	 */
	public function enqueue_assets( string $hook ): void {
		$is_pkwt_settings = false !== strpos( $hook, 'pkwt-settings' );
		$is_dpp_settings = false !== strpos( $hook, 'pkwt-dpp-settings' );
		$page               = $this->get_query_arg_key( 'page' );
		$is_elementor_screen = 'elementor' === $this->get_query_arg_key( 'action' );
		if ( ! $is_pkwt_settings && ! $is_dpp_settings && ! $is_elementor_screen ) {
			return;
		}

		$dpp_pages = array(
			'pkwt-settings-overview',
			'pkwt-settings-duplicate',
			'pkwt-settings-svg-upload',
			'pkwt-settings-ghost-mode',
			'pkwt-settings-classic-editor',
			'pkwt-dpp-settings',
		);
		if ( $is_dpp_settings || in_array( $page, $dpp_pages, true ) ) {
			wp_enqueue_style( 'pkwt-dpp-admin', PKWT_PLUGIN_URL . 'assets/css/dpp-admin.css', array(), PKWT_VERSION );
		}

		$settings = $this->get_settings();
		if ( empty( $settings['enabled'] ) || empty( $settings['enable_elementor_button'] ) ) {
			return;
		}

		if ( ! $is_elementor_screen ) {
			return;
		}

		$post_id = $this->get_query_arg_int( 'post' );
		if ( $post_id <= 0 || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$post_type = get_post_type( $post_id );
		if ( ! $post_type ) {
			return;
		}
		$enabled_types = isset( $settings['enabled_post_types'] ) && is_array( $settings['enabled_post_types'] ) ? $settings['enabled_post_types'] : array();
		if ( ! empty( $enabled_types ) && ! in_array( $post_type, $enabled_types, true ) ) {
			return;
		}

		wp_enqueue_script( 'pkwt-dpp-editor', PKWT_PLUGIN_URL . 'assets/js/dpp-editor.js', array(), PKWT_VERSION, true );
		wp_localize_script(
			'pkwt-dpp-editor',
			'PKWTEditor',
			array(
				'postId'  => $post_id,
				'url'     => admin_url( 'admin.php?action=dpp_duplicate_post&post=' . $post_id ),
				'nonce'   => wp_create_nonce( 'pkwt_dpp_duplicate_' . $post_id ),
				'label'   => __( 'Duplicate Page', 'powerkit-powerful-tools-for-your-website' ),
			)
		);
	}

	/**
	 * Defer heavy module script tags.
	 *
	 * @param string $tag    Script tag.
	 * @param string $handle Script handle.
	 * @param string $src    Script source.
	 *
	 * @return string
	 */
	public function defer_module_scripts( string $tag, string $handle, string $src ): string {
		if ( 'pkwt-dpp-editor' !== $handle ) {
			return $tag;
		}
		if ( false !== strpos( $tag, ' defer' ) ) {
			return $tag;
		}
		return str_replace( '<script ', '<script defer ', $tag );
	}

	/**
	 * Read a sanitized key-like GET value.
	 *
	 * @param string $key     Query key.
	 * @param string $default Default value.
	 *
	 * @return string
	 */
	private function get_query_arg_key( string $key, string $default = '' ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only query parsing.
		return isset( $_GET[ $key ] ) ? sanitize_key( wp_unslash( (string) $_GET[ $key ] ) ) : $default;
	}

	/**
	 * Read an integer GET value.
	 *
	 * @param string $key     Query key.
	 * @param int    $default Default value.
	 *
	 * @return int
	 */
	private function get_query_arg_int( string $key, int $default = 0 ): int {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only query parsing.
		return isset( $_GET[ $key ] ) ? absint( $_GET[ $key ] ) : $default;
	}
}
