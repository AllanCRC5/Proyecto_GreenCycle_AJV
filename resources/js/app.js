import { getTrees, getTree, careTree } from './api.js';
import { renderTree } from './treeCard.js';
import { startCountdown } from './countdown.js';

//
const statusElement = document.querySelector("[data-app-status]");
if (statusElement) {
    statusElement.textContent = "Laravel, JavaScript y Vite están funcionando.";
}

const grid = document.getElementById('tree-grid');

const stoppers = new Map(); // id -> función para detener su contador
const msg = document.getElementById('status-msg');

function say(text, type = 'info') {
  msg.textContent = text;
  msg.dataset.type = type;
}

function errorText({ status, data }) {
  if (status === 401) return 'Tu sesión expiró. Inicia sesión de nuevo.';
  if (status === 403 || status === 404) return 'No se encontró ese árbol.';
  if (status === 409 && data.code === 'CARE_COOLDOWN') return 'Aún no puedes cuidar este árbol. Espera al contador.';
  if (status === 409) return data.message ?? 'Esta acción no está permitida ahora.';
  if (status === 422) return 'Los datos enviados no son válidos.';
  return data.message ?? 'Ocurrió un error. Intenta de nuevo.';
}

// Pinta (o repinta) una tarjeta y arranca su contador si hay cooldown.
function paint(tree) {
  stoppers.get(tree.id)?.();
  const card = renderTree(tree);
  const old = grid.querySelector(`[data-id="${tree.id}"]`);
  old ? old.replaceWith(card) : grid.append(card);

  const btn = card.querySelector('[data-action="care"]');
  const timer = card.querySelector('.tree-card__timer');
  btn.addEventListener('click', () => onCare(tree.id));

  if (tree.status === 'ACTIVE' && tree.next_care_at && Date.parse(tree.next_care_at) > Date.now()) {
    btn.disabled = true;
    const stop = startCountdown(
      tree.next_care_at,
      (t) => (timer.textContent = `Próximo cuidado en ${t}`),
      async () => { // al llegar a 0, volver a consultar al servidor
        const res = await getTree(tree.id);
        if (res.ok) paint(res.data.data);
      }
    );
    stoppers.set(tree.id, stop);
  }
}

async function onCare(id) {
  const card = grid.querySelector(`[data-id="${id}"]`);
  const btn = card.querySelector('[data-action="care"]');
  btn.disabled = true;
  btn.textContent = 'Cuidando…';
  say('Aplicando cuidado…');

  const res = await careTree(id);
  if (res.ok) {
    say('¡Cuidado aplicado!', 'success');
    paint(res.data.data);
    return;
  }
  say(errorText(res), 'error');
  const fresh = await getTree(id); // resincronizar con el servidor
  if (fresh.ok) paint(fresh.data.data);
}

async function initDashboard() {
  say('Cargando tus árboles…');
  const res = await getTrees();
  if (!res.ok) { say(errorText(res), 'error'); return; }
  const trees = res.data.data;
  if (trees.length === 0) { say('Aún no tienes árboles. Planta el primero.'); return; }
  say('');
  trees.forEach(paint);
}
   if (grid) initDashboard(); // app.js se carga en todas las páginas