<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $mdp = $_POST['mdp'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE login = ?");
    $stmt->execute([$login]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($mdp, $admin['mdp'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $erreur = "Identifiants incorrects.";
    }
}
?>

<?php if (isset($erreur)) echo "<p style='color:red;'>$erreur</p>"; ?>

<?php require_once '../includes/db.php'; ?>
<?php require_once '../includes/header.php'; ?>

<h2>Connexion administrateur</h2>
<form action="login.php" method="post">
  <label for="login">Identifiant :</label>
  <input type="text" name="login" id="login" required>

  <label for="mdp">Mot de passe :</label>
  <input type="password" name="mdp" id="mdp" required>

  <button type="submit">Connexion</button>
</form>

<?php require_once '../includes/footer.php'; ?>
