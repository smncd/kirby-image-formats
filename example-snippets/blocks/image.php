<?php
declare(strict_types=1);

use Kirby\Cms\Html;
use Kirby\Toolkit\Str;

/** 
 * @var \Kirby\Cms\Block $block 
 */
$alt     = $block->alt();
$caption = $block->caption();
$crop    = $block->crop()->isTrue();
$link    = $block->link();
$ratio   = $block->ratio()->or('auto');
$src     = null;

$isWeb = $block->location() == 'web';

if ($isWeb) {
    $src = $block->src()->esc();
} elseif ($image = $block->image()->toFile()) {
    $alt = $alt->or($image->alt());
}

?>
<?php if ($src || $image->url()): ?>
    <figure<?= Html::attr(['data-ratio' => $ratio, 'data-crop' => $crop], null, ' ') ?>>
        <?php if ($link->isNotEmpty()): ?>
            <a href="<?= Str::esc($link->toUrl()) ?>">
                <?php if($isWeb): ?>
                    <img src="<?= $src ?>" alt="<?= $alt->esc() ?>">
                <?php else: ?>
                    <?= snippet('picture', [ 'image' => $image, 'alt' => $alt->esc() ]) ?>
                <?php endif ?>
            </a>
        <?php else: ?>
            <?php if($isWeb): ?>
                <img src="<?= $src ?>" alt="<?= $alt->esc() ?>">
            <?php else: ?>
                <?= snippet('picture', [ 'image' => $image, 'alt' => $alt->esc() ]) ?>
            <?php endif ?>
        <?php endif ?>
        
        <?php if ($caption->isNotEmpty()): ?>
            <figcaption>
                <?= $caption ?>
            </figcaption>
        <?php endif ?>
    </figure>
<?php endif ?>
