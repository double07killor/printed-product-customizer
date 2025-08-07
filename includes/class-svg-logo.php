<?php
if (!defined('ABSPATH')) {
    exit;
}

class FPC_SVG_Logo {
    /**
     * Sanitize SVG content by stripping disallowed elements and attributes.
     */
    public static function sanitize_svg($svg_content) {
        if (empty($svg_content)) {
            return '';
        }
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadXML($svg_content, LIBXML_NONET);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $disallowed = ['script', 'foreignObject', 'iframe', 'embed', 'object', 'image', 'style', 'metadata'];
        foreach ($disallowed as $tag) {
            foreach ($xpath->query('//' . $tag) as $el) {
                $el->parentNode->removeChild($el);
            }
        }

        $allowed_tags  = ['svg', 'g', 'path', 'use'];
        $allowed_attrs = ['id', 'class', 'd', 'fill', 'viewBox', 'xmlns', 'width', 'height', 'x', 'y', 'xlink:href'];

        $all = $dom->getElementsByTagName('*');
        for ($i = $all->length - 1; $i >= 0; $i--) {
            $el = $all->item($i);
            if (!in_array($el->nodeName, $allowed_tags, true)) {
                $el->parentNode->removeChild($el);
                continue;
            }
            if ($el->hasAttributes()) {
                $remove_attrs = [];
                foreach ($el->attributes as $attr) {
                    $name = $attr->nodeName;
                    if (stripos($name, 'on') === 0 || !in_array($name, $allowed_attrs, true)) {
                        $remove_attrs[] = $name;
                    }
                }
                foreach ($remove_attrs as $ra) {
                    $el->removeAttribute($ra);
                }
            }
        }

        return $dom->saveXML();
    }

    /**
     * Parse SVG into layer definitions keyed by id or fill color.
     */
    public static function parse_svg_layers($svg_content) {
        $layers = [];
        if (empty($svg_content)) {
            return $layers;
        }
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadXML($svg_content, LIBXML_NONET);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $index = 1;
        foreach ($xpath->query('//*[local-name()="g" or local-name()="path"]') as $node) {
            $id = $node->getAttribute('id');
            if (!$id) {
                $fill = $node->getAttribute('fill');
                if ($fill) {
                    $id = sanitize_title($fill);
                } else {
                    $id = 'layer_' . $index;
                }
            }
            $layers[$id] = [
                'defaultGroup' => '',
                'editable'     => false,
            ];
            $index++;
        }
        return $layers;
    }

    /**
     * Provide available logos for product configuration.
     */
    public static function filter_available_logos($logos) {
        $stored = get_option('fpc_predefined_logos', []);
        foreach ($stored as $slug => $data) {
            $logos[$slug] = $data['label'] ?? $slug;
        }
        return $logos;
    }
}

// Register filter to expose predefined logos.
add_filter('fpc_available_logos', ['FPC_SVG_Logo', 'filter_available_logos']);
