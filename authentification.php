<?php
// Démarrage d'une session si cela n'a pas déjà été fait
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// Vérification de l'éventuelle connexion d'un utilisateur
if (isset($_SESSION['session_user_id'])) {
  // Un utilisateur est connecté
  // Récupération de l'identifiant, du pseudo, du nom et du prénom
  // de l'utilisateur connecté dans des variables de session
  $session_user_id = $_SESSION['session_user_id'];
  $session_username = $_SESSION['session_username'];
} else {
  // Aucun utilisateur connecté
  // Redirection vers la page login.php
  header('Location: login.php');

  // Fin du script si la redirection n'a pas pu se faire
  exit();
}
?>

