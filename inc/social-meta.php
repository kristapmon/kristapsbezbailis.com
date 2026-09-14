<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Open Graph / Twitter / meta-description resolver and emitters.
 *
 * A single fallback chain feeds every social tag so the share card, Twitter
 * card, meta description, and Article JSON-LD cannot drift apart.
 */

/**
 * Post types that get a Social Sharing meta box and per-post overrides.
 *
 * @return string[]
 */
function theme_social_meta_post_types() {
	return apply_filters(
		'theme_social_meta_post_types',
		array( 'post', 'page', 'notes', 'projects' )
	);
}

/**
 * Whether the theme should emit social / description tags.
 *
 * Auto-disables when a common SEO plugin is present so tags are not duplicated.
 *
 * @return bool
 */
function theme_social_meta_is_enabled() {
	$seo_plugin = defined( 'WPSEO_VERSION' )
		|| class_exists( 'RankMath' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'AIOSEO_VERSION' );

	return (bool) apply_filters( 'theme_social_meta_enabled', ! $seo_plugin );
}

/**
 * Register the 1200×630 hard-crop size used for featured-image share cards.
 */
function theme_social_meta_register_image_size() {
	add_image_size( 'theme-og-image', 1200, 630, true );
}
add_action( 'after_setup_theme', 'theme_social_meta_register_image_size' );

/**
 * Build an image array from an attachment ID.
 *
 * @param int    $id   Attachment ID.
 * @param string $size Registered image size.
 * @return array{url:string,width:int,height:int,alt:string,mime:string}|null
 */
function theme_social_meta_image_from_attachment( $id, $size ) {
	$id = (int) $id;
	if ( $id <= 0 || ! wp_attachment_is_image( $id ) ) {
		return null;
	}

	$src = wp_get_attachment_image_src( $id, $size );
	if ( ! $src || empty( $src[0] ) ) {
		return null;
	}

	$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );

	return array(
		'url'    => $src[0],
		'width'  => isset( $src[1] ) ? (int) $src[1] : 0,
		'height' => isset( $src[2] ) ? (int) $src[2] : 0,
		'alt'    => is_string( $alt ) ? $alt : '',
		'mime'   => (string) get_post_mime_type( $id ),
	);
}

/**
 * Featured image at the cropped OG size when that rendition exists, else full.
 *
 * @param int $post_id Post ID.
 * @return array{url:string,width:int,height:int,alt:string,mime:string}|null
 */
function theme_social_meta_featured_image( $post_id ) {
	$id = (int) get_post_thumbnail_id( $post_id );
	if ( $id <= 0 ) {
		return null;
	}

	$size = 'full';
	if ( function_exists( 'image_get_intermediate_size' ) && image_get_intermediate_size( $id, 'theme-og-image' ) ) {
		$size = 'theme-og-image';
	}

	return theme_social_meta_image_from_attachment( $id, $size );
}

/**
 * Site-wide default OG image (attachment ID, with lazy URL→ID resolution).
 *
 * @return array{url:string,width:int,height:int,alt:string,mime:string}|null
 */
function theme_social_meta_default_image() {
	$options = theme_seo_get_options();
	$id      = isset( $options['default_og_image_id'] ) ? (int) $options['default_og_image_id'] : 0;
	$url     = isset( $options['default_og_image'] ) ? (string) $options['default_og_image'] : '';

	if ( $id <= 0 && $url !== '' && function_exists( 'attachment_url_to_postid' ) ) {
		$id = (int) attachment_url_to_postid( $url );
	}

	if ( $id > 0 ) {
		$image = theme_social_meta_image_from_attachment( $id, 'full' );
		if ( $image ) {
			return $image;
		}
	}

	if ( $url !== '' ) {
		return array(
			'url'    => $url,
			'width'  => 0,
			'height' => 0,
			'alt'    => '',
			'mime'   => '',
		);
	}

	return null;
}

/**
 * Strip shortcodes, block markup, and tags, then trim to a word count.
 *
 * @param string $html  Raw post content.
 * @param int    $words Word limit.
 * @return string
 */
function theme_social_meta_trim( $html, $words = 30 ) {
	$text = (string) $html;
	if ( $text === '' ) {
		return '';
	}

	if ( function_exists( 'excerpt_remove_blocks' ) ) {
		$text = excerpt_remove_blocks( $text );
	}
	$text = strip_shortcodes( $text );
	$text = wp_strip_all_tags( $text );

	return wp_trim_words( $text, (int) $words, '…' );
}

/**
 * First non-empty trimmed string from a list.
 *
 * @param array<int, mixed> $values Candidates.
 * @return string
 */
function theme_social_meta_first_nonempty( $values ) {
	foreach ( $values as $value ) {
		if ( is_string( $value ) && trim( $value ) !== '' ) {
			return $value;
		}
	}

	return '';
}

/**
 * Resolved site name for og:site_name / title fallback.
 *
 * @return string
 */
function theme_social_meta_site_name() {
	$options = theme_seo_get_options();
	if ( ! empty( $options['og_site_name'] ) ) {
		return (string) $options['og_site_name'];
	}

	return (string) get_bloginfo( 'name' );
}

/**
 * Site-wide default description (option, then tagline).
 *
 * @return string
 */
function theme_social_meta_default_description() {
	$options = theme_seo_get_options();
	if ( ! empty( $options['default_og_description'] ) ) {
		return (string) $options['default_og_description'];
	}

	return (string) get_bloginfo( 'description' );
}

/**
 * Empty payload used as the resolver starting point.
 *
 * @return array<string, mixed>
 */
function theme_social_meta_empty_data() {
	return array(
		'title'       => '',
		'description' => '',
		'image'       => null,
		'type'        => 'website',
		'url'         => '',
		'site_name'   => theme_social_meta_site_name(),
		'published'   => '',
		'modified'    => '',
		'context'     => 'other',
		'post_id'     => 0,
		'post_type'   => '',
		'tags'        => array(),
		'full'        => false,
	);
}

/**
 * Resolve social meta for a concrete post (singular, blog-index page, editor).
 *
 * @param WP_Post $post              Post.
 * @param bool    $ignore_overrides  Skip per-post OG meta (editor placeholders).
 * @return array<string, mixed>
 */
function theme_social_meta_resolve_post( $post, $ignore_overrides = false ) {
	$data     = theme_social_meta_empty_data();
	$post_id  = (int) $post->ID;
	$post_type = $post->post_type;

	$override_title = '';
	$override_desc  = '';
	$override_image = 0;
	if ( ! $ignore_overrides ) {
		$override_title = (string) get_post_meta( $post_id, '_theme_og_title', true );
		$override_desc  = (string) get_post_meta( $post_id, '_theme_og_description', true );
		$override_image = (int) get_post_meta( $post_id, '_theme_og_image_id', true );
	}

	$title = theme_social_meta_first_nonempty(
		array(
			$override_title,
			get_the_title( $post ),
			theme_social_meta_site_name(),
		)
	);

	$description = $override_desc;
	if ( $description === '' && has_excerpt( $post_id ) ) {
		$description = wp_strip_all_tags( (string) $post->post_excerpt );
	}
	if ( $description === '' && ! post_password_required( $post ) ) {
		$description = theme_social_meta_trim( $post->post_content );
	}
	if ( $description === '' ) {
		$description = theme_social_meta_default_description();
	}

	$image = null;
	if ( $override_image > 0 ) {
		$image = theme_social_meta_image_from_attachment( $override_image, 'full' );
	}
	if ( ! $image && $post_type === 'attachment' ) {
		$image = theme_social_meta_image_from_attachment( $post_id, 'full' );
	}
	if ( ! $image ) {
		$image = theme_social_meta_featured_image( $post_id );
	}
	if ( ! $image ) {
		$image = theme_social_meta_default_image();
	}

	$type = ( $post_type === 'page' || $post_type === 'attachment' ) ? 'website' : 'article';

	$tags = array();
	if ( $post_type === 'post' ) {
		$tag_objs = get_the_tags( $post_id );
		if ( $tag_objs && ! is_wp_error( $tag_objs ) ) {
			foreach ( $tag_objs as $tag ) {
				$tags[] = $tag->name;
			}
		}
	}

	$data['title']       = $title;
	$data['description'] = $description;
	$data['image']       = $image;
	$data['type']        = $type;
	$data['url']         = (string) get_permalink( $post );
	$data['published']   = ( $type === 'article' ) ? (string) get_the_date( 'c', $post ) : '';
	$data['modified']    = ( $type === 'article' ) ? (string) get_the_modified_date( 'c', $post ) : '';
	$data['context']     = 'singular';
	$data['post_id']     = $post_id;
	$data['post_type']   = $post_type;
	$data['tags']        = $tags;
	$data['full']        = true;

	return $data;
}

/**
 * Resolve social meta for the current request or a given post ID.
 *
 * @param int|null $post_id           Optional post ID (editor / explicit lookup).
 * @param bool     $ignore_overrides  Skip per-post OG meta.
 * @return array<string, mixed>
 */
function theme_social_meta_get_data( $post_id = null, $ignore_overrides = false ) {
	static $cache = array();

	$key = ( $post_id ? (string) (int) $post_id : 'current' ) . ':' . ( $ignore_overrides ? '1' : '0' );
	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}

	$cache[ $key ] = theme_social_meta_resolve( $post_id, $ignore_overrides );
	return $cache[ $key ];
}

/**
 * Uncached resolver implementation.
 *
 * @param int|null $post_id          Optional post ID.
 * @param bool     $ignore_overrides Skip per-post OG meta.
 * @return array<string, mixed>
 */
function theme_social_meta_resolve( $post_id, $ignore_overrides ) {
	if ( $post_id ) {
		$post = get_post( (int) $post_id );
		if ( $post instanceof WP_Post ) {
			return theme_social_meta_resolve_post( $post, $ignore_overrides );
		}

		return theme_social_meta_empty_data();
	}

	if ( is_search() || is_404() ) {
		$data            = theme_social_meta_empty_data();
		$data['context'] = is_404() ? '404' : 'search';
		return $data;
	}

	// Thoughts blog index: use the page_for_posts page so its title/overrides apply.
	if ( is_home() && ! is_front_page() ) {
		$posts_page_id = (int) get_option( 'page_for_posts' );
		if ( $posts_page_id > 0 ) {
			$post = get_post( $posts_page_id );
			if ( $post instanceof WP_Post ) {
				$data              = theme_social_meta_resolve_post( $post, $ignore_overrides );
				$data['type']      = 'website';
				$data['url']       = (string) get_permalink( $posts_page_id );
				$data['context']   = 'home';
				$data['published'] = '';
				$data['modified']  = '';
				$data['tags']      = array();
				return $data;
			}
		}

		$data                = theme_social_meta_empty_data();
		$data['title']       = theme_social_meta_first_nonempty( array( wp_get_document_title(), theme_social_meta_site_name() ) );
		$data['description'] = theme_social_meta_default_description();
		$data['image']       = theme_social_meta_default_image();
		$data['type']        = 'website';
		$data['url']         = home_url( '/' );
		$data['context']     = 'home';
		$data['full']        = true;
		return $data;
	}

	// Default posts-on-front homepage (not a singular page).
	if ( is_front_page() && is_home() ) {
		$data                = theme_social_meta_empty_data();
		$data['title']       = theme_social_meta_first_nonempty( array( wp_get_document_title(), theme_social_meta_site_name() ) );
		$data['description'] = theme_social_meta_default_description();
		$data['image']       = theme_social_meta_default_image();
		$data['type']        = 'website';
		$data['url']         = home_url( '/' );
		$data['context']     = 'home';
		$data['full']        = true;
		return $data;
	}

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( ! $post instanceof WP_Post ) {
			$post = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		}
		if ( $post instanceof WP_Post ) {
			return theme_social_meta_resolve_post( $post, $ignore_overrides );
		}
	}

	if ( is_category() || is_tag() || is_tax() ) {
		$term                = get_queried_object();
		$title               = '';
		$description         = '';
		$url                 = '';
		if ( $term && ! is_wp_error( $term ) && isset( $term->name ) ) {
			$title       = $term->name;
			$description = isset( $term->description ) ? wp_strip_all_tags( (string) $term->description ) : '';
			$link        = get_term_link( $term );
			if ( ! is_wp_error( $link ) ) {
				$url = (string) $link;
			}
		}

		$data                = theme_social_meta_empty_data();
		$data['title']       = theme_social_meta_first_nonempty( array( $title, wp_get_document_title(), theme_social_meta_site_name() ) );
		$data['description'] = theme_social_meta_first_nonempty( array( $description, theme_social_meta_default_description() ) );
		$data['image']       = theme_social_meta_default_image();
		$data['type']        = 'website';
		$data['url']         = $url;
		$data['context']     = 'archive';
		$data['full']        = true;
		return $data;
	}

	if ( is_author() ) {
		$author              = get_queried_object();
		$title               = '';
		$description         = '';
		$url                 = '';
		if ( $author && isset( $author->ID ) ) {
			$title       = isset( $author->display_name ) ? $author->display_name : '';
			$description = isset( $author->description ) ? wp_strip_all_tags( (string) $author->description ) : '';
			$url         = (string) get_author_posts_url( $author->ID );
		}

		$data                = theme_social_meta_empty_data();
		$data['title']       = theme_social_meta_first_nonempty( array( $title, wp_get_document_title(), theme_social_meta_site_name() ) );
		$data['description'] = theme_social_meta_first_nonempty( array( $description, theme_social_meta_default_description() ) );
		$data['image']       = theme_social_meta_default_image();
		$data['type']        = 'website';
		$data['url']         = $url;
		$data['context']     = 'archive';
		$data['full']        = true;
		return $data;
	}

	if ( is_post_type_archive() ) {
		$post_type           = get_query_var( 'post_type' );
		$archive_link        = get_post_type_archive_link( $post_type );
		$data                = theme_social_meta_empty_data();
		$data['title']       = theme_social_meta_first_nonempty( array( wp_get_document_title(), theme_social_meta_site_name() ) );
		$data['description'] = theme_social_meta_default_description();
		$data['image']       = theme_social_meta_default_image();
		$data['type']        = 'website';
		$data['url']         = $archive_link ? (string) $archive_link : '';
		$data['context']     = 'archive';
		$data['full']        = true;
		return $data;
	}

	if ( is_archive() ) {
		$data                = theme_social_meta_empty_data();
		$data['title']       = theme_social_meta_first_nonempty( array( wp_get_document_title(), theme_social_meta_site_name() ) );
		$data['description'] = theme_social_meta_default_description();
		$data['image']       = theme_social_meta_default_image();
		$data['type']        = 'website';
		$data['context']     = 'archive';
		$data['full']        = true;
		return $data;
	}

	return theme_social_meta_empty_data();
}

/**
 * Warning copy when an image is smaller than 1200×630 or not ~1.91:1.
 *
 * @param int $width  Pixel width.
 * @param int $height Pixel height.
 * @return string
 */
function theme_social_meta_image_size_warning( $width, $height ) {
	$width  = (int) $width;
	$height = (int) $height;
	if ( $width <= 0 || $height <= 0 ) {
		return '';
	}

	$messages = array();
	if ( $width < 1200 || $height < 630 ) {
		$messages[] = 'Smaller than 1200×630';
	}
	$ratio = $width / $height;
	if ( $ratio < 1.8 || $ratio > 2.0 ) {
		$messages[] = 'Aspect ratio is not ~1.91:1';
	}

	return implode( '. ', $messages );
}

/**
 * Print a single meta tag, skipping empty values.
 *
 * @param string     $attr  `property` or `name`.
 * @param string     $name  Meta key.
 * @param string|int $value Content.
 */
function theme_social_meta_tag( $attr, $name, $value ) {
	if ( $value === '' || $value === null ) {
		return;
	}

	$url_names = array(
		'og:url',
		'og:image',
		'og:image:secure_url',
		'twitter:image',
		'article:author',
	);

	$content = in_array( $name, $url_names, true )
		? esc_url( (string) $value )
		: esc_attr( (string) $value );

	if ( $content === '' ) {
		return;
	}

	echo '<meta ' . esc_attr( $attr ) . '="' . esc_attr( $name ) . '" content="' . $content . '">' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Image alt used on OG/Twitter tags (attachment alt, then share title).
 *
 * @param array<string, mixed>|null $image Image payload.
 * @param string                    $title Fallback title.
 * @return string
 */
function theme_social_meta_image_alt( $image, $title ) {
	if ( is_array( $image ) && ! empty( $image['alt'] ) ) {
		return (string) $image['alt'];
	}

	return (string) $title;
}

/**
 * <meta name="description"> from the shared resolver.
 */
function theme_social_meta_description() {
	if ( ! theme_social_meta_is_enabled() ) {
		return;
	}

	$data = theme_social_meta_get_data();
	if ( empty( $data['full'] ) ) {
		return;
	}

	theme_social_meta_tag( 'name', 'description', $data['description'] );
}
add_action( 'wp_head', 'theme_social_meta_description', 1 );

/**
 * Open Graph tags.
 */
function theme_social_meta_open_graph() {
	if ( ! theme_social_meta_is_enabled() ) {
		return;
	}

	$data = theme_social_meta_get_data();

	theme_social_meta_tag( 'property', 'og:site_name', $data['site_name'] );
	theme_social_meta_tag( 'property', 'og:locale', 'en_US' );

	if ( empty( $data['full'] ) ) {
		return;
	}

	theme_social_meta_tag( 'property', 'og:type', $data['type'] );
	theme_social_meta_tag( 'property', 'og:title', $data['title'] );
	theme_social_meta_tag( 'property', 'og:description', $data['description'] );
	theme_social_meta_tag( 'property', 'og:url', $data['url'] );

	$image = $data['image'];
	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		theme_social_meta_tag( 'property', 'og:image', $image['url'] );
		theme_social_meta_tag( 'property', 'og:image:secure_url', $image['url'] );
		if ( ! empty( $image['width'] ) ) {
			theme_social_meta_tag( 'property', 'og:image:width', (string) $image['width'] );
		}
		if ( ! empty( $image['height'] ) ) {
			theme_social_meta_tag( 'property', 'og:image:height', (string) $image['height'] );
		}
		theme_social_meta_tag( 'property', 'og:image:alt', theme_social_meta_image_alt( $image, $data['title'] ) );
		if ( ! empty( $image['mime'] ) ) {
			theme_social_meta_tag( 'property', 'og:image:type', $image['mime'] );
		}
	}

	if ( $data['type'] === 'article' ) {
		theme_social_meta_tag( 'property', 'article:published_time', $data['published'] );
		theme_social_meta_tag( 'property', 'article:modified_time', $data['modified'] );

		$options = theme_seo_get_options();
		if ( ! empty( $options['facebook_url'] ) ) {
			theme_social_meta_tag( 'property', 'article:author', $options['facebook_url'] );
		}

		if ( ! empty( $data['tags'] ) && is_array( $data['tags'] ) ) {
			foreach ( $data['tags'] as $tag ) {
				theme_social_meta_tag( 'property', 'article:tag', $tag );
			}
		}
	}
}
add_action( 'wp_head', 'theme_social_meta_open_graph', 5 );

/**
 * Twitter Card tags. Card / site / creator behaviour matches the previous emitters.
 */
function theme_social_meta_twitter() {
	if ( ! theme_social_meta_is_enabled() ) {
		return;
	}

	$data    = theme_social_meta_get_data();
	$options = theme_seo_get_options();

	theme_social_meta_tag( 'name', 'twitter:card', 'summary_large_image' );

	if ( ! empty( $options['twitter_handle'] ) ) {
		$handle = ltrim( (string) $options['twitter_handle'], '@' );
		theme_social_meta_tag( 'name', 'twitter:site', '@' . $handle );
		theme_social_meta_tag( 'name', 'twitter:creator', '@' . $handle );
	}

	if ( empty( $data['full'] ) ) {
		return;
	}

	theme_social_meta_tag( 'name', 'twitter:title', $data['title'] );
	theme_social_meta_tag( 'name', 'twitter:description', $data['description'] );

	$image = $data['image'];
	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		theme_social_meta_tag( 'name', 'twitter:image', $image['url'] );
		theme_social_meta_tag( 'name', 'twitter:image:alt', theme_social_meta_image_alt( $image, $data['title'] ) );
	}
}
add_action( 'wp_head', 'theme_social_meta_twitter', 5 );
