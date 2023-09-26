<?php
declare(strict_types=1);

namespace KirbyImageFormats;

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
     * @param File $file
     * 
     * @return array
     */
    public static function getUrls(File $file): array
    {
        $urls = [];

        foreach (Plugin::FORMATS as $format) {
            $filePath = self::_filePath($file->url(), $format);

            if (!F::exists(self::_filePath($file->mediaRoot(), $format))) {
                Plugin::hookFileCreateAfter($file);
            } 
                
            $urls[$format] = $filePath;           
        }
        
        return $urls;
    }

    /**
     * Get array of filesystem paths for converted image.
     * 
     * @param File $file
     * 
     * @return array
     */
    public static function getPaths(File $file): array
    {
        $paths = [];

        foreach (Plugin::FORMATS as $format) {
            $paths[$format] = self::_filePath($file->mediaRoot(), $format);
        }
        
        return $paths;
    }

    /**
     * @param File $file
     * @param string $format
     * 
     * @return string
     */
    private static function _filePath(string $path, string $format): string
    {
        return F::dirname($path) . '/' . pathinfo(F::filename($path), PATHINFO_FILENAME) . '.' . $format;
    }
}