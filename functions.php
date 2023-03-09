<?php

namespace AcMarche\MarcheTail;

use AcMarche\MarcheTail\Inc\AdminBar;
use AcMarche\MarcheTail\Inc\AdminPage;
use AcMarche\MarcheTail\Inc\ApiRoutes;
use AcMarche\MarcheTail\Inc\AssetsLoad;
use AcMarche\MarcheTail\Inc\Filter;
use AcMarche\MarcheTail\Inc\OpenGraph;
use AcMarche\MarcheTail\Inc\RouterMarche;
use AcMarche\MarcheTail\Inc\SecurityConfig;
use AcMarche\MarcheTail\Inc\Seo;
use AcMarche\MarcheTail\Inc\SetupTheme;
use AcMarche\MarcheTail\Inc\ShortCodes;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;

/**
 * Template sf
 */
if (WP_DEBUG === false) {
    HtmlErrorRenderer::setTemplate(get_template_directory().'/error500.php');
} else {
    Debug::enable();
}
/**
 * Initialisation du thème
 */
new SetupTheme();
/**
 * Chargement css, js
 */
new AssetsLoad();
/**
 * Un peu de sécurité
 */
new SecurityConfig();
/**
 * Enregistrement des routes api
 */
new ApiRoutes();
/**
 * Ajout de routage pour pivot
 */
new RouterMarche();
/**
 * Balises pour le référencement
 */
new Seo();
/**
 * Balises pour le social
 */
new OpenGraph();
/**
 * Gpx viewer
 */
new ShortCodes();
/**
 * Admin pages
 */
new AdminPage();
/**
 * Add buttons to admin bar
 */
new AdminBar();
/**
 * Add css to list
 */
New Filter();
