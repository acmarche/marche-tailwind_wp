<?php


namespace AcMarche\MarcheTail\Lib;

use AcMarche\Bottin\Repository\BottinRepository;
use AcMarche\Bottin\RouterBottin;
use DOMElement;
use DOMNode;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Carto
{
    private HttpClientInterface $httpClient;
    private KmlParser $kmlParser;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
        $this->kmlParser  = new KmlParser();
    }

    public function filtres(): array
    {
        return [
            'culture'           => [
                'name'     => 'Culture',
                'icone'    => 'i-book',
                'elements' => [
                    'biblio'        => ['name' => 'Bibliothèques', 'source' => 'bottin', 'id' => 674],
                    'boites_livres' => ['name' => 'Boîtes à livres', 'source' => 'bottin', 'id' => 684],
                    'cinema'        => ['name' => 'Cinéma', 'source' => 'bottin', 'id' => 675],
                    'musees'        => ['name' => 'Musées', 'source' => 'bottin', 'id' => 673],
                    'statues'       => ['name' => 'Statues et sculptures', 'source' => 'kml', 'id' => 'statues'],
                ],
            ],
            'enfance'           => [
                'name'     => 'Enfance',
                'icone'    => 'i-beach-ball',
                'elements' => [
                    'accueillantes' => ['name' => 'Accueillantes', 'source' => 'bottin', 'id' => 672],
                    'jeux'          => ['name' => 'Aires de jeux, parcs', 'source' => 'kml', 'id' => 'jeux'],
                    'creches'       => ['name' => 'Crêches', 'source' => 'bottin', 'id' => 495],
                ],
            ],
            'enseignement'      => [
                'name'     => 'Enseignement',
                'icone'    => 'i-school',
                'elements' => [
                    'enseignement_artistique' => ['name' => 'Artistique', 'source' => 'bottin', 'id' => 682],
                    'enseignement_maternelle' => ['name' => 'Maternel et primaire', 'source' => 'bottin', 'id' => 669],
                    'enseignement_secondaire' => ['name' => 'Secondaire', 'source' => 'bottin', 'id' => 668],
                    'enseignement_superieur'  => ['name' => 'Supérieur', 'source' => 'bottin', 'id' => 670],
                ],
            ],
            'environnement'     => [
                'name'     => 'Environnement',
                'icone'    => 'i-leaf',
                'elements' => [
                    'bulles_verres'    => ['name' => 'Bulles à verres', 'source' => 'bottin', 'id' => 677],
                    'bulles_vetements' => ['name' => 'Bulles à vêtements', 'source' => 'bottin', 'id' => 678],
                    'capteurs'         => ['name' => 'Stations qualité de l\'air', 'source' => 'kml', 'id' => 'capteurs'],
                ],
            ],
            'horeca'            => [
                'name'     => 'Horéca',
                'icone'    => 'i-flatware',
                'elements' => [
                    'brasseries'  => ['name' => 'Brasseries-Bar', 'source' => 'bottin', 'id' => 522],
                    'camping'     => ['name' => 'Camping', 'source' => 'bottin', 'id' => 652],
                    'chambres'    => ['name' => 'Chambres d\'hôtes', 'source' => 'bottin', 'id' => 651],
                    'friteries'   => ['name' => 'Friterie - Snack - sandwicherie', 'source' => 'bottin', 'id' => 523],
                    'gites'       => ['name' => 'Gîtes et meublés de tourisme', 'source' => 'bottin', 'id' => 650],
                    'glaciers'    => ['name' => 'Glaciers - Tea room', 'source' => 'bottin', 'id' => 524],
                    'hotels'      => ['name' => 'Hôtels', 'source' => 'bottin', 'id' => 649],
                    'restaurants' => ['name' => 'Restaurants', 'source' => 'bottin', 'id' => 521],
                ],
            ],
            'infrastructures'   => [
                'name'     => 'Infrastructures',
                'icone'    => 'i-board',
                'elements' => [
                    'cimetieres'   => ['name' => 'Cimetières', 'source' => 'kml', 'id' => 'cimetieres'],
                    'salles_commu' => ['name' => 'Salles communales', 'source' => 'bottin', 'id' => 680],
                ],
            ],
            'mobilite'          => [
                'name'     => 'Mobilité',
                'icone'    => 'i-bus',
                'elements' => [
                    'balade_pieds'        => [
                        'name'   => 'Balade des petits pieds',
                        'source' => 'kml',
                        'id'     => 'balades',
                    ],
                    'parking_centre'      => [
                        'name'   => 'Parkings centre-véhicules ',
                        'source' => 'bottin',
                        'id'     => 679,
                    ],
                    //   'pistes_cyclo'        => ['name' => 'Pistes cyclables', 'source' => 'kml', 'id' => 'cyclos'],
                    'travaux'             => ['name' => 'Travaux', 'source' => 'kml', 'id' => 'travaux'],
                    'velos_stationnement' => [
                        'name'   => 'Parkings vélos',
                        'source' => 'kml',
                        'id'     => 'velos_stationnement',
                    ],
                ],
            ],
            'sante'             => [
                'name'     => 'Santé',
                'icone'    => 'i-healthcase',
                'elements' => [
                    'dentistes'    => ['name' => 'Dentistes', 'source' => 'bottin', 'id' => 383],
                    'hopital'      => ['name' => 'Hôpital', 'source' => 'bottin', 'id' => 681],
                    'kines'        => ['name' => 'Kinésithérapeutes', 'source' => 'bottin', 'id' => 385],
                    'medecins'     => ['name' => 'Médecine générale', 'source' => 'bottin', 'id' => 370],
                    'mutuelles'    => ['name' => 'Mutuelles', 'source' => 'bottin', 'id' => 411],
                    'pharmacies'   => ['name' => 'Pharmacies', 'source' => 'bottin', 'id' => 390],
                    'veterinaires' => ['name' => 'Vétérinaires', 'source' => 'bottin', 'id' => 588],
                ],
            ],
            'securite_routiere' => [
                'name'     => 'Sécurité routière',
                'icone'    => 'i-healthcase',
                'elements' => [
                    'radar'           => ['name' => 'Radars répressifs', 'source' => 'bottin', 'id' => 688],
                    'radar_preventif' => ['name' => 'Radars préventifs', 'source' => 'kml', 'id' => 'radar_preventif'],
                    'trafic'          => ['name' => 'Analyseur de trafic', 'source' => 'kml', 'id' => 'trafic'],
                ],
            ],
            'sport'             => [
                'name'     => 'Sport',
                'icone'    => 'i-chrono',
                'elements' =>
                    $this->getElements(486),
            ],
            'wifi'              => [
                'name'     => 'Wifi gratuit',
                'icone'    => 'i-wifi',
                'elements' => [
                    'wifi' => ['name' => 'Réseau Wifi4EU', 'source' => 'kml', 'id' => 'wifi'],
                ],
            ],
        ];
    }

    public function fetchKml(string $url)
    {
        $request = $this->httpClient->request(
            'GET',
            $url,
            [

            ]
        );

        $content = $request->getContent();

        return $content;
    }


    public function foundSource(string $keySearch): array
    {
        foreach ($this->filtres() as $filtre) {
            foreach ($filtre['elements'] as $key => $element) {
                if ($keySearch == $key) {
                    return $element;
                }
            }
        }

        return [];
    }

    public function loadKml(string $keyword): array
    {
        switch ($keyword) {
            case 'seniors':
                $url = 'https://www.google.com/maps/d/u/1/kml?forcekml=1&mid=1M3CBWAF0BQ7BqLB33xFr3tu10o0';
                break;
            case 'statues':
                $url = 'https://www.google.com/maps/d/u/1/kml?forcekml=1&mid=1Za10EtAUa8zrOqdw2eSdUWL0nVU';
                break;
            case 'jeux':
                $url = 'https://www.google.com/maps/d/u/1/kml?forcekml=1&mid=1TwhxZiIAnzdvAEeUZp08BEQlU88';
                break;
            case 'wifi':
                $url = 'https://www.google.com/maps/d/u/1/kml?forcekml=1&mid=1NABWReYEqCBUaOjd3x5TmyTQEZw6PIfp';
                break;
            case 'travaux':
                $url = 'https://www.google.com/maps/d/u/1/kml?forcekml=1&mid=1kfhp1xhZcusuTMBxkDK5agYS5cQKAlrL';
                break;
            case 'parkings':
                $url = 'https://www.google.com/maps/d/u/1/kml?forcekml=1&mid=1-509jyExlQqn7c1ijeYxrkLVOa8';
                break;
            case 'balade_pieds':
                $url = 'https://www.google.com/maps/d/u/1/kml?forcekml=1&mid=1eC0t63jFfVhLAjGuWTkIkfHHYqc';
                break;
            case 'velos_stationnement':
                $url = 'https://www.google.com/maps/d/u/1/kml?forcekml=1&mid=1A403qynTGRgt3FigLEqpIcRL4CGtazUJ';
                break;
            case 'cimetieres':
                $url = 'https://www.google.com/maps/d/kml?forcekml=1&mid=1Cw-353ODEVCerBBior9Y27Bf8sc';
                break;
            case 'trafic':
                $url = 'https://www.google.com/maps/d/u/0/kml?forcekml=1&mid=1KcaFoc4PdWEPezlKLxPJxOtSDjkdv6gz&lid=TxtOoLFqAYs';
                break;
            case 'radar_preventif':
                $url = 'https://www.google.com/maps/d/u/0/kml?forcekml=1&mid=1KcaFoc4PdWEPezlKLxPJxOtSDjkdv6gz&lid=MmcyPrPTS_w';
                break;
            case 'capteurs':
                $url = 'https://www.marche.be/api/capteurs.php';
                break;
            default:
                $url = false;
                break;
        }

        if ($url) {
            $kmlString = $this->fetchKml($url);
            $kmlParser = new KmlParser();
            $kmlParser->parse($kmlString);
            $places = $kmlParser->getPlacesMark();
            $points = [];
            foreach ($places as $place) {
                if ($place instanceof DOMElement) {
                    $point = $this->getDataFromType($place);
                    if ($point) {
                        $points[] = $point;
                    }
                }
            }

            return $points;
        }

        return [];
    }

    public function getFichesBottin(int $id): array
    {
        $bottinRepository = new BottinRepository();
        $data             = [];
        $fiches           = $bottinRepository->getFichesByCategories([$id]);
        foreach ($fiches as $fiche) {
            $data[] = $this->formatSocieteData($fiche);
        }

        return $data;
    }

    public function getElements(int $id): array
    {
        $bottinRepository = new BottinRepository();
        $data             = [];
        $rubriques        = $bottinRepository->getCategories($id);
        foreach ($rubriques as $rubrique) {
            $data[$rubrique->slug] = ['name' => $rubrique->name, 'source' => 'bottin', 'id' => $rubrique->id];
        }

        return $data;
    }

    public function formatSocieteData($object): array
    {
        $bottinRepository = new BottinRepository();
        $idSite           = $bottinRepository->findSiteFiche($object);

        return [
            'nom'         => $object->societe,
            'latitude'    => $object->latitude,
            'longitude'   => $object->longitude,
            'telephone'   => $object->telephone.' '.$object->gsm,
            'email'       => $object->email,
            'rue'         => $object->rue.', '.$object->numero,
            'localite'    => $object->cp.' '.$object->localite,
            'url'         => RouterBottin::getUrlFicheBottin($idSite, $object),
            'kml'         => false,
            'description' => '',
        ];
    }

    public function getDataFromType(DOMElement $place): ?array
    {
        $placeName   = $this->kmlParser->getValueByTagName($place, 'name');
        $description = $this->kmlParser->getValueByTagName($place, 'description');
        $description = StringUtils::pureHtml($description);
        $description = make_clickable($description);
        $item        = [
            'nom'         => $placeName,
            'description' => $description,
            'kml'         => true,
        ];

        $point = $this->kmlParser->getElementsByTagName($place, 'Point');
        if ($point) {
            return array_merge($item, $this->getCoordinatesPoint($point));
        }

        $lineString = $this->kmlParser->getElementsByTagName($place, 'Point');
        if ($lineString) {
            return null;
        }

        return null;
    }

    public function getCoordinatesPoint(DOMNode $point): array
    {
        $coordinates = $this->kmlParser->getValueByTagName($point, 'coordinates');
        list($longitude, $latitude) = explode(',', $coordinates);

        return [
            'latitude'  => trim($latitude),
            'longitude' => trim($longitude),
        ];
    }

    public function getCoordinatesLine(DOMNode $point)
    {
        $coordinates = $this->kmlParser->getValueByTagName($point, 'coordinates');
        $lines       = explode("\n", $coordinates);
        $items       = [];
        foreach ($lines as $line) {
            if (preg_match('#,#', $line)) {
                list($longitude, $latitude) = explode(',', $line);
                $items[] = [
                    'latitude'  => trim($latitude),
                    'longitude' => trim($longitude),
                ];
            }
        }

        return $items;
    }

}
