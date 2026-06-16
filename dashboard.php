<?php
// La variable $message contiendra les éventuels messages de l'application à afficher
$message = "";

// La variable $message_erreur contiendra les éventuels messages d'erreur de l'application à afficher
$message_erreur = "";

// ***********************************************
// Connexion à la base de données cuicui du serveur localhost
require 'base_connexion.php';

// **********************************************
// Vérification de la connexion d'un utilisateur
// Démarrage d'une session si cela n'a pas déjà été fait
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_SESSION['session_user_id'])) {
// Un utilisateur est connecté
// Récupération de l'identifiant et pseudo
// de l'utilisateur connecté dans les variables de session
    $session_user_id = $_SESSION['session_user_id'];
    $session_username = $_SESSION['session_username'];

// Si aucun message d'erreur
    if (empty($message_erreur)) {
//*******************************************
// Récupération des informations de l'utilisateur connecté
// dans la table users
// Requête SQL
        $requete = "select * from users where user_id = '$session_user_id';";
// Exécution de la requête
        $resultat = mysqli_query($connexion, $requete);
        if ($resultat) {
// Vérification du nombre de lignes du résultat
            if (mysqli_num_rows($resultat) != 0) {

// L'identifiant existe
// Récupération de la ligne de la table correspondant
// à l'utilisateur connecté
                $user = mysqli_fetch_assoc($resultat);

// A) Tableau "BALANCE"   
// 1. Recuperation pour le SUM de type "income"
                $requete_income = "SELECT SUM(amount) AS total FROM transactions "
                        . "WHERE user_id='$session_user_id' AND type='income'";
                $result_income = mysqli_query($connexion, $requete_income);
                $row_income = mysqli_fetch_assoc($result_income);

//si c'est vide, income=0;
                $total_income = $row_income['total'] ?? 0;

// 2. Recuperation pour le SUM de type "expenses"
                $requete_exp = "SELECT SUM(amount) AS total FROM transactions "
                        . "WHERE user_id='$session_user_id' AND type='expense'";
                $result_exp = mysqli_query($connexion, $requete_exp);
                $row_exp = mysqli_fetch_assoc($result_exp);
//si c'est vide, expenses=0;
                $total_expenses = $row_exp['total'] ?? 0;

// 3. Sum de les deux
                $total_balance = $total_income - $total_expenses;

// B) tableau Monthly Expenses
// 1. Monthly Expenses
                $requete_monthly = "SELECT SUM(amount) AS total FROM transactions
                    WHERE user_id='$session_user_id' 
                    AND type='expenses' 
                    AND MONTH(transaction_date) = MONTH(CURRENT_DATE())
                    AND YEAR(transaction_date) = YEAR(CURRENT_DATE())";
                $result_monthly = mysqli_query($connexion, $requete_monthly);
                $row_monthly = mysqli_fetch_assoc($result_monthly);
                $total_monthly_expenses = $row_monthly['total'] ?? 0;

// C)tableau Recent Activities
                $requete_recent_activities = "SELECT transaction_date, description,"
                        . "amount,category FROM transactions "
                        . "WHERE user_id='$session_user_id' "
                        . "ORDER BY transaction_date DESC LIMIT 5";

                $result_recent_activities = mysqli_query($connexion, $requete_recent_activities);

// D) Upcoming Debts
                $requete_upcoming = "SELECT transaction_date, description, amount "
                        . "FROM transactions "
                        . "WHERE user_id='$session_user_id' "
                        . "AND transaction_date >= CURRENT_DATE() "
                        . "AND is_recurring = 1 "
                        . "ORDER BY transaction_date ASC LIMIT 5";
                $result_upcoming = mysqli_query($connexion, $requete_upcoming);

                // E) Graph data
                // E) Graph Data: Last 30 Days of Expenses
                $requete_graph = "SELECT transaction_date, SUM(amount) as daily_total 
                  FROM transactions 
                  WHERE user_id = '$session_user_id' 
                  AND type = 'expense' 
                  AND transaction_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY) 
                  GROUP BY transaction_date 
                  ORDER BY transaction_date ASC";

                $result_graph = mysqli_query($connexion, $requete_graph);

// Create two empty arrays to hold our X-axis (dates) and Y-axis (amounts)
                $graph_dates = [];
                $graph_amounts = [];

                if ($result_graph) {
                    while ($row = mysqli_fetch_assoc($result_graph)) {
                        $graph_dates[] = $row['transaction_date'];
                        $graph_amounts[] = $row['daily_total'];
                    }
                }
            }
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

// S'il y a pas message d'erreur, on affiche le dashboard
if (empty($message_erreur)) {
    ?>
    <!-- **************************************** -->

    <div class="ui container">
        <h2 class="ui header">
            <i class="chart pie icon"></i>
            <div class="content">
                Dashboard
                <div class="sub header">Welcome back <strong><?php echo $session_username?></strong> ! Here is your financial overview.</div>
            </div>
        </h2>

        <div class="ui stackable grid">

            <div class="two column row">
                <div class="eight wide column"> <!-- 8 x 2 = 16 grid -->
                    <div class="ui segment">
                        <h3 class="ui header">Total Balance</h3>
                        <h1 class="ui green header"><?php echo $total_balance ?> €</h1>
                    </div>
                </div>
                <div class="eight wide column">
                    <div class="ui segment">
                        <h3 class="ui header">Incoming vs Outgoing</h3>
                        <p><i class="arrow up green icon"></i> <?php echo $total_income ?> €</p>
                        <p><i class="arrow down red icon"></i> <?php echo $total_expenses ?> €</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="sixteen wide column">
                    <div class="ui segment">
                        <h3 class="ui header">Spending Trends (30 Days)</h3>
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
                            if ($result_recent_activities) {
                                while ($row = mysqli_fetch_assoc($result_recent_activities)) {
                                    ?>
                                    <div class="item">
                                        <i class="large money bill alternate outline middle aligned icon"></i>
                                        <div class="content">
                                            <a class="header"><?php echo $row['transaction_date'] . " - " . $row['description']; ?></a>
                                            <div class="description"><?php echo $row['amount']; ?> € - <?php echo $row['category']; ?> 
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo "No recent activities found";
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

                            <?php
                            // Check if there are actually any upcoming bills
                            if (mysqli_num_rows($result_upcoming) > 0) {

                                while ($row = mysqli_fetch_assoc($result_upcoming)) {
                                    ?>
                                    <div class="item">
                                        <i class="large red clock outline middle aligned icon"></i>
                                        <div class="content">
                                            <div class="header"><?php echo $row['description']; ?></div>
                                            <div class="description">
                                                <strong><?php echo $row['amount']; ?> €</strong>
                                                <br>Due: <?php echo $row['transaction_date']; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                } // End of while loop
                            } else {
                                // What to show if they have no upcoming bills
                                echo "<div class='ui positive message'>You have no upcoming bills!</div>";
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
            <?php
        }         // Pied de page ?>

        
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 2. Use PHP json_encode to hand the data over to JavaScript safely!
    const chartDates = <?php echo json_encode($graph_dates); ?>;
    const chartAmounts = <?php echo json_encode($graph_amounts); ?>;

    // 3. Setup the Chart
    const ctx = document.getElementById('spendingChart').getContext('2d');
    const spendingChart = new Chart(ctx, {
        type: 'line', // You can change this to 'bar' if you prefer!
        data: {
            labels: chartDates, // The X-Axis
            datasets: [{
                label: 'Daily Expenses (€)',
                data: chartAmounts, // The Y-Axis
                borderColor: '#2185d0', // Semantic UI Blue
                backgroundColor: 'rgba(33, 133, 208, 0.2)', // Light blue fill
                borderWidth: 2,
                fill: true,
                tension: 0.3 // Adds a nice curve to the line
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<?php
        require 'footer.php';
        ?>