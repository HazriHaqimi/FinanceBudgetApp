<?php
require 'authentification.php';
// ***********************************************
// index.php — page principale (Dashboard)
// ***********************************************
$message = "";
$message_erreur = "";

// Connexion à la base de données budget_financier
require 'base_connexion.php';


// Mois / année sélectionnés (par défaut : mois et année en cours)
$selected_month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
$selected_year  = isset($_GET['year'])  ? (int)$_GET['year']  : (int)date('Y');
// Sécurité : on borne les valeurs
if ($selected_month < 1 || $selected_month > 12) {
    $selected_month = (int)date('m');
}
if ($selected_year < 2000 || $selected_year > 2100) {
    $selected_year = (int)date('Y');
}

// Liste des mois pour le menu déroulant
$month_names = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];
$selected_period_label = $month_names[$selected_month] . ' ' . $selected_year;

// Plage de dates pour le graphique (toujours les 30 derniers jours, indépendant du filtre)
$chart_range_label = date('M j', strtotime('-30 days')) . ' – ' . date('M j, Y');

// Valeurs par défaut
$total_income = 0;
$total_expenses = 0;
$total_balance = 0;
$total_monthly_expenses = 0;
$result_recent_activities = null;
$upcoming_bills = [];
$graph_dates = [];
$graph_amounts = [];

// A) Solde : revenus - dépenses
$requete_income = "SELECT SUM(amount) AS total FROM transactions
                   WHERE user_id='$session_user_id' AND type='income'
                   AND MONTH(transaction_date) = $selected_month
                   AND YEAR(transaction_date) = $selected_year";
$result_income = mysqli_query($connexion, $requete_income);
$row_income = mysqli_fetch_assoc($result_income);
$total_income = $row_income['total'] ?? 0;

$requete_exp = "SELECT SUM(amount) AS total FROM transactions
                WHERE user_id='$session_user_id' AND type='expense'
                AND MONTH(transaction_date) = $selected_month
                AND YEAR(transaction_date) = $selected_year";
$result_exp = mysqli_query($connexion, $requete_exp);
$row_exp = mysqli_fetch_assoc($result_exp);
$total_expenses = $row_exp['total'] ?? 0;

$total_balance = $total_income - $total_expenses;

// B) Dépenses du mois en cours 
$requete_monthly = "SELECT SUM(amount) AS total FROM transactions
                    WHERE user_id='$session_user_id'
                    AND type='expense'
                    AND MONTH(transaction_date) = $selected_month
                    AND YEAR(transaction_date) = $selected_year";
$result_monthly = mysqli_query($connexion, $requete_monthly);
$row_monthly = mysqli_fetch_assoc($result_monthly);
$total_monthly_expenses = $row_monthly['total'] ?? 0;

// C) Activités récentes
$requete_recent_activities = "SELECT transaction_date, description, amount, category
                              FROM transactions
                              WHERE user_id='$session_user_id'
                              ORDER BY transaction_date DESC, transaction_id DESC LIMIT 5";
$result_recent_activities = mysqli_query($connexion, $requete_recent_activities);

// D) Factures récurrentes à venir : on calcule la prochaine échéance
$requete_upcoming = "SELECT transaction_date, description, amount, recurring_frequency
                     FROM transactions
                     WHERE user_id='$session_user_id' AND is_recurring = 1
                     ORDER BY transaction_date ASC";
$result_upcoming = mysqli_query($connexion, $requete_upcoming);
if ($result_upcoming) {
    $today = new DateTime(date('Y-m-d'));
    while ($row = mysqli_fetch_assoc($result_upcoming)) {
        $next = new DateTime($row['transaction_date']);
        $interval = null;
        switch ($row['recurring_frequency']) {
            case 'daily':   $interval = 'P1D'; break;
            case 'weekly':  $interval = 'P1W'; break;
            case 'monthly': $interval = 'P1M'; break;
            case 'yearly':  $interval = 'P1Y'; break;
        }
        if ($interval) {
            while ($next < $today) {
                $next->add(new DateInterval($interval));
            }
        }
        $row['next_due'] = $next->format('Y-m-d');
        $upcoming_bills[] = $row;
    }
    usort($upcoming_bills, fn($a, $b) => strcmp($a['next_due'], $b['next_due']));
    $upcoming_bills = array_slice($upcoming_bills, 0, 5);
}

// E) Graphique : dépenses des 30 derniers jours
$requete_graph = "SELECT transaction_date, SUM(amount) as daily_total
                  FROM transactions
                  WHERE user_id = '$session_user_id'
                  AND type = 'expense'
                  AND transaction_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
                  GROUP BY transaction_date
                  ORDER BY transaction_date ASC";
$result_graph = mysqli_query($connexion, $requete_graph);
if ($result_graph) {
    while ($row = mysqli_fetch_assoc($result_graph)) {
        $graph_dates[]   = $row['transaction_date'];
        $graph_amounts[] = $row['daily_total'];
    }
}

// Déconnexion de la base de données
require 'base_deconnexion.php';

// Récupération d'un éventuel message "flash"
if (!empty($_SESSION['flash_message'])) {
    $message .= $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
if (!empty($_SESSION['flash_erreur'])) {
    $message_erreur .= $_SESSION['flash_erreur'];
    unset($_SESSION['flash_erreur']);
}

// Construction de la page HTML
require 'header.php';
require 'messages_application.php';
?>

<div class="ui container">
    <h2 class="ui header">
        <i class="chart pie icon"></i>
        <div class="content">
            Dashboard
            <div class="sub header">Welcome back <strong><?php echo htmlspecialchars($session_username) ?></strong> ! Here is your financial overview.</div>
        </div>
    </h2>

    <form method="get" action="index.php" class="ui form" style="margin-bottom: 1em;">
        <div class="inline fields">
            <div class="field">
                <label>Month</label>
                <select name="month" class="ui dropdown">
                    <?php foreach ($month_names as $num => $name): ?>
                        <option value="<?php echo $num; ?>" <?php echo $num === $selected_month ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label>Year</label>
                <select name="year" class="ui dropdown">
                    <?php for ($y = (int)date('Y'); $y >= (int)date('Y') - 4; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo $y === $selected_year ? 'selected' : ''; ?>>
                            <?php echo $y; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="field">
                <button type="submit" class="ui primary button">View</button>
            </div>
        </div>
    </form>

    <div class="ui stackable grid">

        <div class="two column row">
            <div class="eight wide column">
                <div class="ui segment">
                    <h3 class="ui header">Total Balance <small>(<?php echo $selected_period_label; ?>)</small></h3>
                    <h1 class="ui <?php echo $total_balance < 0 ? 'red' : 'green'; ?> header"><?php echo number_format($total_balance, 2) ?> €</h1>
                </div>
            </div>
            <div class="eight wide column">
                <div class="ui segment">
                    <h3 class="ui header">Incoming vs Outgoing <small>(<?php echo $selected_period_label; ?>)</small></h3>
                    <p><i class="arrow up green icon"></i> <?php echo number_format($total_income, 2) ?> €</p>
                    <p><i class="arrow down red icon"></i> <?php echo number_format($total_expenses, 2) ?> €</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="sixteen wide column">
                <div class="ui segment">
                    <h3 class="ui header">
                        Spending Trends — Last 30 Days
                        <div class="sub header"><?php echo $chart_range_label; ?> (always current, ignores the filter above)</div>
                    </h3>
                    <div>
                        <canvas id="spendingChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="two column row">
            <div class="eight wide column">
                <div class="ui segment">
                    <h3 class="ui header">Recent Activity</h3>
                    <div class="ui relaxed divided list">
                        <?php
                        if ($result_recent_activities && mysqli_num_rows($result_recent_activities) > 0) {
                            while ($row = mysqli_fetch_assoc($result_recent_activities)) {
                                ?>
                                <div class="item">
                                    <i class="large money bill alternate outline middle aligned icon"></i>
                                    <div class="content">
                                        <a class="header"><?php echo htmlspecialchars($row['transaction_date'] . " - " . $row['description']); ?></a>
                                        <div class="description"><?php echo $row['amount']; ?> € - <?php echo htmlspecialchars($row['category']); ?></div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<p>No recent activities found.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="eight wide column">
                <div class="ui segment">
                    <h3 class="ui header">
                        <i class="calendar alternate outline icon"></i>
                        <div class="content">Upcoming Recurring Bills</div>
                    </h3>
                    <div class="ui relaxed divided list">
                        <?php if (count($upcoming_bills) > 0): ?>
                            <?php foreach ($upcoming_bills as $row): ?>
                                <div class="item">
                                    <i class="large red clock outline middle aligned icon"></i>
                                    <div class="content">
                                        <div class="header"><?php echo htmlspecialchars($row['description']); ?></div>
                                        <div class="description">
                                            <strong><?php echo $row['amount']; ?> €</strong>
                                            <br>Due: <?php echo $row['next_due']; ?>
                                            <br><small><?php echo ucfirst($row['recurring_frequency'] ?? ''); ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="ui positive message">You have no upcoming bills!</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartDates = <?php echo json_encode($graph_dates); ?>;
        const chartAmounts = <?php echo json_encode($graph_amounts); ?>;
        const ctx = document.getElementById('spendingChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartDates,
                datasets: [{
                    label: 'Daily Expenses (€)',
                    data: chartAmounts,
                    borderColor: '#2185d0',
                    backgroundColor: 'rgba(33, 133, 208, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    </script>
</div>

<?php
require 'footer.php';
?>
