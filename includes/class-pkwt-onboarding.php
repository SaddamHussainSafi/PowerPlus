<?php
/**
 * Onboarding wizard.
 *
 * @package PKWT
 */

namespace PKWT\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Class_PKWT_Onboarding {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'register_page' ) );
		add_action( 'admin_post_pkwt_complete_onboarding', array( $this, 'complete' ) );
	}

	/**
	 * Register onboarding page.
	 *
	 * @return void
	 */
	public function register_page(): void {
		// Empty parent registers a hidden page; passing null is deprecated on PHP 8.1+.
		add_submenu_page(
			'',
			esc_html__( 'PowerPlus Onboarding', 'powerplus-toolkit' ),
			esc_html__( 'PowerPlus Onboarding', 'powerplus-toolkit' ),
			'manage_options',
			'pkwt-onboarding',
			array( $this, 'render' )
		);
	}

	/**
	 * Complete wizard.
	 *
	 * @return void
	 */
	public function complete(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'powerplus-toolkit' ) );
		}

		check_admin_referer( 'pkwt_onboarding_complete' );
		update_option( 'pkwt_wizard_complete', 1 );
		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings' ) );
		exit;
	}

	/**
	 * Render wizard page.
	 *
	 * @return void
	 */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// The standalone wizard has been superseded by the React dashboard, which shows
		// setup state (auth pages, Elementor status, template importer) in the new design.
		// Redirect here so bookmarked/legacy onboarding links land on the modern UI.
		wp_safe_redirect( admin_url( 'admin.php?page=pkwt-settings' ) );
		exit;
	}

	/**
	 * Legacy wizard renderer (retained for reference; no longer routed to).
	 *
	 * @return void
	 */
	public function render_legacy(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = get_option( 'pkwt_settings', array() );
		$steps    = array(
			__( 'Welcome', 'powerplus-toolkit' ),
			__( 'Page Setup', 'powerplus-toolkit' ),
			__( 'First Template', 'powerplus-toolkit' ),
			__( 'Done', 'powerplus-toolkit' ),
		);
		?>
		<div class="pkwt-onboarding-wrap" style="max-width:900px;margin:40px auto;background:#fff;padding:30px;border:1px solid #ddd;">
			<h1><?php echo esc_html__( 'PowerPlus — All-in-One Powerful Toolkit', 'powerplus-toolkit' ); ?></h1>
			<ol style="display:flex;gap:20px;list-style:decimal;padding-left:20px;">
				<?php foreach ( $steps as $step ) : ?>
					<li><?php echo esc_html( $step ); ?></li>
				<?php endforeach; ?>
			</ol>
			<h2><?php echo esc_html__( 'Step 1: Welcome', 'powerplus-toolkit' ); ?></h2>
			<p><?php echo esc_html__( 'This wizard creates authentication pages and helps you start editing with Elementor.', 'powerplus-toolkit' ); ?></p>
			<h2><?php echo esc_html__( 'Step 2: Page Setup', 'powerplus-toolkit' ); ?></h2>
			<ul>
				<li><?php echo esc_html( get_the_title( isset( $settings['login_page_id'] ) ? (int) $settings['login_page_id'] : 0 ) ); ?></li>
				<li><?php echo esc_html( get_the_title( isset( $settings['register_page_id'] ) ? (int) $settings['register_page_id'] : 0 ) ); ?></li>
				<li><?php echo esc_html( get_the_title( isset( $settings['lost_password_page_id'] ) ? (int) $settings['lost_password_page_id'] : 0 ) ); ?></li>
			</ul>
			<h2><?php echo esc_html__( 'Step 3: First Template', 'powerplus-toolkit' ); ?></h2>
			<p><?php echo esc_html__( 'Template choices: Simple, Modern, Split-screen.', 'powerplus-toolkit' ); ?></p>
			<h2><?php echo esc_html__( 'Step 4: Done', 'powerplus-toolkit' ); ?></h2>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=pkwt-settings' ) ); ?>"><?php esc_html_e( 'Open Settings', 'powerplus-toolkit' ); ?></a>
				<?php if ( ! empty( $settings['login_page_id'] ) ) : ?>
					<a class="button" href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $settings['login_page_id'] ) . '&action=elementor' ) ); ?>"><?php esc_html_e( 'Edit Login Page in Elementor', 'powerplus-toolkit' ); ?></a>
				<?php endif; ?>
			</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'pkwt_onboarding_complete' ); ?>
				<input type="hidden" name="action" value="pkwt_complete_onboarding" />
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Finish Wizard', 'powerplus-toolkit' ); ?></button>
			</form>
		</div>
		<?php
	}
}
