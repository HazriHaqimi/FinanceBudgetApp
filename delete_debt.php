<?php
// ***********************************************
// delete_debt.php
// Supprime une dette "they_owe" CRÉÉE par l'utilisateur connecté
// (colonne "They owe" du debt_center).
// - Vérifie côté serveur que la dette appartient bien à l'utilisateur.
// - Supprime UNIQUEMENT la ligne dans la table debts.
//   La transaction d'origine (dépense réelle du créateur) n'est PAS touchée.
// - Les paiements déjà effectués ne sont pas annulés ; le restant dû
//   est simplement "pardonné".
// Reçu depuis le formulaire de debt_center.php (POST).
// ***********************************************

require 'authentification.php';   // donne $session_user_id, $session_username
require 'base_connexion.php';

$debt_id = isset($_POST['debt_id']) ? (int) $_POST['debt_id'] : 0;

if ($debt_id <= 0) {
    $_SESSION['flash_erreur'] = "Invalid debt.";
    require 'base_deconnexion.php';
    header("Location: debt_center.php");
    exit();
}

// Sécurité : on ne supprime QUE si la dette a été créée par l'utilisateur
// connecté (d.user_id) et qu'il s'agit bien d'une dette "they_owe".
$query = "SELECT d.debt_id, u.name
          FROM debts d
          INNER JOIN users u ON u.user_id = d.friend_user_id
          WHERE d.debt_id = $debt_id
            AND d.user_id = $session_user_id
            AND d.debt_type = 'they_owe'
          LIMIT 1";
$result = mysqli_query($connexion, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $debt = mysqli_fetch_assoc($result);
    $name = $debt['name'];

    // Suppression de la dette uniquement (la transaction reste intacte).
    mysqli_query($connexion, "DELETE FROM debts WHERE debt_id = $debt_id AND user_id = $session_user_id");

    $_SESSION['flash_message'] = "Debt with " . $name . " has been deleted.";
} else {
    $_SESSION['flash_erreur'] = "Could not delete debt (not found or not yours).";
}

require 'base_deconnexion.php';
header("Location: debt_center.php");
exit();
?>
