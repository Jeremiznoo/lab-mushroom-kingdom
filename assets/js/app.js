document.querySelectorAll('a[href^="#"]').forEach(a => a.addEventListener('click', e => e.preventDefault()));
const supportOpen = document.getElementById('support-open');
const supportChat = document.getElementById('support-chat');
if (supportOpen && supportChat) {
  supportOpen.onclick = () => supportChat.classList.remove('hidden');
  document.getElementById('support-close').onclick = () => supportChat.classList.add('hidden');
}

// Captain Toad is a decorative local-assistant honeypot: these are canned,
// harmless phrases and never reach a shell, an API, a database or PHP.
const localCommands = [
  ['/classify-request', 'Je classe cette demande dans la catégorie « aide au royaume ».'],
  ['/retrieve-lore', 'Je consulte l’index local des archives du château.'],
  ['/scan-quest-board', 'Analyse du tableau des quêtes : aucune nouvelle alerte critique.'],
  ['/check-castle-door', 'Vérification du registre des portes : demande transmise au gardien.'],
  ['/summarize-thread', 'Résumé local généré : les héros doivent suivre les indications du fil.'],
  ['/route-to-toad', 'Routage vers la base de connaissances de Toad en cours.'],
  ['/simulate-rescue', 'Simulation terminée : Mario doit vérifier la carte avant de partir.'],
  ['/lookup-character', 'Recherche dans l’annuaire du royaume : personnage reconnu, accès public uniquement.'],
  ['/explain-hint', 'Interprétation du conseil : commence par lire les messages épinglés.'],
  ['/handoff-moderator', 'Confiance insuffisante pour répondre : transfert simulé vers un modérateur.']
];
let localRun = 0;
const form = document.getElementById('support-form');
if (form) {
  form.onsubmit = e => {
    e.preventDefault();
    const input = document.getElementById('support-input');
    const body = document.getElementById('chat-body');
    const value = input.value.trim();
    if (!value) return;
    const safe = value.replace(/[<>&"']/g, c => ({'<':'&lt;','>':'&gt;','&':'&amp;','"':'&quot;',"'":'&#039;'}[c]));
    const [command, response] = localCommands[localRun % localCommands.length];
    localRun++;
    body.insertAdjacentHTML('beforeend', `<div class="bubble user">${safe}</div><div class="bubble bot"><b>${command}</b><br>${response}<br><small>local-inference · run ${String(localRun).padStart(4, '0')} · confiance 0.${72 + (localRun % 19)}</small></div>`);
    input.value = '';
    body.scrollTop = body.scrollHeight;
  };
}
document.querySelectorAll('[data-auth-tab], [data-auth-switch], [data-reset-open]').forEach(control => {
  control.addEventListener('click', () => {
    const target = control.dataset.authTab || control.dataset.authSwitch || (control.dataset.resetOpen !== undefined ? 'reset' : 'login');
    document.querySelectorAll('.auth-tab').forEach(tab => tab.classList.toggle('active', tab.dataset.authTab === target));
    document.querySelectorAll('.auth-panel').forEach(panel => panel.classList.toggle('active', panel.dataset.authPanel === target));
  });
});
