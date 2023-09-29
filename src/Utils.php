<?php
declare(strict_types=1);

namespace KirbyImageFormats;

use Exception;
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
    public static function commandExists(string $command): bool
    {
        return function_exists('exec') && !!`which $command`;
    }
    
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

            if (!F::exists(self::_filePath($file->mediaRoot(), $format))) {
                
                if ($format === 'avif' && $file->extension() === 'png') {
                    continue;
                }

                try {
                    Plugin::hookFileCreateAfter($file);
                } catch (Exception $exception) {
                    continue;
                }
            } 
                
            $urls[$format] = self::_filePath($file->url(), $format);           
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
     * @param string $path
     * @param string $format
     * 
     * @return string
     */
    private static function _filePath(string $path, string $format): string
    {
        return F::dirname($path) . '/' . pathinfo(F::filename($path), PATHINFO_FILENAME) . '.' . $format;
    }
}