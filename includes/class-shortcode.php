<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers [ticktweezers_quote]. This is the ONLY thing Elementor
 * (or any page builder) is allowed to touch — it drops the shortcode
 * into a page; everything else is rendered/controlled by this plugin.
 */
class TTQ_Shortcode {

	public function __construct() {
		add_shortcode( 'ticktweezers_quote', array( $this, 'render' ) );
	}

	public function render( $atts = array() ) {
		$settings = TTQ_Admin::get_settings();

		ob_start();
		include TTQ_PATH . 'templates/quote-form.php';
		return ob_get_clean();
	}
}
