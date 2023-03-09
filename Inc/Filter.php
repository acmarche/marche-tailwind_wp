<?php
namespace AcMarche\MarcheTail\Inc;

use function add_filter;

class Filter
{
    public function __construct()
    {
        //add_filter('get_the_archive_title', [Setup::get_instance(), 'removeCategoryPrefixTitle']);
        // Stop WP adding extra <p> </p> to your pages' content
        //   remove_filter('the_content', 'wpautop');
        //    remove_filter('the_excerpt', 'wpautop');
        add_filter('the_content', [$this, 'FilterContent']);
       // add_filter('render_block_data', [$this, 'FilterBlock']);
    }

    function FilterBlock(array $context)
    {
        $blockRender = new BlockRender();
        $blockName = $context['blockName'];
        switch ($blockName) {
            case 'core/gallery';
                return $blockRender->renderGallery($context);
            case 'core/media-text';
                return $blockRender->renderMediaText($context);
            default:
                return $context;
        }
    }

    function FilterContent(string $content)
    {
        $content = preg_replace("#<ul>#", '<ul class="list-group">', $content);
        $content = preg_replace("#<li>#", '<li class="list-group-item">', $content);
        $content = preg_replace("#<table#", '<table class="table table-bordered table-hover"', $content);

        return $content;
    }
}
