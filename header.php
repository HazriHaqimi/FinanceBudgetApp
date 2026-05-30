<?php
// Démarrage d'une session si cela n'a pas déjà été fait
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

if (isset($_SESSION['session_idutilisateur'])) {
  // Un utilisateur est connecté
  // Récupération de l'identifiant, du pseudo, du nom et du prénom
  // de l'utilisateur connecté dans des variables de session
  $session_idutilisateur = $_SESSION['session_idutilisateur'];
  $session_pseudo = $_SESSION['session_pseudo'];
  $session_nom = $_SESSION['session_nom'];
  $session_prenom = $_SESSION['session_prenom'];
}
?>

<!doctype html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Budget Financier</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
    <link rel="stylesheet" type="text/css" href="cuicui.css">
  </head>
  <body>
    <!--<div class="ui inverted fixed menu">-->

    <!--<div class="ui left fixed vertical menu, ui left vertical menu app-sidebar">

      <div class="item"><a href="index.php">Budget Financier</a></div>
      <?php if (!isset($session_idutilisateur)) { ?>
        <a class="item "href="inscription.php">Inscription</a>
      <?php } else { ?>
        <a class="item">Dashboard</a>
        <a class="item">Transaction</a>
        <a class="item">Debt Center</a>
        <a class="item">Contacts</a>
        <a class="item">Reports</a>
        <a class="item">User</a>
        <a class="item">Sign out</a> 
      <?php } ?>

    </div>-->
    <div class="app-layout">
        <div class="ui inverted fixed menu">
            <div class="item"><a href="index.php"><strong>Budget Financier</strong></a></div>
            <?php if (!isset($session_idutilisateur)) { ?>
              <a class="item" href="inscription.php">Inscription</a>
            <?php } else { ?>
              <a class="item">Dashboard</a>
              <a class="item">Transaction</a>
              <a class="item">Debt Center</a>
              <a class="item">Contacts</a>
              <a class="item">Reports</a>
              <a class="item">User</a>
              <a class="item">Sign out</a>
            <?php } ?>
          </div>
    <div class="app-content">
        <div class="ui main text container">

        
<!--       
<div class="ui left fixed vertical menu">
  <div class="item">
    <a href="index.php">Budget Financier</a>
  </div>
  <a class="item">Features</a>
  <a class="item">Testimonials</a>
  <a class="item">Sign-in</a>
</div>

    <div class="ui left fixed vertical menu">
      <div class="ui item"><a href="index.php">Budget Financier</a></div>
      <?php if (!isset($session_idutilisateur)) { ?>
        <div class="item right">- - (-)</div>
        <div class="item"><a href="login.php">Connexion</a></div>
        <div class="item"><a href="inscription.php">Inscription</a></div>
      <?php } else { ?>
        <div class="item right"><?php  echo "$session_prenom " . strtoupper($session_nom) . " ($session_pseudo)";  ?></div>
        <div class="item"><a href="logout.php">Déconnecter</a></div>
        <div class="item"><a href="inscription.php">Compte</a></div>
      <?php } ?>
    </div>
-->