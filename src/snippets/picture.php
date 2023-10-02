<?php
/**
 * Picture snippet.
 * 
 * @author Simon Lagerlöf <contact@smn.codes>
 * @copyright Simon Lagerlöf
 * @license Do No Harm
 */

declare(strict_types=1);

use KirbyImageFormats\Plugin;

$class = isset($class) ? $class : '';

$alt = isset($alt) ? $alt : '';

$fileNames = Plugin::getImageUrls($image) ?: [];

?>

<?php if (isset($image) && $image->url()): ?>
    <picture>
        <?php foreach ($fileNames as $extension => $url): ?>
            <source srcset="<?= $url ?>" type="image/<?= $extension ?>" />
        <?php endforeach ?>
        <img class="<?= $class ?>" src="<?= $image->url() ?>" alt="<?= $alt ?>" />
    </picture>
<?php endif ?>