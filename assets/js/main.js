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

    document.querySelectorAll('[data-i18n-alt]').forEach((el) => {
      const key = el.getAttribute('data-i18n-alt');
      if (key && i18n[key]) {
        el.setAttribute('alt', i18n[key]);
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

  function initSearchUI() {
    const root = document.querySelector('[data-search-root]');

    if (!root) {
      return;
    }

    const openButton = root.querySelector('[data-search-open]');
    const closeButton = root.querySelector('[data-search-close]');
    const overlay = root.querySelector('[data-search-overlay]');
    const input = root.querySelector('[data-search-input]');
    const loading = root.querySelector('[data-search-loading]');
    const results = root.querySelector('[data-search-results]');
    const empty = root.querySelector('[data-search-empty]');

    if (!openButton || !closeButton || !overlay || !input || !loading || !results || !empty) {
      return;
    }

    let controller = null;
    let debounceTimer = null;

    const setOpen = (isOpen) => {
      overlay.classList.toggle('is-open', isOpen);
      if (isOpen) {
        input.focus();
      }
    };

    const clearResults = () => {
      results.innerHTML = '';
      results.hidden = true;
      empty.hidden = true;
      loading.hidden = true;
    };

    const close = () => {
      setOpen(false);
      input.value = '';
      clearResults();
      if (controller) {
        controller.abort();
        controller = null;
      }
    };

    const renderResults = (items) => {
      if (!items.length) {
        results.hidden = true;
        empty.hidden = false;
        return;
      }

      results.innerHTML = items
        .map((item) => `<li><a href="${item.url}">${item.title || i18n.untitled || 'Untitled'}</a></li>`)
        .join('');
      results.hidden = false;
      empty.hidden = true;
    };

    const search = async () => {
      const term = input.value.trim();
      if (term.length < 3) {
        clearResults();
        return;
      }

      if (controller) {
        controller.abort();
      }

      controller = new AbortController();
      loading.hidden = false;
      empty.hidden = true;

      try {
        const url = `${endpoint}?search=${encodeURIComponent(term)}&type=post,page&per_page=8`;
        const response = await fetch(url, {
          signal: controller.signal,
          headers: {
            'X-WP-Nonce': window.vibemagSettings?.searchNonce || '',
          },
        });

        if (!response.ok) {
          throw new Error('Search request failed');
        }

        const data = await response.json();
        renderResults(Array.isArray(data) ? data : []);
      } catch (error) {
        if (error.name !== 'AbortError') {
          clearResults();
        }
      } finally {
        loading.hidden = true;
      }
    };

    openButton.addEventListener('click', () => setOpen(true));
    closeButton.addEventListener('click', close);

    overlay.addEventListener('click', (event) => {
      if (event.target === overlay) {
        close();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
        close();
      }
    });

    input.addEventListener('input', () => {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(search, 300);
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initI18nUI();
    initHomeSlider();
    initThemeMode();
    initBackToTop();
    initSearchUI();
  });
})();
