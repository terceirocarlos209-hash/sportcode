/**
 * Sportscore API Client
 * Wrapper centralizado para todas as chamadas REST
 */
window.sportscore = (() => {
  const base = (window.sseConfig && window.sseConfig.apiBase)
    ? window.sseConfig.apiBase
    : '/wp-json/sportscore/v1';

  const nonce = (window.sseConfig && window.sseConfig.nonce) || '';

  async function get(path) {
    const res = await fetch(base + path, {
      method: 'GET',
      cache: 'no-store',
      headers: {
        'Accept': 'application/json',
        'X-WP-Nonce': nonce,
        'X-Requested-With': 'XMLHttpRequest',
      },
    });

    if (!res.ok) throw new Error(`HTTP ${res.status} — ${path}`);

    const json = await res.json();

    // Normaliza: endpoint retorna {success, data} ou array direto
    if (json && typeof json === 'object' && 'data' in json) {
      return json.data;
    }
    return json;
  }

  function escapeHtml(str) {
    return String(str ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  return { get, escapeHtml };
})();
