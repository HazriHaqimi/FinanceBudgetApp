<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require 'base_connexion.php';

// 1. Grab the ID from the URL safely
if (!isset($_GET['id']) || !isset($_SESSION['session_user_id'])) {
    header("Location: transaction.php"); // FIXED REDIRECT
    exit();
}

$transaction_id = mysqli_real_escape_string($connexion, $_GET['id']);
$session_user_id = $_SESSION['session_user_id'];

// 2. Fetch the existing data (FIXED TABLE NAME 'transactions')
$sql = "SELECT * FROM transactions WHERE transaction_id = '$transaction_id' AND user_id = '$session_user_id'";
$result = mysqli_query($connexion, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Transaction not found or you don't have permission to edit it!";
    exit();
}

$row = mysqli_fetch_assoc($result);

// Les paiements de dette ne se modifient pas ici : ils se gèrent dans le Debt Center
if ($row['category'] === 'Debt Payment') {
    $_SESSION['flash_erreur'] = "Debt payments can only be modified from the Debt Center.";
    header("Location: transaction.php");
    exit();
}

// 3. Bring in our Category array from the add page!
$category_array = ['Entertainment', 'Groceries', 'Rent', 'Income', 'Shopping', 'Transport', 'Utilities'];

// Now we close PHP and build the HTML form...
require 'header.php';
?>

<div class="ui segment container">
    <h2 class="ui header">Edit Transaction</h2>
    
    <form class="ui form" action="update_activity.php" method="POST">
        
        <input type="hidden" name="transaction_id" value="<?php echo $row['transaction_id']; ?>">

        <div class="field">
            <label>Date</label>
            <div class="ui left icon input">
                <i class="calendar icon"></i>
                <input type="date" name="transaction_date" value="<?php echo $row['transaction_date']; ?>" required>
            </div>
        </div>

        <div class="field">
            <label>Description</label>
            <input type="text" name="description" value="<?php echo htmlspecialchars($row['description']); ?>" required>
        </div>

        <div class="two fields">
            <div class="field">
                <label>Amount (€)</label>
                <div class="ui left icon input">
                    <i class="euro sign icon"></i>
                    <input type="number" step="0.01" min="0.01" name="amount" value="<?php echo $row['amount']; ?>" required>
                </div>
            </div>

            <div class="field">
                <label>Category</label>
                <select class="ui fluid dropdown" name="category" required>
                    <option value="">Select a Category...</option>
                    <?php
                    // Loop through categories and automatically select the one saved in the database!
                    foreach ($category_array as $cat) {
                        $selected = ($row['category'] == $cat) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($cat) . '" ' . $selected . '>' . htmlspecialchars($cat) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="inline fields">
            <label>Type of transaction: </label>
            <div class="field">
                <div class="ui radio checkbox">
                    <input type="radio" name="transaction_type" value="expense" <?php echo ($row['type'] == 'expense') ? 'checked' : ''; ?> required>
                    <label>Expenses</label>
                </div>
            </div>
            <div class="field">
                <div class="ui radio checkbox">
                    <input type="radio" name="transaction_type" value="income" <?php echo ($row['type'] == 'income') ? 'checked' : ''; ?> required>
                    <label>Income</label>
                </div>
            </div>
        </div>

        <br>
        <button class="ui blue button" type="submit">Save Changes</button>
        <a href="transaction.php" class="ui basic button">Cancel</a> </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.5.0/semantic.min.js"></script>
<script>
    $(document).ready(function () {
        $('.ui.dropdown').dropdown();
        $('.ui.checkbox').checkbox();
    });
</script>

<?php require 'footer.php'; ?>