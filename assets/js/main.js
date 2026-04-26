(function () {
  const endpoint = window.vibemagSettings?.restUrl || '/wp-json/wp/v2/search';
  const i18n = window.vibemagSettings?.i18n || {};

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




  function initI18nUI() {
    document.querySelectorAll('[data-i18n-text]').forEach((el) => {
      const key = el.getAttribute('data-i18n-text');
      if (key && i18n[key]) {
        el.textContent = i18n[key];
      }
    });

    document.querySelectorAll('[data-i18n-placeholder]').forEach((el) => {
      const key = el.getAttribute('data-i18n-placeholder');
      if (key && i18n[key]) {
        el.setAttribute('placeholder', i18n[key]);
      }
    });

    document.querySelectorAll('[data-i18n-aria-label]').forEach((el) => {
      const key = el.getAttribute('data-i18n-aria-label');
      if (key && i18n[key]) {
        el.setAttribute('aria-label', i18n[key]);
      }
    });
  }

  function initThemeMode() {
    const root = document.documentElement;
    const toggleButton = document.querySelector('[data-theme-toggle]');
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    const stored = localStorage.getItem('vibemag-theme');

    const applyTheme = (theme) => {
      root.setAttribute('data-theme', theme);
      if (toggleButton) {
        toggleButton.textContent = theme === 'dark' ? '☀️' : '🌙';
      }
    };

    const systemTheme = mediaQuery.matches ? 'dark' : 'light';
    applyTheme(stored || systemTheme);

    if (toggleButton) {
      toggleButton.addEventListener('click', () => {
        const current = root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem('vibemag-theme', next);
        applyTheme(next);
      });
    }

    mediaQuery.addEventListener('change', (event) => {
      if (localStorage.getItem('vibemag-theme')) {
        return;
      }
      applyTheme(event.matches ? 'dark' : 'light');
    });
  }

  function initBackToTop() {
    const button = document.getElementById('vibemag-back-to-top');

    if (!button) {
      return;
    }

    const toggleVisibility = () => {
      if (window.scrollY > 500) {
        button.classList.add('is-visible');
      } else {
        button.classList.remove('is-visible');
      }
    };

    window.addEventListener('scroll', toggleVisibility, { passive: true });
    toggleVisibility();

    button.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
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
            title: item.title || i18n.untitled || 'Untitled',
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

  document.addEventListener('DOMContentLoaded', () => {
    initI18nUI();
    initHomeSlider();
    initThemeMode();
    initBackToTop();
  });
})();
