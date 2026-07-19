<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds + sends the two notification emails (admin + customer).
 * Kept as plain static helpers — no state, easy to unit test.
 */
class TTQ_Email {

	public static function send_admin_notification( array $submission, array $settings ) {
		$to      = ! empty( $settings['email_recipients'] ) ? $settings['email_recipients'] : get_option( 'admin_email' );
		$product_label = self::product_label( $submission['product'], $settings );
		$subject = sprintf( __( 'New Quote Request — %1$s (%2$s units)', 'ttq' ), $product_label, $submission['quantity'] );

		$body = self::render_template(
			'admin-notification',
			array(
				'submission'    => $submission,
				'settings'      => $settings,
				'product_label' => $product_label,
			)
		);

		self::send( $to, $subject, $body );
	}

	public static function send_customer_confirmation( array $submission, array $settings ) {
		$product_label = self::product_label( $submission['product'], $settings );
		$subject = __( 'We received your TickTweezers quote request', 'ttq' );

		$body = self::render_template(
			'customer-confirmation',
			array(
				'submission'    => $submission,
				'settings'      => $settings,
				'product_label' => $product_label,
			)
		);

		self::send( $submission['email'], $subject, $body );
	}

	private static function send( $to, $subject, $html_body ) {
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		wp_mail( $to, $subject, $html_body, $headers );
	}

	private static function product_label( $key, $settings ) {
		foreach ( $settings['products'] as $p ) {
			if ( $p['key'] === $key ) {
				return $p['label'];
			}
		}
		return $key;
	}

	private static function render_template( $name, array $vars ) {
		$path = TTQ_PATH . 'templates/emails/' . $name . '.php';
		if ( ! file_exists( $path ) ) {
			return '';
		}
		extract( $vars ); // phpcs:ignore WordPress.PHP.DontExtract
		ob_start();
		include $path;
		return ob_get_clean();
	}
}
