<?php

namespace AcMarche\MarcheTail\Inc;

use WP_Query;
use function add_action;
use function is_admin;

class QueryAlter
{
    public function __construct()
    {
        //  add_action('pre_get_posts', [$this, 'alterMainQuery']);
        add_action('pre_get_posts', [$this, 'modifyWhereCategory']);
        // Hook our custom query function to the pre_get_posts
        add_action('pre_get_posts', [$this, 'orderByQuery']);
    }

    function alterMainQuery($query)
    {
        if ( ! is_admin() && $query->is_main_query()) {
            $query->set('post_type', array('post', 'page', 'bottin_fiche'));
        }

        return $query;
    }

    /**
     * Oblige wp a afficher que les articles de la catégorie en cours
     * et pas ceux des catégories enfants
     *
     * @param WP_Query $query
     */
    function modifyWhereCategory(WP_Query $query)
    {
        if ( ! is_admin() && $query->is_category()) :

            $object = get_queried_object();

            if ($object != null) {
                if ($object->cat_ID) {
                    if ($query->is_main_query()) {
                        $ID_cat = $object->cat_ID;

                        //sinon prend enfant
                        $query->set('category__in', $ID_cat);
                    }
                }
            }
        endif;
    }

    function orderByQuery($query)
    {
        if ($query->is_main_query() && ! is_admin() && $query->is_home()) {
            $query->set('orderby', 'post_title');
            $query->set('order', 'ASC');
        }
    }

}
