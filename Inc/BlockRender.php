<?php

namespace AcMarche\MarcheTail\Inc;

class BlockRender
{
    public function __construct()
    {

    }

    public function renderGallery(array $context)
    {
        $images = $context['attrs']['ids'];
        foreach ($images as $image) {
            $attachment = wp_get_attachment_image($image);
        //    dump($attachment);
        }

        return $context;
    }

    public function renderMediaText(array $context)
    {
       // dump($context);
        return $context;
    }

}
