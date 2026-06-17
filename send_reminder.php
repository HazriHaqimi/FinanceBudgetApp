<?php
// ***********************************************
// send_reminder.php
// Sends an in-app reminder (via the `message` table) to a friend
// who still owes the logged-in user money.
// Reached from debt_center.php:  send_reminder.php?debt_id=XX
// ***********************************************

// Authentification obligatoire (donne $session_user_id et $session_username)
require 'authentification.php';

// Connexion à la base de données
require 'base_connexion.php';

// 1. Récupération et vérification du debt_id passé dans l'URL
$debt_id = isset($_GET['debt_id']) ? (int) $_GET['debt_id'] : 0;

if ($debt_id <= 0) {
    header("Location: debt_center.php");
    exit();
}

// 2. On va chercher la dette + le nom de l'ami.
//    On vérifie aussi que la dette appartient bien à l'utilisateur connecté
//    et que c'est bien quelqu'un qui ME doit de l'argent (they_owe).
$query = "SELECT d.remaining_amount, d.friend_user_id, u.name
          FROM debts d
          INNER JOIN users u ON u.user_id = d.friend_user_id
          WHERE d.debt_id = $debt_id
            AND d.user_id = $session_user_id
            AND d.debt_type = 'they_owe'
            AND d.status = 'pending'
          LIMIT 1";
$result = mysqli_query($connexion, $query);

if ($result && mysqli_num_rows($result) == 1) {
    $debt = mysqli_fetch_assoc($result);

    $friend_user_id = (int) $debt['friend_user_id'];
    $remaining      = number_format($debt['remaining_amount'], 2);

    // 3. Construction du message de rappel
    $texte = "$session_username: Reminder, please pay back the $remaining "
           . "€ you still owe me. Thanks!";
    $texte = mysqli_real_escape_string($connexion, $texte);

    // 4. Envoi du message : de l'utilisateur connecté vers l'ami
    $requete_msg = "INSERT INTO message (IdExpediteur, IdDestinataire, DateMessage, Message)
                    VALUES ($session_user_id, $friend_user_id, current_timestamp(), '$texte')";
    mysqli_query($connexion, $requete_msg);

    // On garde un message de confirmation pour l'afficher au retour
    $_SESSION['flash_message'] = "Reminder sent to " . $debt['name'] . ".";
} else {
    $_SESSION['flash_erreur'] = "Could not send reminder (debt not found).";
}

// 5. Déconnexion et retour au Debt Center
require 'base_deconnexion.php';
header("Location: debt_center.php");
exit();
?>
