<?php

namespace AcMarche\MarcheTail\Inc;

use AcMarche\Conseil\Conseil;
use AcMarche\Conseil\ConseilDb;
use AcMarche\MarcheTail\Lib\Adl;
use AcMarche\MarcheTail\Lib\Cache;
use AcMarche\MarcheTail\Lib\Capteur;
use AcMarche\MarcheTail\Lib\Mailer;
use AcMarche\MarcheTail\Lib\Menu;
use AcMarche\MarcheTail\Lib\Twig;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function add_action;
use function json_decode;


class ShortCodes
{
    private CacheInterface $cache;
    private HttpClientInterface $httpClient;

    public function __construct()
    {
        add_action('init', [$this, 'registerShortcodes']);
        $this->cache = Cache::instance();
    }

    function registerShortcodes()
    {
        add_shortcode('taxe', [new ShortCodes(), 'taxe']);
        add_shortcode('enaos', [new ShortCodes(), 'enaos']);
        add_shortcode('conseil_ordre', [new ShortCodes(), 'conseilOrdre']);
        add_shortcode('conseil_pv', [new ShortCodes(), 'conseilPv']);
        add_shortcode('conseil_archive', [new ShortCodes(), 'conseilArchive']);
        add_shortcode('google_map', [new ShortCodes(), 'googleMap']);
        add_shortcode('menuDisplay', [new ShortCodes(), 'menuDisplay']);
        add_shortcode('adl_chimp', [new ShortCodes(), 'adlChimp']);
        add_shortcode('adl_inscription', [new ShortCodes(), 'adlInscription']);
        add_shortcode('capteur_list', [new ShortCodes(), 'capteurList']);
    }

    public function conseilOrdre(): string
    {
        $conseilDb = new ConseilDb();
        $ordres = $conseilDb->getAllOrdre();
        $twig = Twig::LoadTwig();

        return $twig->render(
            'conseil/_ordre.html.twig',
            [
                'ordres' => $ordres,
            ]
        );
    }

    public function conseilPv(): string
    {
        $year = date('Y');
        $conseilDb = new ConseilDb();
        $pvs = $conseilDb->getByYearPvs($year);
        $twig = Twig::LoadTwig();

        return $twig->render(
            'conseil/_pv.html.twig',
            [
                'pvs' => $pvs,
                'year' => $year,
            ]
        );
    }

    public function conseilArchive(): string
    {
        $twig = Twig::LoadTwig();
        $conseilDb = new ConseilDb();
        $endYear = date('Y') - 1;
        $txt = '';

        $years = range(2019, $endYear);
        rsort($years, SORT_NUMERIC);

        foreach ($years as $year) {
            $pvs = $conseilDb->getByYearPvs($year);
            $pvs = array_map(
                function ($pv) {
                    $pv['name'] = $pv['nom'];
                    $pv['url'] = '/wp-content/uploads/conseil/pv/'.$pv['file_name'];

                    return $pv;
                },
                $pvs
            );
            $txt .= $twig->render(
                'conseil/_archive.html.twig',
                [
                    'year' => $year,
                    'pvs' => $pvs,
                ]
            );
        }

        $txt .= $this->getArchiveByFiles();

        return $txt;
    }

    private function getArchiveByFiles()
    {
        $twig = Twig::LoadTwig();
        $conseil = new Conseil();

        $years = range(2013, 2018);
        rsort($years, SORT_NUMERIC);

        $txt = '';
        foreach ($years as $year) {
            $pvs = $conseil->find_all_files($year);
            $txt .= $twig->render(
                'conseil/_archive.html.twig',
                [
                    'year' => $year,
                    'pvs' => $pvs,
                ]
            );
        }

        return $txt;
    }

    public function enaos()
    {
        return $this->cache->get(
            'enaos',
            function () {
                return file_get_contents('https://api.marche.be/marchebe/necrologie/');
            }
        );
    }

    public function taxe()
    {
        return $this->cache->get(
            'taxes',
            function () {
                $this->httpClient = HttpClient::create();
                $nomenclatures = $this->getContent('/taxes/api2');

                $twig = Twig::LoadTwig();

                $template = 'article/taxe.html.twig';
                $content = $twig->render($template, ['nomenclatures' => $nomenclatures]);

                return $content;
            }
        );
    }

    private function getContent(string $url)
    {
        $base = 'https://extranet.marche.be';
        try {
            $response = $this->httpClient->request('GET', $base.$url);

            return json_decode($response->getContent());
        } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            Mailer::sendError("list taxtes", $e->getMessage());
        }

        return [];
    }

    public function googleMap(array $args): string
    {
        $latitude = $args['lat'];
        $longitude = $args['long'];
        $twig = Twig::LoadTwig();
        $post = get_post();
        $title = $post ? $post->post_title : '';

        $t = $twig->render(
            'map/_carte.html.twig',
            [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'title' => $title,
            ]
        );
        $t = preg_replace("#\n#", "", $t);//bug avec raw de twig

        return $t;
    }

    public function menuDisplay(array $args): string
    {
        $menu = new Menu();
        $items = $menu->getItems($args['site']);
        $twig = Twig::LoadTwig();

        return $twig->render(
            'menu/_items_short_code.html.twig',
            [
                'items' => $items,
            ]
        );
    }

    public function adlChimp(): string
    {
        $mailchimp = new MailChimp();
        $campaings = $mailchimp->getCampaings();
        $twig = Twig::LoadTwig();

        return $twig->render(
            'eco/_campaings.html.twig',
            [
                'campaings' => $campaings,
            ]
        );
    }

    public function adlInscription(): string
    {
        $adl = new Adl();
        $form = $adl->generateForm();

        return $form;
    }

    public function capteurList(): string
    {
        $twig = Twig::LoadTwig();
        $capteur = new Capteur();

        return $twig->render(
            'capteur/_list.html.twig',
            [
                'stations' => $capteur->getCapteurs(),
            ]
        );
    }

}
