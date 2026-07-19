<?php

/** @var array $settings */
if (! defined('ABSPATH')) {
	exit;
}
?>
<div class="ttq-step-head">
	<p class="ttq-sub">
		<?php esc_html_e('Select either Tick Tweezers or the Complete Tick Kit. Both are excellent tick awareness, education, and prevention resources — ideal for public health outreach, community programs, giveaways, schools, parks, and organizations. Both options can be customized.', 'ttq'); ?>
	</p>
</div>

<div class="ttq-product-grid" role="radiogroup"
	aria-label="<?php esc_attr_e('Choose your tick prevention solution', 'ttq'); ?>">
	<?php
	$dynamic_products = TTQ_Admin::get_dynamic_products();
	foreach ($dynamic_products as $i => $product) :
		$is_featured = ! empty($product['featured']) && $product['featured'] !== '0' && $product['featured'] !== 'false';
		// Parse features: pipe-separated string from admin, or fallback array
		$features_raw = isset($product['features']) ? $product['features'] : '';
		$features = array_filter(array_map('trim', explode('|', $features_raw)));
		if (empty($features)) {
			// Hardcoded fallback when no features are set
			$features = array(
				__('Removes ticks safely', 'ttq'),
				__('Precision stainless steel tips', 'ttq'),
				__('Personalized with your logo', 'ttq'),
			);
		}
	?>
		<label class="ttq-product-card<?php echo $is_featured ? ' is-featured' : ''; ?>"
			for="ttq-product-<?php echo esc_attr($product['key']); ?>">

			<?php if ($is_featured) : ?>
				<span class="ttq-product-card__ribbon">
					<svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
						<path
							d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
					</svg>
					<?php esc_html_e('MOST POPULAR', 'ttq'); ?>
				</span>
			<?php endif; ?>

			<input type="radio" name="product" id="ttq-product-<?php echo esc_attr($product['key']); ?>"
				value="<?php echo esc_attr($product['key']); ?>" <?php checked(0 === $i); ?>
				data-label="<?php echo esc_attr($product['label']); ?>"
				class="ttq-product-card__radio ttq-js-product-radio" />
			<span class="ttq-product-card__radio-ui" aria-hidden="true"></span>

			<span class="ttq-product-card__media">
				<?php if (! empty($product['image'])) : ?>
					<img src="<?php echo esc_url($product['image']); ?>" alt="<?php echo esc_attr($product['label']); ?>"
						style="width: 100%; height: 100%; object-fit: contain; object-position: center; image-rendering: -webkit-optimize-contrast;"
						loading="lazy" />
				<?php else : ?>
					<span class="ttq-product-card__placeholder" aria-hidden="true">
						<svg viewBox="0 0 160 120" width="160" height="120">
							<!-- Tick tweezers illustration placeholder -->
							<rect x="10" y="20" width="18" height="80" rx="4" fill="#c62828" transform="rotate(-15 19 60)" />
							<rect x="35" y="20" width="18" height="80" rx="4" fill="#e53935" transform="rotate(-5 44 60)" />
							<rect x="62" y="20" width="18" height="80" rx="4" fill="#1565c0" transform="rotate(5 71 60)" />
							<rect x="88" y="20" width="18" height="80" rx="4" fill="#2e7d32" transform="rotate(15 97 60)" />
							<rect x="114" y="20" width="18" height="80" rx="4" fill="#f57c00" transform="rotate(25 123 60)" />
							<!-- Tick bug -->
							<ellipse cx="130" cy="30" rx="9" ry="11" fill="#333" />
							<circle cx="130" cy="20" r="5" fill="#333" />
							<line x1="122" y1="26" x2="115" y2="22" stroke="#333" stroke-width="1.5" stroke-linecap="round" />
							<line x1="122" y1="30" x2="113" y2="30" stroke="#333" stroke-width="1.5" stroke-linecap="round" />
						</svg>
					</span>
				<?php endif; ?>
			</span>

			<span class="ttq-product-card__body">
				<span class="ttq-product-card__title-row">
					<span class="ttq-product-card__title"><?php echo esc_html($product['label']); ?></span>
				</span>

				<span class="ttq-product-card__features">
					<?php foreach ($features as $feature) : ?>
						<span class="ttq-product-card__feature">
							<span class="ttq-check" aria-hidden="true">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
									stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
									<polyline points="20 6 9 17 4 12" />
								</svg>
							</span>
							<?php echo esc_html($feature); ?>
						</span>
					<?php endforeach; ?>
				</span>

				<?php if (! empty($product['best_for']) && 'tweezers_only' !== $product['key']) : ?>
					<span class="ttq-product-card__bestfor">
						<span class="ttq-product-card__bestfor-icon" aria-hidden="true">
							<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
								stroke-linecap="round" stroke-linejoin="round">
								<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
								<circle cx="9" cy="7" r="4" />
								<path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
							</svg>
						</span>
						<span>
							<strong><?php esc_html_e('Best For:', 'ttq'); ?></strong>
							<?php echo esc_html($product['best_for']); ?>
						</span>
					</span>
				<?php endif; ?>

			</span>
			<span class="ttq-product-card__footer">
				<button type="button" class="ttq-product-card__footer-text ttq-js-toggle-details"
					data-product-key="<?php echo esc_attr($product['key']); ?>">
					<?php echo $is_featured ? esc_html__('See Kit Contents', 'ttq') : esc_html__("What's Included", 'ttq'); ?>
					<span aria-hidden="true">&rsaquo;</span>
				</button>
				<span class="ttq-product-card__footer-arrow" aria-hidden="true">&rsaquo;</span>
			</span>
		</label>
	<?php endforeach; ?>
</div>

<!-- Confirmation banner: tells the user clearly which product is currently selected -->
<div class="ttq-selected-banner ttq-js-selected-banner" role="status" aria-live="polite">
	<span class="ttq-selected-banner__icon" aria-hidden="true">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
			stroke-linecap="round" stroke-linejoin="round">
			<polyline points="20 6 9 17 4 12" />
		</svg>
	</span>
	<span><?php esc_html_e("You've selected:", 'ttq'); ?> <strong class="ttq-js-selected-banner-name"></strong></span>
</div>

<div class="ttq-field-error" data-error-for="product" role="alert"></div>

<div class="ttq-nav-row ttq-nav-row--end">
	<button type="button" class="ttq-btn ttq-btn--primary ttq-js-next"><?php esc_html_e('Continue', 'ttq'); ?> <span
			aria-hidden="true">&rarr;</span></button>
</div>

<!-- ============================================================
     "What's Included" / "See Kit Contents" popup modal.
     One panel per product key; quote.js toggles which is shown.
     ============================================================ -->
<div class="ttq-modal ttq-js-details-modal" hidden>
	<div class="ttq-modal__backdrop ttq-js-modal-close"></div>
	<div class="ttq-modal__panel" role="dialog" aria-modal="true"
		aria-label="<?php esc_attr_e('Product details', 'ttq'); ?>">
		<button type="button" class="ttq-modal__close ttq-js-modal-close"
			aria-label="<?php esc_attr_e('Close', 'ttq'); ?>">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
				stroke-linecap="round" stroke-linejoin="round">
				<line x1="18" y1="6" x2="6" y2="18" />
				<line x1="6" y1="6" x2="18" y2="18" />
			</svg>
		</button>

		<!-- Tick Tweezers Only -->
		<div class="ttq-modal__body" data-modal-body="tweezers_only" hidden>
			<h4><?php esc_html_e('Tick Tweezers Only', 'ttq'); ?></h4>
			<p class="ttq-modal__intro">
				<?php esc_html_e('Your reliable tick removal solution — precise, safe, and built to last.', 'ttq'); ?>
			</p>
			<ul class="ttq-modal__list">
				<li>
					<strong><?php esc_html_e('Precision tip for easy removal', 'ttq'); ?></strong>
					<span><?php esc_html_e('The fine, precision tip lets you grasp the tick firmly and remove it without squeezing — reducing the risk of infection.', 'ttq'); ?></span>
				</li>
				<li>
					<strong><?php esc_html_e('High-quality materials', 'ttq'); ?></strong>
					<span><?php esc_html_e('Made with high-quality stainless steel that is rust-resistant, easy to clean, and built for long-lasting performance.', 'ttq'); ?></span>
				</li>
				<li>
					<strong><?php esc_html_e('CDC compliant', 'ttq'); ?></strong>
					<span><?php esc_html_e('Meets the standards set by the Centers for Disease Control and Prevention for safe tick removal.', 'ttq'); ?></span>
				</li>
				<li>
					<strong><?php esc_html_e('Available in four sizes', 'ttq'); ?></strong>
					<span><?php esc_html_e('3", 3.5", 4" and 4.5" — choose the size that best fits your needs.', 'ttq'); ?></span>
				</li>
				<li>
					<strong><?php esc_html_e('Fully customizable', 'ttq'); ?></strong>
					<span><?php esc_html_e('Add your logo, text, or URL to the front and back — available in 7+ colors, no extra charge.', 'ttq'); ?></span>
				</li>
				<li>
					<strong><?php esc_html_e('Safe & pet-friendly', 'ttq'); ?></strong>
					<span><?php esc_html_e('Expertly designed and safe to use on both people and pets.', 'ttq'); ?></span>
				</li>
			</ul>
		</div>

		<!-- Complete Tick Kit -->
		<div class="ttq-modal__body" data-modal-body="complete_kit" hidden>
			<h4><?php esc_html_e('Complete Tick Removal Kit', 'ttq'); ?></h4>
			<p class="ttq-modal__intro">
				<?php esc_html_e('Everything needed to support safe tick removal and prevention awareness — in one convenient kit.', 'ttq'); ?>
			</p>
			<ul class="ttq-modal__list">
				<li>
					<strong><?php esc_html_e('Tick removal tweezers included', 'ttq'); ?></strong>
					<span><?php esc_html_e('Our precision, CDC-compliant tick tweezers, ready to use right out of the kit.', 'ttq'); ?></span>
				</li>
				<li>
					<strong><?php esc_html_e('Alcohol prep pad & ID card', 'ttq'); ?></strong>
					<span><?php esc_html_e('For cleaning the bite area and helping identify the tick after removal.', 'ttq'); ?></span>
				</li>
				<li>
					<strong><?php esc_html_e('Tick removal guide & essential kit items', 'ttq'); ?></strong>
					<span><?php esc_html_e('Storage/specimen bags, a bandage, and a step-by-step tick removal guide.', 'ttq'); ?></span>
				</li>
				<li>
					<strong><?php esc_html_e('Customized with your branding', 'ttq'); ?></strong>
					<span><?php esc_html_e('Add your logo, message, website URL, or QR code — no extra charge.', 'ttq'); ?></span>
				</li>
				<li>
					<strong><?php esc_html_e('Best for', 'ttq'); ?></strong>
					<span><?php esc_html_e('Health departments, tick awareness campaigns, prevention programs, public health outreach, schools, parks, camps, and community distribution initiatives.', 'ttq'); ?></span>
				</li>
			</ul>
		</div>
	</div>
</div>