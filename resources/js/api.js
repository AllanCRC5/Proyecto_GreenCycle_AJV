// Capa de acceso al API. Cambie USE_MOCK a false cuando el backend esté listo.
export const USE_MOCK = true;
const BASE = '/api/v1';

export const getToken = () => localStorage.getItem('token');
export const setToken = (t) => localStorage.setItem('token', t);
export const clearToken = () => localStorage.removeItem('token');

// Devuelve siempre { ok, status, data }. Fetch NO lanza error con 409/422, por eso se revisa aquí.
// skipAuth: true para login/registro (no llevan token y un 401 no debe redirigir).
async function request(path, options = {}) {
  const { skipAuth, ...opts } = options;
  const headers = { Accept: 'application/json', 'Content-Type': 'application/json' };
  if (!skipAuth && getToken()) headers.Authorization = `Bearer ${getToken()}`;
  try {
    const res = await fetch(BASE + path, { ...opts, headers });
    const data = await res.json().catch(() => ({}));
    if (res.status === 401 && !skipAuth) { // sesión vencida o sin token
      clearToken();
      location.href = '/login';
    }
    return { ok: res.ok, status: res.status, data };
  } catch {
    return { ok: false, status: 0, data: { message: 'Sin conexión con el servidor.' } };
  }
}

// ---------- MOCK (datos falsos para trabajar sin backend) ----------
const MOCK_COOLDOWN_MS = 10_000; // 10 s para poder probar; en real son 60 min
const REQUIRED = 5;
const mockTrees = [
  { id: 1, seed_type: 'Roble', level: 2, health: 60, progress: 3, progress_required: REQUIRED, status: 'ACTIVE', next_care_at: null },
  { id: 2, seed_type: 'Pino', level: 10, health: 100, progress: 0, progress_required: REQUIRED, status: 'MATURE', next_care_at: null },
  { id: 3, seed_type: 'Cedro', level: 1, health: 0, progress: 2, progress_required: REQUIRED, status: 'DEAD', next_care_at: null },
];
const wait = (v) => new Promise((r) => setTimeout(() => r(v), 400));

function mockCare(id) {
  const t = mockTrees.find((x) => x.id === id);
  if (!t) return { ok: false, status: 404, data: { code: 'TREE_NOT_FOUND', message: 'Árbol no encontrado.' } };
  if (t.status !== 'ACTIVE')
    return { ok: false, status: 409, data: { code: 'TREE_NOT_ACTIVE', message: 'Este árbol ya no puede recibir cuidados.' } };
  if (t.next_care_at && Date.now() < Date.parse(t.next_care_at))
    return { ok: false, status: 409, data: { code: 'CARE_COOLDOWN', message: 'El árbol aún no puede recibir cuidados.', retry_at: t.next_care_at } };
  t.health = Math.min(100, t.health + 20);
  t.progress += 1;
  if (t.progress >= t.progress_required) { t.level += 1; t.progress = 0; }
  if (t.level >= 10) t.status = 'MATURE';
  t.next_care_at = new Date(Date.now() + MOCK_COOLDOWN_MS).toISOString();
  return { ok: true, status: 200, data: { data: { ...t } } };
}

// ---------- Funciones públicas ----------
export const getTrees = () =>
  USE_MOCK ? wait({ ok: true, status: 200, data: { data: mockTrees.map((t) => ({ ...t })) } }) : request('/trees');

export const getTree = (id) =>
  USE_MOCK
    ? wait({ ok: true, status: 200, data: { data: { ...mockTrees.find((t) => t.id === id) } } })
    : request(`/trees/${id}`);

export const careTree = (id) =>
  USE_MOCK ? wait(mockCare(id)) : request(`/trees/${id}/care`, { method: 'POST' });

// ---------- AUTENTICACIÓN ----------
function mockAuth(kind, d) {
  const errors = {};
  if (!d.email?.includes('@')) errors.email = ['Escribe un correo válido.'];
  if ((d.password ?? '').length < 8) errors.password = ['La contraseña debe tener al menos 8 caracteres.'];
  if (kind === 'register') {
    if (!d.name?.trim()) errors.name = ['El nombre es obligatorio.'];
    if (d.password !== d.password_confirmation) errors.password = ['Las contraseñas no coinciden.'];
  }
  if (Object.keys(errors).length)
    return { ok: false, status: 422, data: { message: 'Datos no válidos.', errors } };
  return { ok: true, status: kind === 'register' ? 201 : 200, data: { token: 'mock-token' } };
}

export const register = (d) =>
  USE_MOCK ? wait(mockAuth('register', d))
           : request('/register', { method: 'POST', body: JSON.stringify(d), skipAuth: true });

export const login = (d) =>
  USE_MOCK ? wait(mockAuth('login', d))
           : request('/login', { method: 'POST', body: JSON.stringify(d), skipAuth: true });

export async function logout() {
  if (!USE_MOCK) await request('/logout', { method: 'POST' });
  clearToken();
  location.href = '/login';
}