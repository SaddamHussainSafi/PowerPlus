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
<style>
/* ─── Premium Templates Tab ────────────────────────────────────────────── */
.pkwt-tpl-page { max-width: 1140px; padding: 0 0 60px; }

/* Hero header */
.pkwt-tpl-hero {
	background: linear-gradient(135deg,#0f172a 0%,#1e1b4b 60%,#312e81 100%);
	border-radius: 16px;
	padding: 40px 44px;
	margin-bottom: 36px;
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 24px;
	overflow: hidden;
	position: relative;
}
.pkwt-tpl-hero::before {
	content: '';
	position: absolute;
	top: -60px; right: -60px;
	width: 260px; height: 260px;
	background: radial-gradient(circle, rgba(99,102,241,.35) 0%, transparent 70%);
	pointer-events: none;
}
.pkwt-tpl-hero::after {
	content: '';
	position: absolute;
	bottom: -40px; left: 30%;
	width: 200px; height: 200px;
	background: radial-gradient(circle, rgba(139,92,246,.2) 0%, transparent 70%);
	pointer-events: none;
}
.pkwt-tpl-hero__text { position: relative; z-index: 1; }
.pkwt-tpl-hero__badge {
	display: inline-flex;
	align-items: center;
	gap: 6px;
	background: rgba(99,102,241,.25);
	border: 1px solid rgba(99,102,241,.4);
	border-radius: 99px;
	padding: 4px 12px;
	font-size: 11px;
	font-weight: 600;
	color: #a5b4fc;
	letter-spacing: .04em;
	text-transform: uppercase;
	margin-bottom: 14px;
}
.pkwt-tpl-hero__badge::before { content: '✦'; font-size: 9px; }
.pkwt-tpl-hero h1 {
	font-size: 28px !important;
	font-weight: 800 !important;
	color: #fff !important;
	line-height: 1.2 !important;
	margin: 0 0 10px !important;
	padding: 0 !important;
	border: none !important;
}
.pkwt-tpl-hero h1 span { color: #818cf8; }
.pkwt-tpl-hero p {
	font-size: 14px;
	color: rgba(255,255,255,.65);
	margin: 0;
	line-height: 1.6;
	max-width: 480px;
}
.pkwt-tpl-hero__actions {
	position: relative; z-index: 1;
	display: flex;
	flex-direction: column;
	gap: 8px;
	flex-shrink: 0;
}
.pkwt-tpl-hero__stat {
	background: rgba(255,255,255,.07);
	border: 1px solid rgba(255,255,255,.12);
	border-radius: 12px;
	padding: 14px 20px;
	text-align: center;
	min-width: 120px;
}
.pkwt-tpl-hero__stat strong {
	display: block;
	font-size: 24px;
	font-weight: 800;
	color: #fff;
	line-height: 1;
	margin-bottom: 4px;
}
.pkwt-tpl-hero__stat span {
	font-size: 11px;
	color: rgba(255,255,255,.5);
	text-transform: uppercase;
	letter-spacing: .05em;
}

/* Alert banners */
.pkwt-tpl-alert {
	display: flex;
	align-items: flex-start;
	gap: 12px;
	border-radius: 12px;
	padding: 14px 18px;
	margin-bottom: 20px;
	font-size: 13px;
	line-height: 1.5;
}
.pkwt-tpl-alert--warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
.pkwt-tpl-alert--info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
.pkwt-tpl-alert--success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
.pkwt-tpl-alert--error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
.pkwt-tpl-alert__icon { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
.pkwt-tpl-alert__body { flex: 1; }
.pkwt-tpl-alert__title { font-weight: 700; display: block; margin-bottom: 2px; }
.pkwt-tpl-alert a { color: inherit; font-weight: 700; }

/* Section label */
.pkwt-tpl-section-label {
	display: flex;
	align-items: center;
	gap: 10px;
	font-size: 11px;
	font-weight: 700;
	letter-spacing: .07em;
	text-transform: uppercase;
	color: #94a3b8;
	margin-bottom: 18px;
}
.pkwt-tpl-section-label::after {
	content: '';
	flex: 1;
	height: 1px;
	background: linear-gradient(90deg, #e2e8f0, transparent);
}

/* Template cards grid */
.pkwt-tpl-grid {
	display: grid;
	grid-template-columns: repeat(3, 1fr);
	gap: 20px;
	margin-bottom: 40px;
}

/* Individual layout card */
.pkwt-tpl-card {
	background: #fff;
	border: 1.5px solid #e8eaf2;
	border-radius: 16px;
	overflow: hidden;
	transition: border-color .2s, box-shadow .2s, transform .18s;
	box-shadow: 0 2px 8px rgba(0,0,0,.05);
}
.pkwt-tpl-card:hover {
	border-color: #6366f1;
	box-shadow: 0 8px 32px rgba(99,102,241,.15);
	transform: translateY(-3px);
}

/* Large visual preview */
.pkwt-tpl-card__preview {
	height: 160px;
	position: relative;
	overflow: hidden;
	display: flex;
	align-items: stretch;
}
.pkwt-tpl-card__preview-inner {
	width: 100%;
	display: flex;
	position: relative;
}

/* Split-left preview */
.prev-split .prev-panel {
	width: 42%;
	display: flex;
	flex-direction: column;
	justify-content: center;
	padding: 20px 18px;
	gap: 8px;
}
.prev-split .prev-form {
	flex: 1;
	background: #fff;
	display: flex;
	flex-direction: column;
	justify-content: center;
	padding: 20px 18px;
	gap: 8px;
}
.prev-split .prev-logo {
	width: 28px; height: 28px;
	border-radius: 6px;
	background: rgba(255,255,255,.25);
	margin-bottom: 6px;
}
.prev-line { height: 6px; border-radius: 3px; }
.prev-line--white  { background: rgba(255,255,255,.6); }
.prev-line--muted  { background: rgba(255,255,255,.3); }
.prev-line--half   { width: 55%; }
.prev-field { height: 10px; background: #f1f5f9; border-radius: 5px; border: 1px solid #e2e8f0; }
.prev-btn   { height: 12px; border-radius: 6px; margin-top: 4px; }

/* Centered card preview */
.prev-centered {
	align-items: center;
	justify-content: center;
}
.prev-centered .prev-card {
	background: #fff;
	border-radius: 10px;
	padding: 16px 18px;
	width: 62%;
	display: flex;
	flex-direction: column;
	gap: 7px;
	box-shadow: 0 4px 24px rgba(0,0,0,.22);
}
.prev-centered .prev-title { height: 7px; border-radius: 3px; background: #1e293b; width: 50%; }

/* Form-left preview */
.prev-form-left .prev-form-side {
	flex: 1;
	background: #fff;
	display: flex;
	flex-direction: column;
	justify-content: center;
	padding: 20px 18px;
	gap: 8px;
}
.prev-form-left .prev-panel-side {
	width: 42%;
	display: flex;
	flex-direction: column;
	justify-content: flex-end;
	padding: 20px 18px;
	gap: 8px;
}

/* Card meta */
.pkwt-tpl-card__meta {
	padding: 16px 18px 14px;
	border-bottom: 1px solid #f1f5f9;
}
.pkwt-tpl-card__name {
	font-size: 15px;
	font-weight: 800;
	color: #0f172a;
	margin: 0 0 4px;
	line-height: 1.3;
}
.pkwt-tpl-card__desc {
	font-size: 12px;
	color: #94a3b8;
	margin: 0;
	line-height: 1.4;
}

/* Import rows inside card */
.pkwt-tpl-card__imports {
	padding: 12px 18px 16px;
	display: flex;
	flex-direction: column;
	gap: 7px;
}
.pkwt-tpl-import-row {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 7px 10px;
	border-radius: 8px;
	background: #f8fafc;
	border: 1px solid #f1f5f9;
	transition: background .15s, border-color .15s;
}
.pkwt-tpl-import-row:hover { background: #f0f1ff; border-color: #c7d2fe; }
.pkwt-tpl-import-row--no-page { opacity: .6; }

.pkwt-tpl-row-icon {
	width: 28px; height: 28px;
	border-radius: 7px;
	display: flex; align-items: center; justify-content: center;
	flex-shrink: 0;
	font-size: 14px;
}
.pkwt-tpl-row-label {
	flex: 1;
	font-size: 12.5px;
	font-weight: 600;
	color: #334155;
}
.pkwt-tpl-row-actions {
	display: flex;
	align-items: center;
	gap: 5px;
}

/* Import button */
.pkwt-tpl-btn-import {
	display: inline-flex;
	align-items: center;
	gap: 5px;
	background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
	color: #fff !important;
	border: none;
	border-radius: 7px;
	padding: 5px 12px;
	font-size: 11.5px;
	font-weight: 700;
	cursor: pointer;
	transition: opacity .18s, transform .15s, box-shadow .18s;
	box-shadow: 0 2px 8px rgba(99,102,241,.3);
	white-space: nowrap;
	letter-spacing: .01em;
	line-height: 1.4;
	text-decoration: none !important;
}
.pkwt-tpl-btn-import:hover {
	opacity: .9;
	transform: translateY(-1px);
	box-shadow: 0 4px 14px rgba(99,102,241,.4);
	color: #fff !important;
}
.pkwt-tpl-btn-import:active { transform: translateY(0); }
.pkwt-tpl-btn-import:disabled {
	opacity: .45;
	cursor: not-allowed;
	transform: none;
	box-shadow: none;
}
.pkwt-tpl-btn-view {
	width: 28px; height: 28px;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	border-radius: 7px;
	background: #f1f5f9;
	border: 1px solid #e2e8f0;
	color: #64748b !important;
	text-decoration: none !important;
	transition: background .15s, color .15s;
	font-size: 14px;
}
.pkwt-tpl-btn-view:hover { background: #e0e7ff; color: #4f46e5 !important; border-color: #c7d2fe; }

.pkwt-tpl-no-page-note {
	font-size: 11px;
	color: #94a3b8;
	font-style: italic;
}

/* Import status */
.pkwt-dash-import-status {
	margin: 8px 18px 4px;
	border-radius: 8px;
	padding: 10px 14px;
	font-size: 12px;
	line-height: 1.5;
}
.pkwt-dash-import-status--ok  { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
.pkwt-dash-import-status--err { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
.pkwt-dash-import-link { color: inherit; font-weight: 700; text-decoration: underline; margin-left: 8px; }

/* How it works */
.pkwt-tpl-how {
	background: #f8fafc;
	border: 1.5px solid #e8eaf2;
	border-radius: 16px;
	padding: 28px 32px;
}
.pkwt-tpl-how__title {
	font-size: 15px;
	font-weight: 800;
	color: #0f172a;
	margin: 0 0 20px;
}
.pkwt-tpl-how__steps {
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	gap: 16px;
}
.pkwt-tpl-how__step {
	display: flex;
	flex-direction: column;
	gap: 8px;
}
.pkwt-tpl-how__num {
	width: 32px; height: 32px;
	border-radius: 10px;
	background: linear-gradient(135deg, #6366f1, #8b5cf6);
	color: #fff;
	font-size: 13px;
	font-weight: 800;
	display: flex;
	align-items: center;
	justify-content: center;
	flex-shrink: 0;
}
.pkwt-tpl-how__text {
	font-size: 12.5px;
	color: #475569;
	line-height: 1.5;
}
.pkwt-tpl-how__text strong { color: #0f172a; display: block; margin-bottom: 2px; }

/* Spinner */
@keyframes pkwt-dash-spin { to { transform: rotate(360deg); } }
.pkwt-spin-icon { display: inline-block; animation: pkwt-dash-spin .7s linear infinite; }

@media (max-width: 960px) {
	.pkwt-tpl-grid { grid-template-columns: 1fr 1fr; }
	.pkwt-tpl-how__steps { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 640px) {
	.pkwt-tpl-grid { grid-template-columns: 1fr; }
	.pkwt-tpl-hero { flex-direction: column; }
}
</style>

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

<script>
(function(){
	var ajaxUrl = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;

	document.querySelectorAll('.pkwt-dash-import-btn').forEach(function(btn){
		btn.addEventListener('click', function(){
			var setSlug    = btn.dataset.set;
			var pageType   = btn.dataset.pageType;
			var card       = btn.closest('.pkwt-tpl-card');
			var statusEl   = card ? card.querySelector('.pkwt-dash-import-status') : null;
			var allBtns    = card ? card.querySelectorAll('.pkwt-dash-import-btn') : [btn];
			var origHTML   = btn.innerHTML;

			allBtns.forEach(function(b){ b.disabled = true; });
			btn.innerHTML = '<span class="pkwt-spin-icon">&#9696;</span> <?php echo esc_js( __( 'Importing…', 'powerkit-powerful-tools-for-your-website' ) ); ?>';

			if(statusEl){ statusEl.style.display='none'; statusEl.className='pkwt-dash-import-status'; statusEl.innerHTML=''; }

			// Step 1: fresh nonce at click time
			var nfd = new FormData();
			nfd.append('action', 'pkwt_get_import_nonce');

			fetch(ajaxUrl, { method:'POST', credentials:'same-origin', body:nfd })
			.then(function(r){ return r.json(); })
			.then(function(nd){
				if(!nd.success || !nd.data || !nd.data.nonce){ throw { isNonceFail:true }; }

				var fd = new FormData();
				fd.append('action',    'pkwt_ajax_import_template');
				fd.append('nonce',     nd.data.nonce);
				fd.append('set_slug',  setSlug);
				fd.append('page_type', pageType);

				return fetch(ajaxUrl, { method:'POST', credentials:'same-origin', body:fd })
				.then(function(r){
					return r.text().then(function(raw){
						var parsed;
						try { parsed = JSON.parse(raw); }
						catch(e){ throw { isRawResponse:true, raw:raw }; }
						return parsed;
					});
				});
			})
			.then(function(data){
				allBtns.forEach(function(b){ b.disabled = false; });
				btn.innerHTML = origHTML;
				if(statusEl){
					statusEl.style.display = 'block';
					if(data.success){
						statusEl.classList.add('pkwt-dash-import-status--ok');
						var msg  = (data.data && data.data.message) ? data.data.message : '<?php echo esc_js( __( 'Imported!', 'powerkit-powerful-tools-for-your-website' ) ); ?>';
						var html = '&#10003; <strong>' + msg + '</strong>';
						if(data.data && data.data.edit_url)  html += ' <a href="'+data.data.edit_url+'"  target="_blank" class="pkwt-dash-import-link"><?php echo esc_js( __( 'Open in Elementor →', 'powerkit-powerful-tools-for-your-website' ) ); ?></a>';
						if(data.data && data.data.view_url)  html += ' <a href="'+data.data.view_url+'"  target="_blank" class="pkwt-dash-import-link"><?php echo esc_js( __( 'View Page →', 'powerkit-powerful-tools-for-your-website' ) ); ?></a>';
						statusEl.innerHTML = html;
					} else {
						statusEl.classList.add('pkwt-dash-import-status--err');
						statusEl.innerHTML = '&#10007; ' + ((data.data && data.data.message) ? data.data.message : '<?php echo esc_js( __( 'Import failed.', 'powerkit-powerful-tools-for-your-website' ) ); ?>');
					}
				}
			})
			.catch(function(err){
				allBtns.forEach(function(b){ b.disabled = false; });
				btn.innerHTML = origHTML;
				if(statusEl){
					statusEl.style.display = 'block';
					statusEl.classList.add('pkwt-dash-import-status--err');
					var msg;
					if(err && err.isNonceFail) msg = '<?php echo esc_js( __( 'Session expired — please refresh the page.', 'powerkit-powerful-tools-for-your-website' ) ); ?>';
					else if(err && err.isRawResponse) msg = 'Server error: ' + String(err.raw||'').trim().slice(0,180);
					else msg = '<?php echo esc_js( __( 'Import failed. Please try again.', 'powerkit-powerful-tools-for-your-website' ) ); ?>';
					statusEl.innerHTML = '&#10007; ' + msg;
				}
			});
		});
	});
}());
</script>
