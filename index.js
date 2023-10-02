panel.plugin('smncd/kirby-image-formats', {
  components: {
    'k-images-view': {
      props: {
        images: Array,
        api: Object,
      },
      data() {
        return {
          loading: false,
          columns: {
            name: {
              label: 'Image',
              mobile: true,
              width: '40%',
            },
            webp: {
              label: 'WebP',
              width: '4rem',
              mobile: true,
            },
            avif: {
              label: 'AVIF',
              width: '4rem',
              mobile: true,
            },
            url: {
              label: 'Link',
              width: '100%',
            },
          },
          rows: this.formatRows(this.images),
        };
      },
      methods: {
        async generateImages() {
          this.loading = true;

          const res = await fetch(this.api.generateImages, {
            headers: {
              'X-CSRF': this.api.csrf
            }
          });

          const data = await res.json();

          this.rows = this.formatRows(data)

          this.loading = false;
        },
        formatRows(rows) {
          return rows.map(image => ({
            name: image.name,
            webp: image.webp ? '✅' : '❌',
            avif: image.avif ? '✅' : '❌',
            url: image.url,
          }))
        }
      },
      template: `
        <k-inside>
          <k-view>
            <k-header>Images</k-header>
            <template v-if="loading">
              <k-loader />
              <br />
              <k-text align="center">
                Loading... Do not close this window.
              </k-text>
            </template>
            <template v-else>
              <k-button-group>
                <k-button icon="image" theme="positive" @click="generateImages()">Generate missing images</k-button>
                <k-button icon="refresh" @click="generateImages()">Regenerate <strong>all</strong> images</k-button>
              </k-button-group>
              <k-box theme="info" text="This table provides an encompassing view of all the images available to Kirby, along with information regarding the presence of WebP and AVIF versions." />
              <br />
              <k-table
                :columns="columns"
                :rows="rows"
                empty="No images available"
              />
            </template>
          </k-view>
        </k-inside>
      `,
    },
  },
});
