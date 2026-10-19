const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');
const source = fs.readFileSync(path.join(__dirname, '../assets/js/settings.js'), 'utf8');
const flush = () => new Promise(resolve => setImmediate(resolve));

function mount(checked = false, hasStylesheet = false) {
  let change;
  const calls = [];
  const checkbox = { checked, addEventListener(event, callback) { assert.equal(event, 'change'); change = callback; } };
  const select = { value: '', disabled: false, options: [], setAttribute() {}, replaceChildren(...options) { this.options = options; } };
  const status = { textContent: '' };
  const stylesheet = hasStylesheet ? { disabled: false } : null;
  const elements = { 'fonten-google-fonts-enabled': checkbox, 'fonten-family': select, 'fonten-admin-css': stylesheet, 'fonten-catalog-status': status };
  vm.runInNewContext(source, {
    window: { fontenSettings: { url: '/wp-admin/admin-ajax.php', nonce: 'test', loading: 'Loading', error: 'Retry', placeholder: 'Select' } },
    document: { getElementById(id) { return elements[id]; } },
    AbortController, URLSearchParams,
    Option: function(text, value) { this.text = text; this.value = value; },
    fetch(url, options) { return new Promise((resolve, reject) => calls.push({ url, options, resolve, reject })); },
  });
  return {
    checkbox, select, status, stylesheet, calls,
    toggle(value) { checkbox.checked = value; change(); },
    respond(index, fonts) { calls[index].resolve({ ok: true, json: async () => ({ success: true, data: { fonts } }) }); },
  };
}

test('no request on page load; checking immediately fetches and populates the select', async () => {
  const controls = mount();
  assert.equal(controls.calls.length, 0);
  assert.equal(controls.select.disabled, true);
  controls.toggle(true);
  assert.equal(controls.calls.length, 1);
  assert.equal(controls.calls[0].url, '/wp-admin/admin-ajax.php');
  assert.equal(controls.calls[0].options.body.get('consent'), '1');
  assert.equal(controls.calls[0].options.body.get('nonce'), 'test');
  assert.equal(controls.select.disabled, true);
  assert.equal(controls.status.textContent, 'Loading');
  controls.respond(0, ['Estedad', 'Roboto']);
  await flush();
  assert.equal(controls.select.disabled, false);
  assert.deepEqual(controls.select.options.map(option => option.value), ['', 'Estedad', 'Roboto']);
  assert.equal(controls.status.textContent, '');
});

test('unchecking aborts requests, disables CSS, and ignores late responses', async () => {
  const controls = mount(true, true);
  controls.toggle(false);
  assert.equal(controls.stylesheet.disabled, true);
  assert.equal(controls.select.disabled, true);
  controls.toggle(true);
  controls.toggle(false);
  assert.equal(controls.calls[0].options.signal.aborted, true);
  controls.respond(0, ['Roboto']);
  await flush();
  assert.equal(controls.select.options.length, 0);
  assert.equal(controls.select.disabled, true);
  assert.equal(controls.stylesheet.disabled, true);
});

test('a new checkbox request wins over an older response and preserves selection', async () => {
  const controls = mount();
  controls.select.value = 'Estedad';
  controls.toggle(true);
  controls.toggle(false);
  controls.toggle(true);
  controls.respond(1, ['Estedad', 'Roboto']);
  await flush();
  controls.respond(0, ['Old Font']);
  await flush();
  assert.deepEqual(controls.select.options.map(option => option.value), ['', 'Estedad', 'Roboto']);
  assert.equal(controls.select.value, 'Estedad');
});

test('failure displays a retry message and allows another checkbox request', async () => {
  const controls = mount();
  controls.toggle(true);
  controls.calls[0].reject(new Error('Offline'));
  await flush();
  assert.equal(controls.status.textContent, 'Retry');
  assert.equal(controls.select.disabled, false);
  controls.toggle(false);
  controls.toggle(true);
  assert.equal(controls.calls.length, 2);
  controls.respond(1, ['Roboto']);
  await flush();
  assert.equal(controls.status.textContent, '');
});

test('script safely exits when settings controls are absent', () => {
  assert.doesNotThrow(() => vm.runInNewContext(source, { window: {}, document: { getElementById() { return null; } } }));
});
