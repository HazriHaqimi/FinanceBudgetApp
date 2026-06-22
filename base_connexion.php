<?php
// ***********************************************
// Connexion à la base de données budget_financier
//
// Gestion d'erreur manuelle : désactivation des rapports d'erreur
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
mysqli_report(MYSQLI_REPORT_OFF); // on garde la gestion manuelle des erreurs SQL
// Connexion à la base de données budget_financier
$connexion = mysqli_connect("localhost", "root", "", "budget_financier");
if ($connexion) {
  // Changement du jeu de caractères pour utf-8 
  mysqli_set_charset($connexion, "utf8");
} else {
  $message_erreur .= "Erreur de connexion<br>\n";
  $message_erreur .= "Erreur n° " . mysqli_connect_errno() . " : " . mysqli_connect_error() . "<br>\n";
}
?>
