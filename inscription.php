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
$name = "";
$mail = "";
$username = "";
$passe1 = "";
$passe2 = "";
$telephone = "";

// ***********************************************
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
        $requete = "SELECT * FROM users WHERE user_id = '$session_user_id';";
        
        // Exécution de la requête
        $resultat = mysqli_query($connexion, $requete);
        if ($resultat) {
            // Vérification du nombre de lignes du résultat
            if (mysqli_num_rows($resultat) != 0) {
                // L'identifiant existe
                // Récupération de la ligne de la table correspondant
                // à l'utilisateur connecté
                $utilisateur = mysqli_fetch_assoc($resultat);
                
                // Initialisation des variables utilisées pour remplir le formulaire
                // avec les informations de l'utilisateur connecté
                $name = $utilisateur['name'];
                $mail = $utilisateur['email'];
                $username = $utilisateur['username'];
                $passe1 = "";
                $passe2 = "";
                $telephone = $utilisateur['phone_number'];

            } else {
                // L'identifiant n'existe pas !
                $message_erreur .= "Utilisateur inconnu<br>\n";
            }
        } else {
            $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
            $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
        }
    }
}

if (isset($_POST['inscrire']) || isset($_POST['modifier'])) {
    //***************************
    // Clic sur le bouton "S'inscrire" de valeur name="inscrire"
    // ou sur le bouton "Modifier" de valeur name="modifier"
    // Traitement du formulaire
    //
    // Filtrage du contenu de $_POST et assignation à des variables locales
    // htmlspecialchars() : Convertit les caractères spéciaux en entités HTML
    // trim() : Supprime les espaces (ou d'autres caractères) en début et fin de chaîne
    $name = htmlspecialchars($_POST['name']);
    $username = htmlspecialchars($_POST['username']);
    $mail = htmlspecialchars($_POST['mail']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $passe1 = trim($_POST['passe1']);
    $passe2 = trim($_POST['passe2']);

    // Vérification de toutes les valeurs saisies
    if (empty($name)) {
        $message_erreur .= "Le champ Nom Complet est obligatoire<br>\n";
    }

    if (empty($mail)) {
        $message_erreur .= "Le champ mail est obligatoire<br>\n";
    } elseif (strlen($mail) > 250) {
        $message_erreur .= "Le champ mail doit être inférieur à 250 caractères<br>\n";
    } elseif (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i', $mail)) {
        $message_erreur .= "Le champ mail doit être valide mail@domaine.fr<br>\n";
    }

    if (empty($username)) {
        $message_erreur .= "Le champ pseudo est obligatoire<br>\n";
    } elseif (strlen($username) > 10 || strlen($username) < 5) {
        $message_erreur .= "Le pseudo doit être composé de 5 à 10 caractères<br>\n";
    } elseif (!preg_match('/^[a-zA-Z0-9]*$/u', $username)) {
        $message_erreur .= "Le pseudo ne doit comporter que des lettres non accentuées ou des chiffres et pas d'espaces<br>\n";
    }

    if (empty($passe1)) {
        $message_erreur .= "Le mot de passe est obligatoire<br>\n";
    } elseif (strlen($passe1) < 6) {
        $message_erreur .= "Le mot de passe doit contenir au moins 6 caractères<br>\n";
    } elseif (!preg_match('/^[[:graph:]]*$/u', $passe1)) {
        // [[:graph:]] : tous les caractères imprimables sauf l'espace
        $message_erreur .= "Le mot de passe ne doit pas comporter d'espaces<br>\n";
    }

    if (strcmp($passe1, $passe2) != 0) {
        $message_erreur .= "Les mots de passe sont différents<br>\n";
    }

    // Chiffrement du mot de passe
    $passe_chiffre = password_hash($passe1, PASSWORD_DEFAULT);

    // Si aucun message d'erreur
    if (empty($message_erreur)) {
        //*******************************************
        // Saisie des données du formulaire dans la table utilisateur
        // après vérification que le mail et le pseudo n'existent 
        // pas déjà dans la table
        //
        // Vérification que le mail n'existe pas dans la table utilisateur
        if (isset($session_user_id)) {
            // Un utilisateur est connecté
            $requete = "SELECT * FROM users WHERE email = '$mail' AND user_id != '$session_user_id'";
        } else {
            // Aucun utilisateur connecté
            $requete = "SELECT * FROM users WHERE email = '$mail'";
        }
        
        // Exécution de la requête
        $resultat = mysqli_query($connexion, $requete);
        if ($resultat && mysqli_num_rows($resultat) != 0) {
            $message_erreur .= "Le mail \"$mail\" existe déjà<br>\n";
        }

        // Vérification que le pseudo n'existe pas dans la table utilisateur
        if (isset($session_user_id)) {
            // Un utilisateur est connecté
            $requete = "SELECT * FROM users WHERE username = '$username' AND user_id != '$session_user_id'";
        } else {
            // Aucun utilisateur connecté
            $requete = "SELECT * FROM users WHERE username = '$username'";
        }
        
        // Exécution de la requête
        $resultat = mysqli_query($connexion, $requete);
        if ($resultat && mysqli_num_rows($resultat) != 0) {
            $message_erreur .= "Le pseudo \"$username\" existe déjà<br>\n";
        }
    }

    // Si aucun message d'erreur (Enregistrement ou Mise à jour)
    if (empty($message_erreur)) {
        if (isset($session_user_id)) {
            // Un utilisateur est connecté
            // Requête de mise à jour de l'utilisateur dans la table utilisateur
            $requete = "UPDATE users SET 
                        name = '$name', 
                        email = '$mail', 
                        phone_number = '$telephone', 
                        username = '$username', 
                        password_hash = '$passe_chiffre' 
                        WHERE user_id = '$session_user_id'";
        } else {
            // Aucun utilisateur connecté
            // Requête d'insertion de l'utilisateur dans la table utilisateur
            $requete = "INSERT INTO users (name, email, phone_number, username, password_hash) 
                        VALUES ('$name', '$mail', '$telephone', '$username', '$passe_chiffre')";
        }
        
        // Exécution de la requête
        $resultat = mysqli_query($connexion, $requete);
        if ($resultat) {
            // Affiche un message de confirmation ainsi que les valeurs saisies
            if (isset($session_user_id)) {
                $message .= "<div class='ui positive message'><p>Nous avons pris en compte votre modification.</p></div>\n";
            } else {
                $message .= "<div class='ui positive message'><p>Nous avons pris en compte votre inscription. <a href='login.php'>Se connecter ici.</a></p></div>\n";
            }
            // Optional: You can uncomment these if you want to show what was typed!
            /*
            $message .= "<ul>\n";
            $message .= "<li>Nom : " . $name . "</li>\n";
            $message .= "<li>Mail : " . $mail . "</li>\n";
            $message .= "<li>Username : " . $username . "</li>\n";
            $message .= "</ul>\n";
            */
            
        } else {
            $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
            $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
        }
    }
    
     header("Location: login.php");
}

// ***********************************************
// Déconnexion de la base de données
require 'base_deconnexion.php';

// Construction de la page HTML
require 'header.php';

// Affichage des éventuels messages de l'application
require 'messages_application.php';

// S'il y a eu des erreurs ou si aucun appui sur les boutons "S'incrire" ou "Modifier"
if (!empty($message_erreur) || !(isset($_POST['inscrire']) || isset($_POST['modifier'])) || !empty($message)) {
    ?>
    <div class="ui segment">     
        <h1 class="ui header"><?php echo isset($session_user_id) ? "Modifier le Profil" : "Inscription"; ?></h1>
        
        <form class="ui form" method="POST" action="">
            <h4 class="ui dividing header">Coordonnées</h4>
            
            <div class="field">
                <label>Nom Complet (Name)</label>
                <input type="text" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            
            <div class="field">
                <label for="edit-mail">Adresse Mail</label>
                <input type="email" id="edit-mail" name="mail" placeholder="Adresse Mail" value="<?php echo htmlspecialchars($mail); ?>" maxlength="250" required>
            </div>
            
            <div class="field">
                <label for="edit-pseudo">Username</label>
                <input type="text" id="id_username" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username); ?>" minlength="5" maxlength="10" required>
            </div>

            <div class="two fields">
                <div class="field">
                    <label for="edit-passe1">Mot de passe</label>
                    <input type="password" id="edit-passe1" name="passe1" placeholder="Mot de passe" minlength="6" required>
                </div>  
                <div class="field">
                    <label for="edit-passe2">Confirmer le mot de passe</label>
                    <input type="password" id="edit-passe2" name="passe2" placeholder="Confirmer le mot de passe" minlength="6" required>
                </div>
            </div>

            <div class="field">
                <label for="edit-telephone">Numéro de téléphone</label>
                <input type="tel" id="edit-telephone" name="telephone" placeholder="Numéro de téléphone (facultatif)" value="<?php echo htmlspecialchars($telephone); ?>" maxlength="50">
            </div>

            <div class="field">
                <?php if (isset($session_user_id)) { // Un utilisateur est connecté  ?>
                    <input type="submit" class="ui primary button" name="modifier" value="Sauvegarder">
                    <a href="dashboard.php" class="ui basic button">Annuler</a>
                <?php } else { // Aucun utilisateur connecté   ?>
                    <input type="submit" class="ui primary button" name="inscrire" value="S'inscrire">

                    <a href="login.php" class="ui basic button">J'ai déjà un compte</a>
                <?php } ?>
            </div>
        </form>
    </div>
    <?php
}

// Pied de page
require 'footer.php';
?>