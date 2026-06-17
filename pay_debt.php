<?php
// ***********************************************
// pay_debt.php
// Enregistre un paiement vers une personne à qui JE dois de l'argent.
// Réduit remaining_amount, marque 'paid' si tout est réglé,
// et envoie un message au créancier.
// Reçu depuis le formulaire popup de debt_center.php (POST).
// ***********************************************

require 'authentification.php';   // donne $session_user_id, $session_username
require 'base_connexion.php';

$debt_id    = isset($_POST['debt_id'])    ? (int) $_POST['debt_id']      : 0;
$pay_amount = isset($_POST['pay_amount']) ? (float) $_POST['pay_amount'] : 0;

if ($debt_id <= 0 || $pay_amount <= 0) {
    $_SESSION['flash_erreur'] = "Invalid payment amount.";
    require 'base_deconnexion.php';
    header("Location: debt_center.php");
    exit();
}

// On récupère la dette : c'est une dette que JE dois
// (donc je suis l'ami "friend_user_id"), et elle est encore en cours.
$query = "SELECT d.user_id AS creditor_id, d.original_amount, d.remaining_amount, u.name
          FROM debts d
          INNER JOIN users u ON u.user_id = d.user_id
          WHERE d.debt_id = $debt_id
            AND d.friend_user_id = $session_user_id
            AND d.debt_type = 'they_owe'
            AND d.status = 'pending'
          LIMIT 1";
$result = mysqli_query($connexion, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $debt        = mysqli_fetch_assoc($result);
    $remaining   = (float) $debt['remaining_amount'];
    $creditor_id = (int) $debt['creditor_id'];

    // Sécurité : on ne peut pas payer plus que le montant restant
    if ($pay_amount > $remaining) {
        $pay_amount = $remaining;
    }

    $new_remaining = round($remaining - $pay_amount, 2);
    if ($new_remaining < 0) {
        $new_remaining = 0;
    }
    $new_status = ($new_remaining <= 0) ? 'paid' : 'pending';

    // Mise à jour de la dette
    $upd = "UPDATE debts
            SET remaining_amount = $new_remaining,
                status = '$new_status'
            WHERE debt_id = $debt_id";
    mysqli_query($connexion, $upd);

    // Enregistre le paiement comme une DÉPENSE dans mon compte.
    // -> il apparaît dans "Recent Activity" et diminue le "Total Balance" du dashboard.
    $pay_desc = mysqli_real_escape_string($connexion, "Payment to " . $debt['name']);
    $ins_tx = "INSERT INTO transactions
               (user_id, type, category, amount, transaction_date, is_recurring, description, split_status)
               VALUES
               ($session_user_id, 'expense', 'Debt Payment', $pay_amount, CURRENT_DATE(), 0, '$pay_desc', 'none')";
    mysqli_query($connexion, $ins_tx);

    // Enregistre le paiement comme un REVENU pour le CRÉANCIER (la personne payée).
    // -> son Total Balance augmente et ça apparaît dans son Recent Activity.
    $recv_desc = mysqli_real_escape_string($connexion, "Payment from " . $session_username);
    $ins_income = "INSERT INTO transactions
                   (user_id, type, category, amount, transaction_date, is_recurring, description, split_status)
                   VALUES
                   ($creditor_id, 'income', 'Debt Payment', $pay_amount, CURRENT_DATE(), 0, '$recv_desc', 'none')";
    mysqli_query($connexion, $ins_income);

    // Message envoyé au créancier
    $paid_fmt = number_format($pay_amount, 2);
    $rem_fmt  = number_format($new_remaining, 2);
    $texte = "$session_username paid you $paid_fmt €. Remaining: $rem_fmt €.";
    $texte = mysqli_real_escape_string($connexion, $texte);
    $msg = "INSERT INTO message (IdExpediteur, IdDestinataire, DateMessage, Message)
            VALUES ($session_user_id, $creditor_id, current_timestamp(), '$texte')";
    mysqli_query($connexion, $msg);

    if ($new_status === 'paid') {
        $_SESSION['flash_message'] = "Paid " . $debt['name'] . " in full ($paid_fmt €). Debt cleared!";
    } else {
        $_SESSION['flash_message'] = "Paid $paid_fmt € to " . $debt['name'] . ". Remaining: $rem_fmt €.";
    }
} else {
    $_SESSION['flash_erreur'] = "Could not process payment (debt not found).";
}

require 'base_deconnexion.php';
header("Location: debt_center.php");
exit();
?>
