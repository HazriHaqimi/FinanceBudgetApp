<?php
// La variable $message contiendra les éventuels messages de l'application à afficher
$message = "";

// La variable $message_erreur contiendra les éventuels messages d'erreur de l'application à afficher
$message_erreur = "";

// ***********************************************
// Connexion à la base de données cuicui du serveur localhost
require 'base_connexion.php';

// **********************************************
// Traitement du formulaire
//
// Initialisation des variables contenant les données saisies dans le formulaire
// et utilisées pour remplir le formulaire
$sexe = "";
$civilite = "";
$checked_h = "";
$checked_f = "";
$nom = "";
$prenom = "";
$mail = "";
$telephone = "";
$pseudo = "";
$passe1 = "";
$passe2 = "";
$abo_newsletter = "";
$checked_abonews = "";
$commentaire = "";

// ***********************************************
// Vérification de la connexion d'un utilisateur
// Démarrage d'une session si cela n'a pas déjà été fait
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
if (isset($_SESSION['session_idutilisateur'])) {
  // Un utilisateur est connecté
  // Récupération de l'identifiant, du pseudo, du nom et du prénom
  // de l'utilisateur connecté dans les variables de session
  $session_idutilisateur = $_SESSION['session_idutilisateur'];
  $session_pseudo = $_SESSION['session_pseudo'];
  $session_nom = $_SESSION['session_nom'];
  $session_prenom = $_SESSION['session_prenom'];

  // Si aucun message d'erreur
  if (empty($message_erreur)) {
    //*******************************************
    // Récupération des informations de l'utilisateur connecté
    // dans la table utilisateur
    // Requête SQL
    $requete = "select * from utilisateur where IdUtilisateur = '$session_idutilisateur';";
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
        // avec les informations de l'utilisateur connexté
        $sexe = $utilisateur['Sexe'];
        $checked_h = ($sexe == 'H') ? 'checked' : '';
        $checked_f = ($sexe == 'F') ? 'checked' : '';
        $nom = $utilisateur['Nom'];
        $prenom = $utilisateur['Prenom'];
        $mail = $utilisateur['Mail'];
        $telephone = $utilisateur['Telephone'];
        $pseudo = $utilisateur['Pseudo'];
        $passe1 = "";
        $passe2 = "";
        $abo_newsletter = $utilisateur['AboNewsletter'];
        $checked_abonews = ($abo_newsletter == 1) ? 'checked' : '';
        $commentaire = $utilisateur['Commentaire'];
      } else {
        // L'identifiant n'existe pas !
        $message_erreur .= "Utilisateur iconnu<br>\n";
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
  $nom = trim(htmlspecialchars($_POST['nom'], ENT_COMPAT));
  $prenom = trim(htmlspecialchars($_POST['prenom'], ENT_COMPAT));
  $mail = htmlspecialchars($_POST['mail']);
  $telephone = htmlspecialchars($_POST['telephone']);
  $pseudo = htmlspecialchars($_POST['pseudo']);
  $passe1 = trim($_POST['passe1']);
  $passe2 = trim($_POST['passe2']);
  $commentaire = htmlspecialchars($_POST['commentaire']);

  // Vérification de toutes les valeurs saisies

  if (isset($_POST['civilite'])) {
    $sexe = $_POST['civilite'];
    if ($sexe == 'H') {
      $checked_h = "checked";
      $civilite = "M";
    } else {
      $checked_f = "checked";
      $civilite = "Mme";
    }
  } else {
    $message_erreur .= "La civilité doit être cochée<br>\n";
  }

  if (empty($nom)) {
    $message_erreur .= "Le champ nom est obligatoire<br>\n";
  } elseif (strlen($nom) > 100) {
    $message_erreur .= "Le nom ne doit pas comporter plus de 100 caractères<br>\n";
  } elseif (!preg_match('/^([[:alpha:]]|-|[[:space:]]|\')*$/u', $nom)) {
    // [[:alpha:]] : caractères alphabétique
    // [[:space:]] : espace blanc
    $message_erreur .= "Le nom ne doit comporter que des lettres<br>\n";
  }

  if (empty($prenom)) {
    $message_erreur .= "Le champ prenom est obligatoire<br>\n";
  } elseif (strlen($prenom) > 100) {
    $message_erreur .= "Le prénom ne doit pas comporter plus de 100 caractères<br>\n";
  } elseif (!preg_match('/^([[:alpha:]]|-|[[:space:]]|\')*$/u', $prenom)) {
    $message_erreur .= "Le prénom ne doit comporter que des lettres<br>\n";
  }

  if (empty($mail)) {
    $message_erreur .= "Le champ mail est obligatoire<br>\n";
  } elseif (strlen($mail) > 250) {
    $message_erreur .= "Le champ mail doit être inférieur à 250 caractères<br>\n";
  } elseif (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i', $mail)) {
    $message_erreur .= "Le champ mail doit être valide mail@domaine.fr<br>\n";
  }

  if (empty($pseudo)) {
    $message_erreur .= "Le champ pseudo est obligatoire<br>\n";
  } elseif (strlen($pseudo) > 10 || strlen($pseudo) < 5) {
    $message_erreur .= "Le pseudo doit être composé de 5 à 10 caractères<br>\n";
  } elseif (!preg_match('/^[a-zA-Z0-9]*$/u', $pseudo)) {
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

  if (isset($_POST['abo_newsletter'])) {
    $abo_newsletter = "Oui";
    $checked_abonews = "checked";
  } else {
    $abo_newsletter = "Non";
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
    if (isset($session_idutilisateur)) {
      // Un utilisateur est connecté
      $requete = "select * from utilisateur where Mail = '$mail' and IdUtilisateur != $session_idutilisateur";
    } else {
      // Aucun utilisateur connecté
      $requete = "select * from utilisateur where Mail = '$mail'";
    }
    // Exécution de la requête
    $resultat = mysqli_query($connexion, $requete);
    if ($resultat) {
      // Vérification du nombre de lignes du résultat
      if (mysqli_num_rows($resultat) != 0) {
        // Le mail existe déjà dans la table
        $message_erreur .= "Le mail \"$mail\" existe déjà<br>\n";
      }
    } else {
      $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
      $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
    }
    // Vérification que le pseudo n'existe pas dans la table utilisateur
    if (isset($session_idutilisateur)) {
      // Un utilisateur est connecté
      $requete = "select * from utilisateur where Pseudo = '$pseudo' and IdUtilisateur != $session_idutilisateur";
    } else {
      // Aucun utilisateur connecté
      $requete = "select * from utilisateur where Pseudo = '$pseudo'";
    }
    // Exécution de la requête
    $resultat = mysqli_query($connexion, $requete);
    if ($resultat) {
      // Vérification du nombre de lignes du résultat
      if (mysqli_num_rows($resultat) != 0) {
        // Le pseudo existe déjà dans la table
        $message_erreur .= "Le pseudo \"$pseudo\" existe déjà<br>\n";
      }
    } else {
      $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
      $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
    }
  }

  // Si aucun message d'erreur
  if (empty($message_erreur)) {
    if (isset($session_idutilisateur)) {
      // Un utilisateur est connecté
      // Requête de mise à jour de l'utilisateur dans la table utilisateur
      $requete = "update utilisateur set "
              . "Sexe = '$sexe',Nom = \"$nom\",Prenom = \"$prenom\", "
              . "Mail = '$mail', Telephone = ";
      $requete .= empty($telephone) ? "null" : "'$telephone'";
      $requete .= ",Pseudo = '$pseudo',Password = '$passe_chiffre', AboNewsletter = ";
      $requete .= ($abo_newsletter === "Oui") ? "1,Commentaire = " : "0,Commentaire = ";
      $requete .= empty($commentaire) ? "null" : "\"$commentaire\"";
      $requete .= " where IdUtilisateur = $session_idutilisateur;";
    } else {
      // Aucun utilisateur connecté
      // Requête d'insertion de l'utilisateur dans la table utilisateur
      $requete = "insert into utilisateur "
              . "values (null, '$sexe',\"$nom\",\"$prenom\",'$mail',";
      $requete .= empty($telephone) ? "null" : "'$telephone'";
      $requete .= ",'$pseudo','$passe_chiffre',";
      $requete .= ($abo_newsletter === "Oui") ? "1," : "0,";
      $requete .= empty($commentaire) ? "null" : "\"$commentaire\"";
      $requete .= ");";
    }
    // Exécution de la requête
    $resultat = mysqli_query($connexion, $requete);
    if ($resultat) {
      // Affiche un message de confirmation ainsi que les valeurs saisies
      if (isset($session_idutilisateur)) {
        // Un utilisateur est connecté
        $message .= "<p>Nous avons pris en compte votre modification.\n";
      } else {
        // Aucun utilisateur connecté
        $message .= "<p>Nous avons pris en compte votre inscription.\n";
      }
      $message .= "<br>Voici les données saisies :</p>\n";
      $message .= "<ul>\n";
      $message .= "<li>Civilité : " . $civilite . "</li>\n";
      $message .= "<li>Nom : " . $nom . "</li>\n";
      $message .= "<li>Prénom : " . $prenom . "</li>\n";
      $message .= "<li>Mail : " . $mail . "</li>\n";
      if (empty($telephone)) {
        $telephone = "Non saisi";
      }
      $message .= "<li>Téléphone : " . $telephone . "</li>\n";
      $message .= "<li>Pseudo : " . $pseudo . "</li>\n";
      $message .= "<li>Mot de passe chiffré : " . $passe_chiffre . "</li>\n";
      $message .= "<li>Inscription à la newsletter : " . $abo_newsletter . "</li>\n";
      if (empty($commentaire)) {
        $commentaire = "Aucun";
      }
      $message .= "<li>Commentaire : \n<blockquote>" . nl2br($commentaire, false) . "</blockquote></li>\n";
      $message .= "</ul>\n";
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
  <div class="ui segment">     
    <h1 class="ui header">Inscription</h1>
    <form class="ui form" method="POST" action="">
      <h4 class="ui dividing header">Coordonnées</h4>
      <div class="ui inline fields">
        <label>Civilité :</label>
        <div class="field">
          <div class="ui radio checkbox">
            <input type="radio" id="edit-m" name="civilite" value="H" <?php echo $checked_h ?>>
            <label for="edit-m">M</label>
          </div>
        </div>
        <div class="field">
          <div class="ui radio checkbox">
            <input type="radio" id="edit-mme" name="civilite" value="F" <?php echo $checked_f ?>>
            <label for="edit-mme">Mme</label>
          </div>
        </div>
      </div>
      <div class="two fields">
        <div class="field">
          <label for="edit-nom">Nom</label>
          <input type="text" id="edit-nom" name="nom" placeholder="Nom" value="<?php echo $nom ?>" maxlength="100" required>
        </div>
        <div class="field">
          <label for="edit-prenom">Prénom</label>
          <input type="text" id="edit-prenom" name="prenom" placeholder="Prénom" value="<?php echo $prenom ?>" maxlength="100" required>
        </div>
      </div>
      <div class="field">
        <label for="edit-mail">Adresse Mail</label>
        <input type="email" id="edit-mail" name="mail" placeholder="Adresse Mail" value="<?php echo $mail ?>" maxlength="250" required>
      </div>
      <div class="field">
        <label for="edit-telephone">Numéro de téléphone</label>
        <input type="tel" id="edit-telephone" name="telephone" placeholder="Numéro de téléphone (facultatif)" value="<?php echo $telephone ?>" maxlength="50">
      </div>
      <h4 class="ui dividing header">Informations de connexion</h4>
      <div class="field">
        <label for="edit-pseudo">Pseudo</label>
        <input type="text" id="edit-pseudo" name="pseudo" placeholder="Pseudo" value="<?php echo $pseudo ?>" minlength="5" maxlength="10" required>
      </div>  
      <div class="two fields">
        <div class="field">
          <label for="edit-passe1">Mot de passe</label>
          <input type="password" id="edit-passe1" name="passe1" placeholder="Mot de passe" value="" minlength="6" required>
        </div>  
        <div class="field">
          <label for="edit-passe2">Confirmer le mot de passe</label>
          <input type="password" id="edit-passe2" name="passe2" placeholder="Mot de passe" value="" minlength="6" required>
        </div>
      </div>
      <h4 class="ui dividing header">Divers</h4>
      <div class="field">
        <div class="ui checkbox">
          <input type="checkbox" id="edit-abo_newsletter" name="abo_newsletter" value="" <?php echo $checked_abonews ?>>
          <label for="edit-abo_newsletter">Abonnement à la newsletter</label>
        </div>
      </div>
      <div class="field">
        <label for="edit-commentaire">Commentaire</label>
        <textarea id="edit-commentaire" name="commentaire" placeholder="Laissez un commentaire" rows="2"><?php echo $commentaire ?></textarea>
      </div>
      <?php if (isset($session_idutilisateur)) { // Un utilisateur est connecté ?>
        <input type="submit" class="ui button" name="modifier" value="Modifier">
      <?php } else { // Aucun utilisateur connecté  ?>
        <input type="submit" class="ui button" name="inscrire" value="S'inscrire">
      <?php } ?>
    </form>
  </div>
  <?php
}

// Pied de page
require 'footer.php';
?>