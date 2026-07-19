<?php
if (! defined('ABSPATH')) {
	exit;
}
?>
<div class="ttq-overlay ttq-js-overlay" hidden aria-live="assertive" role="status">

	<!-- Submitting state: dots stay fixed in place, color chases around the ring. -->
	<div class="ttq-overlay__panel ttq-js-overlay-submitting">
		<div class="ttq-tick-spinner" aria-hidden="true">
			<svg viewBox="0 0 120 120" class="ttq-tick-spinner__dots">
				<circle cx="60" cy="8" r="6" />
				<circle cx="79.9" cy="11.96" r="6" />
				<circle cx="96.77" cy="23.23" r="6" />
				<circle cx="108.04" cy="40.1" r="6" />
				<circle cx="112" cy="60" r="6" />
				<circle cx="108.04" cy="79.9" r="6" />
				<circle cx="96.77" cy="96.77" r="6" />
				<circle cx="79.9" cy="108.04" r="6" />
				<circle cx="60" cy="112" r="6" />
				<circle cx="40.1" cy="108.04" r="6" />
				<circle cx="23.23" cy="96.77" r="6" />
				<circle cx="11.96" cy="79.9" r="6" />
				<circle cx="8" cy="60" r="6" />
				<circle cx="11.96" cy="40.1" r="6" />
				<circle cx="23.23" cy="23.23" r="6" />
				<circle cx="40.1" cy="11.96" r="6" />
			</svg>
			<svg viewBox="0 0 64 64" class="ttq-tick-spinner__bug">
				<ellipse cx="32" cy="36" rx="14" ry="17" />
				<circle cx="32" cy="18" r="7" />
				<g class="ttq-tick-spinner__legs">
					<line x1="20" y1="28" x2="6" y2="20" />
					<line x1="20" y1="36" x2="4" y2="36" />
					<line x1="20" y1="44" x2="6" y2="52" />
					<line x1="44" y1="28" x2="58" y2="20" />
					<line x1="44" y1="36" x2="60" y2="36" />
					<line x1="44" y1="44" x2="58" y2="52" />
				</g>
			</svg>
		</div>
		<h3><?php esc_html_e('Submitting Your Request…', 'ttq'); ?></h3>
		<p><?php esc_html_e('Please wait while we process your quote request.', 'ttq'); ?></p>
	</div>

	<!-- Success state -->
	<div class="ttq-overlay__panel ttq-js-overlay-success" hidden>
		<div class="ttq-success-check" aria-hidden="true">
			<svg viewBox="0 0 100 100">
				<circle class="ttq-success-check__ring" cx="50" cy="50" r="44" />
				<path class="ttq-success-check__mark" d="M28 52 L44 68 L74 34" />
			</svg>
		</div>
		<h3><?php esc_html_e('Quote Submitted Successfully!', 'ttq'); ?></h3>
		<p class="ttq-js-overlay-success-message">
			<?php esc_html_e('Thanks for helping keep our communities safe and tick smart! Our team will follow up with your quote shortly.', 'ttq'); ?>
		</p>
		<button type="button"
			class="ttq-btn ttq-btn--primary ttq-js-overlay-close"><?php esc_html_e('Done', 'ttq'); ?></button>
	</div>

	<!-- Error state (network / validation failure at submit time) -->
	<div class="ttq-overlay__panel ttq-js-overlay-error" hidden>
		<div class="ttq-error-icon" aria-hidden="true">!</div>
		<h3><?php esc_html_e('Something went wrong', 'ttq'); ?></h3>
		<p class="ttq-js-overlay-error-message"></p>
		<button type="button"
			class="ttq-btn ttq-btn--ghost ttq-js-overlay-close"><?php esc_html_e('Close', 'ttq'); ?></button>
	</div>

</div>