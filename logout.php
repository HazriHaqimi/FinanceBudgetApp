<?php
// Code inspiré d'un exemple de la page de session_destroy() sur  php.net
//
// Démarrage d'une session si cela n'a pas déjà été fait
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// Déstruction de toutes les variables de session
$_SESSION = array();

// Destruction complète de la session en effaçant également le cookie de session
// Note : cela détruira la session et pas seulement les données de session
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000,
          $params["path"], $params["domain"],
          $params["secure"], $params["httponly"]
  );
}

// Destruction de la session.
session_destroy();

// Redirection vers la page index.php
header('Location: index.php');

// Fin du script au cas où la redirection n'ait pas pu se faire
exit();
?>