<?php
// La variable $message contiendra les éventuels messages de l'application à afficher
$message = "";

// La variable $message_erreur contiendra les éventuels messages d'erreur de l'application à afficher
$message_erreur = "";

// ***********************************************
// Connexion à la base de données budget_financier
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

                // Filtre par catégorie (optionnel, via ?category=...)
                $filter_category = isset($_GET['category']) ? mysqli_real_escape_string($connexion, $_GET['category']) : '';

                // Liste des catégories existantes pour remplir le menu déroulant
                $cat_list = [];
                $res_cats = mysqli_query($connexion, "SELECT DISTINCT category FROM transactions "
                        . "WHERE user_id='$session_user_id' AND category IS NOT NULL AND category <> '' "
                        . "ORDER BY category ASC");
                if ($res_cats) {
                    while ($c = mysqli_fetch_assoc($res_cats)) {
                        $cat_list[] = $c['category'];
                    }
                }

                // On ajoute la condition de catégorie seulement si un filtre est choisi
                $where_cat = $filter_category !== '' ? " AND category = '$filter_category' " : "";

                // On compte aussi les dettes encore en cours liées à chaque transaction
                // (pour bloquer la suppression tant que la dette n'est pas réglée).
                $requete_recent_activities = "SELECT t.transaction_id, t.transaction_date, t.description,
                        t.amount, t.category,
                        (SELECT COUNT(*) FROM debts d
                         WHERE d.transaction_id = t.transaction_id AND d.status = 'pending') AS pending_debts
                    FROM transactions t
                    WHERE t.user_id='$session_user_id' "
                        . str_replace('category', 't.category', $where_cat)
                        . " ORDER BY t.transaction_date DESC";

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

// Récupération des messages "flash" (définis avant une redirection, ex: add_transaction)
if (!empty($_SESSION['flash_message'])) {
    $message .= $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
if (!empty($_SESSION['flash_erreur'])) {
    $message_erreur .= $_SESSION['flash_erreur'];
    unset($_SESSION['flash_erreur']);
}

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

                    <!-- Filtre par catégorie -->
                    <form method="GET" action="transaction.php" style="display:inline-block; float:right; margin:0;">
                        <div class="ui left icon input" style="vertical-align:middle;">
                            <i class="filter icon"></i>
                            <select name="category" onchange="this.form.submit()" style="padding-left:2.5em;">
                                <option value="">All categories</option>
    <?php foreach ($cat_list as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($filter_category === $cat) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                    </option>
                                    <?php endforeach; ?>
                            </select>
                        </div>
    <?php if ($filter_category !== ''): ?>
                            <a href="transaction.php" class="ui basic button">Clear</a>
                        <?php endif; ?>
                    </form>
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
    <?php if (mysqli_num_rows($result_recent_activities) == 0): ?>
                                    <tr>
                                        <td colspan="5" style="text-align:center; color:#999;">
                                            No transactions found<?php echo $filter_category !== '' ? ' for "' . htmlspecialchars($filter_category) . '"' : ''; ?>.
                                        </td>
                                    </tr>
    <?php
    else: while ($row = mysqli_fetch_assoc($result_recent_activities)) {
            $is_payment = ($row['category'] === 'Debt Payment');
            $has_pending = ($row['pending_debts'] > 0);
            ?>
                                        <tr>
                                            <td><?php echo $row['transaction_date'] ?></td>
                                            <td><?php echo $row['description'] ?></td>
                                            <td><?php echo $row['category'] ?></td>
                                            <td><?php echo $row['amount'] ?> €</td>
                                            <td>
                                                <?php if ($is_payment): ?>
                                                    <span data-tooltip="Debt payments are managed in the Debt Center" data-position="left center">
                                                        <button class="ui icon button disabled"><i class="trash icon"></i></button>
                                                        <button class="ui icon button disabled"><i class="lock icon"></i></button>
                                                    </span>
                                                <?php else: ?>
                                                    <?php if ($has_pending): ?>
                                                        <span data-tooltip="Delete is locked until the debt is fully paid" data-position="left center">
                                                            <button class="ui icon button disabled"><i class="trash icon"></i></button>
                                                        </span>
                                                    <?php else: ?>
                                                        <a href="delete_activity.php?id=<?php echo $row['transaction_id']; ?>" class="ui red icon button">
                                                            <i class="trash icon"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="edit_activity.php?id=<?php echo $row['transaction_id']; ?>" class="ui grey icon button">
                                                        <i class="edit icon"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php } endif; ?>
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