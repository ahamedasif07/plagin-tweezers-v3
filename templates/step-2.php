<?php

/** @var array $settings */
if (! defined('ABSPATH')) {
	exit;
}

// Colors shown alphabetically by label, regardless of admin save order.
$ttq_colors_alpha = isset($settings['colors']) && is_array($settings['colors']) ? $settings['colors'] : array();
usort($ttq_colors_alpha, function ($a, $b) {
	return strcasecmp(isset($a['label']) ? $a['label'] : '', isset($b['label']) ? $b['label'] : '');
});
?>
<div class="ttq-step-head">
	<h3 class="ttq-js-step2-title"><?php esc_html_e('Customize Your Tick Tweezers', 'ttq'); ?></h3>
	<p class="ttq-sub">
		<?php esc_html_e('Set your quantity, choose your colors and size, add your personalization, and optionally upload your logo.', 'ttq'); ?>
	</p>
</div>

<!-- ── Quantity ──────────────────────────────────────────────────── -->
<div class="ttq-section-card">
	<div class="ttq-section-card__header">
		<span class="ttq-section-card__icon" aria-hidden="true">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<rect x="2" y="7" width="20" height="14" rx="2" />
				<path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
			</svg>
		</span>
		<span
			class="ttq-section-card__title"><?php esc_html_e('Total Quantity / Enter the total number of customized tick tweezers you need.', 'ttq'); ?>
			<span class="req"></span></span>
	</div>

	<div class="ttq-field-group">
		<div class="ttq-qty-stepper">
			<button type="button" class="ttq-qty-btn ttq-js-qty-dec"
				aria-label="<?php esc_attr_e('Decrease quantity', 'ttq'); ?>">&minus;</button>
			<input type="number" id="ttq-quantity" name="quantity" inputmode="numeric" value="" placeholder="_ _"
				min="<?php echo esc_attr($settings['min_quantity']); ?>"
				max="<?php echo esc_attr($settings['max_quantity']); ?>" />
			<button type="button" class="ttq-qty-btn ttq-js-qty-inc"
				aria-label="<?php esc_attr_e('Increase quantity', 'ttq'); ?>">&plus;</button>
		</div>
		<div class="ttq-field-error" data-error-for="quantity" role="alert"></div>
	</div>
</div>

<!-- ── Colors ────────────────────────────────────────────────────── -->
<div class="ttq-section-card">
	<div class="ttq-section-card__header">
		<span class="ttq-section-card__icon" aria-hidden="true">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<circle cx="12" cy="12" r="10" />
				<path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
			</svg>
		</span>
		<span class="ttq-section-card__title"><?php esc_html_e('Select Color(s)', 'ttq'); ?> <span
				class="req">*</span></span>
		<span class="ttq-section-card__hint"><?php esc_html_e('Choose one or more', 'ttq'); ?></span>
	</div>

	<div class="ttq-color-grid" role="group" aria-label="<?php esc_attr_e('Select colors', 'ttq'); ?>">
		<?php foreach ($ttq_colors_alpha as $color) : ?>
			<label class="ttq-color-swatch" style="--ttq-swatch-color: <?php echo esc_attr($color['hex']); ?>">
				<input type="checkbox" name="colors[]" value="<?php echo esc_attr($color['key']); ?>" />
				<span class="ttq-color-swatch__dot" aria-hidden="true"></span>
				<span class="ttq-color-swatch__label"><?php echo esc_html($color['label']); ?></span>
			</label>
		<?php endforeach; ?>
	</div>

	<div class="ttq-custom-color-wrap">
		<label class="ttq-field-label" for="ttq-custom-color"><?php esc_html_e('Other / Custom Color', 'ttq'); ?>
			<span class="ttq-optional"><?php esc_html_e('Optional', 'ttq'); ?></span></label>
		<input type="text" id="ttq-custom-color" name="custom_color" class="ttq-input ttq-custom-color-input"
			placeholder="<?php esc_attr_e('e.g. Navy Blue, Forest Green, Maroon...', 'ttq'); ?>" />
	</div>

	<p class="ttq-color-note">
		<?php esc_html_e('Sample requests are limited to 2 color options. When placing a full order, you may select more than 2 colors.', 'ttq'); ?>
	</p>

	<div class="ttq-field-error" data-error-for="colors" role="alert"></div>
</div>

<!-- ── Sizes ─────────────────────────────────────────────────────── -->

<?php
if (isset($settings['sizes']) && is_array($settings['sizes']) && count($settings['sizes']) > 0) : ?>
	<div class="ttq-section-card ttq-js-sizes-section">
		<div class="ttq-section-card__header">
			<span class="ttq-section-card__icon" aria-hidden="true">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
					stroke-linecap="round" stroke-linejoin="round">
					<path d="M21 6H3" />
					<path d="M10 12H3" />
					<path d="M15 18H3" />
				</svg>
			</span>
			<!-- <span class="ttq-section-card__title"><?php esc_html_e('Select Tick Tweezers Size(s)', 'ttq'); ?> <span
					class="req">*</span>/
				Multiple Sizes Allowed</span> -->
			<span
				class="ttq-section-card__title"><?php esc_html_e(' Select Tick Tweezers Size(s) * Choose the size options that best fit your needs.', 'ttq'); ?>
				<span class="req">*</span>
				<span
					class="ttq-section-card__hint ttq-pill-hint"><?php esc_html_e('Multiple Sizes Allowed', 'ttq'); ?></span>
		</div>

		<div class="ttq-size-grid ttq-size-grid--wide" role="group"
			aria-label="<?php esc_attr_e('Select sizes', 'ttq'); ?>">
			<?php foreach ($settings['sizes'] as $size) : ?>
				<?php if (! empty($size['key'])) :
				?>
					<label class="ttq-size-chip ttq-size-chip--wide">
						<input type="checkbox" name="sizes[]" value="<?php echo esc_attr($size['key']); ?>" />
						<span><?php echo esc_html($size['label']); ?></span>
					</label>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
		<div class="ttq-field-error" data-error-for="sizes" role="alert"></div>
	</div>

<?php endif; ?>

<!-- ── Material (Complete Tick Kit only) ────────────────────────── -->
<div class="ttq-section-card ttq-js-product-only" data-product-only="complete_kit">
	<div class="ttq-section-card__header">
		<span class="ttq-section-card__icon" aria-hidden="true">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<path d="M3 3h18v18H3z" />
				<path d="M3 9h18M9 21V9" />
			</svg>
		</span>
		<span class="ttq-section-card__title"><?php esc_html_e('Choose Your Material', 'ttq'); ?> <span
				class="req">*</span></span>
	</div>
	<p class="ttq-field-hint-text">
		<?php esc_html_e('Both fabric and synthetic leather samples will be shipped.', 'ttq'); ?></p>

	<div class="ttq-choice-grid" role="radiogroup" aria-label="<?php esc_attr_e('Choose your material', 'ttq'); ?>">
		<label class="ttq-choice-card">
			<input type="radio" name="material" value="fabric" checked />
			<span class="ttq-choice-card__radio" aria-hidden="true"></span>
			<span class="ttq-choice-card__icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
					stroke-linecap="round" stroke-linejoin="round">
					<rect x="3" y="3" width="18" height="18" rx="2" />
					<path d="M3 9h18M3 15h18M9 3v18M15 3v18" />
				</svg>
			</span>
			<span class="ttq-choice-card__label"><?php esc_html_e('Fabric / Canvas', 'ttq'); ?></span>
		</label>
		<label class="ttq-choice-card">
			<input type="radio" name="material" value="synthetic_leather" />
			<span class="ttq-choice-card__radio" aria-hidden="true"></span>
			<span class="ttq-choice-card__icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
					stroke-linecap="round" stroke-linejoin="round">
					<path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z" />
				</svg>
			</span>
			<span class="ttq-choice-card__label"><?php esc_html_e('Synthetic Leather', 'ttq'); ?></span>
		</label>
	</div>
	<div class="ttq-field-error" data-error-for="material" role="alert"></div>
</div>

<!-- ── Carabiner Clip (Complete Tick Kit only) ──────────────────── -->
<div class="ttq-section-card ttq-js-product-only" data-product-only="complete_kit">
	<div class="ttq-section-card__header">
		<span class="ttq-section-card__icon" aria-hidden="true">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<circle cx="8" cy="6" r="3" />
				<path d="M8 9v9a3 3 0 0 0 6 0v-2" />
			</svg>
		</span>
		<span class="ttq-section-card__title"><?php esc_html_e('Add a Carabiner Clip?', 'ttq'); ?></span>
		<span class="ttq-section-card__hint"><?php esc_html_e('Optional', 'ttq'); ?></span>
	</div>
	<p class="ttq-field-hint-text">
		<?php esc_html_e('This clip can be added to either Fabric / Canvas or Synthetic Leather.', 'ttq'); ?></p>

	<div class="ttq-choice-grid ttq-choice-grid--stack" role="radiogroup"
		aria-label="<?php esc_attr_e('Add a carabiner clip', 'ttq'); ?>">
		<label class="ttq-choice-card ttq-choice-card--row">
			<input type="radio" name="carabiner_clip" value="yes" />
			<span class="ttq-choice-card__radio" aria-hidden="true"></span>
			<span class="ttq-choice-card__icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
					stroke-linecap="round" stroke-linejoin="round">
					<rect x="7" y="9" width="10" height="12" rx="2" />
					<path d="M9 9V6a3 3 0 0 1 6 0" />
				</svg>
			</span>
			<span class="ttq-choice-card__body">
				<span class="ttq-choice-card__label"><?php esc_html_e('Yes, Add Carabiner Clip', 'ttq'); ?></span>
				<span
					class="ttq-choice-card__desc"><?php esc_html_e('Attach a clip for easy carrying or display.', 'ttq'); ?></span>
			</span>
		</label>
		<label class="ttq-choice-card ttq-choice-card--row">
			<input type="radio" name="carabiner_clip" value="no" checked />
			<span class="ttq-choice-card__radio" aria-hidden="true"></span>
			<span class="ttq-choice-card__icon" aria-hidden="true">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
					stroke-linecap="round" stroke-linejoin="round">
					<circle cx="12" cy="12" r="10" />
					<line x1="8" y1="12" x2="16" y2="12" />
				</svg>
			</span>
			<span class="ttq-choice-card__body">
				<span class="ttq-choice-card__label"><?php esc_html_e('No Clip Needed', 'ttq'); ?></span>
				<span
					class="ttq-choice-card__desc"><?php esc_html_e('Keep the kit as-is without the clip.', 'ttq'); ?></span>
			</span>
		</label>
	</div>
</div>

<!-- ── Personalization ───────────────────────────────────────────── -->
<div class="ttq-section-card">
	<div class="ttq-section-card__header">
		<span class="ttq-section-card__icon" aria-hidden="true">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<path d="M12 20h9" />
				<path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
			</svg>
		</span>
		<span
			class="ttq-section-card__title"><?php esc_html_e('Add Your Personalization / Customization', 'ttq'); ?></span>
		<span class="ttq-section-card__hint"><?php esc_html_e('Optional', 'ttq'); ?></span>
	</div>

	<div class="ttq-field-group--split">
		<div>
			<label class="ttq-field-label ttq-js-side1-label"
				for="ttq-side1"><?php esc_html_e('Side 1 Stamping', 'ttq'); ?></label>
			<input type="text" id="ttq-side1" name="side1"
				maxlength="<?php echo esc_attr($settings['personalization_max_chars']); ?>" class="ttq-js-char-counter"
				data-max="<?php echo esc_attr($settings['personalization_max_chars']); ?>"
				placeholder="<?php esc_attr_e('e.g. your website or phone', 'ttq'); ?>" />
			<p class="ttq-char-counter"><span
					class="ttq-js-char-count">0</span>/<?php echo esc_html($settings['personalization_max_chars']); ?>
				<?php esc_html_e('chars', 'ttq'); ?></p>
			<div class="ttq-field-error" data-error-for="side1" role="alert"></div>
		</div>
		<div>
			<label class="ttq-field-label ttq-js-side2-label"
				for="ttq-side2"><?php esc_html_e('Side 2 Stamping', 'ttq'); ?> <span
					class="ttq-optional"><?php esc_html_e('Optional', 'ttq'); ?></span></label>
			<input type="text" id="ttq-side2" name="side2"
				maxlength="<?php echo esc_attr($settings['personalization_max_chars']); ?>" class="ttq-js-char-counter"
				data-max="<?php echo esc_attr($settings['personalization_max_chars']); ?>"
				placeholder="<?php esc_attr_e('e.g. a tagline or message', 'ttq'); ?>" />
			<p class="ttq-char-counter"><span
					class="ttq-js-char-count">0</span>/<?php echo esc_html($settings['personalization_max_chars']); ?>
				<?php esc_html_e('chars', 'ttq'); ?></p>
			<div class="ttq-field-error" data-error-for="side2" role="alert"></div>
		</div>
	</div>

	<div class="ttq-callout ttq-callout--neutral ttq-callout--compact">
		<span class="ttq-callout__icon" aria-hidden="true">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<path
					d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
			</svg>
		</span>
		<div>
			<?php esc_html_e("Don't have everything finalized yet? No problem — submit the form with whatever information you have, and our team can help with the rest.", 'ttq'); ?>
		</div>
	</div>

	<!-- Additional comments -->
	<div class="ttq-field-group" style="margin-top:18px;">
		<label class="ttq-field-label" for="ttq-comments"><?php esc_html_e('Additional Comments', 'ttq'); ?>
			<span class="ttq-optional"><?php esc_html_e('Optional', 'ttq'); ?></span></label>
		<textarea id="ttq-comments" name="comments" rows="3" class="ttq-input"
			placeholder="<?php esc_attr_e('Anything else we should know about your request?', 'ttq'); ?>"></textarea>
	</div>
</div>

<!-- ── Logo Upload ───────────────────────────────────────────────── -->
<div class="ttq-section-card">
	<div class="ttq-section-card__header">
		<span class="ttq-section-card__icon" aria-hidden="true">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<rect x="3" y="3" width="18" height="18" rx="2" />
				<circle cx="8.5" cy="8.5" r="1.5" />
				<polyline points="21 15 16 10 5 21" />
			</svg>
		</span>
		<span class="ttq-section-card__title"><?php esc_html_e('Upload Your Logo / Branding Artwork', 'ttq'); ?></span>
		<span class="ttq-section-card__hint"><?php esc_html_e('Optional', 'ttq'); ?></span>
	</div>

	<div class="ttq-uploader ttq-js-uploader">
		<div class="ttq-uploader__dropzone ttq-js-dropzone" tabindex="0" role="button"
			aria-label="<?php esc_attr_e('Drag and drop your logo here, or browse files', 'ttq'); ?>">
			<svg width="36" height="36" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor"
				stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
				<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
				<polyline points="17 8 12 3 7 8" />
				<line x1="12" y1="3" x2="12" y2="15" />
			</svg>
			<p><strong><?php esc_html_e('Drag & Drop', 'ttq'); ?></strong> <?php esc_html_e('or', 'ttq'); ?> <span
					class="ttq-link-btn"><?php esc_html_e('Browse File', 'ttq'); ?></span></p>
			<p class="ttq-hint">
				<?php printf(esc_html__('Accepted: %1$s — Max %2$sMB', 'ttq'), esc_html(strtoupper($settings['allowed_file_types'])), esc_html($settings['max_upload_mb'])); ?>
			</p>
			<input type="file" id="ttq-logo-input" class="ttq-visually-hidden ttq-js-file-input"
				accept="<?php echo esc_attr('.' . str_replace(',', ',.', $settings['allowed_file_types'])); ?>" />
		</div>
	</div>
	<div class="ttq-field-error" data-error-for="logo" role="alert"></div>
	<input type="hidden" name="logo_token" class="ttq-js-logo-token" value="" />
</div>

<div class="ttq-nav-row">
	<button type="button" class="ttq-btn ttq-btn--ghost ttq-js-back"><span aria-hidden="true">&larr;</span>
		<?php esc_html_e('Back', 'ttq'); ?></button>
	<button type="button" class="ttq-btn ttq-btn--primary ttq-js-next"><?php esc_html_e('Continue', 'ttq'); ?> <span
			aria-hidden="true">&rarr;</span></button>
</div>