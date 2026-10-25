import { login, register, setToken, logout } from './api.js';

const root = document.getElementById('auth-forms');
const logoutBtn = document.getElementById('logout-btn');
if (logoutBtn) logoutBtn.addEventListener('click', logout);
if (root) initAuth();

function initAuth() {
  const msg = document.getElementById('auth-msg');
  const forms = { login: root.querySelector('#form-login'), register: root.querySelector('#form-register') };

  root.querySelectorAll('[data-show]').forEach((b) =>
    b.addEventListener('click', () => {
      Object.entries(forms).forEach(([k, f]) => (f.hidden = k !== b.dataset.show));
      msg.textContent = '';
      forms[b.dataset.show].querySelector('input').focus();
    })
  );

  Object.entries(forms).forEach(([kind, form]) =>
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = form.querySelector('button[type="submit"]');
      const data = Object.fromEntries(new FormData(form));
      btn.disabled = true;
      setMsg('Un momento…', 'info');

      let res;
      if (kind === 'register') {
        res = await register(data);
        if (res.ok) res = await login({ email: data.email, password: data.password });
      } else {
        res = await login(data);
      }

      if (res.ok) {
        const token = res.data.token ?? res.data.data?.token ?? res.data.access_token;
        if (!token) { setMsg('El servidor no devolvió un token.', 'error'); btn.disabled = false; return; }
        setToken(token);
        setMsg('¡Listo! Entrando…', 'success');
        location.href = '/dashboard';
        return;
      }
      setMsg(errorText(res), 'error');
      btn.disabled = false;
    })
  );

  function setMsg(text, type) { msg.textContent = text; msg.dataset.type = type; }
}

function errorText({ status, data }) {
  if (status === 422 && data.errors) return Object.values(data.errors).flat().join(' ');
  if (status === 401) return 'Correo o contraseña incorrectos.';
  if (status === 429) return 'Demasiados intentos. Espera un minuto.';
  return data.message ?? 'Ocurrió un error. Intenta de nuevo.';
}