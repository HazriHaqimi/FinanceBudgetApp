<?php
// Start the session and connect to the database
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'base_connexion.php';

// Check if the user is logged in AND if there is an ID in the URL
if (isset($_SESSION['session_user_id']) && isset($_GET['id'])) {
    
    $session_user_id = $_SESSION['session_user_id'];
    
    //security
    $transaction_id = mysqli_real_escape_string($connexion, $_GET['id']);

    // We make sure user_id matches so a user can't accidentally delete someone else's data!
    $sql_delete_debts = "DELETE FROM debts 
                         WHERE transaction_id = '$transaction_id' 
                         AND user_id = '$session_user_id'";
                         
    mysqli_query($connexion, $sql_delete_debts);

    $sql_delete_transaction = "DELETE FROM transactions 
                               WHERE transaction_id = '$transaction_id' 
                               AND user_id = '$session_user_id'";

    // Execute the final delete
    if (mysqli_query($connexion, $sql_delete_transaction)) {
        
        // Supprimé
        // Disconnect and instantly redirect the user back to the main page
        require 'base_deconnexion.php';
        header("Location: transaction.php");
        exit();
        
    } else {
        // If something goes horribly wrong with the SQL
        echo "Error deleting transaction: " . mysqli_error($connexion);
    }

} else {
   
    // without clicking a button, we just send them away.
    header("Location: transaction.php");
    exit();
}
?>