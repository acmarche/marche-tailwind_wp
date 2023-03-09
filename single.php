<?php

namespace AcMarche\MarcheTail;

use AcMarche\MarcheTail\Inc\Theme;
use AcMarche\MarcheTail\Lib\Cache;
use AcMarche\MarcheTail\Lib\Menu;
use AcMarche\MarcheTail\Lib\Twig;
use AcMarche\MarcheTail\Lib\WpRepository;

global $post;

$cache  = Cache::instance();
$blodId = get_current_blog_id();
$code   = Cache::generateCodeArticle($blodId, $post->ID);
get_header();

$cache->delete($code);
echo $cache->get(
    $code,
    function () use ($post, $blodId) {

        $image = null;
        if (has_post_thumbnail()) {
            $images = wp_get_attachment_image_src(get_post_thumbnail_id(), 'original');
            if ($images) {
                $image = $images[0];
            }
        }

        $path     = Theme::getPathBlog($blodId);
        $blogName = Theme::getTitleBlog($blodId);
        $color    = Theme::getColorBlog($blodId);

        $tags      = WpRepository::getTags($post->ID);
        $relations = WpRepository::getRelations($post->ID);
        $relations = array_splice($relations, 0, 3);
        $catSlug   = get_query_var('category_name');

        if (preg_match("#/#", $catSlug)) {
            $vars    = explode("/", $catSlug);
            $catSlug = end($vars);
        }

        $urlBack  = '/';
        $nameBack = '';

        $currentCategory = get_category_by_slug($catSlug);
        if ($currentCategory) {
            $urlBack  = get_category_link($currentCategory);
            $nameBack = $currentCategory->name;
        }

        $isActu = array_filter(
            $tags,
            function ($tag) {
                if (preg_match("#artistique#", $tag['name'])) {
                    return $tag;
                }

                return null;
            }
        );

        if (count($isActu) > 0) {
            $urlBack  = $isActu[array_key_first($isActu)]['url'];
            $nameBack = $isActu[array_key_first($isActu)]['name'];
        }

        $content = get_the_content(null, null, $post);
        $content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]&gt;', $content);

        $twig  = Twig::LoadTwig();
        $menu  = new Menu();
        $items = $menu->getAllItems2();

        return $twig->render(
            '@MarcheBe/article.html.twig',
            [
                'post'        => $post,
                'tags'        => $tags,
                'image'       => $image,
                'title'       => $post->post_title,
                'blogName'    => $blogName,
                'color'       => $color,
                'path'        => $path,
                'relations'   => $relations,
                'urlBack'     => $urlBack,
                'nameBack'    => $nameBack,
                'content'     => $content,
                'readspeaker' => true,
            ]
        );
    }
);

get_footer();
