(function () {
  'use strict';

  const checkbox = document.getElementById('fonten-google-fonts-enabled');
  const select = document.getElementById('fonten-family');
  const status = document.getElementById('fonten-catalog-status');
  const config = window.fontenSettings;

  if (!checkbox || !select) {
    return;
  }

  let request;
  let requestId = 0;
  let loading = false;

  function syncFontControls() {
    select.disabled = !checkbox.checked || loading;
    select.setAttribute('aria-busy', loading ? 'true' : 'false');
    const stylesheet = document.getElementById('fonten-admin-css');
    if (stylesheet) {
      stylesheet.disabled = !checkbox.checked;
    }
  }

  async function loadFonts() {
    if (!config) {
      return;
    }

    const id = ++requestId;
    const controller = new AbortController();
    request = controller;
    loading = true;
    if (status) {
      status.textContent = config.loading;
    }
    syncFontControls();

    try {
      const response = await fetch(config.url, {
        method: 'POST',
        credentials: 'same-origin',
        signal: controller.signal,
        body: new URLSearchParams({ action: 'fonten_get_fonts', nonce: config.nonce, consent: '1' }),
      });
      const result = await response.json();
      if (!response.ok || !result.success || !Array.isArray(result.data.fonts)) {
        throw new Error('Catalog unavailable');
      }
      if (id !== requestId || !checkbox.checked) {
        return;
      }

      const previous = select.value;
      const options = [new Option(config.placeholder, '')];
      result.data.fonts.forEach(function (family) {
        options.push(new Option(family, family));
      });
      select.replaceChildren(...options);
      select.value = result.data.fonts.includes(previous) ? previous : '';
      if (status) {
        status.textContent = '';
      }
    } catch (error) {
      if (id === requestId && checkbox.checked && error.name !== 'AbortError' && status) {
        status.textContent = config.error;
      }
    } finally {
      if (id === requestId) {
        request = null;
        loading = false;
        syncFontControls();
      }
    }
  }

  checkbox.addEventListener('change', function () {
    if (request) {
      request.abort();
    }
    ++requestId;
    loading = false;
    if (status) {
      status.textContent = '';
    }
    syncFontControls();
    if (checkbox.checked) {
      loadFonts();
    }
  });
  syncFontControls();
})();
