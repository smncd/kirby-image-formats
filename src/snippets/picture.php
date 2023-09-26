<?php
/**
 * Picture snippet.
 * 
 * @author Simon Lagerlöf <contact@smn.codes>
 * @copyright Simon Lagerlöf
 * @license Do No Harm
 */

declare(strict_types=1);

use KirbyImageFormats\Utils;

$class = isset($class) ? $class : '';

$alt = isset($alt) ? $alt : '';

if (!isset($image)) {
    return;
}

if (!$image->name()) {
    return;
}

$fileNames = Utils::getUrls($image);

?>

<picture>
    <?php foreach ($fileNames as $extension => $url): ?>
        <source srcset="<?= $url ?>" type="image/<?= $extension ?>" />
    <?php endforeach; ?>
    <img class="<?= $class ?>" src="<?= $image->url() ?>" alt="<?= $alt ?>" />
</picture>