<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Singleton orchestrator. Wires up shortcode, ajax handlers and admin screen.
 */
final class TTQ_Plugin {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		new TTQ_Shortcode();
		new TTQ_Ajax();

		if ( is_admin() ) {
			new TTQ_Admin();
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_assets' ) );
	}

	/**
	 * Only load CSS/JS on pages that actually contain the shortcode,
	 * so we don't bloat every page on the site.
	 */
	public function maybe_enqueue_assets() {
		global $post;

		$should_load = false;

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'ticktweezers_quote' ) ) {
			$should_load = true;
		}

		/**
		 * Allow force-loading assets, e.g. if the shortcode is rendered inside
		 * an Elementor template part where has_shortcode() can't see it.
		 */
		$should_load = apply_filters( 'ttq_force_enqueue_assets', $should_load );

		if ( ! $should_load ) {
			return;
		}

		wp_enqueue_style(
			'ttq-quote',
			TTQ_URL . 'assets/css/quote.css',
			array(),
			TTQ_VERSION
		);

		wp_enqueue_script(
			'ttq-quote',
			TTQ_URL . 'assets/js/quote.js',
			array(),
			TTQ_VERSION,
			true
		);

		$settings = TTQ_Admin::get_settings();
		$products = TTQ_Admin::get_dynamic_products();

		wp_localize_script(
			'ttq-quote',
			'TTQ_DATA',
			array(
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'ttq_quote_nonce' ),
				'redirectUrl'   => home_url( '/quote-sample-submitted/' ),
				'products'      => $products,
				'colors'        => $settings['colors'],
				'sizes'         => $settings['sizes'],
				'minQuantity'   => (int) $settings['min_quantity'],
				'maxQuantity'   => (int) $settings['max_quantity'],
				'allowedTypes'  => $settings['allowed_file_types'],
				'maxUploadMb'   => (int) $settings['max_upload_mb'],
				'personalMax'   => (int) $settings['personalization_max_chars'],
				'i18n'          => array(
					'required'        => __( 'This field is required.', 'ttq' ),
					'invalidEmail'    => __( 'Please enter a valid email address.', 'ttq' ),
					'invalidPhone'    => __( 'Please enter a valid phone number.', 'ttq' ),
					'poBoxNotAllowed' => __( 'PO Boxes are not accepted. Please enter a physical delivery address.', 'ttq' ),
					'fileTooLarge'    => __( 'File is too large.', 'ttq' ),
					'fileTypeInvalid' => __( 'File type not supported.', 'ttq' ),
					'minQty'          => __( 'Quantity is below the minimum allowed.', 'ttq' ),
					'maxQty'          => __( 'Quantity exceeds the maximum allowed.', 'ttq' ),
					'genericError'    => __( 'Something went wrong. Please try again.', 'ttq' ),
					'submitting'      => __( 'Submitting Your Request…', 'ttq' ),
					'submittingSub'   => __( 'Please wait while we process your quote request.', 'ttq' ),
					'successTitle'    => __( 'Quote Submitted Successfully!', 'ttq' ),
					'successSub'      => $settings['success_message'],
					'stepOf1'         => __( 'Step 1 of 3', 'ttq' ),
					'stepOf2'         => __( 'Step 2 of 3', 'ttq' ),
					'stepOf3'         => __( 'Step 3 of 3', 'ttq' ),
					'stepOf4'         => __( 'Step 3 of 3', 'ttq' ),
					'title1'          => __( 'Choose Your Product', 'ttq' ),
					'title2'          => __( 'Customize Your Specifications', 'ttq' ),
					'title3'          => __( 'Contact Details', 'ttq' ),
					'title4'          => __( 'Review Your Specifications', 'ttq' ),
				),
			)
		);
	}
}
