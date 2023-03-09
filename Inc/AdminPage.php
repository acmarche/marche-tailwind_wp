<?php

namespace AcMarche\MarcheTail\Inc;

use AcMarche\MarcheTail\Lib\Cache;
use function add_action;

class AdminPage
{
    const NAME_OPTION = 'react_activate';

    public function __construct()
    {
        add_action(
            'admin_menu',
            function () {
                AdminPage::createPage();
            }
        );
    }

    static function createPage()
    {
        add_options_page(
            'Cache',
            'Cache',
            'administrator',
            'cache',
            function () {
                AdminPage::renderPage();
            },
        );
        add_action(
            'admin_init',
            function () {
                AdminPage::registerSetting();
            }
        );
    }

    static function registerSetting()
    {
        register_setting('my-cool-plugin-settings-group', self::NAME_OPTION);
    }

    static function renderPage()
    {
        $cache = Cache::instance();
        if (isset($_GET['slug'])) {
            $slug = $_GET['slug'];
            $blodId = get_current_blog_id();
            $code = Cache::generateCodeBottin($blodId, $slug);

            if ($code) {
                $cache->delete($code);
            }
        }
        // $cache->invalidateTags(['marchebe']);
        ?>
        <div class="wrap">
            <h1>Vider le cache</h1>
            <p>Cache vidé</p>

        </div>
        <?php
    }


}
