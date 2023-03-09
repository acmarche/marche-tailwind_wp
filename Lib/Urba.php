<?php


namespace AcMarche\MarcheTail\Lib;

use AcMarche\Theme\Inc\RouterMarche;
use AcMarche\Theme\Inc\Theme;
use AcMarche\UrbaWeb\Entity\Permis;
use AcMarche\UrbaWeb\UrbaWeb;

class Urba
{
    /**
     * @param int $enqueteId
     *
     * @return Permis|string
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public static function permisCanBeRead(string $numeroPermis)
    {
        $urbaweb = new UrbaWeb(false);
        $twig    = Twig::LoadTwig();
        $result  = $urbaweb->searchPermis(['numeroPermis' => $numeroPermis]);
        $permis  = null;
        if (count($result) > 0) {
            $permisId = $result[0];
            $permis   = self::fullInformationsPermis($permisId);
        }
        if ( ! $permis) {
            return $twig->render(
                'errors/404.html.twig',
                [
                    'title'     => 'Enquête publique non trouvée',
                    'tags'      => [],
                    'color'     => Theme::getColorBlog(Theme::TOURISME),
                    'blogName'  => Theme::getTitleBlog(Theme::TOURISME),
                    'relations' => [],
                ]
            );
        }

        if ( ! $urbaweb->isPublic($permis)) {
            return $twig->render(
                'errors/500.html.twig',
                [
                    'title'     => 'Enquête publique non publique',
                    'message'   => 'Erreur de lecture de l\'enquête',
                    'tags'      => [],
                    'color'     => Theme::getColorBlog(Theme::TOURISME),
                    'blogName'  => Theme::getTitleBlog(Theme::TOURISME),
                    'relations' => [],
                ]
            );
        }

        return $permis;
    }

     /**
     * @return Permis[]
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public static function getEnquetesPubliques(
        \DateTimeInterface $dateDebut = null,
        \DateTimeInterface $dateFin = null
    ): array {
        if ( ! $dateDebut) {
            $dateDebut = new \DateTime();
            $dateDebut->modify('-6 months');
        }
        $dateFin = new \DateTime();
        $dateFin->modify('+12 months');

        $urbaWeb    = new UrbaWeb(false);
        $args       = [
            'debutAffichageEnqueteDe' => $dateDebut->format('Y-m-d'),
            'debutAffichageEnqueteA'  => $dateFin->format('Y-m-d'),
        ];
        $enqueteIds = $urbaWeb->searchAdvancePermis(
            $args
        );
        $args       = [
            'debutAnnonceProjetDe' => $dateDebut->format('Y-m-d'),
            'debutAnnonceProjetA'  => $dateFin->format('Y-m-d'),
        ];
        $annonceIds = $urbaWeb->searchAdvancePermis(
            $args
        );
        $permisIds  = array_merge($enqueteIds, $annonceIds);
        $permisIds  = array_unique($permisIds);

        $all = [];
        foreach ($permisIds as $permisId) {
            $permis = self::fullInformationsPermis($permisId);
            if ($urbaWeb->isPublic($permis)) {
                $all[] = $permis;
            }
        }

        return $all;
    }

    public static function fullInformationsPermis(int $permisId): ?Permis
    {
        $urbaweb = new UrbaWeb(false);

        return $urbaweb->fullInformationsPermis($permisId);
    }

    public static function permisToPost(Permis $permis): \stdClass
    {
        $demandeur = count($permis->demandeurs) > 0 ? $permis->demandeurs[0] : null;
        list($yearD, $monthD, $dayD) = explode('-', $permis->dateDebutAffichage());
        $dateDebut = $dayD.'-'.$monthD.'-'.$yearD;
        list($yearF, $monthF, $dayF) = explode('-', $permis->dateFinAffichage());
        $dateFin            = $dayF.'-'.$monthF.'-'.$yearF;
        $post               = new \stdClass();
        $post->ID           = $permis->numeroPermis;
        $nature             = $permis->nature ? $permis->nature->libelle : '';
        $post->excerpt      = $nature.'<br />'.$permis->natureTexteLibre.'<br />'.$permis->urbain.'<br /> Du '.$dateDebut.' au '.$dateFin;
        $post->post_excerpt = '';
        $type               = $permis->typePermis ? $permis->typePermis->libelle : '';
        $post->post_excerpt = $type.'<br />';
        if ( ! $permis->numeroPermis) {
            $permis->numeroPermis = 'rr';
        }
        $post->url = RouterMarche::getUrlEnquete($permis->numeroPermis);
        $localite  = $permis->adresseSituation ? $permis->adresseSituation->localite : '';
        if ($demandeur) {
            $post->post_title = $demandeur->civilite.' '.$demandeur->nom.' '.$demandeur->prenom.' à '.$localite;
        } else {
            $post->post_title = '';
        }

        return $post;
    }
}
