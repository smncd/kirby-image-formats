# Kirby Image Formats

Kirby CMS Plugin that converts images uploaded to the panel to WebP and AVIF.

### ⚠️ As of writing, this project is very much in it's early stages of development, and not ready for general use. ⚠️

## Requirements

* Kirby CMS ^3.9
* Composer (for now)
* PHP Imagick

## Installation

[To be written]

## Usage

### Panel

No setup is needed, just upload images as normal from the Kirby panel. The plugin automatically generates AVIF and WebP versions of new uploads.

### Snippet

The plugin provides access to the picture snippet, allowing you to integrate it into your templates as follows in PHP:

```php
<?= snippet('picture', [
    'image' => $image,
    'alt' => 'This is the alt text!',
    'class' => 'banner',
    'attr' => [
      'loading' => 'lazy'
    ]
]) ?>
```

The rendered HTML output will be:

```html
<picture>
  <source
    srcset="http://localhost:8000/media/pages/page/1a2b3c4d5e-1738447298/file-name.avif"
    type="image/avif"
  >
  <source
    srcset="http://localhost:8000/media/pages/page/1a2b3c4d5e-1738447298/file-name.webp"
    type="image/webp"
  >
  <img
    class="banner"
    src="http://localhost:8000/media/pages/page/1a2b3c4d5e-1738447298/file-name.jpg"
    alt="This is the alt text!"
    loading="lazy"
  >
</picture>
```
This picture tag incorporates both the converted AVIF and WebP image formats, with the original image serving as a fallback. No .htaccess rewrites are needed because browsers will automatically revert to the original format if AVIF or WebP is unsupported.

This functionality is also seamlessly compatible with the [kirby-twig plugin](https://github.com/wearejust/kirby-twig) in Twig templates:

```twig
{{
  snippet('picture', {
    image: page.image,
    alt: 'This is the alt text!',
    class: 'banner',
    attr: {
      loading: 'lazy'
    }
  })
}}
```

#### Snippet Configuration Options

|Name |Type                                                                          |Required|Description                                   |
|--   |--                                                                            |--      |--                                            |
|image|[Kirby\Cms\File](https://github.com/getkirby/kirby/blob/main/src/Cms/File.php)|yes     |The image file to be queried.                 |
|alt  |string                                                                        |no      |Easy access to the 'alt' attr.                |
|class|string                                                                        |no      |Easy access to the 'class' attr.              |
|attr |array ($key => $value)                                                        |no      |Additional HTML attributes as key-value pairs.|

### Blocks

To use the new `picture` snippet in the default **image** and **gallery** blocks, you need to modify their snippets.

You can either write custom snippets, or copy the php files in the [example-snippets/blocks](./example-snippets/blocks/) folder to `./site/snippets/blocks` in your project (as they will not be enabled by default).

If you are using Twig templates in your project, you can use the Twig snippets instead.

## To do:

### Fully support AVIF

AVIF support is generally a big dodgy in PHP right now (Oct. 2023), as far as I can tell due to lacking support from underlying libraries, such as Imagick or GD.

Generating AVIFs for JPGs seems to work without issues, but not so much for PNGs.

The workaround for now is to only convert JPGs to AVIF, and hope for better support with PNGs in the future.

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
