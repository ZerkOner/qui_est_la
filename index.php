<?php require_once 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Qui est là ? - Pointage</title>
<style>
  body { font-family: Arial, sans-serif; margin: 20px; }
  .tabs { display: flex; margin-bottom: 10px; cursor: pointer; }
  .tab {
    padding: 10px 20px;
    background: #eee;
    border: 1px solid #ccc;
    border-bottom: none;
    margin-right: 5px;
    user-select: none;
  }
  .tab.active {
    background: white;
    font-weight: bold;
    border-bottom: 1px solid white;
  }
  .tab-content {
    border: 1px solid #ccc;
    padding: 15px;
    display: none;
  }
  .tab-content.active { display: block; }
  label { display: block; margin-top: 10px; }
  input, select, button { margin-top: 5px; padding: 7px; width: 100%; max-width: 300px; }
</style>
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

    <div id="personnel-container" style="display:none;">
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

    <div id="formation-container" style="display:none;">
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

    <button type="submit" style="margin-top:15px;">Valider l'entrée</button>
  </form>
</div>

<div id="sortie" class="tab-content">
  <h2>Pointage Sortie</h2>
  <form action="traitement_sortie.php" method="post">
  <label>Identifiant unique (QR code ID)</label>
  <input type="text" name="qr_code_id">

  <label>Ou Email</label>
  <input type="email" name="email">

  <button type="submit" style="margin-top:15px;">Valider la sortie</button>
</form>

</div>

<script src="public/js/tabs.js"></script>

</body>
</html>
