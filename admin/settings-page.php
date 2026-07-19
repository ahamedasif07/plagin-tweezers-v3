<?php
/** @var array $settings */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap ttq-admin">
	<h1><?php esc_html_e( 'TickTweezers Quote Settings', 'ttq' ); ?></h1>
	<p><?php esc_html_e( 'Configure products, options and rules for the [ticktweezers_quote] form. Nothing here is hard-coded in templates.', 'ttq' ); ?></p>

	<form method="post" action="options.php">
		<?php settings_fields( 'ttq_settings_group' ); ?>

		<h2 class="title"><?php esc_html_e( 'Products', 'ttq' ); ?></h2>
		<div class="notice notice-info" style="margin: 15px 0; padding: 12px 15px; border-left-color: #b71c2b;">
			<p style="margin: 0 0 6px; font-size: 14px; font-weight: 600;">
				<?php esc_html_e( 'Products are now managed in their own dedicated menu!', 'ttq' ); ?>
			</p>
			<p style="margin: 0;">
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=ttq_product' ) ); ?>">
					<?php esc_html_e( 'Manage Products (Add, Edit, Delete)', 'ttq' ); ?>
				</a>
			</p>
		</div>

		<h2 class="title"><?php esc_html_e( 'Colors', 'ttq' ); ?></h2>
		<div id="ttq-repeater-colors" class="ttq-repeater" data-rows="<?php echo esc_attr( wp_json_encode( $settings['colors'] ) ); ?>" data-fields="key,label,hex"></div>
		<input type="hidden" name="ttq_settings[colors]" id="ttq-repeater-colors-input" />

		<h2 class="title"><?php esc_html_e( 'Sizes', 'ttq' ); ?></h2>
		<div id="ttq-repeater-sizes" class="ttq-repeater" data-rows="<?php echo esc_attr( wp_json_encode( $settings['sizes'] ) ); ?>" data-fields="key,label"></div>
		<input type="hidden" name="ttq_settings[sizes]" id="ttq-repeater-sizes-input" />

		<h2 class="title"><?php esc_html_e( 'Rules', 'ttq' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th><label for="ttq-min-qty"><?php esc_html_e( 'Minimum Quantity', 'ttq' ); ?></label></th>
				<td><input type="number" id="ttq-min-qty" name="ttq_settings[min_quantity]" value="<?php echo esc_attr( $settings['min_quantity'] ); ?>" class="small-text" /></td>
			</tr>
			<tr>
				<th><label for="ttq-max-qty"><?php esc_html_e( 'Maximum Quantity', 'ttq' ); ?></label></th>
				<td><input type="number" id="ttq-max-qty" name="ttq_settings[max_quantity]" value="<?php echo esc_attr( $settings['max_quantity'] ); ?>" class="small-text" /></td>
			</tr>
			<tr>
				<th><label for="ttq-personal-max"><?php esc_html_e( 'Personalization Max Characters', 'ttq' ); ?></label></th>
				<td><input type="number" id="ttq-personal-max" name="ttq_settings[personalization_max_chars]" value="<?php echo esc_attr( $settings['personalization_max_chars'] ); ?>" class="small-text" /></td>
			</tr>
			<tr>
				<th><label for="ttq-file-types"><?php esc_html_e( 'Allowed File Types', 'ttq' ); ?></label></th>
				<td><input type="text" id="ttq-file-types" name="ttq_settings[allowed_file_types]" value="<?php echo esc_attr( $settings['allowed_file_types'] ); ?>" class="regular-text" /><p class="description"><?php esc_html_e( 'Comma-separated, no dots (e.g. png,jpg,svg,pdf)', 'ttq' ); ?></p></td>
			</tr>
			<tr>
				<th><label for="ttq-max-upload"><?php esc_html_e( 'Max Upload Size (MB)', 'ttq' ); ?></label></th>
				<td><input type="number" id="ttq-max-upload" name="ttq_settings[max_upload_mb]" value="<?php echo esc_attr( $settings['max_upload_mb'] ); ?>" class="small-text" /></td>
			</tr>
		</table>

		<h2 class="title"><?php esc_html_e( 'Emails & Company', 'ttq' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th><label for="ttq-email-recipients"><?php esc_html_e( 'Notification Recipients', 'ttq' ); ?></label></th>
				<td><input type="text" id="ttq-email-recipients" name="ttq_settings[email_recipients]" value="<?php echo esc_attr( $settings['email_recipients'] ); ?>" class="regular-text" /><p class="description"><?php esc_html_e( 'Comma-separated email addresses.', 'ttq' ); ?></p></td>
			</tr>
			<tr>
				<th><label for="ttq-company-name"><?php esc_html_e( 'Company Name', 'ttq' ); ?></label></th>
				<td><input type="text" id="ttq-company-name" name="ttq_settings[company_name]" value="<?php echo esc_attr( $settings['company_name'] ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><label for="ttq-success-message"><?php esc_html_e( 'Success Message', 'ttq' ); ?></label></th>
				<td><textarea id="ttq-success-message" name="ttq_settings[success_message]" rows="3" class="large-text"><?php echo esc_textarea( $settings['success_message'] ); ?></textarea></td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>
