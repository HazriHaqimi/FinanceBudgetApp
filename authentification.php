<?php
// Démarrage d'une session si cela n'a pas déjà été fait
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// Vérification de l'éventuelle connexion d'un utilisateur
if (isset($_SESSION['session_idutilisateur'])) {
  // Un utilisateur est connecté
  // Récupération des variables de session dans des variables du script
  $session_idutilisateur = $_SESSION['session_idutilisateur'];
  $session_pseudo = $_SESSION['session_pseudo'];
  $session_nom = $_SESSION['session_nom'];
  $session_prenom = $_SESSION['session_prenom'];
} else {
  // Aucun utilisateur connecté
  // Redirection vers la page login.php
  header('Location: login.php');

  // Fin du script si la redirection n'a pas pu se faire
  exit();
}
?>