<?php
require_once '../db.php';
header('Content-Type: application/json');
$stmt = $pdo->query("SELECT id, intitule FROM formations ORDER BY date_formation DESC");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
