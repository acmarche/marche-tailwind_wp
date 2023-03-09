<?php

namespace AcMarche\MarcheTail\Inc;

use function add_action;
use function add_filter;

class AssetsLoad
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'main']);
        add_filter('script_loader_tag', fn($tag, $handle, $src) => $this->addDefer($tag, $handle, $src), 10, 3);
    }

    function main()
    {
        wp_enqueue_style(
            'main-marche-css',
            get_template_directory_uri().'/assets/marche.css',
        );

        wp_enqueue_script(
            'menuMobile-js',
            get_template_directory_uri().'/assets/js/alpine/menuMobile.js',
            [],
            false,
            false
        );

        wp_enqueue_script(
            'searchXl-js',
            get_template_directory_uri().'/assets/js/alpine/searchXl.js',
            [],
            false,
            false
        );

        wp_enqueue_script(
            'refreshOffres-js',
            get_template_directory_uri().'/assets/js/alpine/refreshOffres.js',
            [],
            false,
            false
        );

        wp_enqueue_script(
            'share-js',
            get_template_directory_uri().'/assets/js/alpine/share.js',
            [],
            false,
            false
        );

        wp_enqueue_script(
            'alpine-js',
            '//unpkg.com/alpinejs',
            [],
            false,
            false
        );
    }

    function marchebeLeaft()
    {
        wp_register_style(
            'visitmarche-leaflet-css',
            'https://unpkg.com/leaflet@latest/dist/leaflet.css',
            [],
            null
        );
        wp_register_script(
            'visitmarche-leaflet-js',
            'https://unpkg.com/leaflet@latest/dist/leaflet.js',
            [],
            null
        );
        wp_enqueue_script(
            'marchebe-kml',
            get_template_directory_uri().'/assets/js/L.KML.js',
            array(),
        );
    }

    function readSpeaker()
    {
        wp_enqueue_script(
            'marchebe-readspeaker',
            '//cdn1.readspeaker.com/script/11982/webReader/webReader.js?pids=wr',
            array(),
        );
        wp_enqueue_script(
            'marchebe-zoom',
            get_template_directory_uri().'/assets/js/utils/zoom.js',
            array(),
        );
    }

    function addDefer($tag, $handle, $src)
    {
        if (!in_array($handle, ['alpine-js', 'menuMobile-js', 'searchXl-js', 'refreshOffres-js', 'share-js','slider-js'])) {
            return $tag;
        }

        return '<script src="'.esc_url($src).'" defer></script>';
    }


    public static function enqueueLeaflet()
    {
        //todo test this
        //wp_add_inline_script();
        wp_enqueue_style('visitmarche-leaflet-css');
        wp_enqueue_script('visitmarche-leaflet-js');
    }

    public static function enqueueElevation()
    {
        wp_enqueue_style('visitmarche-leaflet-elevation-css');
        wp_enqueue_script('visitmarche-leaflet-ui-js');
        wp_enqueue_script('visitmarche-leaflet-elevation-js');
    }
}
