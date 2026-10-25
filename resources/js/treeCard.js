// Dibuja la tarjeta de un árbol. El estado se indica con ícono + texto, no solo con color.
const STATUS = {
  ACTIVE: { icon: '🌱', label: 'Activo' },
  MATURE: { icon: '🌳', label: 'Maduro: listo para cosechar' },
  DEAD: { icon: '💀', label: 'Muerto' },
  HARVESTED: { icon: '🧺', label: 'Cosechado' },
};

const bar = (label, value, max, extra = '') => `
  <div class="meter">
    <div class="meter__row"><span>${label}</span><span>${value} / ${max} ${extra}</span></div>
    <div class="meter__track" role="progressbar" aria-label="${label}"
         aria-valuemin="0" aria-valuemax="${max}" aria-valuenow="${value}">
      <div class="meter__fill" style="width:${(value / max) * 100}%"></div>
    </div>
  </div>`;

export function renderTree(tree) {
  const st = STATUS[tree.status] ?? { icon: '❔', label: tree.status };
  const card = document.createElement('article');
  card.className = `tree-card tree-card--${tree.status.toLowerCase()}`;
  card.dataset.id = tree.id;
  card.innerHTML = `
    <header class="tree-card__head">
      <h2>${tree.seed_type} #${tree.id}</h2>
      <p class="badge"><span aria-hidden="true">${st.icon}</span> ${st.label}</p>
    </header>
    <p class="tree-card__level">Nivel ${tree.level} de 10</p>
    ${bar('Salud', tree.health, 100, tree.health <= 40 && tree.status === 'ACTIVE' ? '(baja)' : '')}
    ${bar('Progreso al siguiente nivel', tree.progress, tree.progress_required)}
    <p class="tree-card__timer" aria-live="off"></p>
    <button type="button" class="btn" data-action="care">Cuidar</button>`;

  const btn = card.querySelector('[data-action="care"]');
  if (tree.status !== 'ACTIVE') {
    btn.hidden = true; // DEAD, MATURE y HARVESTED no se cuidan (la cosecha es del Sprint 3)
  }
  return card;
}