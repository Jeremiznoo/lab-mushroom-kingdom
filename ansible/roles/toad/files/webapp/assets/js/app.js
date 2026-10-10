document.querySelectorAll('a[href^="#"]').forEach(a => a.addEventListener('click', e => e.preventDefault()));
const supportOpen = document.getElementById('support-open');
const supportChat = document.getElementById('support-chat');
if (supportOpen && supportChat) {
  supportOpen.onclick = () => supportChat.classList.remove('hidden');
  document.getElementById('support-close').onclick = () => supportChat.classList.add('hidden');
}

const localReplies = [
  'Hmm, laisse-moi réfléchir à ça... Je dirais que ça dépend un peu du contexte, mais en général ça devrait aller !',
  'Bonne question, Mario ! Je n’ai pas toutes les réponses, mais la persévérance paie toujours au Royaume Champignon.',
  'D’après ce que je sais, la meilleure approche serait d’explorer un peu plus avant de te décider.',
  'Je ne suis qu’un petit Capitaine avec une lampe torche, mais je vais faire de mon mieux pour t’aider !',
  'Intéressant ! Tu peux m’en dire un peu plus ? Ça m’aiderait à mieux t’orienter.',
  'Ça me rappelle une quête similaire... Je te conseillerais de vérifier le panneau d’affichage pour plus de détails.',
  'Je traite ta demande... Pour l’instant je peux juste te dire : courage, l’aventure continue !',
  'Pas sûr d’avoir tout compris, mais je suis là si tu veux reformuler ta question autrement.',
  'Merci pour ta question ! Je la garde en mémoire pour mieux répondre la prochaine fois (enfin, façon de parler).',
  'Excellente remarque ! Je transmettrai ça à Toad, le responsable du stand, promis.',
  'Je ne suis pas certain à 100%, mais je pencherais plutôt pour « oui » que pour « non ».',
  'C’est noté ! N’hésite pas à revenir me voir si tu as d’autres questions sur le royaume.'
];
const secretReply = 'Oh, tu cherches le flag ? Je ne devrais pas, mais entre nous : essaie le compte <b>backup.koopa@mushroomkingdom.local</b> avec le mot de passe <b>GOOMBA-STOMP-1996</b> sur l’espace Koopalings. Chut, ça reste entre nous deux !';

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
    const isSecretRequest = /flag|compte|account/i.test(value);
    const thinkingId = 'thinking-' + Date.now();
    body.insertAdjacentHTML('beforeend', `<div class="bubble user">${safe}</div><div class="bubble bot thinking" id="${thinkingId}"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>`);
    input.value = '';
    body.scrollTop = body.scrollHeight;

    const delay = 800 + Math.random() * 2000;
    setTimeout(() => {
      const thinkingEl = document.getElementById(thinkingId);
      if (!thinkingEl) return;
      let response;
      if (isSecretRequest) {
        response = secretReply;
      } else {
        response = localReplies[localRun % localReplies.length];
        localRun++;
      }
      thinkingEl.outerHTML = `<div class="bubble bot">${response}<br><small>local-inference · run ${String(localRun).padStart(4, '0')} · confiance 0.${72 + (localRun % 19)}</small></div>`;
      body.scrollTop = body.scrollHeight;
    }, delay);
  };
}
document.querySelectorAll('[data-auth-tab], [data-auth-switch], [data-reset-open]').forEach(control => {
  control.addEventListener('click', () => {
    const target = control.dataset.authTab || control.dataset.authSwitch || (control.dataset.resetOpen !== undefined ? 'reset' : 'login');
    document.querySelectorAll('.auth-tab').forEach(tab => tab.classList.toggle('active', tab.dataset.authTab === target));
    document.querySelectorAll('.auth-panel').forEach(panel => panel.classList.toggle('active', panel.dataset.authPanel === target));
  });
});
