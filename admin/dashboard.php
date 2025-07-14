<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../includes/db.php';
require_once '../includes/header.php';
?>

<h1>Tableau de bord administrateur</h1>

<nav>
  <ul>
    <li><a href="personnels.php">Personnels</a></li>
    <li><a href="formations.php">Formations</a></li>
    <li><a href="pointages.php">Pointages</a></li>  <!-- <-- Ajouté ici -->
    <li><a href="logout.php">Déconnexion</a></li>
  </ul>
</nav>


<?php require_once '../includes/footer.php'; ?>
