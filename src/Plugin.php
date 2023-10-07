<?php
declare(strict_types=1);

namespace Smncd\KirbyImageFormats;

use Exception;
use Kirby\Cms\App as Kirby;
use Kirby\Cms\File;
use Kirby\Cms\Pages;
use Kirby\Cms\Site;
use Kirby\Cms\Users;
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
     * Use for hooks in plugin config.
     *
     * @param string $hook
     * @param File $file
     *
     * @return void
     */
    public static function hook(string $hook, File $file): void
    {
        switch ($hook) {
            case 'file.create:after':
                self::generateImages($file);
                break;
            case 'file.replace:after':
                self::generateImages($file);
                break;
            case 'file.delete:after':
                self::deleteImages($file);
                break;
            default:
                break;
        }
    }

    /**
     * Generate images.
     *
     * @param File $file
     * @param bool $overwrite
     *
     * @return void
     */
    public static function generateImages(File $file, ?bool $overwrite = false): void
    {
        if (!in_array($file->extension(), self::ALLOWED_EXTENSIONS)) {
            return;
        }

        $fileNames = self::getImagePaths($file);

        $webp = $fileNames['webp'];
        $avif = $fileNames['avif'];

        if (($overwrite && F::exists($webp)) || !F::exists($webp)) {
            self::_generateWebP($webp, $file);
        }

        if (($overwrite && F::exists($avif)) || !F::exists($avif)) {
            self::_generateAvif($avif, $file);
        }
    }

    /**
     * Generate all images.
     *
     * @param Kirby $context
     * @param bool $overwrite
     *
     * @return void
     */
    public static function generateAllImages(Kirby $context, ?bool $overwrite = false): void
    {
        foreach (self::getAllImages($context, true) as $image) {
            $file = $image['file'];

            if (!isset($file)) {
                return;
            }

            if (!($file instanceof File)) {
                return;
            }

            self::generateImages($file, $overwrite);
        }
    }

    /**
     * Delete images.
     *
     * @param File $file
     *
     * @return void
     */
    public static function deleteImages(File $file): void
    {
        $fileNames = self::getImagePaths($file);

        foreach ($fileNames as $fileName) {
            if(F::exists($fileName)) {
                F::remove($fileName);
            }
        }
    }

     /**
     * Delete all images.
     *
     * @param Kirby $context
     *
     * @return void
     */
    public static function deleteAllImages(Kirby $context): void
    {
        foreach (self::getAllImages($context, true) as $image) {
            $file = $image['file'];

            if (!isset($file)) {
                return;
            }

            if (!($file instanceof File)) {
                return;
            }

            self::deleteImages($file);
        }
    }

    /**
     * Get array of URL's for converted image.
     *
     * @param File $file
     *
     * @return array
     */
    public static function getImageUrls(File $file): array
    {
        $urls = [];

        foreach (Plugin::FORMATS as $format) {
            if (!F::exists(self::_filePath($file->mediaRoot(), $format))) {
                continue;
            }

            $urls[$format] = self::_filePath($file->url(), $format);
        }

        return $urls;
    }

    /**
     * Get array of filesystem paths for converted image.
     *
     * @param File $file
     * @param bool $includeMissing
     *
     * @return array
     */
    public static function getImagePaths(File $file, ?bool $includeMissing = true): array
    {
        $paths = [];

        foreach (Plugin::FORMATS as $format) {
            $filePath = self::_filePath($file->mediaRoot(), $format);

            if (!$includeMissing && !F::exists($filePath)) {
                continue;
            }

            $paths[$format] = $filePath;
        }

        return $paths;
    }

    /**
     * Get array of all images, and if they have generated versions available.
     *
     * @param Kirby $context
     * @param bool $includeImage
     *
     * @return array
     */
    public static function getAllImages(Kirby $context, ?bool $includeImage = false): array
    {
        $images = [];

        $getImages = fn (Site|Users|Pages $source) => $source->files()->filterBy('type', '==', 'image');

        $sourceImages = [
            ...$getImages($context->site()),
            ...$getImages($context->site()->index()),
            ...$getImages($context->users()),
        ];

        foreach ($sourceImages as $image) {
            $generatedPaths = self::getImagePaths($image);
            $generatedUrls = self::getImageUrls($image);

            $out = [
                'name' => $image->filename(),
                'url' => $image->url(),
                'path' => $image->realpath(),
                'webp' => F::exists($generatedPaths['webp']) ? [
                    'path' => $generatedPaths['webp'],
                    'url' => $generatedUrls['webp'],
                ] : false,
                'avif' => F::exists($generatedPaths['avif']) ? [
                    'path' => $generatedPaths['avif'],
                    'url' => $generatedUrls['avif'],
                ] : false,
            ];

            if ($includeImage) {
                $out['file'] = $image;
            }

            $images[] = $out;
        }

        return $images;
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
