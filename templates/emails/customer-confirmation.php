<?php
/**
 * @var array $submission
 * @var array $settings
 * @var string $product_label
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Look up the full product row (for image / price / best-for) instead of just the label.
$product_row = array();
foreach ( $settings['products'] as $p ) {
	if ( $p['key'] === $submission['product'] ) {
		$product_row = $p;
		break;
	}
}

// Build a key => {label,hex} map for colors so swatches render with real brand colors.
$color_map = array();
foreach ( $settings['colors'] as $c ) {
	$color_map[ $c['key'] ] = $c;
}

// Build a key => label map for sizes.
$size_map = array();
foreach ( $settings['sizes'] as $s ) {
	$size_map[ $s['key'] ] = $s['label'];
}

$selected_sizes = array();
foreach ( $submission['sizes'] as $size_key ) {
	$selected_sizes[] = isset( $size_map[ $size_key ] ) ? $size_map[ $size_key ] : $size_key;
}

$submitted_at = ! empty( $submission['submitted_at'] )
	? date_i18n( 'M j, Y g:i A', strtotime( $submission['submitted_at'] ) )
	: date_i18n( 'M j, Y g:i A' );
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo esc_html( $settings['company_name'] ); ?></title>
</head>
<body style="margin:0;padding:0;background:#f4f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f7;padding:32px 12px;">
<tr>
<td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;">

	<!-- Header -->
	<tr>
		<td style="background:#b71c2b;background:linear-gradient(135deg,#b71c2b 0%,#8f1521 100%);border-radius:12px 12px 0 0;padding:32px 32px 28px;text-align:center;">
			<p style="margin:0 0 6px;font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#f8c9ce;font-weight:600;">Order Confirmation</p>
			<h1 style="margin:0;font-size:24px;line-height:1.3;color:#ffffff;font-weight:700;"><?php echo esc_html( $settings['company_name'] ); ?></h1>
		</td>
	</tr>

	<!-- Success ribbon -->
	<tr>
		<td style="background:#ffffff;padding:0 32px;">
			<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:-18px;">
				<tr>
					<td align="center" style="background:#ecfdf5;border:1px solid #6ee7b7;border-radius:999px;padding:10px 20px;">
						<span style="color:#065f46;font-size:13px;font-weight:700;letter-spacing:0.3px;">&#10003; REQUEST RECEIVED</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<!-- Greeting -->
	<tr>
		<td style="background:#ffffff;padding:28px 32px 8px;">
			<p style="margin:0 0 14px;font-size:16px;color:#111827;">Hi <strong><?php echo esc_html( $submission['name'] ); ?></strong>,</p>
			<p style="margin:0 0 4px;font-size:15px;line-height:1.6;color:#374151;">
				Thank you for submitting your sample / quote request on
				<a href="https://ticktweezers.com" style="color:#b71c2b;text-decoration:none;font-weight:600;">ticktweezers.com</a>.
				Your request has been successfully received and is now being processed &mdash; we'll reach out to you soon with more details.
			</p>
			<p style="margin:14px 0 0;font-size:13px;line-height:1.6;color:#6b7280;">
				Haven't heard from us within a few hours? Please check your spam folder or email us at
				<a href="mailto:sales@ticktweezers.com" style="color:#b71c2b;text-decoration:none;">sales@ticktweezers.com</a>.
			</p>
		</td>
	</tr>

	<!-- Voucher -->
	<tr>
		<td style="background:#ffffff;padding:22px 32px 0;">
			<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:2px dashed #d1d5db;border-radius:14px;background:#fdf3f4;">
				<tr>
					<td style="padding:22px 24px 8px;">
						<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td style="font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#b71c2b;font-weight:700;">Your Submission</td>
								<td align="right" style="font-size:11px;color:#9ca3af;">Ref. <?php echo esc_html( strtoupper( substr( md5( $submission['email'] . $submitted_at ), 0, 8 ) ) ); ?></td>
							</tr>
						</table>
					</td>
				</tr>

				<!-- Product row -->
				<tr>
					<td style="padding:10px 24px 18px;">
						<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;border:1px solid #e5e7eb;">
							<?php if ( ! empty( $product_row['image'] ) ) : ?>
							<tr>
								<td style="padding:16px 16px 0;">
									<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;">
										<tr>
											<td align="center" style="padding:14px;">
												<img src="<?php echo esc_url( $product_row['image'] ); ?>" width="220" alt="<?php echo esc_attr( $product_label ); ?>" style="display:block;width:220px;max-width:100%;height:auto;object-fit:contain;border-radius:6px;" />
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<?php endif; ?>
							<tr>
								<td style="padding:14px;" valign="top">
									<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
										<tr>
											<td valign="top">
												<p style="margin:0 0 3px;font-size:16px;font-weight:700;color:#111827;"><?php echo esc_html( $product_label ); ?></p>
												<?php if ( ! empty( $product_row['best_for'] ) ) : ?>
												<p style="margin:0 0 6px;font-size:12px;color:#6b7280;"><?php echo esc_html( $product_row['best_for'] ); ?></p>
												<?php endif; ?>
												<?php if ( ! empty( $product_row['price'] ) ) : ?>
												<p style="margin:0;font-size:12px;color:#b71c2b;font-weight:700;">$<?php echo esc_html( $product_row['price'] ); ?> / unit</p>
												<?php endif; ?>
											</td>
											<td align="right" valign="top">
												<p style="margin:0;font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;">Quantity</p>
												<p style="margin:2px 0 0;font-size:20px;font-weight:700;color:#111827;"><?php echo esc_html( number_format_i18n( $submission['quantity'] ) ); ?></p>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<!-- Colors -->
				<tr>
					<td style="padding:0 24px 16px;">
						<p style="margin:0 0 8px;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;font-weight:700;">Color(s) Selected</p>
						<table role="presentation" cellpadding="0" cellspacing="0">
							<tr>
								<?php foreach ( $submission['colors'] as $color_key ) :
									$c_label = isset( $color_map[ $color_key ]['label'] ) ? $color_map[ $color_key ]['label'] : ucwords( str_replace( '_', ' ', $color_key ) );
									$c_hex   = isset( $color_map[ $color_key ]['hex'] ) ? $color_map[ $color_key ]['hex'] : '#cccccc';
								?>
								<td style="padding:0 8px 8px 0;">
									<table role="presentation" cellpadding="0" cellspacing="0" style="background:#ffffff;border:1px solid #e5e7eb;border-radius:999px;">
										<tr>
											<td style="padding:5px 12px 5px 6px;">
												<table role="presentation" cellpadding="0" cellspacing="0">
													<tr>
														<td style="width:16px;height:16px;border-radius:50%;background:<?php echo esc_attr( $c_hex ); ?>;border:1px solid #d1d5db;font-size:0;line-height:0;">&nbsp;</td>
														<td style="padding-left:7px;font-size:12px;color:#374151;font-weight:600;white-space:nowrap;"><?php echo esc_html( $c_label ); ?></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
								<?php endforeach; ?>
								<?php if ( ! empty( $submission['custom_color'] ) ) : ?>
								<td style="padding:0 8px 8px 0;">
									<table role="presentation" cellpadding="0" cellspacing="0" style="background:#ffffff;border:1px dashed #b71c2b;border-radius:999px;">
										<tr>
											<td style="padding:5px 12px;font-size:12px;color:#b71c2b;font-weight:600;white-space:nowrap;"><?php echo esc_html( $submission['custom_color'] ); ?> (Custom)</td>
										</tr>
									</table>
								</td>
								<?php endif; ?>
							</tr>
						</table>
					</td>
				</tr>

				<?php if ( ! empty( $selected_sizes ) ) : ?>
				<!-- Sizes -->
				<tr>
					<td style="padding:0 24px 16px;">
						<p style="margin:0 0 8px;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;font-weight:700;">Size(s)</p>
						<table role="presentation" cellpadding="0" cellspacing="0">
							<tr>
								<?php foreach ( $selected_sizes as $size_label ) : ?>
								<td style="padding:0 8px 0 0;">
									<table role="presentation" cellpadding="0" cellspacing="0" style="background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;">
										<tr><td style="padding:6px 14px;font-size:12px;color:#374151;font-weight:600;"><?php echo esc_html( $size_label ); ?></td></tr>
									</table>
								</td>
								<?php endforeach; ?>
							</tr>
						</table>
					</td>
				</tr>
				<?php endif; ?>

				<?php if ( ! empty( $submission['side1'] ) || ! empty( $submission['side2'] ) ) : ?>
				<!-- Personalization -->
				<tr>
					<td style="padding:0 24px 16px;">
						<p style="margin:0 0 8px;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;font-weight:700;">Personalization</p>
						<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;border:1px solid #e5e7eb;border-radius:10px;">
							<?php if ( ! empty( $submission['side1'] ) ) : ?>
							<tr>
								<td style="padding:10px 14px;border-bottom:<?php echo ! empty( $submission['side2'] ) ? '1px solid #f0f0f0' : 'none'; ?>;">
									<span style="font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;">Side 1&nbsp;&nbsp;</span>
									<span style="font-size:13px;color:#111827;font-weight:600;"><?php echo esc_html( $submission['side1'] ); ?></span>
								</td>
							</tr>
							<?php endif; ?>
							<?php if ( ! empty( $submission['side2'] ) ) : ?>
							<tr>
								<td style="padding:10px 14px;">
									<span style="font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.5px;">Side 2&nbsp;&nbsp;</span>
									<span style="font-size:13px;color:#111827;font-weight:600;"><?php echo esc_html( $submission['side2'] ); ?></span>
								</td>
							</tr>
							<?php endif; ?>
						</table>
					</td>
				</tr>
				<?php endif; ?>

				<?php if ( ! empty( $submission['material'] ) || ! empty( $submission['carabiner_clip'] ) ) : ?>
				<!-- Kit options -->
				<tr>
					<td style="padding:0 24px 16px;">
						<p style="margin:0 0 8px;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;font-weight:700;">Kit Options</p>
						<table role="presentation" cellpadding="0" cellspacing="0">
							<tr>
								<?php if ( ! empty( $submission['material'] ) ) : ?>
								<td style="padding:0 8px 8px 0;">
									<table role="presentation" cellpadding="0" cellspacing="0" style="background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;">
										<tr><td style="padding:6px 14px;font-size:12px;color:#374151;font-weight:600;">Material: <?php echo esc_html( 'fabric' === $submission['material'] ? 'Fabric / Canvas' : 'Synthetic Leather' ); ?></td></tr>
									</table>
								</td>
								<?php endif; ?>
								<?php if ( ! empty( $submission['carabiner_clip'] ) ) : ?>
								<td style="padding:0 8px 8px 0;">
									<table role="presentation" cellpadding="0" cellspacing="0" style="background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;">
										<tr><td style="padding:6px 14px;font-size:12px;color:#374151;font-weight:600;">Carabiner Clip: <?php echo esc_html( 'yes' === $submission['carabiner_clip'] ? 'Yes' : 'No' ); ?></td></tr>
									</table>
								</td>
								<?php endif; ?>
							</tr>
						</table>
					</td>
				</tr>
				<?php endif; ?>

				<?php if ( ! empty( $submission['comments'] ) ) : ?>
				<!-- Additional comments -->
				<tr>
					<td style="padding:0 24px 16px;">
						<p style="margin:0 0 8px;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;font-weight:700;">Additional Comments</p>
						<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;border:1px solid #e5e7eb;border-radius:10px;">
							<tr><td style="padding:10px 14px;font-size:13px;color:#111827;"><?php echo nl2br( esc_html( $submission['comments'] ) ); ?></td></tr>
						</table>
					</td>
				</tr>
				<?php endif; ?>

				<!-- Free sample -->
				<tr>
					<td style="padding:0 24px 20px;">
						<table role="presentation" cellpadding="0" cellspacing="0">
							<tr>
								<td style="font-size:12px;color:#6b7280;padding-right:8px;">Free Sample Requested:</td>
								<td>
									<?php if ( 'yes' === $submission['free_sample'] ) : ?>
										<span style="display:inline-block;background:#ecfdf5;color:#065f46;border:1px solid #6ee7b7;border-radius:999px;padding:3px 12px;font-size:12px;font-weight:700;">Yes</span>
									<?php else : ?>
										<span style="display:inline-block;background:#f4f5f7;color:#6b7280;border:1px solid #e5e7eb;border-radius:999px;padding:3px 12px;font-size:12px;font-weight:700;">No</span>
									<?php endif; ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<!-- Contact & delivery -->
	<tr>
		<td style="background:#ffffff;padding:22px 32px 0;">
			<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:10px;">
				<tr>
					<td style="padding:18px 22px;">
						<p style="margin:0 0 12px;font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;font-weight:700;">Contact &amp; Delivery</p>
						<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;color:#374151;line-height:1.9;">
							<tr>
								<td width="120" style="color:#9ca3af;vertical-align:top;">Name</td>
								<td style="font-weight:600;color:#111827;"><?php echo esc_html( $submission['name'] ); ?></td>
							</tr>
							<?php if ( ! empty( $submission['organization'] ) ) : ?>
							<tr>
								<td style="color:#9ca3af;vertical-align:top;">Organization</td>
								<td style="font-weight:600;color:#111827;"><?php echo esc_html( $submission['organization'] ); ?></td>
							</tr>
							<?php endif; ?>
							<tr>
								<td style="color:#9ca3af;vertical-align:top;">Phone</td>
								<td style="font-weight:600;color:#111827;"><?php echo esc_html( $submission['phone'] ); ?></td>
							</tr>
							<tr>
								<td style="color:#9ca3af;vertical-align:top;">Email</td>
								<td style="font-weight:600;color:#111827;"><?php echo esc_html( $submission['email'] ); ?></td>
							</tr>
							<?php if ( ! empty( $submission['address'] ) ) : ?>
							<tr>
								<td style="color:#9ca3af;vertical-align:top;padding-top:4px;">Delivery Address</td>
								<td style="font-weight:600;color:#111827;padding-top:4px;"><?php echo nl2br( esc_html( $submission['address'] ) ); ?></td>
							</tr>
							<?php endif; ?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<!-- Message + sign-off -->
	<tr>
		<td style="background:#ffffff;padding:24px 32px 8px;">
			<p style="margin:0;font-size:14px;line-height:1.7;color:#374151;"><?php echo esc_html( $settings['success_message'] ); ?></p>
			<p style="margin:18px 0 0;font-size:14px;color:#111827;">&mdash; The <?php echo esc_html( $settings['company_name'] ); ?> Team</p>
		</td>
	</tr>

	<!-- Footer -->
	<tr>
		<td style="background:#ffffff;border-radius:0 0 12px 12px;padding:24px 32px 32px;">
			<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #e5e7eb;padding-top:18px;">
				<tr>
					<td style="padding-top:18px;text-align:center;">
						<p style="margin:0 0 4px;font-size:11px;color:#9ca3af;">Submitted <?php echo esc_html( $submitted_at ); ?></p>
						<p style="margin:0;font-size:11px;color:#9ca3af;">
							<?php echo esc_html( $settings['company_name'] ); ?> &middot;
							<a href="mailto:sales@ticktweezers.com" style="color:#9ca3af;">sales@ticktweezers.com</a>
						</p>
					</td>
				</tr>
			</table>
		</td>
	</tr>

</table>
</td>
</tr>
</table>
</body>
</html>
