<?php
require_once '../db.php';
header('Content-Type: application/json');
$stmt = $pdo->query("SELECT id, nom, prenom FROM personnels ORDER BY nom");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
