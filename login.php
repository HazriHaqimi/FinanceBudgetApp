<?php
// La variable $message contiendra les éventuels messages de l'application à afficher
$message = "";

// La variable $message_erreur contiendra les éventuels messages d'erreur de l'application à afficher
$message_erreur = "";

// ***********************************************
// Connexion à la base de données budget_financier
require 'base_connexion.php';

// **********************************************
// Traitement du formulaire
//
// Initialisation des variables contenant les données saisies dans le formulaire
// et utilisées pour remplir le formulaire
$pseudo = "";
$passe = "";

if (isset($_POST['connecter'])) {
    //***************************
    // Clic sur le bouton "Se connecter" de valeur name="connecter"
    // Traitement du formulaire
    $username = htmlspecialchars($_POST['username']);
    $password = trim($_POST['password_hash']);

    // Vérification de toutes les valeurs saisies
    if (empty($username)) {
        $message_erreur .= "Le champ Login est obligatoire<br>\n";
    }

    if (empty($password)) {
        $message_erreur .= "Le mot de passe est obligatoire<br>\n";
    }

    // Si aucun message d'erreur
    if (empty($message_erreur)) {
        //*******************************************
        // Vérification dans la table utilisateur :
        // - que le pseudo existe
        // - que le mot de passe saisi est valide
        //
        // Vérification que le pseudo existe dans la table
        $requete = "select * from users where username = '$username';";
        // Exécution de la requête
        $resultat = mysqli_query($connexion, $requete);
        if ($resultat) {
            // Vérification du nombre de lignes du résultat
            if (mysqli_num_rows($resultat) != 0) {
                // Le pseudo existe
                // Récupération de la ligne de la table correspondant à l'utilisateur 
                // ayant le pseudo $pseudo
                $ligne = mysqli_fetch_assoc($resultat);
                // Vérification que le mot de passe saisi correpond au mot de passe 
                // chiffré récupéré dans la base de données
                if (password_verify($password, $ligne['password_hash'])) {
                
                //temporary
                //if ($password === $ligne['password_hash']) {
                    // Le login et le mot de passe saisis sont valides
                    // -> Initialisation des variables de session
                    // 
                    // Démarrage d'une session si cela n'a pas déjà été fait
                    if (session_status() !== PHP_SESSION_ACTIVE) {
                        session_start();
                    }

                    // Enregistrement de l'identifiant, du pseudo, du nom et du prénom
                    // de l'utilisateur authentifié dans des variables de session
                    $_SESSION['session_user_id'] = $ligne['user_id'];
                    $_SESSION['session_username'] = $ligne['username'];
                    $_SESSION['session_name'] = $ligne['name'];

                    // Redirection vers la page index.php
                    header('Location: dashboard.php');

                    // Fin du script si la redirection n'a pas pu se faire
                    exit();
                } else {
                    // Le mot de passe saisi n'est pas valide
                    $message_erreur .= "Erreur de connexion<br>\n";
                }
            } else {
                // Le login saisi n'est pas valide
                $message_erreur .= "Erreur de connexion<br>\n";
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

// S'il y a eu des erreurs ou si aucun appui sur le bouton "Se connecter"
if (!empty($message_erreur) || !isset($_POST['connecter'])) { ?>

    <div class="ui segment">     
        <h1 class="ui header">Log In</h1>
        <form class="ui form" method="POST" action="">
            <div class="field">
                <label for="edit-pseudo">Username</label>
                <input type="text" id="edit-pseudo" name="username" placeholder="Username" required>
            </div>  
            <div class="field">
                <label for="edit-passe">Password</label>
                <input type="password" id="edit-passe" name="password_hash" placeholder="Password" required>
            </div>
            <div class="field">
                <input type="submit" class="ui primary button" name="connecter" value="Connect">
                <a href="inscription.php" class="ui right floated basic button">Create new account</a>
            </div>  
        </form>
    </div>

<?php } // Pied de page require 'footer.php'; ?>

