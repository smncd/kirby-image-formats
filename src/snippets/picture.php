<?php
/**
 * Picture snippet.
 *
 * @author Simon Lagerlöf <contact@smn.codes>
 * @copyright Simon Lagerlöf
 * @license Do No Harm
 */

declare(strict_types=1);

use Smncd\KirbyImageFormats\Plugin;
use Kirby\Cms\Html;

$attr = isset($attr) && is_array($attr) ? $attr : [];

if (isset($class)) {
    if (is_array($class)) {
        $class = implode(' ', $class);
    }

    if (is_string($class)) {
        $attr['class'] = $class;
    }
}

if (isset($alt) && is_string($alt)) {
    $attr['alt'] = $alt;
}

$fileNames = Plugin::getImageUrls($image) ?: [];

?>

<?php if (isset($image) && $image->url()): ?>
    <picture>
        <?php foreach ($fileNames as $extension => $url): ?>
            <source srcset="<?= $url ?>" type="image/<?= $extension ?>" />
        <?php endforeach ?>
        <img src="<?= $image->url() ?>" <?= Html::attr($attr) ?>/>
    </picture>
<?php endif ?>
