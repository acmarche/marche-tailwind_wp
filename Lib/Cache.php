<?php

namespace AcMarche\MarcheTail\Lib;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\UnicodeString;
use Symfony\Contracts\Cache\CacheInterface;
use const ABSPATH;

class Cache
{
    public const MENU_NAME = 'menu-top';
    public const ICONES_NAME = 'icones-home';
    public const EVENTS = 'events';
    public const OFFRES = 'offres';
    public const OFFRE = 'offre';
    public const SEE_ALSO_OFFRES = 'see_also_offre';
    public const FETCH_OFFRES = 'fetch_offres';
    public static ?CacheInterface $instanceObject = null;

    public static function getPathCache(string $folder): string
    {
        return ABSPATH.'../var/cache/'.$folder;
    }

    public static function instance(): CacheInterface
    {
        if (null !== self::$instanceObject) {
            return self::$instanceObject;
        }

        if (!isset($_ENV['APP_CACHE_DIR'])) {
            (new Dotenv())
                ->bootEnv(ABSPATH.'.env');
        }

        self::$instanceObject =
            new FilesystemAdapter(
                '_visit',
                43200,
                $_ENV['APP_CACHE_DIR']
            );

        return self::$instanceObject;
    }

    public static function refresh(string $code): void
    {
        $request = Request::createFromGlobals();
        $refresh = $request->get('refresh', null);

        $cache = self::instance();
        if ($refresh) {
            $cache->delete($code);
        }
    }

    public static function generateCodeBottin(int $blogId, string $slug): string
    {
        return 'bottin-fiche-'.$blogId.'-'.$slug;
    }

    public static function generateCodeArticle(int $blogId, int $postId): string
    {
        return 'post-'.$blogId.'-'.$postId;
    }

    public static function generateCodeCategory(int $blogId, int $categoryId): string
    {
        return 'category-'.$blogId.'-'.$categoryId;
    }
    public static function generateKey(string $cacheKey): string
    {
        $keyUnicode = new UnicodeString($cacheKey);

        return sanitize_title($keyUnicode->ascii()->toString());
    }

}