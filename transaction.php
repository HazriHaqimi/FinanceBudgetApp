<?php
// La variable $message contiendra les éventuels messages de l'application à afficher
$message = "";

// La variable $message_erreur contiendra les éventuels messages d'erreur de l'application à afficher
$message_erreur = "";

// ***********************************************
// Connexion à la base de données cuicui du serveur localhost
require 'base_connexion.php';

// **********************************************
// Vérification de la connexion d'un utilisateur
// Démarrage d'une session si cela n'a pas déjà été fait
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (isset($_SESSION['session_user_id'])) {
    // Un utilisateur est connecté
    // Récupération de l'identifiant, du pseudo, du nom et du prénom
    // de l'utilisateur connecté dans les variables de session
    $session_user_id = $_SESSION['session_user_id'];
    $session_username = $_SESSION['session_username'];

    // Si aucun message d'erreur
    if (empty($message_erreur)) {
        //*******************************************
        // Récupération des informations de l'utilisateur connecté
        // dans la table utilisateur
        // Requête SQL
        $requete = "select * from users where user_id = '$session_user_id';";
        // Exécution de la requête
        $resultat = mysqli_query($connexion, $requete);
        if ($resultat) {
            // Vérification du nombre de lignes du résultat
            if (mysqli_num_rows($resultat) != 0) {

                $requete_recent_activities = "SELECT transaction_id, transaction_date, description,"
        . "amount,category FROM transactions "
        . "WHERE user_id='$session_user_id' "
        . "ORDER BY transaction_date DESC";

                $result_recent_activities = mysqli_query($connexion, $requete_recent_activities);
            }
        } else {
            $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
            $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
        }
    }
}

// ***********************************************
// Déconnexion de la base de données
require 'base_deconnexion.php';

// Construction de la page HTML
require 'header.php';

// Affichage des éventuels messages de l'application
require 'messages_application.php';

// S'il y a eu des erreurs ou si aucun appui sur les boutons "S'incrire" ou "Modifier"
if (!empty($message_erreur) || !(isset($_POST['inscrire']) || isset($_POST['modifier']))) {
    ?>
    <!-- **************************************** -->
    <!-- Affichage du formulaire                  -->
    <div class="ui container">
        <h2 class="ui header">
            <div class="content">
                Transaction
            </div>
        </h2>

        <div class="ui stackable grid">

            <div class="row">
                <div class="sixteen wide column">
                    <a href="add_transaction.php" class="ui green button">
                        <i class="plus icon"></i> Add Transaction
                    </a>

                    <a href="transaction_filter.php" class="ui basic icon button right floated">
                        <i class="filter icon"></i> Filter
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="sixteen wide column">
                    <div class="ui segment">
                        <h3 class="ui header">Recent activities</h3>
                        <table class="ui padded unstackable celled table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_recent_activities)) { ?>
                                <tr>
                                <td><?php echo $row['transaction_date']?></td>
                                    <td><?php echo $row['description']?></td>
                                    <td><?php echo $row['category']?></td>
                                    <td><?php echo $row['amount']?> €</td>
                                    <td>
                                        <a href="delete_activity.php?id=<?php echo $row['transaction_id']; ?>" class="ui red icon button">
                                            <i class="trash icon"></i>
                                        </a>
                                        <a href="edit_activity.php?id=<?php echo $row['transaction_id']; ?>" class="ui grey icon button">
                                            <i class="edit icon"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php
        }

// Pied de page
        require 'footer.php';
        ?>