<?php
declare(strict_types=1);

namespace KirbyImageFormats;

use Exception;
use Kirby\Cms\File;
use Kirby\Filesystem\F;
use Kirby\Filesystem\Dir;
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
    const FORMATS = ['webp'];

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

        $fileNames = Utils::getPaths($file);

        foreach ($fileNames as $fileName) {
            Dir::make(F::dirname(file: $fileName), true);
        }

        self::_generateWebP($fileNames['webp'], $file);
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
        $fileNames = Utils::getPaths($file);

        foreach ($fileNames as $fileName) {
            $dir = F::dirname($fileName);

            if(Dir::exists($dir)) {
                Dir::remove($dir);
                break;
            }
        }
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
    public static function _generateWebP(string $destination, File $file): void
    {
        try {
            if (in_array($file->extension(), self::ALLOWED_EXTENSIONS)) {
                $source = $file->contentFileDirectory() . '/' . $file->filename();

                WebPConvert::convert(
                    $source,
                    $destination
                );
            }
        } catch (Exception $exception) {
            die($exception->getMessage());
        }
    }
}