<?php
// Authentification obligatoire pour accéder à la page
require 'authentification.php';

// La variable $message contiendra les éventuels messages de l'application à afficher
$message = "";

// La variable $message_erreur contiendra les éventuels messages d'erreur de l'application à afficher
$message_erreur = "";

// ***********************************************
// Connexion à la base de données budget_financier
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
    // Traitement du formulaire des demandes
    if (isset($_POST['accepter']) || isset($_POST['refuser'])) {
        $id_relation = $_POST['id_relation'];
        $id_demandeur = $_POST['id_demandeur'];

        if (isset($_POST['accepter'])) {
            // Accepter la demande : on met RelationAccepte = 1
            $requete = "UPDATE contacts SET RelationAccepte = 1
                    WHERE contact_id = $id_relation;";
            $resultat = mysqli_query($connexion, $requete);
            if ($resultat) {
                // On insère aussi la relation dans l'autre sens
                // On met aussi la relation inverse à 1
                $requete2 = "INSERT INTO contacts VALUES
                            (null, $session_user_id, $id_demandeur, current_timestamp(), 1);";
                $resultat2 = mysqli_query($connexion, $requete2);
                if ($resultat2) {
                    $message .= "Demande acceptée<br>\n";
                } else {
                    $message_erreur .= "Erreur de la requête <strong>$requete2</strong><br>\n";
                    $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
                }
            } else {
                $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
                $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
            }
        } else {
            // Refuser la demande : on supprime la ligne dans relation
            $requete = "DELETE FROM contacts WHERE contact_id = $id_relation;";
            $resultat = mysqli_query($connexion, $requete);
            if ($resultat) {
                $message .= "Demande refusée<br>\n";
            } else {
                $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
                $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
            }
        }
    }
    // **********************************************
    // Traitement du formulaire d'invitation
    //
    if (isset($_POST['inviter'])) {
        $id_invite = $_POST['id_invite'];

        $requete = "INSERT INTO contacts VALUES
                (null, $session_user_id, $id_invite, current_timestamp(), 0);";
        $resultat = mysqli_query($connexion, $requete);
        if ($resultat) {
            // Envoi d'un message de notification via le compte admin (IdUtilisateur = 1)        
            $message_want_friend = "$session_username wants to be your friend.";

            $requete2 = "INSERT INTO message VALUES
                    (null, 1, $id_invite, current_timestamp(),
                    '$message_want_friend');";
            $resultat2 = mysqli_query($connexion, $requete2);

            if ($resultat2) {
                $message .= "Invitation envoyée<br>\n";
            } else {
                $message_erreur .= "Erreur de la requête <strong>$requete2</strong><br>\n";
                $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
            }
        } else {
            $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
            $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
        }
    }
    //***************************************************************************
    //   Construction de la page
    //***************************************************************************
    // Construction de la liste des derniers messages
    $liste_messages = "";

    $requete = "select m.Message, m.DateMessage,
                    u1.username as PseudoExpediteur,
                    u2.username as PseudoDestinataire
                from message m 
                inner join users u1 on
                    u1.user_id=m.IdExpediteur
                inner join users u2 on
                    u2.user_id=m.IdDestinataire
                where m.IdDestinataire=$session_user_id
                order by m.DateMessage DESC
                LIMIT 10;";

    $resultat = mysqli_query($connexion, $requete);
    if ($resultat) {
        while ($ligne = mysqli_fetch_assoc($resultat)) {
            $liste_messages .= "<div class=\"ui segment\">\n";
            $liste_messages .= "<h4 class=\"ui header\">\n";
            $liste_messages .= "From  " . $ligne['PseudoExpediteur']
                    
                    . ", le " . $ligne['DateMessage'] . "</h4>\n";
            $liste_messages .= "<p>" . htmlspecialchars($ligne['Message']) . "</p>\n";
            $liste_messages .= "</div>\n";
        }
    } else {
        $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
        $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
    }

    // **********************************************
    // Construction du formulaire des demandes
    $liste_demandes = "";

    $requete = "select r.contact_id, r.user_id, u.username, u.name
                from contacts r 
                inner join users u on
                    u.user_id=r.user_id
                where r.IdAmi=$session_user_id
                   and RelationAccepte=0;";

    $resultat = mysqli_query($connexion, $requete);
    if ($resultat) {
        while ($ligne = mysqli_fetch_assoc($resultat)) {
            $liste_demandes .= "<div class=\"ui segment\">\n";
            $liste_demandes .= "<form class=\"ui form\" action=\"friends.php\" method=\"POST\">\n";
            $liste_demandes .= $ligne['username'] . " ask to be your friend.\n";
            $liste_demandes .= "<input type=\"hidden\" name=\"id_relation\" value=\"" . $ligne['contact_id'] . "\">\n";
            $liste_demandes .= "<input type=\"hidden\" name=\"pseudo_demandeur\" value=\"" . $ligne['username'] . "\">\n";
            $liste_demandes .= "<input type=\"hidden\" name=\"id_demandeur\" value=\"" . $ligne['user_id'] . "\">\n";
            $liste_demandes .= "<input type=\"submit\" class=\"ui button right floated\" name=\"accepter\" value=\"Accepter\">\n";
            $liste_demandes .= "<input type=\"submit\" class=\"ui button right floated\" name=\"refuser\" value=\"Refuser\">\n";
            $liste_demandes .= "</form>\n";
            $liste_demandes .= "</div>\n";
        }
    } else {
        $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
        $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
    }

    // **********************************************
    // Construction du formulaire d'invitation
    $liste_deroulante_invites = "";
    $requete = "SELECT user_id, username, name
                FROM users
                WHERE user_id != $session_user_id
                  AND user_id != 1
                  AND user_id NOT IN (
                    SELECT IdAmi FROM contacts WHERE user_id = $session_user_id
                  );";
    $resultat = mysqli_query($connexion, $requete);
    if ($resultat) {
        // Vérification du nombre de lignes du résultat
        if (mysqli_num_rows($resultat) == 0) {
            // Aucun invites
            $liste_deroulante_invites = "";
        } else {
            // Récupération des lignes du résultat de la requête
            while ($ligne = mysqli_fetch_assoc($resultat)) {
                // Utilisation des données de chaque ligne pour créer un élément de la liste
                $liste_deroulante_invites .= "<option value=\"" . $ligne['user_id']
                        . "\">" . strtoupper($ligne['name']) . " "
                        . ' (' . $ligne['username']
                        . ")</option>\n";
            }
        }
    } else {
        $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
        $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
    }

    // **********************************************
// Construction de la liste des amis
    $liste_amis = "";

    $requete = "SELECT u.user_id, u.username, u.name
            FROM contacts c
            INNER JOIN users u ON u.user_id = c.IdAmi
            WHERE c.user_id = $session_user_id
              AND c.RelationAccepte = 1
            ORDER BY u.name ASC;";

    $resultat = mysqli_query($connexion, $requete);
    if ($resultat) {
        if (mysqli_num_rows($resultat) == 0) {
            $liste_amis = "";
        } else {
            while ($ligne = mysqli_fetch_assoc($resultat)) {
                $liste_amis .= "<div class=\"ui segment\">\n";
                $liste_amis .= "<h4 class=\"ui header\">"
                        . htmlspecialchars(strtoupper($ligne['name']))
                        . " <span style=\"font-weight:normal;\">(" . htmlspecialchars($ligne['username']) . ")</span></h4>\n";
                $liste_amis .= "</div>\n";
            }
        }
    } else {
        $message_erreur .= "Erreur de la requête <strong>$requete</strong><br>\n";
        $message_erreur .= "Erreur n° " . mysqli_errno($connexion) . " : " . mysqli_error($connexion) . "<br>\n";
    }
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
<!-- Liste des amis                           -->
<div class="ui segment">
    <h1 class="ui header"> My Friends </h1>
    <?php echo empty($liste_amis) ? "<p>You have no friends yet.</p>" : $liste_amis; ?>
</div>
<!-- **************************************** -->
<!-- **************************************** -->
<!-- Liste des derniers messages              -->
<!--                                          -->
<!--  A MODIFIER                              -->
<!--                                          -->
<div class="ui segment">
    <h1 class="ui header"> Messages </h1>
    <?php echo empty($liste_messages) ? "<p>You have no messages.</p>" : $liste_messages; ?>
</div>
<!-- **************************************** -->                

<!-- **************************************** -->                 
<!-- Formulaire d'acceptation d'un nouvel ami -->
<!--                                          -->
<!--  A MODIFIER                              -->
<!--                                          -->
<div class="ui segment">
    <h1 class="ui header"> Friend request </h1>
    <?php echo empty($liste_demandes) ? "<p>You have no requests.</p>" : $liste_demandes; ?>
</div>
<!-- **************************************** -->

<!-- **************************************** -->                 
<!-- Formulaire d'invitation                  -->
<!--                                          -->
<!--  A MODIFIER                              -->
<!--                                          -->
<div class="ui segment">
    <h1 class="ui header"> Invite </h1>
    <form class="ui form" action="friends.php" method="POST">
        <div class="field">
            <select class="ui dropdown" name="id_invite">
                <?php echo empty($liste_deroulante_invites) ? "<option disabled>You are friends with everyone</option>\n" : $liste_deroulante_invites ?>
            </select>
        </div>
        <input type="submit" class="ui button" name="inviter" value="Inviter">
    </form>
</div> 
<!-- **************************************** -->  
<?php require 'footer.php' ?>



