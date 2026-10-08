// Contador informativo: el servidor es quien decide si el cuidado es válido.
export function formatRemaining(ms) {
  const s = Math.max(0, Math.ceil(ms / 1000));
  const h = Math.floor(s / 3600);
  const m = Math.floor((s % 3600) / 60);
  const sec = s % 60;
  const pad = (n) => String(n).padStart(2, '0');
  return h > 0 ? `${h}:${pad(m)}:${pad(sec)}` : `${pad(m)}:${pad(sec)}`;
}

// Llama onTick con el texto restante y onDone al llegar a 0. Devuelve una función para detenerlo.
export function startCountdown(nextCareAt, onTick, onDone) {
  const target = Date.parse(nextCareAt);
  const tick = () => {
    const left = target - Date.now();
    if (left <= 0) { clearInterval(timer); onDone(); return; }
    onTick(formatRemaining(left));
  };
  const timer = setInterval(tick, 1000);
  tick();
  return () => clearInterval(timer);
}