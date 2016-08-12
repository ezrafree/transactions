<?php
$labels = '';
$debits = '';
if( isset($_GET['page']) && $_GET['page'] == 'charts' && isset($_GET['id']) && $_GET['id'] ) {
    foreach($statementsChartData[$_GET['id']] as $label => $value) {
        $labels .= '"' . $label . '",';
        $debits .= $value . ',';
    }
} else {
    foreach($ytdChartData as $label => $value) {
        $labels .= '"' . $label . '",';
        $debits .= $value . ',';
    }
}
$labels = rtrim($labels, ",");
$debits = rtrim($debits, ",");
?>
<div id="main-container" class="charts container-fluid">
    <div class="thead sticky container">
        <div class="row filters">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" id="statement_filter" data-toggle="dropdown" aria-haspopup="true">
                        <span class="text" data-default="View All">
                            <?php
                                if( isset($_GET['page']) && $_GET['page'] == 'charts' && isset($_GET['id']) && $_GET['id'] ) {
                                    echo str_replace('-', '/', $_GET['id']);
                                } else {
                                    echo 'View All';
                                }
                            ?>
                        </span>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="statement_filter">
                        <li<?php $hasId = isset($_GET['id']); if( !$hasId ) echo ' class="active"'; ?>><a href="#charts">View All</a></li>
                        <?php
                            foreach( $transactions AS $statement_date => $transactions_array ) {
                                ?>
                                <li<?php if( isset($_GET['id']) && $_GET['id'] == $statement_date ) echo ' class="active"'; ?>>
                                    <a href="#<?= $statement_date ?>"><?= str_replace('-', '/', $statement_date) ?></a>
                                </li>
                                <?php
                            }
                        ?>
                    </ul>
                    <input type="hidden" name="statement_filter" value="">
                </div>
            </div>
            <div class="col-md-6 hidden-sm hidden-xs"></div>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="total">
                    <?php
                        // statement
                        if( isset($_GET['page']) && $_GET['page'] == 'charts' && isset($_GET['id']) && $_GET['id'] ) {
                            // statement total
                            $statement_date = $_GET['id'];
                            $subtotal = 0;
                            foreach($transactions[$statement_date] AS $transaction) {
                                $subtotal += $transaction['debit'];
                            }
                            echo 'Statement Total <strong>$' . number_format($subtotal, 2) . '</strong><br>';
                            // print income amount
                            echo 'Income <strong>$' . number_format($monthly_income, 2) . '</strong>';
                            // calculate debt or savings
                            if( $subtotal > $monthly_income ) {
                                $total_debt = ($subtotal - $monthly_income);
                                echo '<br><span style="color:#f00;">Debt Incurred</span> <strong>$' . number_format($total_debt, 2) . '</strong>';
                            } else {
                                $total_savings = ($monthly_income - $subtotal);
                                echo '<br><span style="color:#080;">Amount Saved</span> <strong>$' . number_format($total_savings, 2) . '</strong>';
                            }
                        // year-to-date
                        } else {
                            // year-to-date statement total
                            echo 'Year-To-Date Total <strong>$' . number_format($total, 2) . '</strong><br>';
                            // year-to-date income
                            $current_month = date('n');
                            $ytd_income = ($current_month * $monthly_income);
                            foreach( $one_time_incomes AS $income ) {
                                $ytd_income += $income;
                            }
                            echo 'Year-To-Date Income <strong>$' . number_format($ytd_income, 2) . '</strong>';
                            // calculate debt or savings
                            if( $total > $ytd_income ) {
                                $total_debt = ($total - $ytd_income);
                                echo '<br><span style="color:#f00;">Debt Incurred</span> <strong>$' . number_format($total_debt, 2) . '</strong>';
                            } else {
                                $total_savings = ($ytd_income - $total);
                                echo '<br><span style="color:#080;">Amount Saved</span> <strong>$' . number_format($total_savings, 2) . '</strong>';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row centered">
            <div class="col-md-12 col-lg-10">
                <canvas id="myChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
</div>