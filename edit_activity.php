<?php
session_start();
require 'base_connexion.php';

// 1. Grab the ID from the URL
if (!isset($_GET['id']) || !isset($_SESSION['session_user_id'])) {
    header("Location: your_dashboard_page.php");
    exit();
}

$transaction_id = mysqli_real_escape_string($connexion, $_GET['id']);
$session_user_id = $_SESSION['session_user_id'];

// 2. Fetch the existing data
$sql = "SELECT * FROM transaction WHERE transaction_id = '$transaction_id' AND user_id = '$session_user_id'";
$result = mysqli_query($connexion, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Transaction not found!";
    exit();
}

$row = mysqli_fetch_assoc($result);

// Now we close PHP and build the HTML form...
require 'header.php';
?>

<div class="ui container">
    <h2 class="ui header">Edit Transaction</h2>
    
    <form class="ui form" action="update_activity.php" method="POST">
        
        <input type="hidden" name="transaction_id" value="<?php echo $row['transaction_id']; ?>">

        <div class="field">
            <label>Description</label>
            <input type="text" name="description" value="<?php echo htmlspecialchars($row['description']); ?>" required>
        </div>

        <div class="field">
            <label>Amount (€)</label>
            <input type="number" step="0.01" name="amount" value="<?php echo $row['amount']; ?>" required>
        </div>

        <button class="ui blue button" type="submit">Save Changes</button>
        <a href="your_dashboard_page.php" class="ui basic button">Cancel</a>
    </form>
</div>

<?php require 'footer.php'; ?>