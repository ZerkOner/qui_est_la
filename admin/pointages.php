<link rel="stylesheet" href="/qui_est_la/public/css/style.css" />
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';
require_once '../includes/header.php';

// Récupération des filtres
$type_personne = $_GET['type_personne'] ?? 'tous';
$type_action = $_GET['type_action'] ?? 'toutes';

$conditions = [];
$params = [];

if ($type_personne === 'formateur') {
    $conditions[] = "p.formation_id IS NOT NULL";
} elseif ($type_personne === 'visiteur') {
    $conditions[] = "p.personnel_id IS NOT NULL AND p.formation_id IS NULL";
}

if ($type_action === 'entrée' || $type_action === 'sortie') {
    $conditions[] = "p.type_action = ?";
    $params[] = $type_action;
}

$where_clause = count($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
?>

<h2>Historique des pointages</h2>

<!-- Formulaire de filtre -->
<form method="get" action="pointages.php" style="margin-bottom: 20px;">
  <label for="type_personne">Motif :</label>
  <select name="type_personne" id="type_personne">
    <option value="tous" <?= $type_personne === 'tous' ? 'selected' : '' ?>>Tous</option>
    <option value="formateur" <?= $type_personne === 'formateur' ? 'selected' : '' ?>>En formation</option>
    <option value="visiteur" <?= $type_personne === 'visiteur' ? 'selected' : '' ?>>En visite</option>
  </select>

  <label for="type_action" style="margin-left: 20px;">Type d'action :</label>
  <select name="type_action" id="type_action">
    <option value="toutes" <?= $type_action === 'toutes' ? 'selected' : '' ?>>Toutes</option>
    <option value="entrée" <?= $type_action === 'entrée' ? 'selected' : '' ?>>Entrées</option>
    <option value="sortie" <?= $type_action === 'sortie' ? 'selected' : '' ?>>Sorties</option>
  </select>

  <button type="submit" style="margin-left: 20px;">Filtrer</button>
</form>

<!-- Export CSV -->
<form method="get" action="export_csv.php" style="margin-bottom: 20px;">
  <input type="hidden" name="type_personne" value="<?= htmlspecialchars($type_personne) ?>">
  <input type="hidden" name="type_action" value="<?= htmlspecialchars($type_action) ?>">
  <button type="submit">Exporter CSV</button>
</form>

<!-- Tableau -->
<table>
  <tr>
    <th>Date</th>
    <th>Nom du visiteur</th>
    <th>Action</th>
    <th>Motif</th>
    <th>Détail</th>
  </tr>

<?php
try {
    $requete = $pdo->prepare("
        SELECT 
            p.type_action,
            p.horodatage,
            v.nom AS visiteur_nom,
            v.prenom AS visiteur_prenom,
            f.intitule AS formation_intitule,
            pe.nom AS personnel_nom,
            pe.prenom AS personnel_prenom
        FROM pointages p
        JOIN visiteurs v ON p.visiteur_id = v.id
        LEFT JOIN formations f ON p.formation_id = f.id
        LEFT JOIN personnels pe ON p.personnel_id = pe.id
        $where_clause
        ORDER BY p.horodatage DESC
    ");
    $requete->execute($params);

    while ($row = $requete->fetch(PDO::FETCH_ASSOC)) {
        $nom = htmlspecialchars($row['visiteur_prenom'] . ' ' . $row['visiteur_nom']);
        $date = htmlspecialchars($row['horodatage']);
        $action = htmlspecialchars($row['type_action']);

        if ($action === 'sortie') {
            $motif = 'Sortie';
            $detail = '-';
        } elseif (!empty($row['formation_intitule'])) {
            $motif = 'Formation';
            $detail = htmlspecialchars($row['formation_intitule']);
        } elseif (!empty($row['personnel_nom'])) {
            $motif = 'Visite';
            $detail = htmlspecialchars($row['personnel_prenom'] . ' ' . $row['personnel_nom']);
        } else {
            $motif = 'Inconnu';
            $detail = '-';
        }

        $classe = ($action === 'entrée') ? 'bg-entree' : 'bg-sortie';

        echo "<tr class=\"$classe\">
                <td>$date</td>
                <td>$nom</td>
                <td>" . ucfirst($action) . "</td>
                <td>$motif</td>
                <td>$detail</td>
              </tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='5'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
</table>

<?php require_once '../includes/footer.php'; ?>
