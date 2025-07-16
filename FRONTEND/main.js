// Remplace ici par l'URL complète de ton backend déployé
const BACKEND_URL = 'https://TON_BACKEND_URL';

// Chargement dynamique des personnels
async function loadPersonnels() {
  try {
    const resp = await fetch(`${BACKEND_URL}/includes/api_get_personnels.php`);
    if (!resp.ok) throw new Error('Erreur chargement personnels');
    const personnels = await resp.json();
    const select = document.getElementById('personnel-select');
    select.innerHTML = '<option value="">-- Aucun --</option>'; // reset options
    personnels.forEach(p => {
      const opt = document.createElement('option');
      opt.value = p.id;
      opt.textContent = `${p.prenom} ${p.nom}`;
      select.appendChild(opt);
    });
  } catch (err) {
    console.error(err);
  }
}

// Chargement dynamique des formations
async function loadFormations() {
  try {
    const resp = await fetch(`${BACKEND_URL}/includes/api_get_formations.php`);
    if (!resp.ok) throw new Error('Erreur chargement formations');
    const formations = await resp.json();
    const select = document.getElementById('formation-select');
    select.innerHTML = '<option value="">-- Aucune --</option>'; // reset options
    formations.forEach(f => {
      const opt = document.createElement('option');
      opt.value = f.id;
      opt.textContent = f.intitule;
      select.appendChild(opt);
    });
  } catch (err) {
    console.error(err);
  }
}

// Initialisation au chargement
loadPersonnels();
loadFormations();

// Soumission formulaire entrée
document.getElementById('form-entree').addEventListener('submit', async e => {
  e.preventDefault();
  const form = e.target;
  const data = new FormData(form);
  try {
    const resp = await fetch(`${BACKEND_URL}/traitement_entree.php`, {
      method: 'POST',
      body: data,
    });
    const text = await resp.text();
    document.getElementById('entree-message').innerHTML = text;
    form.reset();
    personnelContainer.style.display = 'none';
    formationContainer.style.display = 'none';
  } catch (err) {
    document.getElementById('entree-message').textContent = 'Erreur lors de l\'envoi';
    console.error(err);
  }
});

// Soumission formulaire sortie
document.getElementById('form-sortie').addEventListener('submit', async e => {
  e.preventDefault();
  const form = e.target;
  const data = new FormData(form);
  try {
    const resp = await fetch(`${BACKEND_URL}/traitement_sortie.php`, {
      method: 'POST',
      body: data,
    });
    const text = await resp.text();
    document.getElementById('sortie-message').innerHTML = text;
    form.reset();
  } catch (err) {
    document.getElementById('sortie-message').textContent = 'Erreur lors de l\'envoi';
    console.error(err);
  }
});

// Enregistrement Service Worker
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('sw.js')
    .then(reg => console.log('Service Worker enregistré', reg))
    .catch(err => console.error('Erreur SW', err));
}
