<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ttq-step-head">
	<h3><?php esc_html_e( 'Contact Details', 'ttq' ); ?></h3>
	<p class="ttq-sub"><?php esc_html_e( 'Please provide your contact information and shipping address so we can prepare your sample request or personalized quote.', 'ttq' ); ?></p>
</div>

<div class="ttq-contact-card">

	<div class="ttq-contact-card__section">
		<div class="ttq-contact-card__section-header">
			<span class="ttq-section-card__icon" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1"/></svg>
			</span>
			<span><?php esc_html_e( 'Personal & Organization', 'ttq' ); ?></span>
		</div>

		<div class="ttq-field-group">
			<label class="ttq-field-label" for="ttq-org"><?php esc_html_e( 'Organization Name', 'ttq' ); ?></label>
			<input type="text" id="ttq-org" name="organization" placeholder="<?php esc_attr_e( 'Enter organization name', 'ttq' ); ?>" />
		</div>

		<div class="ttq-field-group--split">
			<div>
				<label class="ttq-field-label" for="ttq-name"><?php esc_html_e( 'Your Name', 'ttq' ); ?> <span class="req">*</span></label>
				<p class="ttq-field-hint-text"><?php esc_html_e( 'First and last name', 'ttq' ); ?></p>
				<input type="text" id="ttq-name" name="name" placeholder="<?php esc_attr_e( 'First and last name', 'ttq' ); ?>" required />
				<div class="ttq-field-error" data-error-for="name" role="alert"></div>
			</div>
			<div>
				<label class="ttq-field-label" for="ttq-phone"><?php esc_html_e( 'Phone Number', 'ttq' ); ?> <span class="req">*</span></label>
				<input type="tel" id="ttq-phone" name="phone" placeholder="<?php esc_attr_e( 'Enter phone number', 'ttq' ); ?>" required />
				<div class="ttq-field-error" data-error-for="phone" role="alert"></div>
			</div>
		</div>
	</div>

	<div class="ttq-contact-card__section">
		<div class="ttq-contact-card__section-header">
			<span class="ttq-section-card__icon" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
			</span>
			<span><?php esc_html_e( 'Email & Notifications', 'ttq' ); ?></span>
		</div>

		<div class="ttq-field-group">
			<label class="ttq-field-label" for="ttq-email"><?php esc_html_e( 'Email Address', 'ttq' ); ?> <span class="req">*</span></label>
			<input type="email" id="ttq-email" name="email" placeholder="<?php esc_attr_e( 'Enter email address', 'ttq' ); ?>" required />
			<div class="ttq-field-error" data-error-for="email" role="alert"></div>
		</div>

		<div class="ttq-field-group">
			<label class="ttq-field-label" for="ttq-freesample">
				<?php esc_html_e( 'Would you like a free sample?', 'ttq' ); ?>
			</label>
			<div class="ttq-radio-row">
				<label class="ttq-radio-pill">
					<input type="radio" name="free_sample" value="yes" class="ttq-js-free-sample" />
					<span><?php esc_html_e( 'Yes', 'ttq' ); ?></span>
				</label>
				<label class="ttq-radio-pill">
					<input type="radio" name="free_sample" value="no" class="ttq-js-free-sample" checked />
					<span><?php esc_html_e( 'No', 'ttq' ); ?></span>
				</label>
			</div>

			<div class="ttq-callout ttq-callout--info ttq-callout--compact ttq-js-sample-note" hidden>
				<span class="ttq-callout__icon" aria-hidden="true">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
				</span>
				<div>
					<?php esc_html_e( 'Great! Keep an eye out for an email from', 'ttq' ); ?>
					<a href="mailto:sales@ticktweezers.com">sales@ticktweezers.com</a>
					<?php esc_html_e( 'regarding your free sample.', 'ttq' ); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="ttq-contact-card__section ttq-js-address-section">
		<div class="ttq-contact-card__section-header">
			<span class="ttq-section-card__icon" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
			</span>
			<span><?php esc_html_e( 'Shipping Address', 'ttq' ); ?> <span class="req">*</span></span>
		</div>

		<div class="ttq-field-group">
			<p class="ttq-field-hint-text"><?php esc_html_e( 'Please enter your complete shipping address, including city, state, and ZIP code.', 'ttq' ); ?></p>
			<textarea id="ttq-address" name="address" rows="3" placeholder="<?php esc_attr_e( 'Enter complete shipping address', 'ttq' ); ?>"></textarea>
			<div class="ttq-field-error" data-error-for="address" role="alert"></div>

			<div class="ttq-callout ttq-callout--warning">
				<span class="ttq-callout__icon" aria-hidden="true">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
				</span>
				<div>
					<strong><?php esc_html_e( 'Please Note: PO Boxes are not accepted.', 'ttq' ); ?></strong>
					<p><?php esc_html_e( 'A physical shipping address is required for delivery so we can process and ship your sample request.', 'ttq' ); ?></p>
				</div>
			</div>
		</div>
	</div>

	<div class="ttq-callout ttq-callout--success ttq-callout--compact">
		<span class="ttq-callout__icon" aria-hidden="true">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
		</span>
		<div>
			<strong><?php esc_html_e( 'Your information is safe with us.', 'ttq' ); ?></strong>
			<?php esc_html_e( 'We respect your privacy and will never share your information.', 'ttq' ); ?>
		</div>
	</div>

</div>

<div class="ttq-nav-row">
	<button type="button" class="ttq-btn ttq-btn--ghost ttq-js-back"><span aria-hidden="true">&larr;</span> <?php esc_html_e( 'Back', 'ttq' ); ?></button>
	<button type="button" class="ttq-btn ttq-btn--primary ttq-js-next"><?php esc_html_e( 'Continue to Review', 'ttq' ); ?> <span aria-hidden="true">&rarr;</span></button>
</div>
