(function () {
  const endpoint = window.vibemagSettings?.restUrl || '/wp-json/wp/v2/search';

  function initHomeSlider() {
    const sliderElement = document.querySelector('.vibemag-home-slider');

    if (!sliderElement || typeof window.Swiper === 'undefined') {
      return;
    }

    // eslint-disable-next-line no-new
    new window.Swiper(sliderElement, {
      loop: false,
      slidesPerView: 1,
      spaceBetween: 24,
      pagination: {
        el: '.vibemag-home-slider .swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.vibemag-home-slider .swiper-button-next',
        prevEl: '.vibemag-home-slider .swiper-button-prev',
      },
    });
  }

  window.vibemagSearch = function vibemagSearch() {
    return {
      open: false,
      query: '',
      results: [],
      loading: false,
      controller: null,

      close() {
        this.open = false;
        this.query = '';
        this.results = [];
        this.loading = false;

        if (this.controller) {
          this.controller.abort();
          this.controller = null;
        }
      },

      async doSearch() {
        const term = this.query.trim();

        if (term.length < 3) {
          this.results = [];
          return;
        }

        if (this.controller) {
          this.controller.abort();
        }

        this.controller = new AbortController();
        this.loading = true;

        try {
          const url = `${endpoint}?search=${encodeURIComponent(term)}&type=post&per_page=8`;
          const response = await fetch(url, {
            signal: this.controller.signal,
            headers: {
              'X-WP-Nonce': window.vibemagSettings?.searchNonce || '',
            },
          });

          if (!response.ok) {
            throw new Error('Search request failed');
          }

          const data = await response.json();

          this.results = data.map((item) => ({
            id: item.id,
            title: item.title || 'Untitled',
            url: item.url,
          }));
        } catch (error) {
          if (error.name !== 'AbortError') {
            this.results = [];
          }
        } finally {
          this.loading = false;
        }
      },
    };
  };

  document.addEventListener('DOMContentLoaded', initHomeSlider);
})();
