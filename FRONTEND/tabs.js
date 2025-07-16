document.addEventListener('DOMContentLoaded', function () {
  const tabs = document.querySelectorAll('.tab');
  const contents = document.querySelectorAll('.tab-content');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      // Désactive tous les onglets et contenus
      tabs.forEach(t => t.classList.remove('active'));
      contents.forEach(c => c.classList.remove('active'));

      // Active l'onglet cliqué et son contenu associé
      tab.classList.add('active');
      const id = tab.getAttribute('data-tab');
      document.getElementById(id).classList.add('active');
    });
  });

  // Gestion affichage du formulaire dynamique dans entrée (objet)
  const objetSelect = document.getElementById('objet');
  const personnelContainer = document.getElementById('personnel-container');
  const formationContainer = document.getElementById('formation-container');

  if (objetSelect) {
    objetSelect.addEventListener('change', function () {
      const val = this.value;
      if (val === 'personnel') {
        personnelContainer.style.display = 'block';
        formationContainer.style.display = 'none';
      } else if (val === 'formation') {
        personnelContainer.style.display = 'none';
        formationContainer.style.display = 'block';
      } else {
        personnelContainer.style.display = 'none';
        formationContainer.style.display = 'none';
      }
    });
  }
});
