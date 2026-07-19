<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="ttq-step-head">
	<h3><?php esc_html_e( 'Review Your Request', 'ttq' ); ?></h3>
	<p class="ttq-sub"><?php esc_html_e( 'Please review the details you selected for your quote/sample below.', 'ttq' ); ?></p>
</div>

<!-- "Almost done!" info banner -->
<div class="ttq-callout ttq-callout--info ttq-almost-done">
	<span class="ttq-callout__icon" aria-hidden="true">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
	</span>
	<div>
		<strong><?php esc_html_e( 'Almost done!', 'ttq' ); ?></strong>
		<p><?php esc_html_e( 'Please review your selections below. You can go back to make changes if needed.', 'ttq' ); ?></p>
	</div>
</div>

<!-- 3-column review grid -->
<div class="ttq-review-grid">

	<!-- Column 1: Product Details -->
	<div class="ttq-review-card">
		<h4 class="ttq-review-card__title">
			<span class="ttq-review-card__icon" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
			</span>
			<?php esc_html_e( 'PRODUCT DETAILS', 'ttq' ); ?>
		</h4>

		<div class="ttq-review-card__product-preview">
			<img class="ttq-js-review-product-img ttq-review-card__thumb" alt="<?php esc_attr_e( 'Selected product', 'ttq' ); ?>" />
			<span class="ttq-js-review-product-name ttq-review-card__product-name"></span>
		</div>

		<dl class="ttq-review-dl">
			<div>
				<dt><?php esc_html_e( 'Product Selected', 'ttq' ); ?></dt>
				<dd data-review="product">&mdash;</dd>
			</div>
			<div>
				<dt><?php esc_html_e( 'Quantity', 'ttq' ); ?></dt>
				<dd data-review="quantity">&mdash;</dd>
			</div>
			<div>
				<dt><?php esc_html_e( 'Selected Size', 'ttq' ); ?></dt>
				<dd data-review="sizes">&mdash;</dd>
			</div>
			<div class="ttq-js-review-material-row">
				<dt><?php esc_html_e( 'Material', 'ttq' ); ?></dt>
				<dd data-review="material">&mdash;</dd>
			</div>
			<div class="ttq-js-review-clip-row">
				<dt><?php esc_html_e( 'Carabiner Clip', 'ttq' ); ?></dt>
				<dd data-review="carabiner_clip">&mdash;</dd>
			</div>
			<div>
				<dt><?php esc_html_e( 'Side 1 Personalization', 'ttq' ); ?></dt>
				<dd data-review="side1">&mdash;</dd>
			</div>
			<div>
				<dt><?php esc_html_e( 'Side 2 Personalization', 'ttq' ); ?></dt>
				<dd data-review="side2">&mdash;</dd>
			</div>
			<div>
				<dt><?php esc_html_e( 'Selected Color(s)', 'ttq' ); ?></dt>
				<dd data-review="colors">&mdash;</dd>
			</div>
			<div>
				<dt><?php esc_html_e( 'Comments', 'ttq' ); ?></dt>
				<dd data-review="comments">&mdash;</dd>
			</div>
		</dl>

		<?php if ( ! empty( $settings['products'] ) ) : ?>
			<p class="ttq-review-card__product-tag ttq-js-review-product-tag"></p>
		<?php endif; ?>
	</div>

	<!-- Column 2: Contact Information -->
	<div class="ttq-review-card">
		<h4 class="ttq-review-card__title">
			<span class="ttq-review-card__icon" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
			</span>
			<?php esc_html_e( 'CONTACT INFORMATION', 'ttq' ); ?>
		</h4>

		<dl class="ttq-review-dl">
			<div>
				<dt>
					<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
					<?php esc_html_e( 'Name', 'ttq' ); ?>
				</dt>
				<dd data-review="name">&mdash;</dd>
			</div>
			<div>
				<dt>
					<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
					<?php esc_html_e( 'Email Address', 'ttq' ); ?>
				</dt>
				<dd data-review="email">&mdash;</dd>
			</div>
			<div>
				<dt>
					<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.58 3.38a2 2 0 0 1 1.99-2.18h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9a16 16 0 0 0 6.09 6.09l.94-.94a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
					<?php esc_html_e( 'Phone Number', 'ttq' ); ?>
				</dt>
				<dd data-review="phone">&mdash;</dd>
			</div>
			<div>
				<dt>
					<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
					<?php esc_html_e( 'Organization', 'ttq' ); ?>
				</dt>
				<dd data-review="organization">&mdash;</dd>
			</div>
			<div>
				<dt>
					<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
					<?php esc_html_e( 'Free Sample', 'ttq' ); ?>
				</dt>
				<dd data-review="free_sample">&mdash;</dd>
			</div>
		</dl>
	</div>

	<!-- Column 3: Shipping Address -->
	<div class="ttq-review-card">
		<h4 class="ttq-review-card__title">
			<span class="ttq-review-card__icon" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
			</span>
			<?php esc_html_e( 'SHIPPING ADDRESS', 'ttq' ); ?>
		</h4>

		<div class="ttq-review-address ttq-js-review-address-wrap">
			<p class="ttq-review-address__label"><?php esc_html_e( 'Delivery Address', 'ttq' ); ?></p>
			<p data-review="address" class="ttq-review-address__text">&mdash;</p>
		</div>
		<p class="ttq-js-review-no-address ttq-hint" hidden><?php esc_html_e( 'No free sample requested — no shipping address on file.', 'ttq' ); ?></p>

		<div class="ttq-callout ttq-callout--warning ttq-callout--compact">
			<span class="ttq-callout__icon" aria-hidden="true">
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="16" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
			</span>
			<div>
				<strong><?php esc_html_e( 'PO Boxes are not accepted.', 'ttq' ); ?></strong>
				<p><?php esc_html_e( 'A physical address is required for delivery.', 'ttq' ); ?></p>
			</div>
		</div>

		<!-- Uploaded logo (if any) -->
		<div class="ttq-review-logo-wrap">
			<img class="ttq-js-review-logo ttq-review-logo" alt="<?php esc_attr_e( 'Uploaded logo', 'ttq' ); ?>" style="display:none;" />
			<p class="ttq-js-review-no-logo ttq-hint"><?php esc_html_e( 'No logo uploaded.', 'ttq' ); ?></p>
		</div>
	</div>

</div><!-- .ttq-review-grid -->

<div class="ttq-callout ttq-callout--neutral">
	<span class="ttq-callout__icon" aria-hidden="true">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
	</span>
	<div>
		<strong><?php esc_html_e( 'Need to make a change?', 'ttq' ); ?></strong>
		<p><?php esc_html_e( 'Use the Back button to go to any previous step and update your information.', 'ttq' ); ?></p>
	</div>
</div>

<div class="ttq-field-error" data-error-for="submit" role="alert"></div>

<div class="ttq-nav-row">
	<button type="button" class="ttq-btn ttq-btn--ghost ttq-js-back"><span aria-hidden="true">&larr;</span> <?php esc_html_e( 'Back', 'ttq' ); ?></button>
	<button type="button" class="ttq-btn ttq-btn--primary ttq-btn--cta ttq-js-submit">
		<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
		<?php esc_html_e( 'Submit My Request Now!', 'ttq' ); ?> <span aria-hidden="true">&rarr;</span>
	</button>
</div>

<p class="ttq-safe-note">
	<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
	<?php esc_html_e( 'Your information is safe and will only be used to process your quote or sample request.', 'ttq' ); ?>
</p>
