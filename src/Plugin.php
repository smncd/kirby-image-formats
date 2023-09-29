<?php
declare(strict_types=1);

namespace KirbyImageFormats;

use Exception;
use Kirby\Cms\File;
use Kirby\Filesystem\F;
use WebPConvert\WebPConvert;

/**
 * Main plugin class.
 * 
 * @author Simon Lagerlöf <contact@smn.codes>
 * @copyright Simon Lagerlöf
 * @license Do No Harm
 * 
 */
class Plugin 
{
    /**
     * Plugin name.
     * 
     * @var string
     */
    const NAME = 'kirby-image-formats';

    /**
     * Files that will be converted.
     * 
     * 
     * @var array<string>
     */
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png'];

    /**
     * Formats for image to be converted into.
     * 
     * @var array<string>
     */
    const FORMATS = ['avif', 'webp'];

    /**
     * After file has been created.
     * 
     * 
     * @param File $file
     * 
     * @return void
     */
    public static function hookFileCreateAfter(File $file): void
    {
        if (!in_array($file->extension(), self::ALLOWED_EXTENSIONS)) {
            return;
        }

        $fileNames = self::getPaths($file);

        self::_generateWebP($fileNames['webp'], $file);
        self::_generateAvif($fileNames['avif'], $file);
    }

    /**
     * After file has been replaced.
     * 
     * 
     * @param File $file
     * 
     * @return void
     */
    public static function hookFileReplaceAfter(File $file): void
    {
        self::hookFileCreateAfter($file);
    }

    /**
     * After file has been deleted.
     * 
     * 
     * @param File $file
     * 
     * @return void
     */
    public static function hookFileDeleteAfter(File $file): void
    {
        $fileNames = self::getPaths($file);

        foreach ($fileNames as $fileName) {
            $dir = F::dirname($fileName);

            if(F::exists($dir)) {
                F::remove($dir);
            }
        }
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
     * Get file path.
     * 
     * @param string $path
     * @param string $format
     * 
     * @return string
     */
    private static function _filePath(string $path, string $format): string
    {
        return F::dirname($path) . '/' . pathinfo(F::filename($path), PATHINFO_FILENAME) . '.' . $format;
    }

    /**
     * Generate WebP.
     * 
     * @see https://github.com/rosell-dk/webp-convert
     * 
     * @param string $destination
     * @param File $file
     * 
     * @return void
     */
    private static function _generateWebP(string $destination, File $file): void
    {
        try {
            $source = $file->contentFileDirectory() . '/' . $file->filename();

            WebPConvert::convert(
                $source,
                $destination
            );
            
        } catch (Exception $exception) {
            die($exception->getMessage());
        }
    }

    /**
     * Generate Avif.
     * 
     * It seems converting png's to avif is not very reliable, 
     * as the alpha channel is not carried through to the output. 
     * So, let's only convert jpg's to avif, for now.
     * 
     * @param string $destination
     * @param File $file
     * 
     * @return void
     */
    private static function _generateAvif(string $destination, File $file): void
    {
        if (!class_exists('Imagick') || !in_array($file->extension(), ['jpg', 'jpeg'])) {
            return;
        }
        
        try {      
            $source = $file->contentFileDirectory() . '/' . $file->filename();
    
            $image = new \Imagick($source);
            $image->setImageFormat('avif');
            $image->writeImage($destination);

        } catch (Exception $exception) {
            die($exception->getMessage());
        }
    }
}