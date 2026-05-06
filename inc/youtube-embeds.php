<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Lightweight YouTube embeds for oEmbed URLs and the [youtube] shortcode.
 */

function theme_youtube_extract_id( $value ) {
	$value = trim( (string) $value );
	if ( $value === '' ) {
		return '';
	}

	if ( preg_match( '/^[A-Za-z0-9_-]{11}$/', $value ) ) {
		return $value;
	}

	$parts = wp_parse_url( $value );
	if ( empty( $parts['host'] ) ) {
		return '';
	}

	$host = strtolower( preg_replace( '/^www\./', '', $parts['host'] ) );
	$path = isset( $parts['path'] ) ? trim( $parts['path'], '/' ) : '';

	if ( in_array( $host, array( 'youtube.com', 'm.youtube.com', 'music.youtube.com', 'youtube-nocookie.com' ), true ) ) {
		if ( isset( $parts['query'] ) ) {
			parse_str( $parts['query'], $query );
			if ( ! empty( $query['v'] ) && preg_match( '/^[A-Za-z0-9_-]{11}$/', $query['v'] ) ) {
				return $query['v'];
			}
		}

		if ( preg_match( '~^(?:embed|v|shorts)/([A-Za-z0-9_-]{11})~', $path, $matches ) ) {
			return $matches[1];
		}
	}

	if ( $host === 'youtu.be' && preg_match( '~^([A-Za-z0-9_-]{11})~', $path, $matches ) ) {
		return $matches[1];
	}

	return '';
}

function theme_youtube_is_url( $url ) {
	return theme_youtube_extract_id( $url ) !== '';
}

function theme_youtube_parse_time_to_seconds( $time ) {
	$time = strtolower( trim( (string) $time ) );
	if ( $time === '' ) {
		return 0;
	}

	if ( is_numeric( $time ) ) {
		return max( 0, (int) $time );
	}

	$total = 0;
	if ( preg_match_all( '/(\d+)\s*([hms])/', $time, $matches, PREG_SET_ORDER ) ) {
		foreach ( $matches as $match ) {
			$value = (int) $match[1];
			if ( $match[2] === 'h' ) {
				$total += $value * HOUR_IN_SECONDS;
			} elseif ( $match[2] === 'm' ) {
				$total += $value * MINUTE_IN_SECONDS;
			} else {
				$total += $value;
			}
		}
	}

	return max( 0, $total );
}

function theme_youtube_extract_start( $url, $fallback = 0 ) {
	$start = max( 0, (int) $fallback );
	$parts = wp_parse_url( (string) $url );

	if ( ! empty( $parts['query'] ) ) {
		parse_str( $parts['query'], $query );
		if ( isset( $query['start'] ) ) {
			return theme_youtube_parse_time_to_seconds( $query['start'] );
		}
		if ( isset( $query['t'] ) ) {
			return theme_youtube_parse_time_to_seconds( $query['t'] );
		}
	}

	if ( ! empty( $parts['fragment'] ) ) {
		parse_str( $parts['fragment'], $fragment );
		if ( isset( $fragment['t'] ) ) {
			return theme_youtube_parse_time_to_seconds( $fragment['t'] );
		}
	}

	return $start;
}

function theme_youtube_ratio_padding( $ratio ) {
	$ratio = trim( (string) $ratio );

	if ( preg_match( '/^(\d+(?:\.\d+)?):(\d+(?:\.\d+)?)$/', $ratio, $matches ) ) {
		$width  = (float) $matches[1];
		$height = (float) $matches[2];

		if ( $width > 0 && $height > 0 ) {
			return ( $height / $width ) * 100;
		}
	}

	return 56.25;
}

function theme_youtube_sanitize_width( $width ) {
	$width = trim( (string) $width );

	if ( preg_match( '/^\d+(?:\.\d+)?(?:px|%|rem|em|vw|vh|vmin|vmax)$/', $width ) ) {
		return $width;
	}

	return '100%';
}

function theme_youtube_enqueue_assets() {
	wp_enqueue_script(
		'theme-youtube-embeds',
		get_template_directory_uri() . '/assets/js/youtube-embeds.js',
		array(),
		'1.0.0',
		true
	);
}

function theme_youtube_render_embed( $args ) {
	$defaults = array(
		'id'       => '',
		'url'      => '',
		'ratio'    => '16:9',
		'start'    => 0,
		'title'    => __( 'YouTube video', 'kristapsbezbailis' ),
		'width'    => '100%',
		'autoplay' => 'true',
	);
	$args = wp_parse_args( $args, $defaults );

	$video_id = theme_youtube_extract_id( $args['id'] );
	if ( $video_id === '' ) {
		$video_id = theme_youtube_extract_id( $args['url'] );
	}

	if ( $video_id === '' ) {
		return '';
	}

	$start = theme_youtube_extract_start( $args['url'], theme_youtube_parse_time_to_seconds( $args['start'] ) );
	$autoplay = strtolower( (string) $args['autoplay'] ) === 'false' ? '0' : '1';
	$title = sanitize_text_field( $args['title'] );
	$padding = theme_youtube_ratio_padding( $args['ratio'] );
	$width = theme_youtube_sanitize_width( $args['width'] );
	$thumb = sprintf( 'https://i.ytimg.com/vi/%s/hqdefault.jpg', rawurlencode( $video_id ) );

	theme_youtube_enqueue_assets();

	return sprintf(
		'<div class="theme-youtube-embed" data-youtube-id="%1$s" data-youtube-start="%2$d" data-youtube-autoplay="%3$s" data-youtube-title="%4$s" style="--theme-youtube-ratio:%5$.4f%%;max-width:%6$s" role="button" tabindex="0" aria-label="%7$s"><img src="%8$s" alt="%4$s" loading="lazy" decoding="async"><span class="theme-youtube-play" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M8 5v14l11-7z"></path></svg></span></div>',
		esc_attr( $video_id ),
		$start,
		esc_attr( $autoplay ),
		esc_attr( $title ),
		$padding,
		esc_attr( $width ),
		esc_attr( sprintf( __( 'Play %s', 'kristapsbezbailis' ), $title ) ),
		esc_url( $thumb )
	);
}

function theme_youtube_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'id'       => '',
			'url'      => '',
			'ratio'    => '16:9',
			'start'    => 0,
			'title'    => __( 'YouTube video', 'kristapsbezbailis' ),
			'width'    => '100%',
			'autoplay' => 'true',
		),
		$atts,
		'youtube'
	);

	$embed = theme_youtube_render_embed( $atts );
	if ( $embed === '' ) {
		return '<p class="theme-youtube-error">' . esc_html__( 'YouTube embed: invalid or missing video ID.', 'kristapsbezbailis' ) . '</p>';
	}

	return $embed;
}
add_shortcode( 'youtube', 'theme_youtube_shortcode' );

function theme_youtube_oembed_html( $html, $url, $attr, $post_id ) {
	if ( ! theme_youtube_is_url( $url ) ) {
		return $html;
	}

	$width = '100%';
	if ( ! empty( $attr['width'] ) && is_numeric( $attr['width'] ) ) {
		$width = absint( $attr['width'] ) . 'px';
	}

	$embed = theme_youtube_render_embed(
		array(
			'url'   => $url,
			'title' => __( 'YouTube video', 'kristapsbezbailis' ),
			'width' => $width,
		)
	);

	return $embed === '' ? $html : $embed;
}
add_filter( 'embed_oembed_html', 'theme_youtube_oembed_html', 10, 4 );
