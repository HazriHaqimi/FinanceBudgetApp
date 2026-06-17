<?php
// Authentification obligatoire pour accéder à la page
// require 'authentification.php'; 
$message = "";
$message_erreur = "";

// Connexion à la base de données budget_financier
require 'base_connexion.php';

// =========================================================
// NEW: LOGIC TO PROCESS THE "ADD CONTACT" SUBMIT BUTTON
// =========================================================
if (isset($_POST['ajouter'])) {
    // Clean data from form inputs
    $nom = trim(htmlspecialchars($_POST['nom'], ENT_COMPAT));
    $mail = htmlspecialchars($_POST['mail']);
    $telephone = htmlspecialchars($_POST['telephone']);

    // Check required fields
    if (empty($nom)) {
        $message_erreur .= "Le champ nom est obligatoire.<br>\n";
    }
    if (empty($mail)) {
        $message_erreur .= "Le champ email est obligatoire.<br>\n";
    }

    // If no errors, insert into database!
    if (empty($message_erreur)) {
        // NOTE: Make sure your table name in your database is actually 'contacts'
        $requete = "INSERT INTO contacts (nom, telephone, email) VALUES ('$nom', '$telephone', '$mail')";
        
        $resultat = mysqli_query($connexion, $requete);

        if ($resultat) {
            $message .= "Le contact <strong>$nom</strong> a bien été ajouté !<br>\n";
            // Clear inputs so the box blanks out for next entry
            $nom = ""; $mail = ""; $telephone = "";
        } else {
            $message_erreur .= "Erreur SQL : " . mysqli_error($connexion) . "<br>\n";
        }
    }
}

// Déconnexion de la base de données
require 'base_deconnexion.php';

// Construction de la page HTML
require 'header.php';

// Displays logs if $message or $message_erreur contain data
require 'messages_application.php';
?>

<div class="ui container" style="margin-top: 2rem;">
    <div class="ui container" style="margin-top: 2rem;">

        <details style="width: 100%; margin-bottom: 2rem;" <?php echo !empty($message_erreur) ? 'open' : ''; ?>>
            <summary style="list-style: none; display: flex; gap: 10px; cursor: pointer;">
                <div class="ui left labeled icon button" style="flex-grow: 1; margin: 0;">
                    Contact List
                </div>
                <div class="ui icon button basic" style="margin: 0;">
                    <i class="plus icon"></i>
                </div>
            </summary>

            <div class="ui segment" style="margin-top: 10px; border: 1px solid rgba(34, 36, 38, 0.15); box-shadow: none; padding: 1.5rem; background: #fff;">
                <form class="ui form" method="POST" action="">
                    <h4 class="ui dividing header">Contact details</h4>     
                    <div class="field">      
                        <label for="edit-nom">Name</label>
                        <input type="text" id="edit-nom" name="nom" placeholder="Name" value="<?php echo isset($nom) ? $nom : '' ?>" maxlength="100" required>
                    </div>
                    <div class="field">
                        <label for="edit-mail">Email Address</label>
                        <input type="email" id="edit-mail" name="mail" placeholder="Email address" value="<?php echo isset($mail) ? $mail : '' ?>" maxlength="250" required>
                    </div>
                    <div class="field">
                        <label for="edit-telephone">Phone Number</label>
                        <input type="tel" id="edit-telephone" name="telephone" placeholder="Phone number" value="<?php echo isset($telephone) ? $telephone : '' ?>" maxlength="50">
                    </div>
                    
                    <input type="submit" class="ui button black" name="ajouter" value="Add">
                </form>
            </div>
        </details>

        <h3 class="ui header">They owe</h3>

        <div class="ui styled fluid accordion" style="margin-bottom: 2rem; box-shadow: none; border: 1px solid rgba(34, 36, 38, 0.15);">
            <div class="title" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <span><strong>John Doe</strong></span>
                <span>10 € / 100 <i class="dropdown icon" style="margin-left: 10px; float: none;"></i></span>
            </div>
            <div class="content" style="background-color: #fafafa; padding: 1.5rem !important;">
                <div class="ui grid">
                    <div class="two column row">
                        <div class="ten wide column">
                            <div style="line-height: 1.8;">
                                <div><i class="user icon"></i> <strong>John Doe</strong></div>
                                <div><i class="phone icon"></i> +33 6 12 34 56 78</div>
                                <div><i class="envelope icon"></i> john.doe@email.com</div>
                            </div>
                        </div>
                        <div class="six wide column right aligned">
                            <div class="ui statistics mini compact right floated">
                                <div class="ui red statistic" style="margin: 0;">
                                    <div class="value">90 €</div>
                                    <div class="label" style="font-size: 0.8rem; margin-top: 5px;">Remaining Debt</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="one column row">
                        <div class="column right aligned" style="padding-top: 0;">
                            <a href="https://wa.me/33612345678" target="_blank" class="ui green basic button small"><i class="whatsapp icon"></i> Whatsapp</a>
                            <a href="mailto:john.doe@email.com" class="ui blue basic button small"><i class="envelope outline icon"></i> Email</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="title" style="display: flex; justify-content: space-between; align-items: center; width: 100%; border-top: 1px solid rgba(34, 36, 38, 0.15);">
                <span><strong>Jane Smith</strong></span>
                <span>10 € / 100 <i class="dropdown icon" style="margin-left: 10px; float: none;"></i></span>
            </div>
            <div class="content" style="background-color: #fafafa; padding: 1.5rem !important;">
                <div class="ui grid">
                    <div class="two column row">
                        <div class="ten wide column">
                            <div style="line-height: 1.8;">
                                <div><i class="user icon"></i> <strong>Jane Smith</strong></div>
                                <div><i class="phone icon"></i> +33 6 12 34 56 78</div>
                                <div><i class="envelope icon"></i> jane.smith@email.com</div>
                            </div>
                        </div>
                        <div class="six wide column right aligned">
                            <div class="ui statistics mini compact right floated">
                                <div class="ui red statistic" style="margin: 0;">
                                    <div class="value">90 €</div>
                                    <div class="label" style="font-size: 0.8rem; margin-top: 5px;">Remaining Debt</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="ui header">You owe</h3>

        <div class="ui styled fluid accordion" style="box-shadow: none; border: 1px solid rgba(34, 36, 38, 0.15);">
            <div class="title" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <span><strong>Alex Rivera</strong></span>
                <span>10 € / 100 <i class="dropdown icon" style="margin-left: 10px; float: none;"></i></span>
            </div>
            <div class="content">
                <p>Transaction history or additional debt details for Alex Rivera go here.</p>
            </div>
        </div>

    </div>
</div>

<?php
// Pied de page
require 'footer.php';
?>