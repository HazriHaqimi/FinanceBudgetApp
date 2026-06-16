<?php
// Authentification obligatoire pour accéder à la page
require 'authentification.php';

// La variable $message contiendra les éventuels messages de l'application à afficher
$message = "";

// La variable $message_erreur contiendra les éventuels messages d'erreur de l'application à afficher
$message_erreur = "";

// ***********************************************
// Connexion à la base de données cuicui du serveur localhost
//
require 'base_connexion.php';

// Si aucun message d'erreur
if (empty($message_erreur)) {
  //***************************************************************************
  // ATTENTION : La page doit être construite APRES le traitement
  // des formulaires
  // Ainsi, les constructions :
  //  - du formulaire d'envoi d'un message
  //  - de la liste des derniers message
  //  - du formulaire des demandes
  //  - du formulaire d'invitation
  // doivent être placées APRES les traitements des formulaires :
  //   - d'envoi d'un messaage
  //   - des demandes
  //   - d'invitation
  // afin que les éventuelles modifications des relations soient visibles
  // après appui sur les boutons correspondants
  //***************************************************************************
  //
  //
  //***************************************************************************
  //   Traitement des formulaires
  //***************************************************************************
  //
  // **********************************************
  // Traitement du formulaire d'envoi d'un message
  //
  // La variable $id_expediteur contient l'identifiant de l'utilisateur connecté
  $id_expediteur = $session_idutilisateur;
  
  // Initialisation des variables contenant les données saisies dans le formulaire
  $id_destinataire = "";
  $message_a_envoyer = "";

  if (isset($_POST['envoyer'])) {
    //***************************
    // Clic sur le bouton "Envoyer" de valeur name="envoyer"
    // Traitement du formulaire d'envoi de message
    $id_destinataire = $_POST['id_destinataire'];
    $message_a_envoyer = $_POST['message_a_envoyer'];

    // Vérification de la validité des valeurs saisies

    if (empty($id_destinataire)) {
      $message_erreur .= "Le champ destinataire est obligatoire<br>\n";
    } elseif (!ctype_digit($id_destinataire)) {
      $message_erreur .= "L'identifiant $id_destinataire n'est pas valide<br>\n";
    }

    // Si aucun message d'erreur
    if (empty($message_erreur)) {
      //*******************************************
      // Saisie des données du formulaire dans la table message
      // après verification que l'identifiant du destinataire est 
      // bien ami avec le destinataire
      //
      $requete = "select *
                from relation
                where IdDemandeur = $id_expediteur
                  and IdAmi = $id_destinataire
                  and RelationAccepte = true";
      $resultat = mysqli_query($connexion, $requete);
      if ($resultat) {
        // Vérification du nombre de lignes du résultat
        if (mysqli_num_rows($resultat) == 0) {
          // $id_destinataire n'est pas ami avec $id_expediteur
          $message_erreur .= "Impossible d'envoyer un message à ce destinataire<br>\n";
        }
      } else {
        $message_erreur .= "Erreur de la requête $requete<br>\n";
        $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
      }
    }

    // Si aucun message d'erreur
    if (empty($message_erreur)) {
      // Requête d'insertion du message dans la table message
      $requete = "insert into message values"
              . "(null, $id_expediteur, $id_destinataire, current_timestamp(),"
              . " \"$message_a_envoyer\");";

      // Exécution de la requête
      $resultat = mysqli_query($connexion, $requete);
      if ($resultat) {
        // Affiche un message de confirmation de l'envoi du message
        $message .= "Message envoyé<br>\n";
      } else {
        $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
        $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
      }
    }
  }

  // **********************************************
  // Traitement du formulaire des demandes
  //
  //  A FAIRE
  //
  // **********************************************
  // Traitement du formulaire d'invitation
  //
  //  A FAIRE
  //
  //***************************************************************************
  //   Construction de la page
  //***************************************************************************
  //
  // **********************************************
  // Construction du formulaire d'envoi d'un message
  // 
  // La variable $liste_deroulante_destinataires contient la liste déroulante des destinataires
  $liste_deroulante_destinataires = "";

  // Construction de la liste déroulante
  // des destinataires potentiels du message à envoyer
  // = liste des "amis" de l'expéditeur
  //  
  // Requête d'extraction de la liste des "amis" de l'expéditeur
  $requete = "select IdAmi, Nom, Prenom, Pseudo
              from relation inner join utilisateur on IdUtilisateur = IdAmi
              where IdDemandeur = $id_expediteur and RelationAccepte = true
              order by Nom, Prenom;";

// Exécution de la requête
  $resultat = mysqli_query($connexion, $requete);
  if ($resultat) {
    // Vérification du nombre de lignes du résultat
    if (mysqli_num_rows($resultat) == 0) {
      // Aucun destinataire
      $liste_deroulante_destinataires = "";
    } else {
      // Récupération des lignes du résultat de la requête
      while ($ligne = mysqli_fetch_assoc($resultat)) {
        // Utilisation des données de chaque ligne pour créer un élément de la liste
        $liste_deroulante_destinataires .= "<option value=\"" . $ligne['IdAmi']
                . "\">" . strtoupper($ligne['Nom']) . " "
                . $ligne['Prenom']. ' (' . $ligne['Pseudo']
                . ")</option>\n";
      }
    }
  } else {
    $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
    $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
  }

  // **********************************************
  // Construction de la liste des derniers messages
  //
  //
  //  A FAIRE
  //  
  // **********************************************
  // Construction du formulaire des demandes
  //
  //  A FAIRE
  //  
  // **********************************************
  // Construction du formulaire d'invitation
  //
  //  A FAIRE
}

// ***********************************************
// Déconnexion de la base de données
require 'base_deconnexion.php';

// Construction de la page HTML
require 'header.php';

// Affichage des éventuels messages de l'application
require 'messages_application.php';
?>
<!-- **************************************** -->                 
<!-- Formulaire d'envoi de message            -->
<div class="ui segment">
  <h1 class="ui header">Envoyer un message</h1>
  <div class="ui divider"></div>
  <form class="ui form" action="" method="POST">
    <div class="field">
      <label>Destinataire</label>
      <select class="ui dropdown" name="id_destinataire">
<?php echo empty($liste_deroulante_destinataires) ? "<option disabled>Aucun destinataire</option>\n" : $liste_deroulante_destinataires ?>
      </select>
    </div>
    <div class="ui field">
      <label>Message</label>
      <textarea rows="4" name="message_a_envoyer"></textarea>
    </div>
    <input type="submit" class="ui button" name="envoyer" value="Envoyer" <?php echo empty($liste_deroulante_destinataires) ? "disabled" : ""; ?> >
  </form>
</div>               
<!-- **************************************** -->      

<!-- **************************************** -->
<!-- Liste des derniers messages              -->
<!--                                          -->
<!--  A MODIFIER                              -->
<!--                                          -->
<div class="ui segment">
  <h1 class="ui header"> Derniers messages </h1>
  <div class="ui segment">
    <h4 class="ui header">
      De admin à faceless, le 2026-03-05 13:27:42              </h4>
    <p>John DOE (johndoe) aimerait être ami avec vous.</p>
  </div>  
  <div class="ui segment">
    <h4 class="ui header">
      De faceless à bartsim, le 2026-02-15 14:53:00              </h4>
    <p>Super tout fonctionne ! Enfin je parle de la partie corrigée par nos enseignants ! </p>
  </div>  
  <div class="ui segment">
    <h4 class="ui header">
      De bartsim à faceless, le 2026-02-15 14:50:00              </h4>
    <p>Bonjour, je tente de faire fonctionner cette super application que nous développons en LO07. </p>
  </div>  
</div>
<!-- **************************************** -->                

<!-- **************************************** -->                 
<!-- Formulaire d'acceptation d'un nouvel ami -->
<!--                                          -->
<!--  A MODIFIER                              -->
<!--                                          -->
<div class="ui segment">
  <h1 class="ui header"> Liste des demandes </h1>
  <div class="ui segment">
    <form class="ui form" action="index.php" method="POST"> 
      John DOE (johndoe) vous demande en ami. 
      <input type="hidden" name="id_relation" value="15">                  
      <input type="hidden" name="pseudo_demandeur" value="johndoe">
      <input type="hidden" name="id_demandeur" value="9">
      <input type="submit" class="ui button right floated" name="accepter" value="Accepter">
      <input type="submit" class="ui button right floated" name="refuser" value="Refuser">
    </form>
  </div>
</div>
<!-- **************************************** -->

<!-- **************************************** -->                 
<!-- Formulaire d'invitation                  -->
<!--                                          -->
<!--  A MODIFIER                              -->
<!--                                          -->
<div class="ui segment">
  <h1 class="ui header"> Invitation </h1>
  <form class="ui form" action="index.php" method="POST">
    <div class="field">
      <select class="ui dropdown" name="id_invite">
        <option value="6">WHITE Walter</option>
      </select>
    </div>
    <input type="submit" class="ui button" name="inviter" value="Inviter">
  </form>
</div>
<!-- **************************************** -->  
<?php require 'footer.php' ?>