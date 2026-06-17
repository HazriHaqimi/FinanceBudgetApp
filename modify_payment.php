<?php
// ***********************************************
// modify_payment.php
// Corrige le montant TOTAL payé sur une dette que JE dois.
// - met à jour le restant dû (et le statut) de la dette
// - ajuste les DEUX soldes : moi (payeur) et le créancier
//   en créant une transaction d'ajustement de chaque côté
// - prévient le créancier par un message
// Reçu depuis le popup "Modify" de debt_center.php (POST).
// ***********************************************

require 'authentification.php';   // donne $session_user_id, $session_username
require 'base_connexion.php';

$debt_id  = isset($_POST['debt_id'])  ? (int) $_POST['debt_id']    : 0;
$new_paid = isset($_POST['new_paid']) ? (float) $_POST['new_paid'] : -1;

if ($debt_id <= 0 || $new_paid < 0) {
    $_SESSION['flash_erreur'] = "Invalid amount.";
    require 'base_deconnexion.php';
    header("Location: debt_center.php");
    exit();
}

// On récupère la dette : c'est une dette que JE dois (je suis friend_user_id).
$query = "SELECT d.user_id AS creditor_id, d.original_amount, d.remaining_amount, u.name
          FROM debts d
          INNER JOIN users u ON u.user_id = d.user_id
          WHERE d.debt_id = $debt_id
            AND d.friend_user_id = $session_user_id
            AND d.debt_type = 'they_owe'
          LIMIT 1";
$result = mysqli_query($connexion, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $debt        = mysqli_fetch_assoc($result);
    $original    = (float) $debt['original_amount'];
    $remaining   = (float) $debt['remaining_amount'];
    $creditor_id = (int) $debt['creditor_id'];
    $name        = $debt['name'];

    // Le nouveau montant payé ne peut pas dépasser la dette totale
    if ($new_paid > $original) {
        $new_paid = $original;
    }

    $old_paid = round($original - $remaining, 2);
    $new_paid = round($new_paid, 2);
    $diff     = round($new_paid - $old_paid, 2);   // + = j'ai payé plus, - = remboursement

    if ($diff == 0.0) {
        $_SESSION['flash_message'] = "No change made (amount is the same).";
        require 'base_deconnexion.php';
        header("Location: debt_center.php");
        exit();
    }

    // 1) Mise à jour de la dette
    $new_remaining = round($original - $new_paid, 2);
    if ($new_remaining < 0) {
        $new_remaining = 0;
    }
    $new_status = ($new_remaining <= 0) ? 'paid' : 'pending';
    mysqli_query($connexion, "UPDATE debts
                              SET remaining_amount = $new_remaining, status = '$new_status'
                              WHERE debt_id = $debt_id");

    // 2) Ajustement de MON solde uniquement (modèle "ma part").
    //    Le créancier n'est pas touché : il n'avait jamais compté cet argent
    //    comme un revenu, il avançait juste ma part.
    $amt = abs($diff);
    if ($diff > 0) {
        // J'ai payé PLUS : dépense supplémentaire pour moi
        $my_desc = mysqli_real_escape_string($connexion, "Payment adjustment to " . $name);
        $my_type = 'expense';
    } else {
        // J'ai payé MOINS : remboursement -> revenu pour moi
        $my_desc = mysqli_real_escape_string($connexion, "Refund from " . $name);
        $my_type = 'income';
    }

    mysqli_query($connexion, "INSERT INTO transactions
        (user_id, type, category, amount, transaction_date, is_recurring, description, split_status)
        VALUES ($session_user_id, '$my_type', 'Debt Payment', $amt, CURRENT_DATE(), 0, '$my_desc', 'none')");

    // 3) Message au créancier (information)
    $new_paid_fmt = number_format($new_paid, 2);
    $amt_fmt      = number_format($amt, 2);
    if ($diff > 0) {
        $texte = "$session_username corrected the payment: paid $amt_fmt € more (total paid now $new_paid_fmt €).";
    } else {
        $texte = "$session_username corrected the payment: $amt_fmt € refunded (total paid now $new_paid_fmt €).";
    }
    $texte = mysqli_real_escape_string($connexion, $texte);
    mysqli_query($connexion, "INSERT INTO message (IdExpediteur, IdDestinataire, DateMessage, Message)
                              VALUES ($session_user_id, $creditor_id, current_timestamp(), '$texte')");

    $_SESSION['flash_message'] = "Payment to $name updated. Total paid is now $new_paid_fmt €.";
} else {
    $_SESSION['flash_erreur'] = "Could not modify payment (debt not found).";
}

require 'base_deconnexion.php';
header("Location: debt_center.php");
exit();
?>
