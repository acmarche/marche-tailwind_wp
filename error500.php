<?php

namespace AcMarche\MarcheTail;


use AcMarche\MarcheTail\Inc\RouterMarche;
use AcMarche\MarcheTail\Lib\Mailer;
use AcMarche\MarcheTail\Lib\Twig;

/**
 * This page is called by symfony @file  functions.php
 */
//$statusCode;  $statusText;
//get_header();
//todo header et body sur meme ligne, si header pas en fixed ca va
Twig::rend500Page();
get_footer();
