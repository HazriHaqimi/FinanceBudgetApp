<?php
session_start();
require 'base_connexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['session_user_id'])) {
    
    $session_user_id = $_SESSION['session_user_id'];
    $session_username = $_SESSION['session_username'] ?? '';

    // 1. Grab the data from the form
    // (Always clean inputs going into the database!)
    $transaction_id = mysqli_real_escape_string($connexion, $_POST['transaction_id']);
    $description = mysqli_real_escape_string($connexion, $_POST['description']);
    $amount = mysqli_real_escape_string($connexion, $_POST['amount']);

    // On récupère la catégorie ET l'ancien montant (pour ajuster les dettes liées)
    $check = mysqli_query($connexion, "SELECT category, amount FROM transactions
                                       WHERE transaction_id = '$transaction_id'
                                         AND user_id = '$session_user_id'");
    $check_row = $check ? mysqli_fetch_assoc($check) : null;

    // Garde-fou : on n'autorise pas la modification d'un paiement de dette ici
    if ($check_row && $check_row['category'] === 'Debt Payment') {
        $_SESSION['flash_erreur'] = "Debt payments can only be modified from the Debt Center.";
        header("Location: transaction.php");
        exit();
    }

    // Garde-fou : le montant doit être strictement positif
    if ((float) $amount <= 0) {
        $_SESSION['flash_erreur'] = "The amount must be greater than 0.";
        header("Location: transaction.php");
        exit();
    }

    // 2. The UPDATE Query
    // We say: Update the transactions table, SET these specific columns to the new values,
    // WHERE the ID matches the hidden input from our form.
    $sql_update = "UPDATE transactions
                   SET description = '$description', amount = '$amount'
                   WHERE transaction_id = '$transaction_id' AND user_id = '$session_user_id'";

    if (mysqli_query($connexion, $sql_update)) {
        // Modèle "ma part" : le montant d'une transaction représente MA part.
        // Le modifier ne change que MON solde, pas la dette des amis : ce sont
        // des valeurs indépendantes gérées dans le Debt Center.
        $_SESSION['flash_message'] = "Transaction updated.";
        header("Location: transaction.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($connexion);
    }
}
?>