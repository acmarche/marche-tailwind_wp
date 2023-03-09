<?php

namespace AcMarche\MarcheTail;

use AcMarche\MarcheTail\Lib\Menu;
use AcMarche\MarcheTail\Lib\Twig;
?>
    <!doctype html>
<html lang="fr">
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="https://gmpg.org/xfn/11">
        <link rel="icon" type="image/png" href="<?php echo get_template_directory_uri() ?>/assets/images/favicon.png"/>
        <?php wp_head();        ?>
    </head>

<body <?php body_class(); ?> id="app">
    <?php
wp_body_open();
$menu = new Menu();
$items = $menu->getAllItems2();

Twig::rendPage(
    '@MarcheBe/header/_header.html.twig',
    [
        'items' => $items,
    ]
);
