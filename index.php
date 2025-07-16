<?php require_once 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Qui est là ? - Pointage</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="mobile-web-app-capable" content="yes" />
  <link rel="stylesheet" href="/qui_est_la/public/css/index.css">
  <link rel="manifest" href="/qui_est_la/public/manifest.json">
<meta name="theme-color" content="#2c3e50" />
<link rel="apple-touch-icon" href="/qui_est_la/public/icons/icon-192.png">

</head>
<body>

<h1>Bienvenue au système de pointage</h1>

<div class="tabs">
  <div class="tab active" data-tab="entree">Entrée</div>
  <div class="tab" data-tab="sortie">Sortie</div>
</div>

<div id="entree" class="tab-content active">
  <h2>Pointage Entrée</h2>
  <form action="traitement_entree.php" method="post">
    <label>Nom <sup>*</sup></label>
    <input type="text" name="nom" required>

    <label>Prénom <sup>*</sup></label>
    <input type="text" name="prenom" required>

    <label>Email <sup>*</sup></label>
    <input type="email" name="email" required>

    <label>Objet de la visite <sup>*</sup></label>
    <select name="objet" id="objet" required>
      <option value="">-- Choisir --</option>
      <option value="personnel">Membre du personnel</option>
      <option value="formation">Formation</option>
    </select>

    <div id="personnel-container" class="sub-container">
      <label>Personne à rencontrer</label>
      <select name="personnel_id">
        <option value="">-- Aucun --</option>
        <?php
          $personnels = $pdo->query("SELECT id, nom, prenom FROM personnels ORDER BY nom")->fetchAll();
          foreach ($personnels as $p) {
              echo "<option value='".htmlspecialchars($p['id'])."'>".htmlspecialchars($p['prenom'])." ".htmlspecialchars($p['nom'])."</option>";
          }
        ?>
      </select>
    </div>

    <div id="formation-container" class="sub-container">
      <label>Formation</label>
      <select name="formation_id">
        <option value="">-- Aucune --</option>
        <?php
          $formations = $pdo->query("SELECT id, intitule FROM formations ORDER BY date_formation DESC")->fetchAll();
          foreach ($formations as $f) {
              echo "<option value='".htmlspecialchars($f['id'])."'>".htmlspecialchars($f['intitule'])."</option>";
          }
        ?>
      </select>
    </div>

    <button type="submit">Valider l'entrée</button>
  </form>
</div>

<div id="sortie" class="tab-content">
  <h2>Pointage Sortie</h2>
  <form action="traitement_sortie.php" method="post">
    <label>Identifiant unique (QR code ID)</label>
    <input type="text" name="qr_code_id">

    <label>Ou Email</label>
    <input type="email" name="email">

    <button type="submit">Valider la sortie</button>
  </form>
</div>

<script src="public/js/tabs.js"></script>
<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/qui_est_la/public/js/sw.js')
      .then(reg => console.log('Service Worker enregistré', reg))
      .catch(err => console.error('Erreur SW', err));
  }
</script>

</body>
</html>
