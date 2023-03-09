<?php


namespace AcMarche\MarcheTail\Inc;

use AcMarche\Bottin\Elasticsearch\Searcher;
use AcMarche\Bottin\Repository\BottinRepository;
use AcMarche\MarcheTail\Lib\Mailer;
use AcMarche\MarcheTail\Lib\Carto;
use AcMarche\MarcheTail\Lib\WpRepository;
use AcSort;
use Elastica\Exception\InvalidException;
use WP_Error;
use WP_HTTP_Response;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Enregistrement des routes pour les api pour les composants react
 * Class Api
 * @package AcMarche\MarcheTail\Inc
 */
class ApiData
{
    /**
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_HTTP_Response|WP_REST_Response
     */
    public static function findPosts(WP_REST_Request $request)
    {
        $catParent = $request->get_param('catParent');
        if ( ! $catParent) {
            return new WP_Error(500, 'missing param catParent');
        }

        $wpRepository = new WpRepository();
        $posts        = $wpRepository->getPostsAndFiches($catParent);

        $category_order = get_term_meta($catParent, 'acmarche_category_sort', true);
        if ($category_order == 'manual') {
            $posts = AcSort::getSortedItems($catParent, $posts);
        }

        return rest_ensure_response($posts);
    }

    public static function ca_events()
    {
        return rest_ensure_response(WpRepository::getEvents());
    }

    /**
     * @param WP_REST_Request $request
     *
     * @return WP_Error|WP_HTTP_Response|WP_REST_Response
     */
    public static function search(WP_REST_Request $request)
    {
        $keyword = $request->get_param('keyword');
        if ( ! $keyword) {
            return new WP_Error(500, 'missing param keyword');
        }

        $searcher = new Searcher();

        try {
            $results = $searcher->search(urldecode($keyword));
            $data    = ['count' => $results->count()];
        } catch (InvalidException $e) {
            Mailer::sendError("wp error search".$keyword, $e->getMessage());

            return rest_ensure_response([]);
        }

        /**
         * Je nettoie le resultat car je n'arrive pas avec react
         */
        $resultat = [];
        foreach ($results->getResults() as $result) {
            $hit        = $result->getHit();
            $resultat[] = $hit['_source'];
        }
        $data['hits'] = $resultat;

        return rest_ensure_response($data);
    }

    public static function suggest(WP_REST_Request $request)
    {
        $keyword = $request->get_param('keyword');
        if ( ! $keyword) {
            return new WP_Error(500, 'missing param keyword');
        }

        $searcher = new Searcher();

        try {
            $results = $searcher->suggest($keyword);
        } catch (InvalidException $e) {
            Mailer::sendError("wp error suggest: ".$keyword, $e->getMessage());

            return rest_ensure_response([]);
        }

        /**
         * Je nettoie le resultat car je n'arrive pas avec react
         */
        $data = [];
        foreach ($results->getSuggests() as $suggest) {
            foreach ($suggest as $suggest2) {
                foreach ($suggest2['options'] as $option) {
                    $data[] = $option['text'];
                }
            }
        }

        return rest_ensure_response($data);

    }

    // This plugin returns the societe and the id of all companies in the bottin to retrieve them in the gutenberg block
    static function ca_bottinSocieteId()
    {
        $bottinRepository = new BottinRepository();
        $allfiches        = $bottinRepository->getFiches();
        $fichesSocieteId  = [];

        foreach ($allfiches as $fiche) {
            $fichesSociete             = $fiche->societe;
            $fichesId                  = $fiche->id;
            $formattedFiche            = [];
            $formattedFiche['societe'] = $fichesSociete;
            $formattedFiche['id']      = $fichesId;
            $formattedFiche['slug']    = $fiche->slug;

            $fichesSocieteId[] = $formattedFiche;
        }

        return rest_ensure_response($fichesSocieteId);
    }

    // This plugin also adds a custom endpoint that returns all sheets of the bottin based on their id
    static function ca_bottin($parameter)
    {
        $bottinRepository = new BottinRepository();
        $fiches           = $bottinRepository->getFicheById($parameter['ficheId']);

        return rest_ensure_response($fiches);
    }

    public static function mapKml(WP_REST_Request $request)
    {
        $keyword = $request->get_param('keyword');
        if ( ! $keyword) {
            return new WP_Error(500, 'missing param keyword');
        }

        $carto = new Carto();
        $data  = $carto->loadKml($keyword);

        return rest_ensure_response(['kmlText' => $data]);
    }

    public static function filtres()
    {
        $carto = new Carto();

        return rest_ensure_response($carto->filtres());
    }

    static function mapData(WP_REST_Request $request)
    {
        $keyword = $request->get_param('keyword');
        if ( ! $keyword) {
            Mailer::sendError("error carto", "missing param keyword");

            return new WP_Error(500, 'missing param keyword');
        }

        $carto   = new Carto();
        $element = $carto->foundSource($keyword);
        if (count($element) === 0) {
            $data = ['error' => true];
            Mailer::sendError("error carto", "missing source for keyword ".$keyword);

            return rest_ensure_response($data);
        }

        $source = $element['source'];

        if ($source == 'bottin') {

            $fiches = $carto->getFichesBottin($element['id']);
            $data   = ['data' => $fiches, 'kml' => false];

            return rest_ensure_response($data);
        }

        if ($source == 'kml') {
            $carto  = new Carto();
            $fiches = $carto->loadKml($keyword);
            $data   = ['data' => $fiches, 'kml' => false];

            return rest_ensure_response($data);
        }

        $data = ['error' => true];

        return rest_ensure_response($data);
    }
}
