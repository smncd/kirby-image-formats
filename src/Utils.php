<?php
declare(strict_types=1);

namespace KirbyImageFormats;

use Kirby\Cms\App as Kirby;
use Kirby\Cms\File;
use Kirby\Filesystem\F;

/**
 * Image utility class.
 * 
 * @author Simon Lagerlöf <contact@smn.codes>
 * @copyright Simon Lagerlöf
 * @license Do No Harm
 * 
 */
class Utils
{   
    /**
     * Get array of URL's for converted image.
     * 
     * @param Kirby $context
     * @param File $file
     * 
     * @return array
     */
    public static function getUrls(Kirby $context, File $file): array
    {
        $root = $context->root('index');

        $urls = [];

        foreach (Plugin::FORMATS as $format) {
            $filePath = self::_filePath($file, $format);

            if (!F::exists($root . $filePath)) {
                Plugin::hookFileCreateAfter($context, $file);
            } 
                
            $urls[$format] = $context->url('index') . $filePath;           
        }
        
        return $urls;
    }

    /**
     * Get array of filesystem paths for converted image.
     * 
     * @param Kirby $context
     * @param File $file
     * 
     * @return array
     */
    public static function getPaths(Kirby $context, File $file): array
    {
        $paths = [];

        foreach (Plugin::FORMATS as $format) {
            $paths[$format] = $context->root('index') . self::_filePath($file, $format);
        }
        
        return $paths;
    }

    /**
     * @param File $file
     * @param string $format
     * 
     * @return string
     */
    private static function _filePath(File $file, string $format): string
    {
        return '/_' . $format . '/'. $file->mediaToken() . '-' . $file->exif()->timestamp() . '/' . $file->name() . '.' . $format;
    }
}