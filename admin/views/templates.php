<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View template file.

/**
 * Elementor Page Templates library view — Premium UI.
 *
 * @package PKWT
 */

$pkwt_notice = isset( $_GET['pkwt_notice'] ) ? sanitize_key( wp_unslash( $_GET['pkwt_notice'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$tpl_page_id = isset( $_GET['tpl_page_id'] ) ? absint( $_GET['tpl_page_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$notice_messages = array(
	'tpl_imported'  => __( 'Template imported successfully!', 'powerkit-powerful-tools-for-your-website' ),
	'tpl_failed'    => __( 'Import failed. Please try again.', 'powerkit-powerful-tools-for-your-website' ),
	'tpl_not_found' => __( 'Template file not found.', 'powerkit-powerful-tools-for-your-website' ),
	'tpl_no_page'   => __( 'No target page found. Configure your pages in General settings first.', 'powerkit-powerful-tools-for-your-website' ),
	'tpl_invalid'   => __( 'Invalid template selection.', 'powerkit-powerful-tools-for-your-website' ),
);

$is_elementor_active = class_exists( '\Elementor\Plugin' );

$tpl_library = new \PKWT\Elementor\Class_PKWT_Template_Library();
$layout_sets = $tpl_library->get_layout_sets();

$page_type_labels = array(
	'login'    => __( 'Login', 'powerkit-powerful-tools-for-your-website' ),
	'register' => __( 'Register', 'powerkit-powerful-tools-for-your-website' ),
	'lost'     => __( 'Forgot Password', 'powerkit-powerful-tools-for-your-website' ),
	'reset'    => __( 'Reset Password', 'powerkit-powerful-tools-for-your-website' ),
);

$pkwt_settings = get_option( 'pkwt_settings', array() );
$page_id_map   = array(
	'login'    => isset( $pkwt_settings['login_page_id'] ) ? absint( $pkwt_settings['login_page_id'] ) : 0,
	'register' => isset( $pkwt_settings['register_page_id'] ) ? absint( $pkwt_settings['register_page_id'] ) : 0,
	'lost'     => isset( $pkwt_settings['lost_password_page_id'] ) ? absint( $pkwt_settings['lost_password_page_id'] ) : 0,
	'reset'    => isset( $pkwt_settings['reset_password_page_id'] ) ? absint( $pkwt_settings['reset_password_page_id'] ) : 0,
);

// Layout visual data
$layout_visuals = array(
	'split-left'    => array( 'gradient' => 'linear-gradient(135deg,#0f172a 0%,#1e293b 100%)', 'accent' => '#6366f1', 'label_color' => '#fff', 'layout' => 'split-left' ),
	'centered-card' => array( 'gradient' => 'linear-gradient(135deg,#1a1a4e 0%,#2563eb 100%)', 'accent' => '#2563eb', 'label_color' => '#fff', 'layout' => 'centered' ),
	'form-left'     => array( 'gradient' => 'linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%)', 'accent' => '#7c3aed', 'label_color' => '#fff', 'layout' => 'form-left' ),
	'dreamer'       => array( 'gradient' => 'linear-gradient(135deg,#e0d4f5 0%,#c8b8e8 50%,#b8a0e0 100%)', 'accent' => '#3b5bdb', 'label_color' => '#3b3056', 'layout' => 'split-left' ),
	'uxolist'       => array( 'gradient' => 'linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#1e40af 100%)', 'accent' => '#3b82f6', 'label_color' => '#fff', 'layout' => 'split-left' ),
	'gradient-hub'  => array( 'gradient' => 'linear-gradient(135deg,#3b4ce8 0%,#5b21b6 50%,#7c3aed 100%)', 'accent' => '#a78bfa', 'label_color' => '#fff', 'layout' => 'form-left' ),
);
?>
<div class="pkwt-tpl-page">

	<!-- ── Hero ──────────────────────────────────────────────────────── -->
	<div class="pkwt-tpl-hero">
		<div class="pkwt-tpl-hero__text">
			<div class="pkwt-tpl-hero__badge"><?php esc_html_e( 'Template Library', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			<h1><?php esc_html_e( 'Beautiful', 'powerkit-powerful-tools-for-your-website' ); ?> <span><?php esc_html_e( 'Page Templates', 'powerkit-powerful-tools-for-your-website' ); ?></span></h1>
			<p><?php esc_html_e( 'One-click full-page Elementor layouts for your login, register, forgot password, and reset password pages. Import any design instantly.', 'powerkit-powerful-tools-for-your-website' ); ?></p>
		</div>
		<div class="pkwt-tpl-hero__actions">
			<div class="pkwt-tpl-hero__stat">
				<strong>3</strong>
				<span><?php esc_html_e( 'Layouts', 'powerkit-powerful-tools-for-your-website' ); ?></span>
			</div>
			<div class="pkwt-tpl-hero__stat">
				<strong>12</strong>
				<span><?php esc_html_e( 'Templates', 'powerkit-powerful-tools-for-your-website' ); ?></span>
			</div>
		</div>
	</div>

	<?php if ( ! $is_elementor_active ) : ?>
	<div class="pkwt-tpl-alert pkwt-tpl-alert--warning">
		<div class="pkwt-tpl-alert__icon">⚠️</div>
		<div class="pkwt-tpl-alert__body">
			<span class="pkwt-tpl-alert__title"><?php esc_html_e( 'Elementor Required', 'powerkit-powerful-tools-for-your-website' ); ?></span>
			<?php esc_html_e( 'These templates require Elementor to be installed and activated.', 'powerkit-powerful-tools-for-your-website' ); ?>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( '' !== $pkwt_notice && isset( $notice_messages[ $pkwt_notice ] ) ) :
		$is_ok = ( 'tpl_imported' === $pkwt_notice );
	?>
	<div class="pkwt-tpl-alert pkwt-tpl-alert--<?php echo $is_ok ? 'success' : 'error'; ?>">
		<div class="pkwt-tpl-alert__icon"><?php echo $is_ok ? '✅' : '❌'; ?></div>
		<div class="pkwt-tpl-alert__body">
			<?php echo esc_html( $notice_messages[ $pkwt_notice ] ); ?>
			<?php if ( $is_ok && $tpl_page_id > 0 ) : ?>
				<a href="<?php echo esc_url( get_edit_post_link( $tpl_page_id ) . '&action=elementor' ); ?>" target="_blank" class="pkwt-dash-import-link"><?php esc_html_e( 'Open in Elementor →', 'powerkit-powerful-tools-for-your-website' ); ?></a>
				<a href="<?php echo esc_url( get_permalink( $tpl_page_id ) ); ?>" target="_blank" class="pkwt-dash-import-link"><?php esc_html_e( 'View Page →', 'powerkit-powerful-tools-for-your-website' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>

	<?php if ( array_sum( $page_id_map ) === 0 ) : ?>
	<div class="pkwt-tpl-alert pkwt-tpl-alert--info">
		<div class="pkwt-tpl-alert__icon">💡</div>
		<div class="pkwt-tpl-alert__body">
			<span class="pkwt-tpl-alert__title"><?php esc_html_e( 'Set up your pages first', 'powerkit-powerful-tools-for-your-website' ); ?></span>
			<?php printf(
				/* translators: %s: link */
				esc_html__( 'Assign your login, register, and password pages in %s before importing templates.', 'powerkit-powerful-tools-for-your-website' ),
				'<a href="' . esc_url( admin_url( 'admin.php?page=pkwt-settings-general' ) ) . '">' . esc_html__( 'General Settings', 'powerkit-powerful-tools-for-your-website' ) . '</a>'
			); ?>
		</div>
	</div>
	<?php endif; ?>

	<!-- ── Layouts grid ──────────────────────────────────────────────── -->
	<div class="pkwt-tpl-section-label"><?php esc_html_e( 'Choose a layout', 'powerkit-powerful-tools-for-your-website' ); ?></div>

	<div class="pkwt-tpl-grid">
	<?php foreach ( $layout_sets as $set_slug => $set ) :
		$vis = $layout_visuals[ $set_slug ] ?? array( 'gradient' => '#6366f1', 'accent' => '#6366f1' );
		$gradient = $vis['gradient'];
		$accent   = $vis['accent'];
		$layout   = $vis['layout'];
		?>
		<div class="pkwt-tpl-card">

			<!-- Visual preview -->
			<div class="pkwt-tpl-card__preview">
				<?php if ( 'split-left' === $layout ) : ?>
				<div class="pkwt-tpl-card__preview-inner prev-split" style="background:#fff;">
					<div class="prev-panel" style="background:<?php echo esc_attr( $gradient ); ?>;">
						<div class="prev-logo"></div>
						<div class="prev-line prev-line--white" style="width:70%;"></div>
						<div class="prev-line prev-line--muted prev-line--half"></div>
						<div class="prev-line prev-line--muted" style="width:80%;"></div>
					</div>
					<div class="prev-form">
						<div class="prev-line" style="background:#0f172a;width:50%;height:7px;margin-bottom:4px;"></div>
						<div class="prev-field"></div>
						<div class="prev-field"></div>
						<div class="prev-btn" style="background:<?php echo esc_attr( $accent ); ?>;"></div>
					</div>
				</div>

				<?php elseif ( 'centered' === $layout ) : ?>
				<div class="pkwt-tpl-card__preview-inner prev-centered" style="background:<?php echo esc_attr( $gradient ); ?>;">
					<div class="prev-card">
						<div class="prev-title"></div>
						<div class="prev-field"></div>
						<div class="prev-field"></div>
						<div class="prev-btn" style="background:<?php echo esc_attr( $accent ); ?>;"></div>
					</div>
				</div>

				<?php else : ?>
				<div class="pkwt-tpl-card__preview-inner prev-form-left" style="background:#fff;">
					<div class="prev-form-side">
						<div class="prev-line" style="background:#0f172a;width:50%;height:7px;margin-bottom:4px;"></div>
						<div class="prev-field"></div>
						<div class="prev-field"></div>
						<div class="prev-btn" style="background:<?php echo esc_attr( $accent ); ?>;"></div>
					</div>
					<div class="prev-panel-side" style="background:<?php echo esc_attr( $gradient ); ?>;">
						<div class="prev-line prev-line--white" style="width:80%;"></div>
						<div class="prev-line prev-line--muted prev-line--half"></div>
					</div>
				</div>
				<?php endif; ?>
			</div>

			<!-- Title -->
			<div class="pkwt-tpl-card__meta">
				<h3 class="pkwt-tpl-card__name"><?php echo esc_html( $set['label'] ); ?></h3>
				<p class="pkwt-tpl-card__desc"><?php echo esc_html( $set['description'] ); ?></p>
			</div>

			<!-- Import rows per page type -->
			<div class="pkwt-tpl-card__imports">
				<?php foreach ( $page_type_labels as $page_type => $page_label ) :
					$target_page_id = $page_id_map[ $page_type ];
					$page_url       = $target_page_id > 0 ? get_permalink( $target_page_id ) : '';
					$has_page       = $target_page_id > 0;

					// Row icon colours
					$icon_colors = array(
						'login'    => array( 'bg' => '#ede9fe', 'color' => '#6d28d9' ),
						'register' => array( 'bg' => '#dcfce7', 'color' => '#16a34a' ),
						'lost'     => array( 'bg' => '#fef9c3', 'color' => '#ca8a04' ),
						'reset'    => array( 'bg' => '#fee2e2', 'color' => '#dc2626' ),
					);
					$ic = $icon_colors[ $page_type ];
					$icons = array(
						'login'    => '&#128100;',
						'register' => '&#43;',
						'lost'     => '&#128231;',
						'reset'    => '&#128274;',
					);
					?>
					<div class="pkwt-tpl-import-row <?php echo $has_page ? '' : 'pkwt-tpl-import-row--no-page'; ?>">
						<div class="pkwt-tpl-row-icon" style="background:<?php echo esc_attr( $ic['bg'] ); ?>;color:<?php echo esc_attr( $ic['color'] ); ?>;">
							<?php echo $icons[ $page_type ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<span class="pkwt-tpl-row-label"><?php echo esc_html( $page_label ); ?></span>
						<div class="pkwt-tpl-row-actions">
							<?php if ( $has_page ) : ?>
								<button type="button"
									class="pkwt-tpl-btn-import pkwt-dash-import-btn"
									data-set="<?php echo esc_attr( $set_slug ); ?>"
									data-page-type="<?php echo esc_attr( $page_type ); ?>"
									data-page-id="<?php echo esc_attr( $target_page_id ); ?>"
									<?php echo ! $is_elementor_active ? 'disabled' : ''; ?>>
									<span class="dashicons dashicons-download" style="font-size:14px;width:14px;height:14px;margin-top:1px;"></span>
									<?php esc_html_e( 'Import', 'powerkit-powerful-tools-for-your-website' ); ?>
								</button>
								<?php if ( ! empty( $page_url ) ) : ?>
									<a href="<?php echo esc_url( $page_url ); ?>" target="_blank" class="pkwt-tpl-btn-view" title="<?php esc_attr_e( 'View page', 'powerkit-powerful-tools-for-your-website' ); ?>">
										<span class="dashicons dashicons-external" style="font-size:14px;width:14px;height:14px;"></span>
									</a>
								<?php endif; ?>
							<?php else : ?>
								<span class="pkwt-tpl-no-page-note"><?php esc_html_e( 'Not configured', 'powerkit-powerful-tools-for-your-website' ); ?></span>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>

				<div class="pkwt-dash-import-status" style="display:none;"></div>
			</div>

		</div><!-- .pkwt-tpl-card -->
	<?php endforeach; ?>
	</div><!-- .pkwt-tpl-grid -->

	<!-- ── How it works ──────────────────────────────────────────────── -->
	<div class="pkwt-tpl-how">
		<p class="pkwt-tpl-how__title"><?php esc_html_e( 'How to use templates', 'powerkit-powerful-tools-for-your-website' ); ?></p>
		<div class="pkwt-tpl-how__steps">
			<div class="pkwt-tpl-how__step">
				<div class="pkwt-tpl-how__num">1</div>
				<div class="pkwt-tpl-how__text"><strong><?php esc_html_e( 'Install Elementor', 'powerkit-powerful-tools-for-your-website' ); ?></strong><?php esc_html_e( 'Make sure Elementor is installed and active on your site.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			</div>
			<div class="pkwt-tpl-how__step">
				<div class="pkwt-tpl-how__num">2</div>
				<div class="pkwt-tpl-how__text"><strong><?php esc_html_e( 'Assign pages', 'powerkit-powerful-tools-for-your-website' ); ?></strong><?php esc_html_e( 'Go to General settings and assign pages for login, register and passwords.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			</div>
			<div class="pkwt-tpl-how__step">
				<div class="pkwt-tpl-how__num">3</div>
				<div class="pkwt-tpl-how__text"><strong><?php esc_html_e( 'Click Import', 'powerkit-powerful-tools-for-your-website' ); ?></strong><?php esc_html_e( 'Pick a layout and click Import next to the page type you want to apply it to.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			</div>
			<div class="pkwt-tpl-how__step">
				<div class="pkwt-tpl-how__num">4</div>
				<div class="pkwt-tpl-how__text"><strong><?php esc_html_e( 'Customise', 'powerkit-powerful-tools-for-your-website' ); ?></strong><?php esc_html_e( 'Open the page in Elementor and customise colours, images, and text to match your brand.', 'powerkit-powerful-tools-for-your-website' ); ?></div>
			</div>
		</div>
	</div>

</div><!-- .pkwt-tpl-page -->

