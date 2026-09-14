<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Social Sharing meta box, post-meta registration, and Theme SEO extras.
 *
 * Meta registration runs on every request so REST can expose the keys for
 * posts/pages. The box, scripts, and save handler are admin-only.
 */

/**
 * Register the three OG override keys on each covered post type.
 */
function theme_social_meta_register_post_meta() {
	$types = theme_social_meta_post_types();

	foreach ( $types as $type ) {
		register_post_meta(
			$type,
			'_theme_og_title',
			array(
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => 'theme_social_meta_auth_callback',
				'show_in_rest'      => true,
			)
		);
		register_post_meta(
			$type,
			'_theme_og_description',
			array(
				'single'            => true,
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_textarea_field',
				'auth_callback'     => 'theme_social_meta_auth_callback',
				'show_in_rest'      => true,
			)
		);
		register_post_meta(
			$type,
			'_theme_og_image_id',
			array(
				'single'            => true,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'auth_callback'     => 'theme_social_meta_auth_callback',
				'show_in_rest'      => true,
			)
		);
	}
}
add_action( 'init', 'theme_social_meta_register_post_meta', 20 );

/**
 * REST / meta auth: the current user must be able to edit the post.
 *
 * @param bool   $allowed  Incoming allowance.
 * @param string $meta_key Meta key.
 * @param int    $post_id  Post ID.
 * @return bool
 */
function theme_social_meta_auth_callback( $allowed, $meta_key, $post_id ) {
	unset( $allowed, $meta_key );
	return current_user_can( 'edit_post', $post_id );
}

if ( ! is_admin() ) {
	return;
}

/**
 * Register the Social Sharing meta box on every covered post type.
 */
function theme_social_meta_add_meta_box() {
	foreach ( theme_social_meta_post_types() as $type ) {
		add_meta_box(
			'theme_social_meta_box',
			'Social Sharing',
			'theme_social_meta_box_callback',
			$type,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'theme_social_meta_add_meta_box' );

/**
 * Render the Social Sharing fields.
 *
 * @param WP_Post $post Current post.
 */
function theme_social_meta_box_callback( $post ) {
	wp_nonce_field( 'theme_social_meta_box', 'theme_social_meta_nonce' );

	$title       = (string) get_post_meta( $post->ID, '_theme_og_title', true );
	$description = (string) get_post_meta( $post->ID, '_theme_og_description', true );
	$image_id    = (int) get_post_meta( $post->ID, '_theme_og_image_id', true );

	$fallback = theme_social_meta_get_data( $post->ID, true );

	$preview_src = '';
	$width       = 0;
	$height      = 0;
	if ( $image_id > 0 ) {
		$full = wp_get_attachment_image_src( $image_id, 'full' );
		$mid  = wp_get_attachment_image_src( $image_id, 'medium' );
		if ( $full ) {
			$width  = (int) $full[1];
			$height = (int) $full[2];
		}
		if ( $mid ) {
			$preview_src = $mid[0];
		} elseif ( $full ) {
			$preview_src = $full[0];
		}
	}

	$warning = theme_social_meta_image_size_warning( $width, $height );
	$fallback_image_note = 'Leave empty to use the featured image, then the first image in the content, then the site default.';
	if ( ! empty( $fallback['image']['url'] ) ) {
		$fallback_image_note .= ' Current fallback: the image WordPress will pick automatically.';
	}
	?>
	<div class="theme-social-meta-box">
		<p class="description">Overrides used only on share cards (Facebook, X, LinkedIn). The browser title is unchanged. Recommended image: 1200×630 px (1.91:1), JPG or PNG, under 5 MB. Minimum 600×315. Images added only at render time (shortcodes, oEmbed) are not used as fallbacks.</p>

		<p>
			<label for="theme_og_title"><strong>OG title</strong></label><br>
			<input type="text"
				id="theme_og_title"
				name="theme_og_title"
				class="widefat theme-og-count"
				data-limit="70"
				value="<?php echo esc_attr( $title ); ?>"
				placeholder="<?php echo esc_attr( $fallback['title'] ); ?>">
			<span class="theme-og-counter" data-for="theme_og_title">0 / 70</span>
			<span class="description">Warns above 70 characters (X truncates around 70, Facebook around 88). Not truncated on save.</span>
		</p>

		<p>
			<label for="theme_og_description"><strong>OG description</strong></label><br>
			<textarea id="theme_og_description"
				name="theme_og_description"
				class="widefat theme-og-count"
				data-limit="200"
				rows="3"
				placeholder="<?php echo esc_attr( $fallback['description'] ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
			<span class="theme-og-counter" data-for="theme_og_description">0 / 200</span>
			<span class="description">Warns above 200 characters (X truncates around 200). Not truncated on save.</span>
		</p>

		<p>
			<label for="theme_og_image_id"><strong>OG image</strong></label><br>
			<input type="hidden" id="theme_og_image_id" name="theme_og_image_id" value="<?php echo esc_attr( $image_id ? (string) $image_id : '' ); ?>">
			<button type="button" class="button" id="theme_og_image_select">Select image</button>
			<button type="button" class="button theme-og-remove" id="theme_og_image_remove"<?php echo $image_id ? '' : ' style="display:none;"'; ?>>Remove</button>
			<span class="description"><?php echo esc_html( $fallback_image_note ); ?></span>
		</p>
		<div class="theme-og-preview" id="theme_og_image_preview">
			<?php if ( $preview_src ) : ?>
				<img src="<?php echo esc_url( $preview_src ); ?>" alt="">
			<?php endif; ?>
		</div>
		<p class="theme-og-dimensions" id="theme_og_image_dimensions" data-width="<?php echo esc_attr( (string) $width ); ?>" data-height="<?php echo esc_attr( (string) $height ); ?>">
			<?php
			if ( $width && $height ) {
				echo esc_html( $width . ' × ' . $height . ' px' );
			}
			?>
		</p>
		<p class="theme-og-warning" id="theme_og_image_warning"<?php echo $warning === '' ? ' style="display:none;"' : ''; ?>><?php echo esc_html( $warning ); ?></p>
	</div>
	<?php
}

/**
 * Persist OG overrides. Empty fields delete the meta so "is overridden" is !== ''.
 *
 * @param int $post_id Post ID.
 */
function theme_social_meta_save_post( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['theme_social_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['theme_social_meta_nonce'] ) ), 'theme_social_meta_box' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( ! in_array( get_post_type( $post_id ), theme_social_meta_post_types(), true ) ) {
		return;
	}

	$title = isset( $_POST['theme_og_title'] ) ? sanitize_text_field( wp_unslash( $_POST['theme_og_title'] ) ) : '';
	if ( $title === '' ) {
		delete_post_meta( $post_id, '_theme_og_title' );
	} else {
		update_post_meta( $post_id, '_theme_og_title', $title );
	}

	$description = isset( $_POST['theme_og_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['theme_og_description'] ) ) : '';
	if ( $description === '' ) {
		delete_post_meta( $post_id, '_theme_og_description' );
	} else {
		update_post_meta( $post_id, '_theme_og_description', $description );
	}

	$image_id = isset( $_POST['theme_og_image_id'] ) ? absint( wp_unslash( $_POST['theme_og_image_id'] ) ) : 0;
	if ( $image_id > 0 && wp_attachment_is_image( $image_id ) ) {
		update_post_meta( $post_id, '_theme_og_image_id', $image_id );
	} else {
		delete_post_meta( $post_id, '_theme_og_image_id' );
	}
}
add_action( 'save_post', 'theme_social_meta_save_post' );

/**
 * Media picker + counters on post edit screens for covered types.
 *
 * @param string $hook Current admin page.
 */
function theme_social_meta_admin_scripts( $hook ) {
	if ( $hook !== 'post.php' && $hook !== 'post-new.php' ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || ! in_array( $screen->post_type, theme_social_meta_post_types(), true ) ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'theme-social-meta-box',
		get_template_directory_uri() . '/assets/js/theme-social-meta-box.js',
		array( 'jquery' ),
		'1.0',
		true
	);

	wp_add_inline_style(
		'wp-admin',
		'
		.theme-social-meta-box .theme-og-counter { display: block; margin-top: 4px; color: #646970; }
		.theme-social-meta-box .theme-og-counter.is-over { color: #d63638; font-weight: 600; }
		.theme-social-meta-box .theme-og-preview { margin-top: 10px; }
		.theme-social-meta-box .theme-og-preview img { max-width: 300px; height: auto; border: 1px solid #ddd; border-radius: 4px; }
		.theme-social-meta-box .theme-og-dimensions { color: #646970; margin: 6px 0 0; }
		.theme-social-meta-box .theme-og-warning { color: #d63638; margin: 6px 0 0; }
		.theme-social-meta-box .theme-og-remove { color: #a00; margin-left: 8px; }
		.theme-social-meta-box textarea { width: 100%; }
		'
	);
}
add_action( 'admin_enqueue_scripts', 'theme_social_meta_admin_scripts' );
