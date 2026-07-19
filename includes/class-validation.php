<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Central place for sanitizing + validating quote data.
 * Nothing from $_POST / $_FILES should be trusted anywhere else in the plugin.
 */
class TTQ_Validation {

	/**
	 * @return array { valid: bool, errors: array<string,string>, data: array }
	 */
	public static function validate_step( $step, array $raw, array $settings ) {
		$errors = array();
		$data   = array();

		switch ( $step ) {

			case 'step-1':
				$product = isset( $raw['product'] ) ? sanitize_key( wp_unslash( $raw['product'] ) ) : '';
				$valid_keys = wp_list_pluck( $settings['products'], 'key' );
				if ( '' === $product || ! in_array( $product, $valid_keys, true ) ) {
					$errors['product'] = __( 'Please choose a product.', 'ttq' );
				}
				$data['product'] = $product;
				break;

			case 'step-2':
				$quantity = isset( $raw['quantity'] ) ? absint( $raw['quantity'] ) : 0;
				if ( $quantity < (int) $settings['min_quantity'] ) {
					$errors['quantity'] = __( 'Quantity is below the minimum allowed.', 'ttq' );
				} elseif ( $quantity > (int) $settings['max_quantity'] ) {
					$errors['quantity'] = __( 'Quantity exceeds the maximum allowed.', 'ttq' );
				}
				$data['quantity'] = $quantity;

				$colors = array();
				if ( ! empty( $raw['colors'] ) && is_array( $raw['colors'] ) ) {
					foreach ( wp_unslash( $raw['colors'] ) as $c ) {
						$colors[] = TTQ_Admin::sanitize_option_key( $c );
					}
				}
				$custom_color = isset( $raw['custom_color'] ) ? sanitize_text_field( wp_unslash( $raw['custom_color'] ) ) : '';
				if ( empty( $colors ) && empty( $custom_color ) ) {
					$errors['colors'] = __( 'Please select at least one color or type a custom color.', 'ttq' );
				}
				$data['colors']       = $colors;
				$data['custom_color'] = $custom_color;

				$sizes = array();
				if ( ! empty( $raw['sizes'] ) && is_array( $raw['sizes'] ) ) {
					foreach ( wp_unslash( $raw['sizes'] ) as $s ) {
						$sizes[] = TTQ_Admin::sanitize_option_key( $s );
					}
				}

				// Sizes are opt-in per product. Only require a selection when
				// the chosen product actually has sizes configured; otherwise
				// the size step is hidden on the front end and shouldn't be
				// enforced here either.
				$sizes_required = true;
				if ( isset( $raw['product'] ) && class_exists( 'TTQ_Admin' ) ) {
					$product_key = sanitize_key( wp_unslash( $raw['product'] ) );
					foreach ( TTQ_Admin::get_dynamic_products() as $product_row ) {
						if ( $product_row['key'] === $product_key ) {
							$sizes_required = '' !== trim( (string) $product_row['sizes'] );
							break;
						}
					}
				}

				if ( $sizes_required && empty( $sizes ) ) {
					$errors['sizes'] = __( 'Please select at least one size.', 'ttq' );
				}
				$data['sizes'] = $sizes;

				$max_chars = (int) $settings['personalization_max_chars'];
				$side1 = isset( $raw['side1'] ) ? sanitize_text_field( wp_unslash( $raw['side1'] ) ) : '';
				$side2 = isset( $raw['side2'] ) ? sanitize_text_field( wp_unslash( $raw['side2'] ) ) : '';
				if ( mb_strlen( $side1 ) > $max_chars ) {
					$errors['side1'] = sprintf( __( 'Maximum %d characters.', 'ttq' ), $max_chars );
				}
				if ( mb_strlen( $side2 ) > $max_chars ) {
					$errors['side2'] = sprintf( __( 'Maximum %d characters.', 'ttq' ), $max_chars );
				}
				$data['side1'] = $side1;
				$data['side2'] = $side2;

				// Material & carabiner clip only apply to the Complete Tick Kit product.
				$product_key_for_kit = isset( $raw['product'] ) ? sanitize_key( wp_unslash( $raw['product'] ) ) : '';
				$material = isset( $raw['material'] ) ? sanitize_key( wp_unslash( $raw['material'] ) ) : '';
				$clip     = isset( $raw['carabiner_clip'] ) && 'yes' === $raw['carabiner_clip'] ? 'yes' : 'no';

				if ( 'complete_kit' === $product_key_for_kit ) {
					if ( '' === $material || ! in_array( $material, array( 'fabric', 'synthetic_leather' ), true ) ) {
						$errors['material'] = __( 'Please choose a material.', 'ttq' );
					}
					$data['material']       = $material ? $material : 'fabric';
					$data['carabiner_clip'] = $clip;
				} else {
					$data['material']       = '';
					$data['carabiner_clip'] = '';
				}

				$data['comments'] = isset( $raw['comments'] ) ? sanitize_textarea_field( wp_unslash( $raw['comments'] ) ) : '';
				break;

			case 'step-3':
				$org   = isset( $raw['organization'] ) ? sanitize_text_field( wp_unslash( $raw['organization'] ) ) : '';
				$name  = isset( $raw['name'] ) ? sanitize_text_field( wp_unslash( $raw['name'] ) ) : '';
				$phone = isset( $raw['phone'] ) ? sanitize_text_field( wp_unslash( $raw['phone'] ) ) : '';
				$email = isset( $raw['email'] ) ? sanitize_email( wp_unslash( $raw['email'] ) ) : '';
				$address = isset( $raw['address'] ) ? sanitize_textarea_field( wp_unslash( $raw['address'] ) ) : '';
				$free_sample = isset( $raw['free_sample'] ) && 'yes' === $raw['free_sample'] ? 'yes' : 'no';

				if ( '' === $name ) {
					$errors['name'] = __( 'Name is required.', 'ttq' );
				}
				if ( '' === $phone || ! preg_match( '/^[0-9+\-() .]{7,20}$/', $phone ) ) {
					$errors['phone'] = __( 'Please enter a valid phone number.', 'ttq' );
				}
				if ( '' === $email || ! is_email( $email ) ) {
					$errors['email'] = __( 'Please enter a valid email address.', 'ttq' );
				}
				// Shipping address is only required when the user requested a free sample.
				if ( 'yes' === $free_sample ) {
					if ( '' === $address ) {
						$errors['address'] = __( 'Shipping address is required for a free sample.', 'ttq' );
					} elseif ( self::looks_like_po_box( $address ) ) {
						$errors['address'] = __( 'PO Boxes are not accepted. Please enter a physical delivery address.', 'ttq' );
					}
				} elseif ( '' !== $address && self::looks_like_po_box( $address ) ) {
					$errors['address'] = __( 'PO Boxes are not accepted. Please enter a physical delivery address.', 'ttq' );
				}

				$data['organization'] = $org;
				$data['name']         = $name;
				$data['phone']        = $phone;
				$data['email']        = $email;
				$data['address']      = $address;
				$data['free_sample']  = $free_sample;
				break;
		}

		return array(
			'valid'  => empty( $errors ),
			'errors' => $errors,
			'data'   => $data,
		);
	}

	private static function looks_like_po_box( $address ) {
		return (bool) preg_match( '/\bP(ost)?\.?\s?O\.?\s?Box\b/i', $address );
	}

	/**
	 * Validate an uploaded logo file. Returns WP_Error or the $_FILES sub-array.
	 */
	public static function validate_upload( array $file, array $settings ) {
		if ( empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
			return new WP_Error( 'ttq_no_file', __( 'No file was uploaded.', 'ttq' ) );
		}

		$max_bytes = (int) $settings['max_upload_mb'] * MB_IN_BYTES;
		if ( $file['size'] > $max_bytes ) {
			return new WP_Error( 'ttq_file_too_large', __( 'File is too large.', 'ttq' ) );
		}

		$allowed = array_map( 'trim', explode( ',', $settings['allowed_file_types'] ) );
		$filetype = wp_check_filetype( $file['name'] );

		if ( empty( $filetype['ext'] ) || ! in_array( strtolower( $filetype['ext'] ), $allowed, true ) ) {
			return new WP_Error( 'ttq_file_type_invalid', __( 'File type not supported.', 'ttq' ) );
		}

		return $file;
	}
}
