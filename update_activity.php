<?php
session_start();
require 'base_connexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['session_user_id'])) {
    
    $session_user_id = $_SESSION['session_user_id'];
    
    // 1. Grab the data from the form
    // (Always clean inputs going into the database!)
    $transaction_id = mysqli_real_escape_string($connexion, $_POST['transaction_id']);
    $description = mysqli_real_escape_string($connexion, $_POST['description']);
    $amount = mysqli_real_escape_string($connexion, $_POST['amount']);
    
    // 2. The UPDATE Query
    // We say: Update the transactions table, SET these specific columns to the new values, 
    // WHERE the ID matches the hidden input from our form.
    $sql_update = "UPDATE transactions 
                   SET description = '$description', amount = '$amount'
                   WHERE transaction_id = '$transaction_id' AND user_id = '$session_user_id'";

    if (mysqli_query($connexion, $sql_update)) {
        // Success! Send them back to the dashboard
        header("Location: transaction.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($connexion);
    }
}
?>