<?php

namespace AcMarche\MarcheTail;


use AcMarche\MarcheTail\Lib\Menu;
use AcMarche\MarcheTail\Lib\Twig;

get_header();

$menu = new Menu();
$items = $menu->getMenuTop();
Twig::rend404Page();
get_footer();