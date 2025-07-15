<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db.php';

// Récupération des filtres
$type_personne = $_GET['type_personne'] ?? 'tous';
$type_action = $_GET['type_action'] ?? 'toutes';

$conditions = [];
$params = [];

if ($type_personne === 'formateur') {
    $conditions[] = "p.formation_id IS NOT NULL";
} elseif ($type_personne === 'visiteur') {
    $conditions[] = "p.personnel_id IS NOT NULL";
}

if ($type_action === 'entrée' || $type_action === 'sortie') {
    $conditions[] = "p.type_action = ?";
    $params[] = $type_action;
}

$where_clause = count($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

$requete = $pdo->prepare("
    SELECT 
        p.horodatage,
        p.type_action,
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

// Préparation CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=pointages.csv');
$output = fopen('php://output', 'w');

// En-tête
fputcsv($output, ['Date', 'Nom', 'Action', 'Motif', 'Détail']);

while ($row = $requete->fetch(PDO::FETCH_ASSOC)) {
    $nom = $row['visiteur_prenom'] . ' ' . $row['visiteur_nom'];
    $action = ucfirst($row['type_action']);
    $date = $row['horodatage'];

    if ($row['type_action'] === 'sortie') {
        $motif = 'Sortie';
        $detail = '-';
    } elseif (!empty($row['formation_intitule'])) {
        $motif = 'Formation';
        $detail = $row['formation_intitule'];
    } elseif (!empty($row['personnel_nom'])) {
        $motif = 'Visite';
        $detail = $row['personnel_prenom'] . ' ' . $row['personnel_nom'];
    } else {
        $motif = 'Inconnu';
        $detail = '-';
    }

    fputcsv($output, [$date, $nom, $action, $motif, $detail]);
}

fclose($output);
exit;
