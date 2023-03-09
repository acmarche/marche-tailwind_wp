<?php
/**
 * Template Name: Home-Page-Principal
 */

namespace AcMarche\MarcheTail;

use AcMarche\MarcheTail\Lib\HomeData;
use AcMarche\MarcheTail\Lib\Menu;
use AcMarche\MarcheTail\Lib\Twig;
use AcMarche\MarcheTail\Lib\WpRepository;
use Symfony\Component\HttpFoundation\Request;

get_header();

$wpRepository = new WpRepository();
$news         = $wpRepository->getAllNews(6);
$events       = $wpRepository->getEvents(max: 6);

$pageAlert    = WpRepository::getPageAlert();
$contentAlert = null;
$dateAlert    = null;

if ($pageAlert) {
    $request   = Request::createFromGlobals();
    $dateAlert = preg_replace("#(\D)#", "", $pageAlert->post_modified);
    $close     = (bool)$request->cookies->get('closeAlert'.$dateAlert);
    if ($close) {
        $pageAlert = null;
    } else {
        $contentAlert = get_the_content(null, null, $pageAlert);
        $contentAlert = apply_filters('the_content', $contentAlert);
        $contentAlert = str_replace(']]>', ']]&gt;', $contentAlert);
    }
}

$imagesBg = [
    '/wp-content/themes/marchebe/assets/tartine/rsc/img/bg_home.jpg',
    '/wp-content/themes/marchebe/assets/images/home/fond1.jpg',
    '/wp-content/themes/marchebe/assets/images/home/fond2.jpg',
    '/wp-content/themes/marchebe/assets/images/home/fond3.jpg',
    '/wp-content/themes/marchebe/assets/images/home/fond42.jpg',
    '/wp-content/themes/marchebe/assets/images/home/fond5.jpg',
    '/wp-content/themes/marchebe/assets/images/home/marche-bg.jpg',
];

$imageBg = $imagesBg[4];
$date    = new \DateTime();
$heure   = $date->format('H');
if ($heure > 16 || $heure <= 7) {
    $imageBg = $imagesBg[0];
}

$menu  = new Menu();
$items = $menu->getAllItems2();
Twig::rendPage(
    '@MarcheBe/homepage.html.twig',
    [
        'actus'        => $news,
        'events'       => $events,
        'pageAlert'    => $pageAlert,
        'contentAlert' => $contentAlert,
        'imageBg'      => $imageBg,
        'dateAlert'    => $dateAlert,
        'items'        => $items,
        'icones'       => HomeData::icones(),
        'results'      => [],
        'widgets'      => HomeData::widgets,
        'partners'     => HomeData::partners(),
    ]
);
get_footer();