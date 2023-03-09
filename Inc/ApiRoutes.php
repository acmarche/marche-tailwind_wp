<?php
namespace AcMarche\MarcheTail\Inc;

use function add_action;
use function add_filter;
use function is_admin;

/**
 * Enregistrement des routes pour les api pour les composants react
 * Class Api
 * @package AcMarche\MarcheTail\Inc
 */
class ApiRoutes
{
    public function __construct()
    {
        if (!is_admin()) {
            $this->registerCateogry();
            $this->registerEvent();
            $this->registerSearch();
            $this->mapApi();
        }
        $this->registerBottin();

        add_filter('application_password_is_api_request', function ($api_request) {
            if (empty($api_request)) {
                return strpos($_SERVER['REQUEST_URI'], '/wp-json/') !== false;
            }

            return $api_request;
        }, 10, 1);
    }

    public function registerEvent()
    {
        add_action(
            'rest_api_init',
            function () {
                register_rest_route(
                    'ca/v1',
                    'events',
                    [
                        'methods' => 'GET',
                        'callback' => function () {
                            return ApiData::ca_events();
                        },
                    ]
                );
            }
        );
    }

    public function registerBottin()
    {
        add_action(
            'rest_api_init',
            function () {
                register_rest_route(
                    'ca/v1',
                    'bottinsocieteid',
                    [
                        'methods' => 'GET',
                        'callback' => function () {
                            return ApiData::ca_bottinSocieteId();
                        },
                        'permission_callback' => '__return_true',
                    ]
                );
            }
        );

        add_action(
            'rest_api_init',
            function () {
                register_rest_route(
                    'ca/v1',
                    'bottin/(?P<ficheId>.*+)',
                    [
                        'methods' => 'GET',
                        'callback' => function ($args) {
                            return ApiData::ca_bottin($args);
                        },
                        'permission_callback' => '__return_true',
                    ]
                );
            }
        );
    }

    public function registerCateogry()
    {
        add_action(
            'rest_api_init',
            function () {
                register_rest_route(
                    'jfs/v1',
                    'all/(?P<catParent>.*+)',
                    [
                        'methods' => 'GET',
                        'callback' => function ($args) {
                            return ApiData::findPosts($args);
                        },
                    ]
                );
            }
        );
    }

    public function registerSearch()
    {
        add_action(
            'rest_api_init',
            function () {
                register_rest_route(
                    'search',
                    'search/(?P<keyword>.*+)',
                    [
                        'methods' => 'GET',
                        'callback' => function ($args) {
                            return ApiData::search($args);
                        },
                    ]
                );
            }
        );

        add_action(
            'rest_api_init',
            function () {
                register_rest_route(
                    'search',
                    'suggest/(?P<keyword>.*+)',
                    [
                        'methods' => 'GET',
                        'callback' => function ($args) {
                            return ApiData::suggest($args);
                        },
                    ]
                );
            }
        );
    }

    function mapApi()
    {
        add_action(
            'rest_api_init',
            function () {
                register_rest_route(
                    'map',
                    'kml/(?P<keyword>.*+)',
                    [
                        'methods' => 'GET',
                        'callback' => function ($args) {
                            return ApiData::mapKml($args);
                        },
                    ]
                );
            }
        );

        add_action(
            'rest_api_init',
            function () {
                register_rest_route(
                    'map',
                    'filtres',
                    [
                        'methods' => 'GET',
                        'callback' => function () {
                            return ApiData::filtres();
                        },
                    ]
                );
            }
        );

        add_action(
            'rest_api_init',
            function () {
                register_rest_route(
                    'map',
                    'data/(?P<keyword>.*+)',
                    [
                        'methods' => 'GET',
                        'callback' => function ($args) {
                            return ApiData::mapData($args);
                        },

                    ]
                );
            }
        );
    }

    /**
     * Todo pour list/users !!
     */
    function secureApi()
    {
        add_filter(
            'rest_authentication_errors',
            function ($result) {
                // If a previous authentication check was applied,
                // pass that result along without modification.
                if (true === $result || is_wp_error($result)) {
                    return $result;
                }

                // No authentication has been performed yet.
                // Return an error if user is not logged in.
                if (!is_user_logged_in()) {
                    return new \WP_Error(
                        'rest_not_logged_in',
                        __('You are not currently logged in.'),
                        array('status' => 401)
                    );
                }

                // Our custom authentication check should have no effect
                // on logged-in requests
                return $result;
            }
        );
    }
}
