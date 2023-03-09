<?php

namespace AcMarche\MarcheTail;

use AcMarche\MarcheTail\Inc\Theme;
use AcMarche\MarcheTail\Lib\Cache;
use AcMarche\MarcheTail\Lib\Twig;
use AcMarche\MarcheTail\Lib\WpRepository;
use AcSort;
use SortLink;

get_header();

$cat_ID = get_queried_object_id();
$cache  = Cache::instance();
$blodId = get_current_blog_id();
$code   = Cache::generateCodeCategory($blodId, $cat_ID);

$cache->delete($code);
$wpRepository = new WpRepository();
$children     = $wpRepository->getChildrenOfCategory($cat_ID);
$isReact      = count($children) > 0;

$category    = get_category($cat_ID);
$description = category_description($cat_ID);
$title       = single_cat_title('', false);

$blodId   = get_current_blog_id();
$path     = Theme::getPathBlog($blodId);
$siteSlug = Theme::getTitleBlog($blodId);
$color    = Theme::getColorBlog($blodId);
$blogName = Theme::getTitleBlog($blodId);

$posts    = $wpRepository->getPostsAndFiches($cat_ID);
$parent   = $wpRepository->getParentCategory($cat_ID);
$urlBack  = $path;
$nameBack = $blogName;

if ($parent) {
    $urlBack  = get_category_link($parent->term_id);
    $nameBack = $parent->name;
}
if ($urlBack == '') {
    $urlBack  = '/';//bug if blog citoyen
    $nameBack = 'l\'accueil';
}

$sortLink       = SortLink::linkSortArticles($cat_ID);
$category_order = get_term_meta($cat_ID, 'acmarche_category_sort', true);
if ($category_order == 'manual') {
    $posts = AcSort::getSortedItems($cat_ID, $posts);
}

Twig::rendPage(
    '@MarcheBe/category.html.twig',
    [
        'name'        => $title,
        'category'    => $category,
        'siteSlug'    => $siteSlug,
        'color'       => $color,
        'blogName'    => $blogName,
        'path'        => $path,
        'subTitle'    => 'Tout',
        'description' => $description,
        'children'    => $children,
        'posts'       => $posts,
        'category_id' => $cat_ID,
        'urlBack'     => $urlBack,
        'nameBack'    => $nameBack,
        'sortLink'    => $sortLink,
        'excerpt'     => $category->description,
    ]
);
get_footer();