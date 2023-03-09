<?php

namespace AcMarche\MarcheTail\Lib;

class HomeData
{
    const widgets = [
        [
            'title' => "Avis de publication",
            'intro' => "Enquêtes publiques, <br>assemblées institutions...",
            'class' => "bg-img-widgets-1",
            'url'   => "/",
            'id'    => 1,
        ],
        [
            'title' => "Economie",
            'intro' => "Infos pratiques, <br>commerces locaux...",
            'class' => "bg-img-widgets-2",
            'url'   => "/",
            'id'    => 2,
        ],
        [
            'title' => "Participation citoyenne",
            'intro' =>
                "Plateforme de l'enveloppe participative,<br> consultations publiques...",
            'class' => "bg-img-widgets-3",
            'url'   => "/",
            'id'    => 3,
        ],
        [
            'title' => "Plateforme de volontariat",
            'intro' => "Offres et demandes du tissu associatif",
            'class' => "bg-img-widgets-4",
            'url'   => "/",
            'id'    => 4,
        ],
        [
            'title' => "Avis de décès",
            'intro' => "Annonces nécrologiques <br> de notre commune",
            'class' => "bg-img-widgets-5",
            'url'   => "/",
            'id'    => 5,
        ],
    ];

    public static function partners(): array
    {
        return [
            [
                'url' => "https://cap.marche.be",
                'img' => get_template_directory_uri()."/assets/images/img/img_sponsor02.png",
                'alt' => "cap commerçant",
                'id'  => 1,
            ],
            [
                'url' => "https://www.visitmarche.be",
                'img' => get_template_directory_uri()."/assets/images/img/visit_logo_b.png",
                'alt' => "visit marche",
                'id'  => 2,
            ],
            [
                'url' => "/economie",
                'img' => get_template_directory_uri()."/assets/images/img/img_sponsor04.png",
                'alt' => "economie",
                'id'  => 3,
            ],
            [
                'url' => "http://www.paysdefamenne.be",
                'img' => get_template_directory_uri()."/assets/images/img/img_sponsor03.png",
                'alt' => "pays de famenne",
                'id'  => 4,
            ],
            [
                'url' => "https://www.famenneardenne.be",
                'img' => get_template_directory_uri()."/assets/images/img/img_sponsor01.png",
                'alt' => "famenne ardenne",
                'id'  => 5,
            ],
            [
                'url' => "https://mcfa.marche.be",
                'img' => get_template_directory_uri()."/assets/images/img/img_sponsor05.png",
                'alt' => "mcfa",
                'id'  => 6,
            ],
            [
                'url' => "https://artistes.marche.be",
                'img' => get_template_directory_uri()."/assets/images/img/artistes.png",
                'alt' => "artistes",
                'id'  => 7,
            ],
        ];
    }

    public static function icones(): array
    {
        return [
            [
                'title'     => "Piscine",
                'icon'      => "i-swimmer",
                'iconHover' => "group-hover:i-swimmer-white",
                'id'        => 1,
                'url'       => "/",
            ],
            [
                'title'     => "Environnement <br/>Déchet",
                'icon'      => "i-leaf",
                'iconHover' => "group-hover:i-leaf-white",
                'id'        => 2,
                'url'       => "/",
            ],
            [
                'title'     => "Travaux <br/>Arrêtés de Police",
                'icon'      => "i-traffic-cone",
                'iconHover' => "group-hover:i-traffic-cone-white",
                'id'        => 3,
                'url'       => "/",
            ],
            [
                'title'     => "Enfance<br/>Jeunesse",
                'icon'      => "i-beach-ball",
                'iconHover' => "group-hover:i-beach-ball-white",
                'id'        => 4,
                'url'       => "/",
            ],
            [
                'title'     => "Carte dynamique",
                'icon'      => "i-map",
                'iconHover' => "group-hover:i-map-white",
                'id'        => 5,
                'url'       => "/",
            ],
            [
                'title'     => "CPAS",
                'icon'      => "i-handshake",
                'iconHover' => "group-hover:i-handshake-white",
                'id'        => 6,
                'url'       => "/",
            ],
            [
                'title'     => "@Marche.be",
                'icon'      => "i-envelope",
                'iconHover' => "group-hover:i-envelope-white",
                'id'        => 7,
                'url'       => "/",
            ],
        ];
    }
}