<?php
// Démarrage d'une session si cela n'a pas déjà été fait
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

if (isset($_SESSION['session_user_id'])) {
  // Un utilisateur est connecté
  // Récupération de l'identifiant, du pseudo, du nom et du prénom
  // de l'utilisateur connecté dans des variables de session
  $session_user_id = $_SESSION['session_user_id'];
  $session_username = $_SESSION['session_username'];
}
?>

<!doctype html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Budget Financier</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
    <link rel="stylesheet" type="text/css" href="cuicui.css">
  </head>
  <body>
   
    <div class="app-layout">
        <div class="ui inverted fixed menu">
            <div class="item"><strong><a href="dashboard.php">Budget Financier</a></strong></div>
            
            
              <?php //Si l'utilisateur n'est pas encore connecte
              if (!isset($session_user_id)) { ?>
                    <div class="right menu">
                        <!-- <a class="item" href="login.php">Sign in</a> -->
                        <!-- <a class="item" href="inscription.php">Inscription</a> -->
                    </div>

                <?php }
                
                else { ?>
                    <a class="item" href="dashboard.php">Dashboard</a>
                    <a class="item" href="transaction.php">Transaction</a>
                    <a class="item" href="debt_center.php">Debt Center</a>
                    <!--<a class="item" href="contacts.php">Contacts</a>-->
                    <a class="item" href="friends.php">Friends</a>

                    <div class="right menu">
                        <div class="item">
                            <i class="user circle icon"></i> 
                            <a href="inscription.php"><?php echo htmlspecialchars($session_username); ?></a>
                        </div>
                        <a class="item" href="logout.php" id="sign_out">Sign out</a>
                    </div>

                <?php } ?>
          </div>
    <div class="app-content">
        <div class="ui main text container">

  