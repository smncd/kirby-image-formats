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
use KirbyImageFormats\Plugin;

@include_once __DIR__ . '/vendor/autoload.php';

Kirby::plugin('smncd/kirby-image-formats', [
    'hooks' => [
        'file.create:after'  => fn (File $file) => Plugin::hook('file.create:after', $file),
        'file.replace:after' => fn (File $file) => Plugin::hook('file.replace:after', $file),
        'file.delete:after'  => fn (File $file) => Plugin::hook('file.delete:after', $file),
    ],
    'snippets' => [
        'picture' => __DIR__ . '/src/snippets/picture.php',
    ],
]);
