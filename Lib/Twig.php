<?php

namespace AcMarche\MarcheTail\Lib;

use AcMarche\MarcheTail\Inc\RouterMarche;
use AcMarche\Pivot\DependencyInjection\PivotContainer;
use AcMarche\Pivot\Entities\Specification\SpecData;
use AcMarche\Pivot\Spec\SpecTypeEnum;
use AcMarche\Pivot\Utils\UrnToSkip;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\String\UnicodeString;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Extra\Intl\IntlExtension;
use Twig\Extra\String\StringExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;
use const ABSPATH;
use const WP_DEBUG;

class Twig
{
    public static ?Environment $instanceObject = null;

    public static function LoadTwig(?string $path = null): Environment
    {
        if (self::$instanceObject) {
            return self::$instanceObject;
        }

        if (!$path) {
            $path = get_template_directory().'/templates';
        }

        $loader = new FilesystemLoader($path);
        (new Dotenv())
            ->bootEnv(ABSPATH.'.env');

        $environment = new Environment(
            $loader,
            [
                'cache' => $_ENV['APP_CACHE_DIR'] ?? Cache::getPathCache('twig'),
                'debug' => WP_DEBUG,
                'strict_variables' => WP_DEBUG,
            ]
        );

        $loader->addPath(get_template_directory().'/templates/', 'MarcheBe');
        $environment->addExtension(new DebugExtension());

        $environment->addExtension(new StringExtension());
        $environment->addExtension(new IntlExtension());

        $environment->addGlobal('template_directory', get_template_directory_uri());
        $environment->addFilter(self::categoryLink());
        $environment->addFunction(self::showTemplate());
        $environment->addFunction(self::currentUrl());
        $environment->addFunction(self::templateUri());
        $environment->addFunction(self::isExternalUrl());
        $environment->addFilter(self::removeHtml());
        $environment->addFilter(self::renderValuePivot());
        $environment->addFilter(self::checkDisplay());
        $environment->addFunction(self::wwwUrl());
        $environment->addFilter(self::puriferHtml());

        self::$instanceObject = $environment;

        return self::$instanceObject;
    }

    public static function rendPage(string $templatePath, array $variables = []): void
    {
        $twig = self::LoadTwig();
        try {
            echo $twig->render(
                $templatePath,
                $variables,
            );
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            $url = RouterMarche::getCurrentUrl();
            $error = $e->getMessage();
            if ($e->getLine()) {
                $error .= $e->getLine();
            }
            if ($e->getFile()) {
                $error .= $e->getFile();
            }
            Mailer::sendError('Error page: '.$templatePath, $url.' \n '.$error);
            echo $twig->render(
                '@MarcheBe/errors/500.html.twig',
                [
                    'message' => $e->getMessage()." ligne ".$e->getLine()." file ".$e->getFile(),
                    'error' => $e,
                    'name' => "La page n'a pas pu être chargée",
                    'excerpt' => null,
                    'image' => get_template_directory_uri().'/assets/images/error500.jpg',
                    'urlBack' => '/',
                    'imagePosition' => 'bottom center',
                    'categoryName' => 'Accueil',
                    'nameBack' => 'Acceuil',
                ]
            );
        }
    }

    public static function rend500Page(): void
    {
        $twig = self::LoadTwig();

        echo $twig->render(
            '@MarcheBe/errors/500.html.twig',
            [
                'excerpt' => null,
                'image' => get_template_directory_uri().'/assets/images/error500.jpg',
                'urlBack' => '/',
                'imagePosition' => 'bottom center',
                'categoryName' => 'Accueil',
                'nameBack' => 'Acceuil',
            ]
        );
    }

    public static function rend404Page(): void
    {
        $twig = self::LoadTwig();

        echo $twig->render(
            '@MarcheBe/errors/404.html.twig',
            [
                'excerpt' => null,
                'name' => null,
                'message' => null,
                'image' => get_template_directory_uri().'/assets/images/error404.jpg',
                'imagePosition' => 'bottom center',
                'url' => RouterMarche::getCurrentUrl(),
                'urlBack' => '/',
                'categoryName' => 'Accueil',
                'nameBack' => 'Acceuil',
            ]
        );
    }

    /**
     * For sharing pages.
     */
    public static function currentUrl(): TwigFunction
    {
        return new TwigFunction(
            'currentUrl',
            fn(): string => RouterMarche::getCurrentUrl()
        );
    }

    /**
     * For sharing pages
     * @return TwigFunction
     */
    public static function wwwUrl(): TwigFunction
    {
        return new TwigFunction(
            'wwwUrl',
            function (): string {
                return RouterMarche::getUrlWww();
            }
        );
    }


    protected static function categoryLink(): TwigFilter
    {
        return new TwigFilter(
            'category_link',
            fn(int $categoryId): ?string => get_category_link($categoryId)
        );
    }

    protected static function showTemplate(): TwigFunction
    {
        return new TwigFunction(
            'showTemplate',
            function (): string {
                if (true === WP_DEBUG) {
                    global $template;

                    return 'template: '.$template;
                }

                return '';
            }
        );
    }

    protected static function isExternalUrl(): TwigFunction
    {
        return new TwigFunction(
            'isExternalUrl',
            function (string $url): bool {
                if (preg_match('#http#', $url)) {
                    return !preg_match('#https://www.marche.be#', $url);
                }

                return false;
            }
        );
    }

    private static function autoLink(): TwigFilter
    {
        return new TwigFilter(
            'auto_link',
            fn(string $text, string $type): string => match ($type) {
                'url' => '<a href="'.$text.'">'.$text.'</a>',
                'mail' => '<a href="mailto:'.$text.'">'.$text.'</a>',
                'tel' => '<a href="tel:'.$text.'">'.$text.'</a>',
                default => $text,
            }
        );
    }

    private static function templateUri(): TwigFunction
    {
        return new TwigFunction(
            'template_uri',
            fn(): string => get_template_directory_uri()
        );
    }

    private static function removeHtml(): TwigFilter
    {
        return new TwigFilter(
            'remove_html',
            function (?string $text): ?string {
                if (!$text) {
                    return $text;
                }

                return strip_tags($text);
            },
            [
                'is_safe' => ['html'],
            ]
        );
    }

    /**
     *
     * @return TwigFilter
     */
    public static function puriferHtml(): TwigFilter
    {
        return new TwigFilter(
            'puriferHtml',
            function (?string $content): ?string {
                return StringUtils::pureHtml($content);
            }
        );
    }

    private static function renderValuePivot(): TwigFilter
    {
        return new TwigFilter(
            'format_pivot_value',
            function (SpecData $specData): ?string {

                $value = match ($specData->type) {
                    SpecTypeEnum::BOOLEAN->value => '',
                    SpecTypeEnum::TEXTML->value => $specData->value,
                    SpecTypeEnum::STRINGML->value => $specData->value,
                    SpecTypeEnum::CURRENCY->value => $specData->value.' €',
                    SpecTypeEnum::DATE->value => $specData->value,
                    SpecTypeEnum::PHONE->value, SpecTypeEnum::GSM->value => '<a href="tel:'.$specData->value.'">'.$specData->value.'</a>',
                    SpecTypeEnum::EMAIL->value => '<a href="mailto:'.$specData->value.'">'.$specData->value.'</a>',
                    SpecTypeEnum::URL->value, SpecTypeEnum::URL_FACEBOOK->value, SpecTypeEnum::URL_TRIPADVISOR->value => '<a href="'.$specData->value.'">'.$specData->value.'</a>',
                    SpecTypeEnum::CHOICE->value, SpecTypeEnum::HCHOICE->value => false,
                    default => $specData->value
                };
                if ($value === false) {
                    $urnDefinitionRepository = PivotContainer::getUrnDefinitionRepository(WP_DEBUG);
                    if ($urnDefinition = $urnDefinitionRepository->findByUrn($specData->value)) {
                        $label = $urnDefinition->labelByLanguage('fr');
                        if ($label) {
                            return $label;
                        }
                    }
                }

                return $value;
            }, [
                'is_safe' => ['html'],
            ]
        );
    }

    /**
     * N'affiche pas en fr si urn commence par en ou nl
     * en:urn:fld:orc
     * @return TwigFilter
     */
    private static function checkDisplay(): TwigFilter
    {
        return new TwigFilter(
            'pivot_check_display',
            function (SpecData $specData): bool {
                if (in_array($specData->urn, UrnToSkip::urns)) {
                    return false;
                }
                $text = (new UnicodeString($specData->urn));
                if ($text->startsWith('urn')) {
                    return true;
                }

                return false;
            }
        );
    }

}
