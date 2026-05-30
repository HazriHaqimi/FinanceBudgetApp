<?php
// La variable $message contiendra les éventuels messages de l'application à afficher
$message = "";

// La variable $message_erreur contiendra les éventuels messages d'erreur de l'application à afficher
$message_erreur = "";

// ***********************************************
// Connexion à la base de données cuicui du serveur localhost
require 'base_connexion.php';

// **********************************************
// **********************************************
// Traitement du formulaire transaction
//
// Initialisation des variables contenant les données saisies dans le formulaire
// et utilisées pour remplir le formulaire
$date = "";
$description = "";
$category = "";
$amount = "";
$splitwith_no = "";
$splitwith_other = "";
$recurring_no = "";
$recurring_yes = "";
$type_expenses = "";
$type_income = "";

// ***********************************************
// Vérification de la connexion d'un utilisateur
// Démarrage d'une session si cela n'a pas déjà été fait
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 1. ARE THEY LOGGED IN?
if (!isset($_SESSION['session_user_id'])) {
    header("Location: login.php"); // Or whatever your login page is named
    exit();
}

$session_user_id = $_SESSION['session_user_id'];

// 2. DID THEY HIT THE SAVE BUTTON?
if (isset($_POST['save'])) {

    $transaction_date = mysqli_real_escape_string($connexion, $_POST['transaction_date']);
    $description = mysqli_real_escape_string($connexion, $_POST['description_text']);
    $amount = mysqli_real_escape_string($connexion, $_POST['amount']);
    $category = mysqli_real_escape_string($connexion, $_POST['category']);
    $split_status = mysqli_real_escape_string($connexion, $_POST['split_status']);
    $is_recurring = mysqli_real_escape_string($connexion, $_POST['is_recurring']);
    $transaction_type = mysqli_real_escape_string($connexion, $_POST['transaction_type']);

    $requete = "INSERT INTO transaction 
                (user_id, type, category, amount, transaction_date, is_recurring, description, split_status) 
                VALUES 
                ('$session_user_id', '$transaction_type', '$category', '$amount', '$transaction_date', '$is_recurring', '$description', '$split_status')";

    if (mysqli_query($connexion, $requete)) {

        $new_transaction_id = mysqli_insert_id($connexion);

        if ($split_status == 'others' && !empty($_POST['friends']) && isset($_POST['friend_amounts'])) {
            $friends_array = explode(',', $_POST['friends']);
            $friend_amounts = $_POST['friend_amounts'];

            foreach ($friends_array as $contact_id) {
                $contact_id = mysqli_real_escape_string($connexion, $contact_id);

                $specific_amount = 0;
                if (isset($friend_amounts[$contact_id])) {
                    $specific_amount = mysqli_real_escape_string($connexion, $friend_amounts[$contact_id]);
                }

                $sql_debt = "INSERT INTO debts 
                         (user_id, contact_id, transaction_id, debt_type, original_amount, remaining_amount, status) 
                         VALUES 
                         ('$session_user_id', '$contact_id', '$new_transaction_id', 'they owe', '$specific_amount', '$specific_amount', 'pending')";

                mysqli_query($connexion, $sql_debt);
            }
        }

        // REDIRECT ON SUCCESS (Change 'your_dashboard_page.php' to your actual file name!)
        require 'base_deconnexion.php';
        header("Location: your_dashboard_page.php");
        exit();
    } else {
        $message_erreur = "Fatal Error saving transaction: " . mysqli_error($connexion);
    }
}

// Déconnexion de la base de données
require 'base_deconnexion.php';

// Construction de la page HTML
require 'header.php';
require 'messages_application.php';
?>

<!-- **************************************** -->
<!-- Affichage du formulaire                  -->
<div class="ui segment">     
    <h1 class="ui header">Add transaction</h1>
    <form id="main_transaction_form" class="ui form" method="POST" action="">

        <div class="field">
            <!-- later do this automatically PHP -->
            <label>Date</label>
            <div class="ui left icon input">
                <i class="calendar icon"></i>
                <input type="date" name="transaction_date" required>
            </div>

        </div> 
        <div class="field">
            <label>Description</label>
            <input type="text" name="description_text" placeholder="Add description here...">
        </div>
        <div class="two fields">
            <div class="field">
                <label>Amount (€)</label>
                <div class="ui left icon input">
                    <i class="euro sign icon"></i>
                    <input type="number" name="amount" placeholder="0.00" required>
                </div>
            </div>

            <div class="field">
                <label>Category</label>
                <select class="ui fluid dropdown" name="category" required>
                    <option value="">Select a Category...</option>
                    <option value="Entertainment">Entertainment</option>
                    <option value="Groceries">Groceries</option>
                    <option value="Rent">Rent</option>
                    <option value="Income">Income</option>
                </select>
            </div>
        </div> 
        <div class="inline fields">
            <label>Split with: </label>
            <div class="field">
                <div class="ui radio checkbox">
                    <input type="radio" name="split_status" id="split_none" value="none" checked onchange="toggleFriendsList()">
                    <label for="split_none">None</label>
                </div>
            </div>
            <div class="field">
                <div class="ui radio checkbox">
                    <input type="radio" name="split_status" id="split_others" value="others" onchange="toggleFriendsList()">
                    <label for="split_others">Others</label>
                </div>
            </div>
        </div>

        <div class="field" id="friend_dropdown_box" style="display: none;">
            <label>Select Friend(s)</label>

            <div class="ui fluid multiple search selection dropdown" id="friends_dropdown">
                <input type="hidden" name="friends">
                <i class="dropdown icon"></i>
                <div class="default text">Search friends...</div>

                <div class="menu">
                    <div class="item" data-value="ADD_NEW">
                        <i class="green plus icon"></i> Add more person
                    </div>

                    <div class="divider"></div>

                    <div class="item" data-value="1">Alex</div>
                    <div class="item" data-value="2">Sarah</div>
                    <div class="item" data-value="3">John</div>
                </div>
            </div>
        </div>



        <div class="inline fields">
            <label>Recurring? </label>
            <div class="field">
                <div class="ui radio checkbox">
                    <input type="radio" name="is_recurring" id="no_recurring" value="0" checked onchange="toggleRecurringOptions()">
                    <label for="no_recurring">No</label>
                </div>
            </div>
            <div class="field">
                <div class="ui radio checkbox">
                    <input type="radio" name="is_recurring" id="yes_recurring" value="1" onchange="toggleRecurringOptions()">
                    <label for="yes_recurring">Yes</label>
                </div>
            </div>
        </div>

        <div class="field" id="recurring_options_box" style="display: none;">
            <label>How often?</label>
            <select class="ui fluid dropdown" name="recurring_frequency">
                <option value="">Select frequency...</option>
                <option value="daily">Every Day</option>
                <option value="weekly">Every Week</option>
                <option value="monthly">Every Month</option>
                <option value="yearly">Every Year</option>
            </select>
        </div>

        <div class="inline fields">
            <label>Type of transaction: </label>
            <div class="field">
                <div class="ui radio checkbox">
                    <input type="radio" name="transaction_type" id="type_expenses" value="expense" required>
                    <label for="type_expenses">Expenses</label>
                </div>
            </div>
            <div class="field">
                <div class="ui radio checkbox">
                    <input type="radio" name="transaction_type" id="type_income" value="income" required>
                    <label for="type_income">Income</label>
                </div>
            </div>
        </div>
        <input type="submit" class="ui button" name="save" value="Save">
    </form>
    <div class="ui tiny modal" id="addContactModal">
        <div class="header">Add New Friend</div>
        <div class="content">
            <form class="ui form">
                <div class="field">
                    <label>Name</label>
                    <input type="text" id="new_friend_name" placeholder="Full Name">
                </div>
                <div class="field">
                    <label>Phone Number</label>
                    <input type="tel" id="new_friend_phone" placeholder="Phone Number">
                </div>
                <div class="field">
                    <label>Email</label>
                    <input type="email" id="new_friend_email" placeholder="Email Address">
                </div>
            </form>
        </div>
        <div class="actions">
            <div class="ui black deny button">Cancel</div>
            <div class="ui green positive right labeled icon button">
                Save Contact <i class="checkmark icon"></i>
            </div>
        </div>
    </div>

    <div class="ui mini modal" id="splitDetailsModal">
        <div class="header">How much do they owe?</div>
        <div class="content">
            <h3 class="ui center aligned header" id="splitFriendName">Friend Name</h3>

            <div class="ui form">
                <div class="inline fields">
                    <label>Calculate by:</label>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="split_method" value="amount" checked>
                            <label>Amount (€)</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="ui radio checkbox">
                            <input type="radio" name="split_method" value="percentage">
                            <label>Percentage (%)</label>
                        </div>
                    </div>
                </div>

                <div class="field">
                    <label>Enter Value</label>
                    <input type="number" step="0.01" id="split_value" placeholder="0.00">
                </div>
            </div>
        </div>
        <div class="actions">
            <div class="ui black deny button">Cancel</div>
            <div class="ui blue positive button">Confirm</div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.5.0/semantic.min.js"></script>

<script>
                                $(document).ready(function () {
                                    let currentSelectedFriendId = null;

                                    $('#friends_dropdown').dropdown({
                                        onAdd: function (addedValue, addedText, $addedChoice) {
                                            if (addedValue === 'ADD_NEW') {
                                                $('#addContactModal').modal('show');
                                                setTimeout(function () {
                                                    $('#friends_dropdown').dropdown('remove selected', 'ADD_NEW');
                                                }, 10);
                                            } else {
                                                currentSelectedFriendId = addedValue;

                                                $('#splitFriendName').text(addedText);
                                                $('#split_value').val('');

                                                $('#splitDetailsModal').modal({
                                                    closable: false,
                                                    onApprove: function () {
                                                        let val = parseFloat($('#split_value').val());
                                                        let method = $('input[name="split_method"]:checked').val();
                                                        let totalAmount = parseFloat($('input[name="amount"]').val());

                                                        if (isNaN(val) || isNaN(totalAmount)) {
                                                            alert("Please enter the total Transaction Amount first, and a valid split value.");
                                                            return false;
                                                        }

                                                        let finalOwed = 0;
                                                        if (method === 'percentage') {
                                                            finalOwed = totalAmount * (val / 100);
                                                        } else {
                                                            finalOwed = val;
                                                        }

                                                        let hiddenInputHtml = '<input type="hidden" id="hidden_amount_' + currentSelectedFriendId + '" name="friend_amounts[' + currentSelectedFriendId + ']" value="' + finalOwed.toFixed(2) + '">';

                                                        // FIX: We target the specific ID of the main form!
                                                        if ($('#hidden_amount_' + currentSelectedFriendId).length) {
                                                            $('#hidden_amount_' + currentSelectedFriendId).val(finalOwed.toFixed(2));
                                                        } else {
                                                            $('#main_transaction_form').append(hiddenInputHtml);
                                                        }
                                                    }
                                                }).modal('show');
                                            }
                                        },
                                        onRemove: function (removedValue, removedText, $removedChoice) {
                                            $('#hidden_amount_' + removedValue).remove();
                                        }
                                    });

                                    $('.ui.dropdown').not('#friends_dropdown').dropdown();
                                    $('.ui.checkbox').checkbox();
                                });

                                // RESTORED TOGGLE FUNCTIONS
                                function toggleFriendsList() {
                                    var othersRadio = document.getElementById('split_others');
                                    var dropdownBox = document.getElementById('friend_dropdown_box');
                                    if (othersRadio && dropdownBox) {
                                        if (othersRadio.checked) {
                                            dropdownBox.style.display = 'block';
                                        } else {
                                            dropdownBox.style.display = 'none';
                                        }
                                    }
                                }

                                function toggleRecurringOptions() {
                                    var yesRecurringRadio = document.getElementById('yes_recurring');
                                    var recurringBox = document.getElementById('recurring_options_box');
                                    if (yesRecurringRadio && recurringBox) {
                                        if (yesRecurringRadio.checked) {
                                            recurringBox.style.display = 'block';
                                        } else {
                                            recurringBox.style.display = 'none';
                                        }
                                    }
                                }
</script>
<?php
// Pied de page
require 'footer.php';
?>

