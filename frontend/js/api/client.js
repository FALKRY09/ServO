const API_BASE_URL = 'http://127.0.0.1:8000/api';

export async function api(path, options = {}) {
  const headers = { Accept: 'application/json', ...(options.headers || {}) };
  const token = sessionStorage.getItem('servo_token');
  if (token) headers.Authorization = `Bearer ${token}`;
  if (options.body && !(options.body instanceof FormData)) headers['Content-Type'] = 'application/json';
  console.debug('[ServO API] request', options.method || 'GET', path, { hasToken: Boolean(token) });
  const response = await fetch(`${API_BASE_URL}${path}`, { ...options, headers });
  const data = await response.json().catch(() => ({}));
  console.debug('[ServO API] response', response.status, path);
  if (!response.ok) { const error = new Error(data.message || `Request gagal (${response.status}).`); error.status = response.status; error.path = path; throw error; }
  return data;
}

export async function download(path) {
  const token = sessionStorage.getItem('servo_token');
  const headers = { Accept: 'application/octet-stream' };
  if (token) headers.Authorization = `Bearer ${token}`;
  const response = await fetch(`${API_BASE_URL}${path}`, { headers });
  if (!response.ok) {
    const data = await response.json().catch(() => ({}));
    throw new Error(data.message || 'Download gagal.');
  }
  const disposition = response.headers.get('Content-Disposition') || '';
  const match = disposition.match(/filename="?([^";]+)"?/i);
  return { blob: await response.blob(), filename: match?.[1] || 'lampiran' };
}
