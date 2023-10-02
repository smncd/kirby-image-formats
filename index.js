panel.plugin('smncd/kirby-image-formats', {
  components: {
    'k-images-view': {
      props: {
        images: Array,
      },
      data() {
        return {
          columns: {
            image: {
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
          rows: this.images,
        };
      },
      template: `
        <k-inside>
          <k-view>
            <k-header>Images</k-header>
            <k-box theme="info" text="This table provides an encompassing view of all the images available to Kirby, along with information regarding the presence of WebP and AVIF versions." />
            <br />
            <k-table
              :columns="columns"
              :rows="rows"
              empty="No images available"
            />
          </k-view>
        </k-inside>
      `,
    },
  },
});
