<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * All AJAX endpoints for the quote wizard. Registered for both logged-in
 * and logged-out users since this is a public-facing lead-gen form.
 */
class TTQ_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_ttq_validate_step', array( $this, 'validate_step' ) );
		add_action( 'wp_ajax_nopriv_ttq_validate_step', array( $this, 'validate_step' ) );

		add_action( 'wp_ajax_ttq_upload_logo', array( $this, 'upload_logo' ) );
		add_action( 'wp_ajax_nopriv_ttq_upload_logo', array( $this, 'upload_logo' ) );

		add_action( 'wp_ajax_ttq_submit_quote', array( $this, 'submit_quote' ) );
		add_action( 'wp_ajax_nopriv_ttq_submit_quote', array( $this, 'submit_quote' ) );
	}

	private function verify_nonce() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ttq_quote_nonce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed. Please refresh the page and try again.', 'ttq' ) ), 403 );
		}
	}

	/**
	 * Validates a single step's data via AJAX (called on "Next").
	 */
	public function validate_step() {
		$this->verify_nonce();

		$step = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';
		$raw  = isset( $_POST['fields'] ) ? (array) $_POST['fields'] : array();

		$settings = TTQ_Admin::get_settings();
		$result   = TTQ_Validation::validate_step( $step, $raw, $settings );

		if ( ! $result['valid'] ) {
			wp_send_json_error( array( 'errors' => $result['errors'] ), 422 );
		}

		wp_send_json_success( array( 'data' => $result['data'] ) );
	}

	/**
	 * Handles the logo upload independently, so the user gets instant
	 * feedback/preview without waiting for full form submission.
	 */
	public function upload_logo() {
		$this->verify_nonce();

		if ( empty( $_FILES['logo'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No file received.', 'ttq' ) ), 400 );
		}

		$settings = TTQ_Admin::get_settings();

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- validated below.
		$file = $_FILES['logo'];

		$validated = TTQ_Validation::validate_upload( $file, $settings );
		if ( is_wp_error( $validated ) ) {
			wp_send_json_error( array( 'message' => $validated->get_error_message() ), 422 );
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		add_filter( 'upload_dir', array( $this, 'filter_upload_dir' ) );
		$overrides = array( 'test_form' => false );
		$moved     = wp_handle_upload( $file, $overrides );
		remove_filter( 'upload_dir', array( $this, 'filter_upload_dir' ) );

		if ( isset( $moved['error'] ) ) {
			wp_send_json_error( array( 'message' => $moved['error'] ), 500 );
		}

		// Store a short-lived token pointing at the path so submit_quote can pick it up
		// without trusting a client-supplied file path directly.
		$token = wp_generate_password( 20, false );
		set_transient( 'ttq_logo_' . $token, $moved['file'], HOUR_IN_SECONDS );

		wp_send_json_success(
			array(
				'token'    => $token,
				'previewUrl' => $moved['url'],
				'fileName' => basename( $moved['file'] ),
			)
		);
	}

	public function filter_upload_dir( $dirs ) {
		$dirs['subdir'] = '/' . TTQ_UPLOAD_SUBDIR;
		$dirs['path']   = $dirs['basedir'] . $dirs['subdir'];
		$dirs['url']    = $dirs['baseurl'] . $dirs['subdir'];
		return $dirs;
	}

	/**
	 * Final submission: re-validates everything server-side (never trust the
	 * client, even though every step was already validated on the way through),
	 * persists a record, sends admin + customer emails.
	 */
	public function submit_quote() {
		$this->verify_nonce();

		$settings = TTQ_Admin::get_settings();

		$step1 = TTQ_Validation::validate_step( 'step-1', isset( $_POST['step1'] ) ? (array) $_POST['step1'] : array(), $settings );

		$step2_raw             = isset( $_POST['step2'] ) ? (array) $_POST['step2'] : array();
		$step2_raw['product']  = $step1['data']['product'];
		$step2                 = TTQ_Validation::validate_step( 'step-2', $step2_raw, $settings );

		$step3 = TTQ_Validation::validate_step( 'step-3', isset( $_POST['step3'] ) ? (array) $_POST['step3'] : array(), $settings );

		$errors = array_merge( $step1['errors'], $step2['errors'], $step3['errors'] );
		if ( ! empty( $errors ) ) {
			wp_send_json_error( array( 'errors' => $errors, 'message' => __( 'Please review the highlighted fields.', 'ttq' ) ), 422 );
		}

		$logo_path = '';
		$logo_url  = '';
		if ( ! empty( $_POST['logo_token'] ) ) {
			$token = sanitize_text_field( wp_unslash( $_POST['logo_token'] ) );
			$path  = get_transient( 'ttq_logo_' . $token );
			if ( $path && file_exists( $path ) ) {
				$logo_path = $path;
				$upload_dir = wp_upload_dir();
				$logo_url   = trailingslashit( $upload_dir['baseurl'] ) . TTQ_UPLOAD_SUBDIR . '/' . basename( $path );
			}
		}

		$submission = array(
			'product'       => $step1['data']['product'],
			'quantity'      => $step2['data']['quantity'],
			'colors'        => $step2['data']['colors'],
			'custom_color'  => isset( $step2['data']['custom_color'] ) ? $step2['data']['custom_color'] : '',
			'sizes'         => $step2['data']['sizes'],
			'side1'         => $step2['data']['side1'],
			'side2'         => $step2['data']['side2'],
			'material'      => isset( $step2['data']['material'] ) ? $step2['data']['material'] : '',
			'carabiner_clip' => isset( $step2['data']['carabiner_clip'] ) ? $step2['data']['carabiner_clip'] : '',
			'comments'      => isset( $step2['data']['comments'] ) ? $step2['data']['comments'] : '',
			'organization'  => $step3['data']['organization'],
			'name'          => $step3['data']['name'],
			'phone'         => $step3['data']['phone'],
			'email'         => $step3['data']['email'],
			'address'       => $step3['data']['address'],
			'free_sample'   => $step3['data']['free_sample'],
			'logo_url'      => $logo_url,
			'logo_path'     => $logo_path,
			'submitted_at'  => current_time( 'mysql' ),
		);

		/**
		 * Persist as a CPT-free lightweight record via options/post — kept simple
		 * here as a custom post type row so it shows up nicely in wp-admin list
		 * tables without a custom DB table/migration to maintain.
		 */
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'ttq_quote',
				'post_status' => 'publish',
				'post_title'  => sprintf( '%s — %s', $submission['name'], $submission['product'] ),
				'meta_input'  => array( '_ttq_submission' => $submission ),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Could not save your request. Please try again.', 'ttq' ) ), 500 );
		}

		TTQ_Email::send_admin_notification( $submission, $settings );
		TTQ_Email::send_customer_confirmation( $submission, $settings );

		wp_send_json_success(
			array(
				'message'   => $settings['success_message'],
				'reference' => $post_id,
			)
		);
	}
}
