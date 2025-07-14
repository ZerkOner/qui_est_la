<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';
require_once '../includes/header.php';

// Traitement de l'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $fonction = trim($_POST['fonction']);
    $email = trim($_POST['email']);
    $local = trim($_POST['local']);

    $stmt = $pdo->prepare("INSERT INTO personnels (nom, prenom, fonction, email, local) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $fonction, $email, $local]);

    // Rechargement pour éviter la redite du POST
    header("Location: personnels.php");
    exit();
}

// Récupération de la liste
$stmt = $pdo->query("SELECT * FROM personnels ORDER BY nom");
$personnels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Membres du personnel</h2>

<table border="1" cellpadding="5">
  <tr>
    <th>Nom</th>
    <th>Prénom</th>
    <th>Fonction</th>
    <th>Email</th>
    <th>Local</th>
  </tr>
  <?php foreach ($personnels as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row['nom']) ?></td>
      <td><?= htmlspecialchars($row['prenom']) ?></td>
      <td><?= htmlspecialchars($row['fonction']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td><?= htmlspecialchars($row['local']) ?></td>
    </tr>
  <?php endforeach; ?>
</table>

<h3>Ajouter un nouveau membre</h3>
<form action="personnels.php" method="post">
  <label>Nom :</label><br>
  <input type="text" name="nom" required><br>

  <label>Prénom :</label><br>
  <input type="text" name="prenom" required><br>

  <label>Fonction :</label><br>
  <input type="text" name="fonction" required><br>

  <label>Email :</label><br>
  <input type="email" name="email" required><br>

  <label>Local :</label><br>
  <input type="text" name="local" required><br><br>

  <button type="submit" name="ajouter">Ajouter</button>
</form>

<?php require_once '../includes/footer.php'; ?>
