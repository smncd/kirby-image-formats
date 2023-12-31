<?php
/**
 * Main plugin config.
 *
 * @author Simon Lagerlöf <contact@smn.codes>
 * @copyright Simon Lagerlöf
 * @license Do No Harm
 *
 * Licensed under the Do No Harm License, Version 0.3 (the "License"); you may not use this file except
 * in compliance with the License. You may obtain a
 * [copy](https://github.com/raisely/NoHarm/blob/publish/licenses/Apache-2.0-NoHarm.md) of the License.
 *
 * Unless required by applicable law or agreed to in writing, software distributed under the License is
 * distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
 * implied. See the License for the specific language governing permissions and limitations under the
 * License.
 */

declare(strict_types=1);

use Kirby\Cms\App as Kirby;
use Kirby\Cms\File;
use Smncd\KirbyImageFormats\Plugin;

@include_once Kirby::instance()->root('base') . '/vendor/autoload.php';

Kirby::plugin('smncd/kirby-image-formats', [
    'hooks' => [
        'file.create:after'  => fn (File $file) => Plugin::hook('file.create:after', $file),
        'file.replace:after' => fn (File $file) => Plugin::hook('file.replace:after', $file),
        'file.delete:after'  => fn (File $file) => Plugin::hook('file.delete:after', $file),
    ],
    'snippets' => [
        'picture' => __DIR__ . '/src/snippets/picture.php',
    ],
    'areas' => [
        'image-formats' => fn (Kirby $kirby) => [
            'label' => 'Images',
            'icon' => 'image',
            'breadcrumbLabel' => 'Images',
            'menu' => true,
            'link' => 'image-formats',
            'views' => [
                [
                    'pattern' => 'image-formats',
                    'action'  => fn () => [
                        'component' => 'k-images-view',
                        'title' => 'Images',
                        'props' => [
                            'api' => [
                                'generateImages' => $kirby->url('api') . '/generate-images',
                                'deleteImages' => $kirby->url('api') . '/delete-images',
                                'csrf' => csrf(),
                            ],
                            'images' => Plugin::getAllImages($kirby),
                        ],
                    ],
                ],
            ],
        ],
    ],
    'api' => [
        'routes' => fn (Kirby $kirby) => [
            [
                'pattern' => 'generate-images',
                'method' => 'POST',
                'action'  => function () use ($kirby) {
                    $overwrite = $kirby->request()->body()->get('overwrite', false);
                    $image = $kirby->request()->body()->get('image', null);

                    if(isset($image)) {
                        $file = null;

                        foreach (Plugin::getAllImages($kirby, true) as $item) {
                            if ($item['path'] === $image) {
                                $file = $item['file'];
                                break;
                            }
                        }

                        if($file instanceof File) {
                            Plugin::generateImages($file);
                        }
                    } else {
                        Plugin::generateAllImages($kirby, $overwrite);
                    }

                    return Plugin::getAllImages($kirby);
                },
            ],
            [
                'pattern' => 'delete-images',
                'method' => 'POST',
                'action'  => function () use ($kirby) {
                    Plugin::deleteAllImages($kirby);

                    return Plugin::getAllImages($kirby);
                },
            ],
        ],
    ],
]);
