<?php
// ***********************************************
// Connexion à la base de données cuicui du serveur localhost
//
// Gestion d'erreur manuelle : désactivation des rapports d'erreur
error_reporting(0); // Désactivation du rapport d'erreurs de PHP
mysqli_report(MYSQLI_REPORT_OFF); // Désactivation du rapport d'erreur mysqli
// Connexion à la base de données cuicui du serveur localhost
$connexion = mysqli_connect("localhost", "root", "", "cuicui");
if ($connexion) {
  // Changement du jeu de caractères pour utf-8 
  mysqli_set_charset($connexion, "utf8");
} else {
  $message_erreur .= "Erreur de connexion<br>\n";
  $message_erreur .= "Erreur n° " . mysqli_connect_errno() . " : " . mysqli_connect_error() . "<br>\n";
}
?>
