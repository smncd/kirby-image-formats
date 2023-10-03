/**
 * Plugin panel script.
 *
 * @author Simon Lagerlöf <contact@smn.codes>
 * @copyright Simon Lagerlöf
 * @license Do No Harm
 */

panel.plugin('smncd/kirby-image-formats', {
  components: {
    'k-regenerate-image-field-preview': {
      props: {
        row: Object,
      },
      data() {
        return this.row.regenerate;
      },
      methods: {
        async regenerateImage(path) {
          if (typeof path !== 'string') {
            return;
          }

          await this.row.regenerate.apiRequest('regenerate-images', path);
        }
      },
      template: `
        <div class="k-html-field-preview"  title="Regenerate images">
          <k-button icon="refresh" @click="regenerateImage(path)"></k-button>
        </div>
      `,
    },
    'k-images-view': {
      props: {
        images: Array,
        api: Object,
      },
      data() {
        return {
          loading: false,
          error: false,
          columns: {
            name: {
              label: 'Image',
              mobile: true,
              width: '40%',
              type: 'html',
            },
            webp: {
              label: 'WebP',
              width: '4rem',
              mobile: true,
              type: 'html',
            },
            avif: {
              label: 'AVIF',
              width: '4rem',
              mobile: true,
              type: 'html',
            },
            url: {
              label: 'Link',
              width: '100%',
              type: 'html',
            },
            regenerate: {
              label: ' ',
              width: '2.5rem',
              type: 'regenerate-image',
            },
          },
          rows: this.formatRows(this.images),
        };
      },
      methods: {
        async apiRequest(action, image = undefined) {
          let endpoint = '';

          switch (action) {
            case 'generate-images':
            case 'regenerate-images':
              endpoint = this.api.generateImages;
              break;
            case 'delete-images':
              endpoint = this.api.deleteImages;
              break
            default:
              return;
          }

          try {
            this.loading = true;

            const res = await fetch(endpoint, {
              method: 'POST',
              headers: {
                'X-CSRF': this.api.csrf,
              },
              body: JSON.stringify({
                overwrite: action === 'regenerate-images',
                image: image
              })
            });

            const data = await res.json();

            this.rows = this.formatRows(data)

            this.loading = false;
          } catch (error) {
            this.error = true;
            this.loading = false;
          }
        },
        formatRows(rows) {
          return rows.map(image => ({
            name: image.name,
            webp: image.webp ? '✅' : '❌',
            avif: image.avif ? '✅' : image.name.endsWith('png') ? '<i title="AVIFs are currently not supported for PNGs">❔</i>' : '❌',
            url: `<a href="${image.url}">${image.url}</a>`,
            regenerate: {
              path: image.path,
              api: this.api,
              apiRequest: this.apiRequest
            }
          }))
        }
      },
      template: `
        <k-inside>
          <k-view>
            <k-header>Images</k-header>
            <template v-if="error">
              <k-box theme="negative" text="An error occured!"/>
            </template>
            <template v-if="loading">
              <k-loader />
              <br />
              <k-text align="center">
                Loading... Do not close this window.
              </k-text>
            </template>
            <template v-else>
              <k-button-group>
                <k-button icon="image" theme="positive" @click="apiRequest('generate-images')">Generate missing images</k-button>
                <k-button icon="refresh" @click="apiRequest('regenerate-images')">Regenerate <strong>all</strong> images</k-button>
                <k-button icon="trash" theme="negative" @click="apiRequest('delete-images')">Delete generated images</k-button>
              </k-button-group>
              <k-box theme="info" text="This table provides an encompassing view of all the images available to Kirby, along with information regarding the presence of WebP and AVIF versions." />
              <br />
              <k-table
                :columns="columns"
                :rows="rows"
                empty="No images available"
              >
                <template #regenerate="{ row }">
                  <div>AAAAAAAAAa</div>
                </template>
              </k-table>
            </template>
          </k-view>
        </k-inside>
      `,
    },
  },
});
