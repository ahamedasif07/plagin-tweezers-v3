<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * wp-admin screen for configuring the quote wizard, plus custom post types
 * used for submissions (ttq_quote) and dynamic product management (ttq_product).
 */
class TTQ_Admin {

	const OPTION_KEY = 'ttq_settings';

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_submission_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'save_post_ttq_product', array( $this, 'save_product_meta' ) );
		add_filter( 'manage_ttq_product_posts_columns', array( $this, 'product_columns' ) );
		add_action( 'manage_ttq_product_posts_custom_column', array( $this, 'product_column_content' ), 10, 2 );
	}

	public function enqueue_admin_assets( $hook ) {
		// Enqueue on the settings page, or when editing/creating a ttq_product
		if ( 'toplevel_page_ttq-settings' !== $hook && 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}

		global $post_type;
		if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'ttq_product' !== $post_type ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'ttq-admin', TTQ_URL . 'assets/css/admin.css', array(), TTQ_VERSION );
		wp_enqueue_script( 'ttq-admin', TTQ_URL . 'assets/js/admin.js', array(), TTQ_VERSION, true );
	}

	public function register_post_type() {
		// Register Quote Submissions
		register_post_type(
			'ttq_quote',
			array(
				'label'           => __( 'Quote Requests', 'ttq' ),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => 'ttq-settings',
				'supports'        => array( 'title' ),
				'capability_type' => 'post',
				'menu_icon'       => 'dashicons-list-view',
			)
		);

		// Register Products for Dynamic Management
		register_post_type(
			'ttq_product',
			array(
				'labels'          => array(
					'name'               => __( 'Products', 'ttq' ),
					'singular_name'      => __( 'Product', 'ttq' ),
					'add_new_item'       => __( 'Add New Product', 'ttq' ),
					'edit_item'          => __( 'Edit Product', 'ttq' ),
					'new_item'           => __( 'New Product', 'ttq' ),
					'view_item'          => __( 'View Product', 'ttq' ),
					'search_items'       => __( 'Search Products', 'ttq' ),
					'not_found'          => __( 'No products found', 'ttq' ),
					'not_found_in_trash' => __( 'No products found in Trash', 'ttq' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => 'ttq-settings',
				'supports'        => array( 'title', 'thumbnail', 'page-attributes' ),
				'hierarchical'    => false,
				'capability_type' => 'post',
				'menu_icon'       => 'dashicons-cart',
			)
		);

		// Run migration helper
		if ( is_admin() ) {
			$this->migrate_products();
		}
	}

	/**
	 * Automatically migrates existing repeater products to CPT posts
	 * if no CPT products exist in the database.
	 */
	private function migrate_products() {
		$posts = get_posts( array(
			'post_type'      => 'ttq_product',
			'post_status'    => 'any',
			'posts_per_page' => 1,
		) );

		if ( ! empty( $posts ) ) {
			return;
		}

		$settings = self::get_settings();
		$products = isset( $settings['products'] ) ? $settings['products'] : array();

		foreach ( $products as $i => $prod ) {
			$post_id = wp_insert_post( array(
				'post_type'   => 'ttq_product',
				'post_status' => 'publish',
				'post_title'  => $prod['label'],
				'post_name'   => $prod['key'],
				'menu_order'  => $i,
			) );

			if ( ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_ttq_price', $prod['price'] );
				update_post_meta( $post_id, '_ttq_best_for', $prod['best_for'] );
				update_post_meta( $post_id, '_ttq_featured', $prod['featured'] );
				update_post_meta( $post_id, '_ttq_features', $prod['features'] );
				update_post_meta( $post_id, '_ttq_colors', isset( $prod['colors'] ) ? $prod['colors'] : 'black,brown,green,orange,red,gray,yellow,blue,light_blue,lime_green,hot_pink' );
				update_post_meta( $post_id, '_ttq_sizes', isset( $prod['sizes'] ) ? $prod['sizes'] : '3in,3_5in,4in,4_5in' );
				update_post_meta( $post_id, '_ttq_image_url', $prod['image'] );
			}
		}
	}

	public function add_menu() {
		// Top-level parent — no callback, just a container for submenus.
		add_menu_page(
			__( 'TickTweezers', 'ttq' ),
			__( 'TickTweezers', 'ttq' ),
			'manage_options',
			'ttq-settings',
			array( $this, 'render_settings_page' ),
			'dashicons-forms',
			56
		);

		// Explicit "Quote Settings" submenu so it is always visible.
		add_submenu_page(
			'ttq-settings',
			__( 'Quote Settings', 'ttq' ),
			__( 'Quote Settings', 'ttq' ),
			'manage_options',
			'ttq-settings',               // same slug → reuses parent callback
			array( $this, 'render_settings_page' )
		);
	}

	public function register_settings() {
		register_setting( 'ttq_settings_group', self::OPTION_KEY, array( $this, 'sanitize_settings' ) );
	}

	public static function default_settings() {
		return array(
			'products' => array(
				array( 'key' => 'tweezers_only', 'label' => 'Tick Tweezers Only', 'price' => '1.25', 'best_for' => '', 'image' => '', 'featured' => '', 'features' => 'Removes ticks safely|Precision stainless steel tips|Personalized with your logo', 'colors' => 'black,brown,green,orange,red,gray,yellow,blue,light_blue,lime_green,hot_pink', 'sizes' => '3in,3_5in,4in,4_5in' ),
				array( 'key' => 'complete_kit', 'label' => 'Complete Tick Kit', 'price' => '6.25', 'best_for' => 'Health Departments, Outdoor Organizations, Schools & Camps', 'image' => '', 'featured' => '1', 'features' => 'Tick removal tool included|Alcohol prep pad & ID card|Personalized with your logo', 'colors' => 'black,brown,green,orange,red,gray,yellow,blue,light_blue,lime_green,hot_pink', 'sizes' => '3in,3_5in,4in,4_5in' ),
			),
			'colors'                     => array(
				array( 'key' => 'black',     'label' => 'Black',      'hex' => '#000000' ),
				array( 'key' => 'brown',     'label' => 'Brown',      'hex' => '#8a5a2c' ),
				array( 'key' => 'green',     'label' => 'Green',      'hex' => '#2e7d32' ),
				array( 'key' => 'orange',    'label' => 'Orange',     'hex' => '#f57c00' ),
				array( 'key' => 'red',       'label' => 'Red',        'hex' => '#c62828' ),
				array( 'key' => 'gray',      'label' => 'Gray',       'hex' => '#9e9e9e' ),
				array( 'key' => 'yellow',    'label' => 'Yellow',     'hex' => '#fdd835' ),
				array( 'key' => 'blue',      'label' => 'Blue',       'hex' => '#142d6b' ),
				array( 'key' => 'light_blue','label' => 'Light Blue', 'hex' => '#4fc3f7' ),
				array( 'key' => 'lime_green','label' => 'Lime Green', 'hex' => '#32c832' ),
				array( 'key' => 'hot_pink',  'label' => 'Hot Pink',   'hex' => '#f50057' ),
			),
			'sizes'                      => array(
				array( 'key' => '3in',   'label' => '3"' ),
				array( 'key' => '3_5in', 'label' => '3.5"' ),
				array( 'key' => '4in',   'label' => '4"' ),
				array( 'key' => '4_5in', 'label' => '4.5"' ),
			),
			'min_quantity'               => 25,
			'max_quantity'               => 10000,
			'allowed_file_types'         => 'png,jpg,jpeg,svg,pdf,eps,ai',
			'max_upload_mb'              => 10,
			'personalization_max_chars'  => 30,
			'email_recipients'           => get_option( 'admin_email' ),
			'company_name'               => 'TickTweezers',
			'success_message'            => __( 'Thanks for helping keep our communities safe and tick smart! Our team will follow up with your quote shortly.', 'ttq' ),
		);
	}

	public static function get_settings() {
		$saved    = get_option( self::OPTION_KEY, array() );
		$defaults = self::default_settings();
		$settings = wp_parse_args( $saved, $defaults );

		// Always merge in any new default colors that are not yet saved.
		$saved_keys = array_column( isset( $saved['colors'] ) ? $saved['colors'] : array(), 'key' );
		foreach ( $defaults['colors'] as $default_color ) {
			if ( ! in_array( $default_color['key'], $saved_keys, true ) ) {
				$settings['colors'][] = $default_color;
			}
		}

		return $settings;
	}

	/**
	 * Pulls list of products dynamically from CPT ttq_product posts.
	 * If no posts exist yet, it falls back to the database option settings.
	 */
	public static function get_dynamic_products() {
		$posts = get_posts( array(
			'post_type'      => 'ttq_product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		) );

		$products = array();
		foreach ( $posts as $p ) {
			$post_id     = $p->ID;
			$price       = get_post_meta( $post_id, '_ttq_price', true );
			$best_for    = get_post_meta( $post_id, '_ttq_best_for', true );
			$featured    = get_post_meta( $post_id, '_ttq_featured', true );
			$features    = get_post_meta( $post_id, '_ttq_features', true );
			$colors      = get_post_meta( $post_id, '_ttq_colors', true );
			$sizes       = get_post_meta( $post_id, '_ttq_sizes', true );
			$image_url   = get_post_meta( $post_id, '_ttq_image_url', true );

			if ( empty( $image_url ) ) {
				$image_url = get_the_post_thumbnail_url( $post_id, 'medium' );
			}

			$products[] = array(
				'key'      => $p->post_name,
				'label'    => $p->post_title,
				'price'    => $price,
				'best_for' => $best_for,
				'image'    => $image_url ? $image_url : '',
				'featured' => $featured,
				'features' => $features,
				'colors'   => $colors,
				'sizes'    => $sizes,
			);
		}

		if ( empty( $products ) ) {
			$settings = self::get_settings();
			return isset( $settings['products'] ) ? $settings['products'] : array();
		}

		return $products;
	}

	public function sanitize_settings( $input ) {
		$clean = self::default_settings();

		if ( isset( $input['min_quantity'] ) ) {
			$clean['min_quantity'] = absint( $input['min_quantity'] );
		}
		if ( isset( $input['max_quantity'] ) ) {
			$clean['max_quantity'] = absint( $input['max_quantity'] );
		}
		if ( isset( $input['max_upload_mb'] ) ) {
			$clean['max_upload_mb'] = absint( $input['max_upload_mb'] );
		}
		if ( isset( $input['personalization_max_chars'] ) ) {
			$clean['personalization_max_chars'] = absint( $input['personalization_max_chars'] );
		}
		if ( isset( $input['allowed_file_types'] ) ) {
			$clean['allowed_file_types'] = sanitize_text_field( wp_unslash( $input['allowed_file_types'] ) );
		}
		if ( isset( $input['email_recipients'] ) ) {
			$clean['email_recipients'] = sanitize_text_field( wp_unslash( $input['email_recipients'] ) );
		}
		if ( isset( $input['company_name'] ) ) {
			$clean['company_name'] = sanitize_text_field( wp_unslash( $input['company_name'] ) );
		}
		if ( isset( $input['success_message'] ) ) {
			$clean['success_message'] = sanitize_textarea_field( wp_unslash( $input['success_message'] ) );
		}

		// Colors and sizes repeaters
		foreach ( array( 'colors', 'sizes' ) as $repeater ) {
			if ( ! empty( $input[ $repeater ] ) ) {
				$decoded = json_decode( wp_unslash( $input[ $repeater ] ), true );
				if ( is_array( $decoded ) ) {
					$clean[ $repeater ] = self::sanitize_repeater( $decoded );
				}
			}
		}

		return $clean;
	}

	private static function sanitize_repeater( array $rows ) {
		$out = array();
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) { continue; }
			$clean_row = array();
			foreach ( $row as $k => $v ) {
				$key = sanitize_key( $k );
				if ( ! is_scalar( $v ) ) { continue; }
				$clean_row[ $key ] = sanitize_text_field( wp_unslash( (string) $v ) );
			}
			if ( ! empty( $clean_row ) ) {
				$out[] = $clean_row;
			}
		}
		return $out;
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$settings = self::get_settings();
		include TTQ_PATH . 'admin/settings-page.php';
	}

	public function add_submission_meta_box() {
		add_meta_box( 'ttq_submission_details', __( 'Submission Details', 'ttq' ), array( $this, 'render_submission_meta_box' ), 'ttq_quote', 'normal', 'high' );
		add_meta_box( 'ttq_product_details', __( 'Product Details', 'ttq' ), array( $this, 'render_product_meta_box' ), 'ttq_product', 'normal', 'high' );
	}

	public function render_submission_meta_box( $post ) {
		$submission = get_post_meta( $post->ID, '_ttq_submission', true );
		include TTQ_PATH . 'admin/submission-meta-box.php';
	}

	public function render_product_meta_box( $post ) {
		$settings = self::get_settings();
		$colors   = isset( $settings['colors'] ) ? $settings['colors'] : array();
		$sizes    = isset( $settings['sizes'] ) ? $settings['sizes'] : array();

		$price       = get_post_meta( $post->ID, '_ttq_price', true );
		$best_for    = get_post_meta( $post->ID, '_ttq_best_for', true );
		$featured    = get_post_meta( $post->ID, '_ttq_featured', true );
		$features    = get_post_meta( $post->ID, '_ttq_features', true );
		$image_url   = get_post_meta( $post->ID, '_ttq_image_url', true );
		$prod_colors = get_post_meta( $post->ID, '_ttq_colors', true );
		$prod_sizes  = get_post_meta( $post->ID, '_ttq_sizes', true );

		if ( ! is_array( $prod_colors ) ) {
			$prod_colors = ! empty( $prod_colors ) ? explode( ',', $prod_colors ) : array();
		}
		if ( ! is_array( $prod_sizes ) ) {
			$prod_sizes = ! empty( $prod_sizes ) ? explode( ',', $prod_sizes ) : array();
		}

		wp_nonce_field( 'ttq_save_product_meta', 'ttq_product_meta_nonce' );
		?>
		<table class="form-table">
			<tr>
				<th><label for="ttq-price"><?php esc_html_e( 'Price', 'ttq' ); ?></label></th>
				<td>
					<input type="text" id="ttq-price" name="ttq_price" value="<?php echo esc_attr( $price ); ?>" class="regular-text" placeholder="e.g. 1.25" />
				</td>
			</tr>
			<tr>
				<th><label for="ttq-best-for"><?php esc_html_e( 'Best For', 'ttq' ); ?></label></th>
				<td>
					<input type="text" id="ttq-best-for" name="ttq_best_for" value="<?php echo esc_attr( $best_for ); ?>" class="regular-text" placeholder="e.g. Health Departments, Schools" />
				</td>
			</tr>
			<tr>
				<th><label for="ttq-featured"><?php esc_html_e( 'Most Popular / Featured', 'ttq' ); ?></label></th>
				<td>
					<label>
						<input type="checkbox" id="ttq-featured" name="ttq_featured" value="1" <?php checked( $featured, '1' ); ?> />
						<?php esc_html_e( 'Mark as Most Popular / Featured', 'ttq' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th><label for="ttq-features"><?php esc_html_e( 'Features List', 'ttq' ); ?></label></th>
				<td>
					<textarea id="ttq-features" name="ttq_features" rows="4" class="large-text" placeholder="Feature 1|Feature 2|Feature 3"><?php echo esc_textarea( $features ); ?></textarea>
					<p class="description"><?php esc_html_e( 'Separate features with a pipe (|) character.', 'ttq' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="ttq-image-url"><?php esc_html_e( 'Product Image', 'ttq' ); ?></label></th>
				<td>
					<div style="display:flex; flex-direction:column; gap:6px; max-width: 350px;">
						<img id="ttq-preview-img" src="<?php echo esc_url( $image_url ); ?>" style="max-width:100px; max-height:100px; object-fit:contain; border:1px solid #ccc; background:#f9f9f9; border-radius:4px; <?php echo empty( $image_url ) ? 'display:none;' : ''; ?>" />
						<input type="text" id="ttq-image-url" name="ttq_image_url" value="<?php echo esc_attr( $image_url ); ?>" class="regular-text" style="width:100%;" />
						<button type="button" id="ttq-upload-btn" class="button button-secondary" style="align-self:flex-start;"><?php esc_html_e( 'Upload / Select Image', 'ttq' ); ?></button>
						<a href="#" id="ttq-remove-img" style="color:#b32d2e; font-size:11px;"><?php esc_html_e( 'Remove Image', 'ttq' ); ?></a>
					</div>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Available Colors', 'ttq' ); ?></th>
				<td>
					<?php if ( empty( $colors ) ) : ?>
						<p class="description"><?php esc_html_e( 'No colors configured. Please configure colors in Quote Settings first.', 'ttq' ); ?></p>
					<?php else : ?>
						<div style="display: flex; flex-direction: column; gap: 6px;">
							<?php foreach ( $colors as $color ) : ?>
								<label style="display: inline-flex; align-items: center; gap: 8px;">
									<input type="checkbox" name="ttq_colors[]" value="<?php echo esc_attr( $color['key'] ); ?>" <?php checked( in_array( $color['key'], $prod_colors, true ) ); ?> />
									<span style="display:inline-block; width: 12px; height: 12px; border-radius: 50%; background: <?php echo esc_attr( $color['hex'] ); ?>; border: 1px solid #ccc;"></span>
									<?php echo esc_html( $color['label'] ); ?>
								</label>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Available Sizes', 'ttq' ); ?></th>
				<td>
					<?php if ( empty( $sizes ) ) : ?>
						<p class="description"><?php esc_html_e( 'No sizes configured. Please configure sizes in Quote Settings first.', 'ttq' ); ?></p>
					<?php else : ?>
						<div style="display: flex; flex-direction: column; gap: 6px;">
							<?php foreach ( $sizes as $size ) : ?>
								<label style="display: inline-flex; align-items: center; gap: 8px;">
									<input type="checkbox" name="ttq_sizes[]" value="<?php echo esc_attr( $size['key'] ); ?>" <?php checked( in_array( $size['key'], $prod_sizes, true ) ); ?> />
									<?php echo esc_html( $size['label'] ); ?>
								</label>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</td>
			</tr>
		</table>
		<?php
	}

	public function save_product_meta( $post_id ) {
		if ( ! isset( $_POST['ttq_product_meta_nonce'] ) || ! wp_verify_nonce( $_POST['ttq_product_meta_nonce'], 'ttq_save_product_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['ttq_price'] ) ) {
			update_post_meta( $post_id, '_ttq_price', sanitize_text_field( $_POST['ttq_price'] ) );
		}
		if ( isset( $_POST['ttq_best_for'] ) ) {
			update_post_meta( $post_id, '_ttq_best_for', sanitize_text_field( $_POST['ttq_best_for'] ) );
		}
		
		$featured = isset( $_POST['ttq_featured'] ) ? '1' : '';
		update_post_meta( $post_id, '_ttq_featured', $featured );

		if ( isset( $_POST['ttq_features'] ) ) {
			update_post_meta( $post_id, '_ttq_features', sanitize_textarea_field( $_POST['ttq_features'] ) );
		}
		if ( isset( $_POST['ttq_image_url'] ) ) {
			update_post_meta( $post_id, '_ttq_image_url', esc_url_raw( $_POST['ttq_image_url'] ) );
		}

		$colors = isset( $_POST['ttq_colors'] ) && is_array( $_POST['ttq_colors'] ) ? array_map( array( __CLASS__, 'sanitize_option_key' ), $_POST['ttq_colors'] ) : array();
		update_post_meta( $post_id, '_ttq_colors', implode( ',', $colors ) );

		$sizes = isset( $_POST['ttq_sizes'] ) && is_array( $_POST['ttq_sizes'] ) ? array_map( array( __CLASS__, 'sanitize_option_key' ), $_POST['ttq_sizes'] ) : array();
		update_post_meta( $post_id, '_ttq_sizes', implode( ',', $sizes ) );
	}

	/**
	 * Like sanitize_key(), but preserves decimal points so size keys such as
	 * "3.4" survive round-tripping instead of being mangled into "34".
	 */
	public static function sanitize_option_key( $key ) {
		$key = strtolower( trim( (string) $key ) );
		$key = preg_replace( '/\s+/', '_', $key );
		$key = preg_replace( '/[^a-z0-9_.\-]/', '', $key );
		return $key;
	}

	public function product_columns( $columns ) {
		$new_columns = array(
			'cb'       => $columns['cb'],
			'title'    => $columns['title'],
			'price'    => __( 'Price', 'ttq' ),
			'featured' => __( 'Featured', 'ttq' ),
			'date'     => $columns['date'],
		);
		return $new_columns;
	}

	public function product_column_content( $column, $post_id ) {
		if ( 'price' === $column ) {
			$price = get_post_meta( $post_id, '_ttq_price', true );
			echo $price ? '$' . esc_html( $price ) : '—';
		} elseif ( 'featured' === $column ) {
			$featured = get_post_meta( $post_id, '_ttq_featured', true );
			echo '1' === $featured ? '<span style="color:#2e7d32; font-weight:bold;">★ Yes</span>' : 'No';
		}
	}
}
