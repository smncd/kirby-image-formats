# Kirby Image Formats

Kirby CMS Plugin.

Converts images to WebP on upload 🖼️

### ⚠️ As of writing, this project is very much in it's early stages of development, and not ready for general use. ⚠️

## Requirements

* Kirby CMS ^3.9

## Installation

[To be written]

<!--```bash
composer require smncd/kirby-image-formats
```

That's it! The plugin requires no setup or configuration to run. WebP images are generated on upload and stored in the `media` dir. -->

## Usage

### Panel

No setup is needed from the panel, just upload images as normal. The plugin automatically generates WebP versions of new uploads.

### Snippet
The plugin gives you access to the `picture` snippet, which can be used like so within your templates:

```php
<?= snippet('picture', [ 
    'image' => $image, 
    'alt' => 'This is the alt text!',
    'class' => 'banner',
]) ?>
```

The rendered HTML will be:

```html
<picture>
  <source 
    srcset="http://localhost:8000/media/pages/page/1a2b3c4d5e-1738447298/file-name.webp" 
    type="image/webp"
  >
  <img 
    class="banner" 
    src="http://localhost:8000/media/pages/page/1a2b3c4d5e-1738447298/file-name.jpg" 
    alt="This is the alt text!"
  >
</picture>
```

As you can see, the outputed picture tag uses both the converted WebP file, and original image as fallback.
This way, you won't need any .htacess rewrites, as the browser will automatically fall back to the original, if WebP is not supported.

If a converted image would be missing or not found, the plugin will create it on page load.

This also works natively with the [kirby-twig plugin](https://github.com/wearejust/kirby-twig):

```twig
{{ 
  snippet('picture', {
    image: page.image,
    alt: 'This is the alt text!',
    class: 'banner',
  }) 
}}
```

#### Snippet options

|Name |Type                                                                          |Required|
|--   |--                                                                            |--      |
|image|[Kirby\Cms\File](https://github.com/getkirby/kirby/blob/main/src/Cms/File.php)|yes     |
|alt  |string                                                                        |no      |
|class|string                                                                        |no      |

### Blocks

To use the new `picture` snippet in the default **image** and **gallery** blocks, you need to modify their snippets.

You can either write custom snippets, or copy the php files in the [example-snippets/blocks](./example-snippets/blocks/) folder to `./site/snippets/blocks` in your project (as they will not be enabled by default). If you are using Twig templates in your project, you can use the Twig snippets instead.

## To do:

### Support AVIF

I'm aiming to be able to introduce support for generating AVIF images as soon as this is more fesable on php. Right now it's a bit hit or miss.

### Config options

Make options to configure the plugin available in the Kirby `config.php` file.
Such as enable/disable, generated image quality, etc.

### General stability

Make sure the plugin is stable and works with all [installation methods](https://getkirby.com/docs/guide/plugins/plugin-setup-basic#the-three-plugin-installation-methods).

## Licensing and ownership

Copyright © 2023 Simon Lagerlöf

Licensed under the [Do No Harm License](./LICENSE).

Unless required by applicable law or agreed to in writing, software distributed under the License is
distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or
implied. See the License for the specific language governing permissions and limitations under the
License.

### Dependencies

This plugin depends on the following external packages:

* [**rosell-dk/webp-convert**](https://github.com/rosell-dk/webp-convert) - MIT