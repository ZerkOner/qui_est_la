<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';
require_once '../includes/header.php';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $intitule = trim($_POST['intitule']);
    $date_formation = $_POST['date_formation'];
    $local = trim($_POST['local']);
    $formateur_id = $_POST['formateur_id'] !== '' ? (int)$_POST['formateur_id'] : null;

    $stmt = $pdo->prepare("
        INSERT INTO formations (intitule, date_formation, local, formateur_id)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$intitule, $date_formation, $local, $formateur_id]);

    header("Location: formations.php");
    exit();
}

// Récupération des formations avec formateur
$stmt = $pdo->query("
    SELECT f.intitule, f.date_formation, f.local,
           p.nom AS formateur_nom, p.prenom AS formateur_prenom
    FROM formations f
    LEFT JOIN personnels p ON f.formateur_id = p.id
    ORDER BY f.date_formation DESC
");
$formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des formateurs pour le <select>
$personnels = $pdo->query("SELECT id, nom, prenom FROM personnels ORDER BY nom")->fetchAll();
?>

<h2>Liste des formations</h2>

<table border="1" cellpadding="5">
  <tr>
    <th>Intitulé</th>
    <th>Date</th>
    <th>Local</th>
    <th>Formateur</th>
  </tr>
  <?php foreach ($formations as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row['intitule']) ?></td>
      <td><?= htmlspecialchars($row['date_formation']) ?></td>
      <td><?= htmlspecialchars($row['local']) ?></td>
      <td>
        <?= $row['formateur_nom'] ? htmlspecialchars($row['formateur_prenom'] . ' ' . $row['formateur_nom']) : 'Non défini' ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<h3>Ajouter une formation</h3>

<form action="formations.php" method="post">
  <label>Intitulé :</label><br>
  <input type="text" name="intitule" required><br>

  <label>Date :</label><br>
  <input type="date" name="date_formation" required><br>

  <label>Local :</label><br>
  <input type="text" name="local" required><br>

  <label>Formateur :</label><br>
  <select name="formateur_id">
    <option value="">-- Aucun --</option>
    <?php foreach ($personnels as $p): ?>
      <option value="<?= $p['id'] ?>">
        <?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?>
      </option>
    <?php endforeach; ?>
  </select><br><br>

  <button type="submit" name="ajouter">Ajouter</button>
</form>

<?php require_once '../includes/footer.php'; ?>
