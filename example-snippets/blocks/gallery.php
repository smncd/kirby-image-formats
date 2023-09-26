<?php
declare(strict_types=1);

use Kirby\Cms\Html;

$caption = $block->caption();
$crop    = $block->crop()->isTrue();
$ratio   = $block->ratio()->or('auto');

?>
<figure<?= Html::attr(['data-ratio' => $ratio, 'data-crop' => $crop], null, ' ') ?>>
  <ul>
    <?php foreach ($block->images()->toFiles() as $image): ?>
    <li>
        <?php snippet('picture', [ 'image' => $image ]) ?>
    </li>
    <?php endforeach ?>
  </ul>
  <?php if ($caption->isNotEmpty()): ?>
  <figcaption>
    <?= $caption ?>
  </figcaption>
  <?php endif ?>
</figure>