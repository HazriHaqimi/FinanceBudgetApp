<?php
$message = "";
$message_erreur = "";

// ***********************************************
// Connexion à la base de données
require 'base_connexion.php';

// Session + vérification connexion (DOIT être fait avant de lire $_SESSION)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['session_user_id'])) {
    header("Location: login.php");
    exit();
}
$session_user_id = $_SESSION['session_user_id'];

// Dettes que JE dois : les transactions créées par quelqu'un d'autre
// où JE suis l'ami avec qui on a partagé (donc c'est MOI qui dois).
// On affiche le nom du créateur (d.user_id) = la personne à qui je dois.
$query_i_owe = "SELECT d.debt_id, d.original_amount, d.remaining_amount, d.due_date, d.status,
                       u.name, u.username, t.description
                FROM debts d
                INNER JOIN users u ON u.user_id = d.user_id
                INNER JOIN transactions t ON t.transaction_id = d.transaction_id
                WHERE d.friend_user_id = '$session_user_id'
                  AND d.debt_type = 'they_owe'
                  AND d.status = 'pending'";
$result_i_owe = mysqli_query($connexion, $query_i_owe);

// Dettes qu'on ME doit (they_owe)
$query_they_owe = "SELECT d.debt_id, d.original_amount, d.remaining_amount, d.due_date, d.status,
                          u.name, u.username, t.description
                   FROM debts d
                   INNER JOIN users u ON u.user_id = d.friend_user_id
                   INNER JOIN transactions t ON t.transaction_id = d.transaction_id
                   WHERE d.user_id = '$session_user_id'
                     AND d.debt_type = 'they_owe'
                     AND d.status = 'pending'";
$result_they_owe = mysqli_query($connexion, $query_they_owe);

// ***********************************************
// Déconnexion de la base de données
require 'base_deconnexion.php';

// Construction de la page HTML
require 'header.php';

// Récupération des messages "flash" (définis avant une redirection)
if (!empty($_SESSION['flash_message'])) {
    $message .= $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
if (!empty($_SESSION['flash_erreur'])) {
    $message_erreur .= $_SESSION['flash_erreur'];
    unset($_SESSION['flash_erreur']);
}

// Affichage des logs (même système que la page friends)
require 'messages_application.php';
?>

<div class="ui container" style="margin-top: 2rem;">
    <div class="ui two column stackable divided grid">

        <!-- ============ I OWE ============ -->
        <div class="column">
            <h2 class="ui header text centered">I owe</h2>

            <?php if (!$result_i_owe || mysqli_num_rows($result_i_owe) == 0): ?>
                <p>You don't owe anyone right now.</p>
            <?php else: while ($row = mysqli_fetch_assoc($result_i_owe)):
                $pct = $row['original_amount'] > 0
                     ? round((1 - $row['remaining_amount'] / $row['original_amount']) * 100)
                     : 0; ?>
                <div class="ui fluid card">
                    <div class="content">
                        <div class="header"><?php echo htmlspecialchars($row['name']); ?></div>
                        <div class="meta" style="color:#999; font-style:italic; margin-top:3px;">
                            <i class="tag icon"></i> <?php echo htmlspecialchars($row['description'] ?: 'No description'); ?>
                        </div>
                        <div class="meta" style="display: flex; justify-content: space-between; margin-top: 5px;">
                            <span>Remaining: <?php echo number_format($row['remaining_amount'], 2); ?> €</span>
                            <strong>Total: <?php echo number_format($row['original_amount'], 2); ?> €</strong>
                        </div>
                        <div class="description" style="margin: 15px 0;">
                            <div class="ui teal progress" style="margin: 0;">
                                <div class="bar" style="width: <?php echo $pct; ?>%; min-width: 0;"></div>
                            </div>
                        </div>
                        <?php $paid = $row['original_amount'] - $row['remaining_amount']; ?>
                        <div class="extra content" style="padding-left: 0; padding-right: 0; display: flex; justify-content: space-between; align-items: center;">
                            <span><i class="calendar icon"></i> Due: <?php echo $row['due_date'] ?? 'N/A'; ?></span>
                            <div>
                                <?php if ($paid > 0): ?>
                                <button type="button" class="ui tiny blue basic button modify-btn"
                                        data-debt-id="<?php echo $row['debt_id']; ?>"
                                        data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                                        data-original="<?php echo number_format($row['original_amount'], 2, '.', ''); ?>"
                                        data-paid="<?php echo number_format($paid, 2, '.', ''); ?>">
                                    Modify
                                </button>
                                <?php endif; ?>
                                <button type="button" class="ui tiny green basic button pay-btn"
                                        data-debt-id="<?php echo $row['debt_id']; ?>"
                                        data-name="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>"
                                        data-remaining="<?php echo number_format($row['remaining_amount'], 2, '.', ''); ?>">
                                    Pay
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; endif; ?>
        </div>

        <!-- ============ THEY OWE ============ -->
        <div class="column">
            <h2 class="ui header text centered">They owe</h2>

            <?php if (!$result_they_owe || mysqli_num_rows($result_they_owe) == 0): ?>
                <p>Nobody owes you right now.</p>
            <?php else: while ($row = mysqli_fetch_assoc($result_they_owe)):
                $pct = $row['original_amount'] > 0
                     ? round((1 - $row['remaining_amount'] / $row['original_amount']) * 100)
                     : 0; ?>
                <div class="ui fluid card">
                    <div class="content">
                        <div class="header"><?php echo htmlspecialchars($row['name']); ?></div>
                        <div class="meta" style="color:#999; font-style:italic; margin-top:3px;">
                            <i class="tag icon"></i> <?php echo htmlspecialchars($row['description'] ?: 'No description'); ?>
                        </div>
                        <div class="meta" style="display: flex; justify-content: space-between; margin-top: 5px;">
                            <span>Remaining: <?php echo number_format($row['remaining_amount'], 2); ?> €</span>
                            <strong>Total: <?php echo number_format($row['original_amount'], 2); ?> €</strong>
                        </div>
                        <div class="description" style="margin: 15px 0;">
                            <div class="ui orange progress" style="margin: 0;">
                                <div class="bar" style="width: <?php echo $pct; ?>%; min-width: 0;"></div>
                            </div>
                        </div>
                        <div class="extra content" style="padding-left: 0; padding-right: 0; display: flex; justify-content: space-between; align-items: center;">
                            <span><i class="calendar icon"></i> Due: <?php echo $row['due_date'] ?? 'N/A'; ?></span>
                            <a href="send_reminder.php?debt_id=<?php echo $row['debt_id']; ?>" class="ui tiny red basic button">Send a reminder</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; endif; ?>
        </div>

    </div>
</div>

<!-- ============ PAYMENT POPUP ============ -->
<div id="payOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000;"></div>
<div id="payModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:25px; border-radius:8px; width:340px; max-width:90%; z-index:1001; box-shadow:0 5px 25px rgba(0,0,0,0.3);">
    <h3 class="ui header" style="margin-top:0;">Make a payment</h3>
    <p>Paying <strong id="payName"></strong></p>
    <form method="POST" action="pay_debt.php" id="payForm">
        <input type="hidden" name="debt_id" id="payDebtId">
        <div class="field" style="margin-bottom:8px;">
            <label><strong>Amount to pay (€)</strong></label>
            <input type="number" step="0.01" min="0.01" name="pay_amount" id="payAmount" placeholder="0.00" required>
        </div>
        <p style="color:#888; font-size:0.9em;">Remaining: <span id="payRemaining"></span> € (you can't pay more than this)</p>
        <div style="text-align:right; margin-top:15px;">
            <button type="button" class="ui button" onclick="closePayModal()">Cancel</button>
            <button type="submit" class="ui green button">Confirm</button>
        </div>
    </form>
</div>

<script>
    function closePayModal() {
        document.getElementById('payOverlay').style.display = 'none';
        document.getElementById('payModal').style.display = 'none';
    }

    document.querySelectorAll('.pay-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var remaining = parseFloat(btn.getAttribute('data-remaining'));
            document.getElementById('payDebtId').value = btn.getAttribute('data-debt-id');
            document.getElementById('payName').textContent = btn.getAttribute('data-name');
            document.getElementById('payRemaining').textContent = remaining.toFixed(2);
            var amt = document.getElementById('payAmount');
            amt.value = '';
            amt.max = remaining; // client-side cap
            document.getElementById('payOverlay').style.display = 'block';
            document.getElementById('payModal').style.display = 'block';
        });
    });

    document.getElementById('payOverlay').addEventListener('click', closePayModal);

    // Validation : montant > 0 et <= restant dû
    document.getElementById('payForm').addEventListener('submit', function (e) {
        var amt = parseFloat(document.getElementById('payAmount').value);
        var max = parseFloat(document.getElementById('payAmount').max);
        if (isNaN(amt) || amt <= 0) {
            e.preventDefault();
            alert('Please enter a valid amount.');
            return;
        }
        if (amt > max + 0.0001) {
            e.preventDefault();
            alert('You cannot pay more than the remaining amount (' + max.toFixed(2) + ' €).');
        }
    });
</script>

<!-- ============ MODIFY PAYMENT POPUP ============ -->
<div id="modOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000;"></div>
<div id="modModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:25px; border-radius:8px; width:360px; max-width:90%; z-index:1001; box-shadow:0 5px 25px rgba(0,0,0,0.3);">
    <h3 class="ui header" style="margin-top:0;">Correct your payment</h3>
    <p>Debt with <strong id="modName"></strong></p>
    <form method="POST" action="modify_payment.php" id="modForm">
        <input type="hidden" name="debt_id" id="modDebtId">
        <p style="color:#888; font-size:0.9em; margin-bottom:10px;">
            Total debt: <strong id="modOriginal"></strong> € &nbsp;|&nbsp;
            Paid so far: <strong id="modPaidLabel"></strong> €
        </p>
        <div class="field" style="margin-bottom:8px;">
            <label><strong>New total amount paid (€)</strong></label>
            <input type="number" step="0.01" min="0" name="new_paid" id="modNewPaid" required>
        </div>
        <p style="color:#888; font-size:0.85em;">
            Lower it and you get the difference back (the other person loses it). It can't be more than the total debt.
        </p>
        <div style="text-align:right; margin-top:15px;">
            <button type="button" class="ui button" onclick="closeModModal()">Cancel</button>
            <button type="submit" class="ui blue button">Save change</button>
        </div>
    </form>
</div>

<script>
    function closeModModal() {
        document.getElementById('modOverlay').style.display = 'none';
        document.getElementById('modModal').style.display = 'none';
    }

    document.querySelectorAll('.modify-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var original = parseFloat(btn.getAttribute('data-original'));
            var paid = parseFloat(btn.getAttribute('data-paid'));
            document.getElementById('modDebtId').value = btn.getAttribute('data-debt-id');
            document.getElementById('modName').textContent = btn.getAttribute('data-name');
            document.getElementById('modOriginal').textContent = original.toFixed(2);
            document.getElementById('modPaidLabel').textContent = paid.toFixed(2);
            var input = document.getElementById('modNewPaid');
            input.value = paid.toFixed(2);
            input.max = original;
            document.getElementById('modOverlay').style.display = 'block';
            document.getElementById('modModal').style.display = 'block';
        });
    });

    document.getElementById('modOverlay').addEventListener('click', closeModModal);

    // Validation : 0 <= nouveau montant payé <= dette totale
    document.getElementById('modForm').addEventListener('submit', function (e) {
        var val = parseFloat(document.getElementById('modNewPaid').value);
        var max = parseFloat(document.getElementById('modNewPaid').max);
        if (isNaN(val) || val < 0) {
            e.preventDefault();
            alert('Please enter a valid amount.');
            return;
        }
        if (val > max + 0.0001) {
            e.preventDefault();
            alert('The amount paid cannot be more than the total debt (' + max.toFixed(2) + ' €).');
        }
    });
</script>

<?php
// Pied de page
require 'footer.php';
?>
