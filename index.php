<?php
namespace AcMarche\MarcheTail;


use AcMarche\MarcheTail\Lib\Twig;

get_header();

Twig::rendPage(
    '@MarcheBe/empty.html.twig',
    [
        'name' => 'Page index',
        'post' => null,
        'excerpt' => '',
        'tags' => [],
        'image' => null,
        'icone' => null,
        'recommandations' => [],
        'bgCat' => '',
        'urlBack' => '',
        'categoryName' => 'name',
        'nameBack' => '',
        'content' => '',
    ]
);

get_footer();