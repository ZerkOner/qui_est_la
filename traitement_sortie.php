<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Méthode non autorisée.");
}

// Récupérer qr_code_id et email du formulaire
$qr_code_id = isset($_POST['qr_code_id']) ? trim($_POST['qr_code_id']) : '';
$email = isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '';

if (empty($qr_code_id) && empty($email)) {
    die("Veuillez saisir votre identifiant unique ou votre email.");
}

// Recherche du visiteur par qr_code_id ou email
if ($qr_code_id !== '') {
    $stmt = $pdo->prepare("SELECT * FROM visiteurs WHERE qr_code_id = ?");
    $stmt->execute([$qr_code_id]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM visiteurs WHERE email = ?");
    $stmt->execute([$email]);
}

$visiteur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$visiteur) {
    die("Visiteur non reconnu.");
}

$visiteur_id = $visiteur['id'];

// Récupérer le dernier pointage
$stmt = $pdo->prepare("SELECT * FROM pointages WHERE visiteur_id = ? ORDER BY horodatage DESC LIMIT 1");
$stmt->execute([$visiteur_id]);
$dernier_pointage = $stmt->fetch(PDO::FETCH_ASSOC);

if ($dernier_pointage) {
    $dernier_type = $dernier_pointage['type_action'];
    $nouveau_type = ($dernier_type === 'entrée') ? 'sortie' : 'entrée';
} else {
    $nouveau_type = 'entrée';
}

$formation_id = null;
$personnel_id = null;
$horodatage = date('Y-m-d H:i:s');

try {
    $stmt = $pdo->prepare("
        INSERT INTO pointages (visiteur_id, type_action, horodatage, formation_id, personnel_id)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$visiteur_id, $nouveau_type, $horodatage, $formation_id, $personnel_id]);
} catch (PDOException $e) {
    die("Erreur lors de l'enregistrement du pointage : " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Pointage <?= htmlspecialchars($nouveau_type) ?></title>
    <?php
// Après avoir affiché le message de confirmation de sortie
echo '<p>Sortie enregistrée. Vous allez être redirigé dans 3 secondes :( </p>';
?>

<script>
  setTimeout(() => {
    window.location.href = 'index.php';
  }, 3000);
</script>

</head>
<body>
    <h2>Bonjour, <?= htmlspecialchars($visiteur['prenom']) . ' ' . htmlspecialchars($visiteur['nom']) ?></h2>
    <p>Votre action <strong><?= htmlspecialchars($nouveau_type) ?></strong> a bien été enregistrée à <?= htmlspecialchars($horodatage) ?>.</p>
</body>
</html>
