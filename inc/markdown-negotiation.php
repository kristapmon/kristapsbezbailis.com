<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Markdown content negotiation.
 *
 * When a request includes `Accept: text/markdown` (and prefers it over HTML),
 * short-circuit the normal WordPress template load and emit a markdown
 * representation of the current query with `Content-Type: text/markdown`.
 *
 * See: https://developers.cloudflare.com/fundamentals/reference/markdown-for-agents/
 *      https://isitagentready.com/.well-known/agent-skills/markdown-negotiation/SKILL.md
 */

add_action( 'template_redirect', 'theme_handle_markdown_negotiation', 1 );

function theme_handle_markdown_negotiation() {
	if ( ! theme_wants_markdown() ) {
		return;
	}

	if ( is_feed() || is_embed() || is_robots() || is_trackback() ) {
		return;
	}

	if ( post_password_required() ) {
		return;
	}

	if ( is_preview() ) {
		return;
	}

	if ( is_404() ) {
		theme_emit_markdown( theme_render_markdown_404() );
		return;
	}

	if ( is_front_page() && ! is_home() ) {
		theme_emit_markdown( theme_render_markdown_front_page() );
		return;
	}

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			if ( is_page() ) {
				$template = get_page_template_slug( $post->ID );
				if ( $template === 'template-projects.php' ) {
					theme_emit_markdown( theme_render_markdown_projects_listing( $post ) );
					return;
				}
				if ( $template === 'template-notes.php' ) {
					theme_emit_markdown( theme_render_markdown_notes_listing( $post ) );
					return;
				}
			}
			theme_emit_markdown( theme_render_markdown_singular( $post ) );
			return;
		}
	}

	if ( is_home() ) {
		theme_emit_markdown( theme_render_markdown_blog_index() );
		return;
	}

	if ( is_post_type_archive() || is_category() || is_tag() || is_tax() || is_author() || is_date() || is_search() ) {
		theme_emit_markdown( theme_render_markdown_archive() );
		return;
	}
}

/**
 * Returns true when the request's Accept header explicitly asks for
 * `text/markdown` with a q-value >= that of `text/html`.
 */
function theme_wants_markdown() {
	if ( is_admin() ) { return false; }
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) { return false; }
	if ( defined( 'DOING_CRON' ) && DOING_CRON ) { return false; }
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) { return false; }
	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) { return false; }

	if ( empty( $_SERVER['HTTP_ACCEPT'] ) ) {
		return false;
	}

	$accept = strtolower( (string) $_SERVER['HTTP_ACCEPT'] );
	return theme_accept_prefers_markdown( $accept );
}

function theme_accept_prefers_markdown( $accept ) {
	$md_q   = null;
	$html_q = null;

	$parts = explode( ',', $accept );
	foreach ( $parts as $part ) {
		$part = trim( $part );
		if ( $part === '' ) { continue; }

		$segments = explode( ';', $part );
		$type     = trim( array_shift( $segments ) );
		$q        = 1.0;
		foreach ( $segments as $segment ) {
			$segment = trim( $segment );
			if ( stripos( $segment, 'q=' ) === 0 ) {
				$q = (float) substr( $segment, 2 );
			}
		}

		if ( $type === 'text/markdown' ) {
			$md_q = max( (float) ( $md_q ?? 0 ), $q );
		} elseif ( $type === 'text/html' ) {
			$html_q = max( (float) ( $html_q ?? 0 ), $q );
		}
	}

	if ( $md_q === null || $md_q <= 0 ) {
		return false;
	}
	if ( $html_q === null ) {
		return true;
	}
	return $md_q >= $html_q;
}

function theme_emit_markdown( $markdown ) {
	$markdown = (string) $markdown;
	if ( $markdown === '' ) {
		$markdown = "# " . get_bloginfo( 'name' ) . "\n";
	}

	$tokens = (int) ceil( mb_strlen( $markdown ) / 4 );

	status_header( 200 );
	header( 'Content-Type: text/markdown; charset=utf-8' );
	header( 'X-Markdown-Tokens: ' . $tokens );
	header( 'Vary: Accept' );
	nocache_headers();

	echo $markdown;
	exit;
}

/* ---------------------------------------------------------------------------
 * Renderers
 * ------------------------------------------------------------------------- */

function theme_render_markdown_front_page() {
	$post = get_queried_object();
	$title = $post instanceof WP_Post ? get_the_title( $post ) : get_bloginfo( 'name' );

	$out   = '# ' . theme_md_text( $title ) . "\n\n";
	$tag   = get_bloginfo( 'description' );
	if ( $tag ) {
		$out .= '> ' . theme_md_text( $tag ) . "\n\n";
	}

	if ( $post instanceof WP_Post ) {
		$html = apply_filters( 'the_content', $post->post_content );
		$body = Theme_Html_To_Markdown::convert( $html );
		if ( $body !== '' ) {
			$out .= $body . "\n\n";
		}
		$out .= '---' . "\n\n";
		$out .= 'Canonical: ' . theme_md_link_inline( get_permalink( $post ) ) . "\n";
	} else {
		$out .= 'Canonical: ' . theme_md_link_inline( home_url( '/' ) ) . "\n";
	}
	return $out;
}

function theme_render_markdown_singular( WP_Post $post ) {
	$title = get_the_title( $post );

	$out = '# ' . theme_md_text( $title ) . "\n\n";

	$meta_line = theme_render_markdown_singular_meta( $post );
	if ( $meta_line !== '' ) {
		$out .= $meta_line . "\n\n";
	}

	if ( has_excerpt( $post ) ) {
		$excerpt = wp_strip_all_tags( get_the_excerpt( $post ) );
		if ( $excerpt !== '' ) {
			$out .= '> ' . theme_md_text( $excerpt ) . "\n\n";
		}
	}

	if ( has_post_thumbnail( $post ) ) {
		$img_id  = get_post_thumbnail_id( $post );
		$img_src = wp_get_attachment_image_url( $img_id, 'full' );
		$img_alt = (string) get_post_meta( $img_id, '_wp_attachment_image_alt', true );
		if ( $img_src ) {
			$out .= '![' . theme_md_text( $img_alt ) . '](' . $img_src . ")\n\n";
		}
	}

	if ( $post->post_type === 'projects' ) {
		$project_url = get_post_meta( $post->ID, '_project_url', true );
		if ( $project_url ) {
			$out .= '[View live project](' . $project_url . ")\n\n";
		}
	}

	$html = apply_filters( 'the_content', $post->post_content );
	$body = Theme_Html_To_Markdown::convert( $html );
	if ( $body !== '' ) {
		$out .= $body . "\n\n";
	}

	$tags = get_the_tags( $post->ID );
	if ( $tags && ! is_wp_error( $tags ) ) {
		$labels = array();
		foreach ( $tags as $tag ) {
			$labels[] = '[' . theme_md_text( $tag->name ) . '](' . get_tag_link( $tag->term_id ) . ')';
		}
		$out .= 'Tags: ' . implode( ', ', $labels ) . "\n\n";
	}

	$out .= '---' . "\n\n";
	$out .= 'Canonical: ' . theme_md_link_inline( get_permalink( $post ) ) . "\n";

	return $out;
}

function theme_render_markdown_singular_meta( WP_Post $post ) {
	$parts = array();

	if ( in_array( $post->post_type, array( 'post', 'blog' ), true ) ) {
		$parts[] = get_the_date( 'jS F Y', $post );
		if ( function_exists( 'get_reading_time' ) ) {
			$parts[] = get_reading_time( $post->ID );
		}
	}

	if ( $post->post_type === 'notes' ) {
		$terms = get_the_terms( $post->ID, 'category' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$parts[] = 'Category: ' . theme_md_text( $terms[0]->name );
		}
	}

	if ( ! $parts ) {
		return '';
	}

	return '*' . implode( ' · ', array_map( 'theme_md_text', $parts ) ) . '*';
}

function theme_render_markdown_blog_index() {
	$blog_page_id = (int) get_option( 'page_for_posts' );
	$title        = $blog_page_id ? get_the_title( $blog_page_id ) : 'Blog';

	$out = '# ' . theme_md_text( $title ) . "\n\n";

	if ( $blog_page_id ) {
		$blog_page = get_post( $blog_page_id );
		if ( $blog_page && trim( $blog_page->post_content ) !== '' ) {
			$intro = Theme_Html_To_Markdown::convert( apply_filters( 'the_content', $blog_page->post_content ) );
			if ( $intro !== '' ) {
				$out .= $intro . "\n\n";
			}
		}
	}

	global $wp_query;
	$out .= theme_render_markdown_post_list( $wp_query->posts );

	$out .= "\n---\n\n";
	$out .= 'Canonical: ' . theme_md_link_inline( $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/' ) ) . "\n";

	return $out;
}

function theme_render_markdown_projects_listing( WP_Post $page ) {
	$out  = '# ' . theme_md_text( get_the_title( $page ) ) . "\n\n";

	if ( trim( $page->post_content ) !== '' ) {
		$intro = Theme_Html_To_Markdown::convert( apply_filters( 'the_content', $page->post_content ) );
		if ( $intro !== '' ) {
			$out .= $intro . "\n\n";
		}
	}

	$projects = get_posts( array(
		'post_type'      => 'projects',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'suppress_filters' => false,
	) );

	if ( ! $projects ) {
		$out .= "_No projects yet._\n";
	} else {
		foreach ( $projects as $project ) {
			$project_url = get_post_meta( $project->ID, '_project_url', true );
			$permalink   = get_permalink( $project );
			$has_content = trim( $project->post_content ) !== '';
			$primary     = $project_url ? $project_url : ( $has_content ? $permalink : '' );

			$title = theme_md_text( get_the_title( $project ) );
			$out  .= '## ' . ( $primary ? '[' . $title . '](' . $primary . ')' : $title ) . "\n\n";

			if ( has_excerpt( $project ) ) {
				$excerpt = wp_strip_all_tags( get_the_excerpt( $project ) );
				if ( $excerpt !== '' ) {
					$out .= theme_md_text( $excerpt ) . "\n\n";
				}
			}

			$links = array();
			if ( $has_content ) {
				$links[] = '[More info](' . $permalink . ')';
			}
			if ( $project_url ) {
				$links[] = '[Visit project](' . $project_url . ')';
			}
			if ( $links ) {
				$out .= implode( ' · ', $links ) . "\n\n";
			}
		}
	}

	$out .= "---\n\n";
	$out .= 'Canonical: ' . theme_md_link_inline( get_permalink( $page ) ) . "\n";
	return $out;
}

function theme_render_markdown_notes_listing( WP_Post $page ) {
	$out = '# ' . theme_md_text( get_the_title( $page ) ) . "\n\n";

	if ( trim( $page->post_content ) !== '' ) {
		$intro = Theme_Html_To_Markdown::convert( apply_filters( 'the_content', $page->post_content ) );
		if ( $intro !== '' ) {
			$out .= $intro . "\n\n";
		}
	}

	$notes = get_posts( array(
		'post_type'      => 'notes',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'suppress_filters' => false,
	) );

	if ( ! $notes ) {
		$out .= "_No notes yet._\n";
	} else {
		foreach ( $notes as $note ) {
			$title = theme_md_text( get_the_title( $note ) );
			$out  .= '## [' . $title . '](' . get_permalink( $note ) . ")\n\n";

			$terms = get_the_terms( $note->ID, 'category' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$out .= '*' . theme_md_text( $terms[0]->name ) . "*\n\n";
			}

			if ( has_excerpt( $note ) ) {
				$excerpt = wp_strip_all_tags( get_the_excerpt( $note ) );
				if ( $excerpt !== '' ) {
					$out .= theme_md_text( $excerpt ) . "\n\n";
				}
			}
		}
	}

	$out .= "---\n\n";
	$out .= 'Canonical: ' . theme_md_link_inline( get_permalink( $page ) ) . "\n";
	return $out;
}

function theme_render_markdown_archive() {
	$object = get_queried_object();

	if ( is_search() ) {
		$title = sprintf( 'Search results for "%s"', get_search_query() );
	} elseif ( $object instanceof WP_Term ) {
		$title = single_term_title( '', false ) ?: $object->name;
	} elseif ( $object instanceof WP_Post_Type ) {
		$title = $object->labels->name;
	} elseif ( $object instanceof WP_User ) {
		$title = 'Author: ' . $object->display_name;
	} elseif ( is_date() ) {
		$title = wp_get_document_title();
	} else {
		$title = wp_get_document_title();
	}

	$out = '# ' . theme_md_text( $title ) . "\n\n";

	if ( $object instanceof WP_Term && $object->description ) {
		$out .= theme_md_text( wp_strip_all_tags( $object->description ) ) . "\n\n";
	}

	global $wp_query;
	$out .= theme_render_markdown_post_list( $wp_query->posts );

	$out .= "---\n\n";
	$canonical = '';
	if ( $object instanceof WP_Term ) {
		$canonical = get_term_link( $object );
	} elseif ( $object instanceof WP_Post_Type ) {
		$canonical = get_post_type_archive_link( $object->name );
	} elseif ( $object instanceof WP_User ) {
		$canonical = get_author_posts_url( $object->ID );
	}
	if ( ! $canonical || is_wp_error( $canonical ) ) {
		$canonical = home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
	}
	$out .= 'Canonical: ' . theme_md_link_inline( $canonical ) . "\n";

	return $out;
}

function theme_render_markdown_404() {
	status_header( 404 );
	$out  = "# 404 Not Found\n\n";
	$out .= "The page you requested could not be found.\n\n";
	$out .= '[Return home](' . home_url( '/' ) . ")\n";
	return $out;
}

/* ---------------------------------------------------------------------------
 * Helpers
 * ------------------------------------------------------------------------- */

function theme_render_markdown_post_list( $posts ) {
	if ( empty( $posts ) ) {
		return "_No posts found._\n\n";
	}

	$out = '';
	foreach ( $posts as $post ) {
		$title     = theme_md_text( get_the_title( $post ) );
		$permalink = get_permalink( $post );
		$out      .= '## [' . $title . '](' . $permalink . ")\n\n";

		$parts = array();
		$date  = get_the_date( 'jS F Y', $post );
		if ( $date ) { $parts[] = $date; }
		if ( in_array( $post->post_type, array( 'post', 'blog' ), true ) && function_exists( 'get_reading_time' ) ) {
			$reading = get_reading_time( $post->ID );
			if ( $reading ) { $parts[] = $reading; }
		}
		if ( $parts ) {
			$out .= '*' . theme_md_text( implode( ' · ', $parts ) ) . "*\n\n";
		}

		if ( has_excerpt( $post ) ) {
			$excerpt = wp_strip_all_tags( get_the_excerpt( $post ) );
		} else {
			$excerpt = wp_trim_words( wp_strip_all_tags( $post->post_content ), 40, '...' );
		}
		if ( $excerpt !== '' ) {
			$out .= theme_md_text( $excerpt ) . "\n\n";
		}
	}
	return $out;
}

function theme_md_text( $text ) {
	$text = (string) $text;
	$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$text = preg_replace( "/\s+/", ' ', $text );
	return trim( $text );
}

function theme_md_link_inline( $url ) {
	$url = (string) $url;
	return '<' . $url . '>';
}
