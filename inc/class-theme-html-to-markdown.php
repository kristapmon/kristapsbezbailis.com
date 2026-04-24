<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Minimal HTML -> Markdown converter used by the markdown content-negotiation
 * layer. Walks a DOM tree with DOMDocument and emits CommonMark-ish output
 * covering the tags WordPress core blocks produce.
 *
 * No external dependencies. Unknown wrappers (e.g. <div class="wp-block-*">)
 * are transparent: their children are rendered, the wrapper itself is dropped.
 */
class Theme_Html_To_Markdown {

	/** @var int */
	private $list_depth = 0;

	/** @var array<int,array{type:string,index:int}> */
	private $list_stack = array();

	public static function convert( $html ) {
		$instance = new self();
		return $instance->run( (string) $html );
	}

	private function run( $html ) {
		$html = trim( $html );
		if ( $html === '' ) {
			return '';
		}

		$previous = libxml_use_internal_errors( true );
		$dom      = new DOMDocument( '1.0', 'UTF-8' );

		$wrapped = '<?xml encoding="UTF-8"><div id="__theme_md_root__">' . $html . '</div>';
		$dom->loadHTML( $wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );

		libxml_clear_errors();
		libxml_use_internal_errors( $previous );

		$root = $dom->getElementById( '__theme_md_root__' );
		if ( ! $root ) {
			return trim( wp_strip_all_tags( $html ) );
		}

		$out = $this->walk_children( $root );
		return $this->tidy( $out );
	}

	private function walk_children( DOMNode $node ) {
		$out = '';
		foreach ( $node->childNodes as $child ) {
			$out .= $this->walk( $child );
		}
		return $out;
	}

	private function walk( DOMNode $node ) {
		if ( $node->nodeType === XML_TEXT_NODE ) {
			return $this->escape_text( $node->nodeValue );
		}

		if ( $node->nodeType !== XML_ELEMENT_NODE ) {
			return '';
		}

		$tag = strtolower( $node->nodeName );

		switch ( $tag ) {
			case 'h1': case 'h2': case 'h3':
			case 'h4': case 'h5': case 'h6':
				$level = (int) substr( $tag, 1 );
				return "\n\n" . str_repeat( '#', $level ) . ' ' . $this->inline( $node ) . "\n\n";

			case 'p':
				$inner = trim( $this->walk_children( $node ) );
				if ( $inner === '' ) {
					return '';
				}
				return "\n\n" . $inner . "\n\n";

			case 'br':
				return "  \n";

			case 'hr':
				return "\n\n---\n\n";

			case 'strong': case 'b':
				$inner = trim( $this->walk_children( $node ) );
				return $inner === '' ? '' : '**' . $inner . '**';

			case 'em': case 'i':
				$inner = trim( $this->walk_children( $node ) );
				return $inner === '' ? '' : '*' . $inner . '*';

			case 'del': case 's': case 'strike':
				$inner = trim( $this->walk_children( $node ) );
				return $inner === '' ? '' : '~~' . $inner . '~~';

			case 'code':
				if ( $node->parentNode && strtolower( $node->parentNode->nodeName ) === 'pre' ) {
					return $node->textContent;
				}
				$inner = $node->textContent;
				return $inner === '' ? '' : '`' . $inner . '`';

			case 'pre':
				$code = $node->textContent;
				return "\n\n```\n" . rtrim( $code, "\n" ) . "\n```\n\n";

			case 'a':
				$href = $this->attr( $node, 'href' );
				$text = trim( $this->walk_children( $node ) );
				if ( $text === '' ) {
					$text = $href;
				}
				if ( $href === '' || stripos( $href, 'javascript:' ) === 0 ) {
					return $text;
				}
				return '[' . $text . '](' . $href . ')';

			case 'img':
				$src = $this->attr( $node, 'src' );
				if ( $src === '' ) {
					$src = $this->attr( $node, 'data-src' );
				}
				if ( $src === '' ) {
					return '';
				}
				$alt = $this->attr( $node, 'alt' );
				return '![' . $alt . '](' . $src . ')';

			case 'iframe':
				$src = $this->attr( $node, 'src' );
				if ( $src === '' ) {
					return '';
				}
				return "\n\n[Embedded content: " . $src . '](' . $src . ")\n\n";

			case 'blockquote':
				$inner = trim( $this->walk_children( $node ) );
				if ( $inner === '' ) {
					return '';
				}
				$lines = preg_split( "/\r\n|\n|\r/", $inner );
				$quoted = array();
				foreach ( $lines as $line ) {
					$quoted[] = '> ' . $line;
				}
				return "\n\n" . implode( "\n", $quoted ) . "\n\n";

			case 'ul':
				return $this->render_list( $node, 'ul' );

			case 'ol':
				return $this->render_list( $node, 'ol' );

			case 'li':
				return $this->walk_children( $node );

			case 'figure':
				$caption = '';
				foreach ( $node->childNodes as $child ) {
					if ( $child->nodeType === XML_ELEMENT_NODE && strtolower( $child->nodeName ) === 'figcaption' ) {
						$caption = trim( $this->walk_children( $child ) );
					}
				}
				$body = '';
				foreach ( $node->childNodes as $child ) {
					if ( $child->nodeType === XML_ELEMENT_NODE && strtolower( $child->nodeName ) === 'figcaption' ) {
						continue;
					}
					$body .= $this->walk( $child );
				}
				$body = trim( $body );
				if ( $body === '' && $caption === '' ) {
					return '';
				}
				$out = "\n\n" . $body;
				if ( $caption !== '' ) {
					$out .= "\n\n*" . $caption . '*';
				}
				return $out . "\n\n";

			case 'figcaption':
				return '';

			case 'table':
				return $this->render_table( $node );

			case 'script': case 'style': case 'noscript':
				return '';

			default:
				return $this->walk_children( $node );
		}
	}

	private function render_list( DOMNode $node, $type ) {
		$this->list_stack[] = array( 'type' => $type, 'index' => 1 );
		$this->list_depth++;

		$indent = str_repeat( '  ', $this->list_depth - 1 );
		$out    = "\n";

		foreach ( $node->childNodes as $child ) {
			if ( $child->nodeType !== XML_ELEMENT_NODE || strtolower( $child->nodeName ) !== 'li' ) {
				continue;
			}

			$frame  = &$this->list_stack[ count( $this->list_stack ) - 1 ];
			$marker = $type === 'ol' ? ( $frame['index'] . '. ' ) : '- ';
			$frame['index']++;

			$item = $this->walk_children( $child );
			$item = $this->normalize_list_item( $item, $indent );

			$out .= $indent . $marker . ltrim( $item ) . "\n";
		}

		array_pop( $this->list_stack );
		$this->list_depth--;

		return "\n" . $out . "\n";
	}

	private function normalize_list_item( $text, $indent ) {
		$text  = trim( $text );
		$text  = preg_replace( "/\n{2,}/", "\n", $text );
		$lines = preg_split( "/\r\n|\n|\r/", $text );
		$out   = array();
		$first = true;
		foreach ( $lines as $line ) {
			if ( $first ) {
				$out[] = $line;
				$first = false;
				continue;
			}
			if ( trim( $line ) === '' ) {
				continue;
			}
			$out[] = $indent . '  ' . $line;
		}
		return implode( "\n", $out );
	}

	private function render_table( DOMNode $node ) {
		$rows = array();
		foreach ( $node->getElementsByTagName( 'tr' ) as $tr ) {
			$row = array();
			foreach ( $tr->childNodes as $cell ) {
				if ( $cell->nodeType !== XML_ELEMENT_NODE ) { continue; }
				$name = strtolower( $cell->nodeName );
				if ( $name !== 'td' && $name !== 'th' ) { continue; }
				$row[] = str_replace( '|', '\\|', trim( $this->inline( $cell ) ) );
			}
			if ( $row ) {
				$rows[] = $row;
			}
		}
		if ( ! $rows ) {
			return '';
		}
		$header = array_shift( $rows );
		$out    = "\n\n| " . implode( ' | ', $header ) . " |\n";
		$out   .= '| ' . implode( ' | ', array_fill( 0, count( $header ), '---' ) ) . " |\n";
		foreach ( $rows as $row ) {
			while ( count( $row ) < count( $header ) ) { $row[] = ''; }
			$out .= '| ' . implode( ' | ', $row ) . " |\n";
		}
		return $out . "\n";
	}

	private function inline( DOMNode $node ) {
		$text = $this->walk_children( $node );
		$text = preg_replace( "/\s*\n\s*/", ' ', $text );
		return trim( $text );
	}

	private function attr( DOMNode $node, $name ) {
		if ( ! $node instanceof DOMElement ) { return ''; }
		return trim( $node->getAttribute( $name ) );
	}

	private function escape_text( $text ) {
		$text = str_replace( array( "\r\n", "\r" ), "\n", $text );
		$text = preg_replace( '/([\\\\`*_\[\]])/', '\\\\$1', $text );
		return $text;
	}

	private function tidy( $text ) {
		$text = preg_replace( "/[ \t]+\n/", "\n", $text );
		$text = preg_replace( "/\n{3,}/", "\n\n", $text );
		return trim( $text ) . "\n";
	}
}
