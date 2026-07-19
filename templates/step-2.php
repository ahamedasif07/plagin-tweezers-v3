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
		<span class="ttq-section-card__hint ttq-pill-hint"><?php esc_html_e('Optional', 'ttq'); ?></span>
	</div>

	<p class="ttq-field-hint-text ttq-personalization-sub">
		<?php esc_html_e('Add a name, tagline, website URL, and/or logo to Side 1 and/or Side 2 of your tick removal tweezers.', 'ttq'); ?>
	</p>

	<div class="ttq-callout ttq-callout--warning ttq-callout--compact">
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

	<div class="ttq-personalization-grid">
		<?php
		$ttq_personalization_sides = array(
			'side1' => array(
				'title'      => __('Side 1 Personalization', 'ttq'),
				'upload_hint' => __('Upload a logo or artwork for Side 1.', 'ttq'),
			),
			'side2' => array(
				'title'      => __('Side 2 Personalization (Optional)', 'ttq'),
				'upload_hint' => __('Upload a logo or artwork for Side 2.', 'ttq'),
			),
		);
		foreach ($ttq_personalization_sides as $ttq_side_key => $ttq_side) :
		?>
			<div class="ttq-personalization-col">
				<h4 class="ttq-personalization-col__title"><?php echo esc_html($ttq_side['title']); ?></h4>

				<div class="ttq-tag-row" role="group"
					aria-label="<?php echo esc_attr(sprintf(__('%s personalization type', 'ttq'), $ttq_side['title'])); ?>">
					<button type="button" class="ttq-tag-btn ttq-js-tag-btn"
						data-target="ttq-<?php echo esc_attr($ttq_side_key); ?>">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round">
							<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
							<circle cx="12" cy="7" r="4" />
						</svg>
						<span><?php esc_html_e('Name', 'ttq'); ?></span>
					</button>
					<button type="button" class="ttq-tag-btn ttq-js-tag-btn"
						data-target="ttq-<?php echo esc_attr($ttq_side_key); ?>">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round">
							<polyline points="4 7 4 4 20 4 20 7" />
							<line x1="12" y1="4" x2="12" y2="20" />
							<line x1="9" y1="20" x2="15" y2="20" />
						</svg>
						<span><?php esc_html_e('Tagline', 'ttq'); ?></span>
					</button>
					<button type="button" class="ttq-tag-btn ttq-js-tag-btn"
						data-target="ttq-<?php echo esc_attr($ttq_side_key); ?>">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round">
							<circle cx="12" cy="12" r="10" />
							<line x1="2" y1="12" x2="22" y2="12" />
							<path
								d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
						</svg>
						<span><?php esc_html_e('Website URL', 'ttq'); ?></span>
					</button>
					<button type="button" class="ttq-tag-btn ttq-js-tag-btn-logo"
						data-target="ttq-<?php echo esc_attr($ttq_side_key); ?>-dropzone">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
							stroke-linecap="round" stroke-linejoin="round">
							<rect x="3" y="3" width="18" height="18" rx="2" />
							<circle cx="8.5" cy="8.5" r="1.5" />
							<polyline points="21 15 16 10 5 21" />
						</svg>
						<span><?php esc_html_e('Logo', 'ttq'); ?></span>
					</button>
				</div>

				<div class="ttq-field-group">
					<label class="ttq-field-label"
						for="ttq-<?php echo esc_attr($ttq_side_key); ?>"><?php esc_html_e('Custom Text (Name, Tagline, or Website URL)', 'ttq'); ?></label>
					<input type="text" id="ttq-<?php echo esc_attr($ttq_side_key); ?>"
						name="<?php echo esc_attr($ttq_side_key); ?>"
						maxlength="<?php echo esc_attr($settings['personalization_max_chars']); ?>"
						class="ttq-js-char-counter"
						data-max="<?php echo esc_attr($settings['personalization_max_chars']); ?>"
						placeholder="<?php esc_attr_e('Enter a name, tagline, or website URL', 'ttq'); ?>" />
					<p class="ttq-hint"><?php esc_html_e('You can provide a name, tagline, or website URL.', 'ttq'); ?></p>
					<p class="ttq-char-counter"><span
							class="ttq-js-char-count">0</span>/<?php echo esc_html($settings['personalization_max_chars']); ?>
						<?php esc_html_e('chars', 'ttq'); ?></p>
					<div class="ttq-field-error" data-error-for="<?php echo esc_attr($ttq_side_key); ?>" role="alert"></div>
				</div>

				<div class="ttq-field-group ttq-side-uploader-group">
					<label class="ttq-field-label"><?php esc_html_e('Upload Logo / Artwork', 'ttq'); ?></label>
					<div class="ttq-uploader ttq-js-side-uploader" data-side="<?php echo esc_attr($ttq_side_key); ?>">
						<div class="ttq-uploader__dropzone ttq-side-dropzone ttq-js-side-dropzone"
							id="ttq-<?php echo esc_attr($ttq_side_key); ?>-dropzone" tabindex="0" role="button"
							aria-label="<?php echo esc_attr(sprintf(__('Click to upload or drag and drop a logo for %s', 'ttq'), $ttq_side['title'])); ?>">
							<svg width="28" height="28" viewBox="0 0 24 24" aria-hidden="true" fill="none"
								stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
								<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
								<polyline points="17 8 12 3 7 8" />
								<line x1="12" y1="3" x2="12" y2="15" />
							</svg>
							<p><?php esc_html_e('Click to upload or drag and drop', 'ttq'); ?></p>
							<p class="ttq-hint"><?php echo esc_html(strtoupper($settings['allowed_file_types'])); ?></p>
							<input type="file" class="ttq-visually-hidden ttq-js-side-file-input"
								accept="<?php echo esc_attr('.' . str_replace(',', ',.', $settings['allowed_file_types'])); ?>" />
						</div>
						<div class="ttq-uploader__preview ttq-js-side-preview" hidden>
							<img class="ttq-js-side-preview-img" alt="" />
							<span class="ttq-js-side-preview-name"></span>
							<button type="button"
								class="ttq-link-btn ttq-link-btn--danger ttq-js-side-upload-remove"><?php esc_html_e('Remove', 'ttq'); ?></button>
						</div>
						<div class="ttq-field-error" data-error-for="<?php echo esc_attr($ttq_side_key); ?>_logo"
							role="alert"></div>
						<input type="hidden" name="<?php echo esc_attr($ttq_side_key); ?>_logo_token"
							class="ttq-js-side-logo-token" value="" />
					</div>
					<p class="ttq-hint"><?php echo esc_html($ttq_side['upload_hint']); ?></p>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="ttq-callout ttq-callout--warning ttq-callout--compact">
		<span class="ttq-callout__icon" aria-hidden="true">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<circle cx="12" cy="12" r="10" />
				<line x1="12" y1="16" x2="12" y2="12" />
				<line x1="12" y1="8" x2="12.01" y2="8" />
			</svg>
		</span>
		<div>
			<strong><?php esc_html_e('Not sure yet?', 'ttq'); ?></strong>
			<?php esc_html_e("That's okay. You can leave this section blank or submit whatever information you have now. We'll follow up to help finalize your customization.", 'ttq'); ?>
		</div>
	</div>

	<p class="ttq-personalization-examples">
		<?php esc_html_e('Examples: Organization Name', 'ttq'); ?> &bull;
		<?php esc_html_e('Tick Removal Tweezers', 'ttq'); ?>
		&bull; <?php esc_html_e('BE TICK SMART', 'ttq'); ?>
	</p>

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