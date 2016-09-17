<?php
// prepare an array of all category names
$categories = array();
foreach( $statementsChartData AS $monthYear => $data ) {
	foreach( $data AS $category_name => $amount ) {
		if( !in_array( $category_name, $categories ) && $category_name != 'Rent' ) {
			$category_slug = strtolower( str_replace( ' ', '-', str_replace( '/', '-', $category_name ) ) );
			$categories[$category_slug] = $category_name;
		}
	}
}
asort($categories);

// prepare the monthly trends
$labels = '';
$debits = '';
if( isset($_GET['id']) && $_GET['id'] ) {
	// prepare the monthly trends for a specific category
	$category_slug = $_GET['id'];
	foreach( $statementsChartData AS $statement_date => $data ) {
		foreach( $data AS $category_name => $value ) {
			if( $category_name == $categories[$category_slug] ) {
				$month = substr($statement_date, 0, 2);
				$year = substr($statement_date, 3, 4);
				$date = $year . "-" . $month . "-01";
				$label = date('F', strtotime($date) );
				$labels .= '"' . $label . '",';
				$debits .= $value . ',';
			}
		}
	}
} else {
	// prepare the monthly trends for all categories
	foreach($totalsChartData as $statement_date => $value) {
		$month = substr($statement_date, 0, 2);
		$year = substr($statement_date, 3, 4);
		$date = $year . "-" . $month . "-01";
		$label = date('F', strtotime($date) );
		$labels .= '"' . $label . '",';
		$debits .= $value . ',';
	}
}
$labels = rtrim($labels, ",");
$debits = rtrim($debits, ",");

?>
<div id="main-container" class="trends container-fluid">
	<div class="thead sticky container">
		<div class="row filters">
			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="dropdown">
					<button class="btn btn-default dropdown-toggle" type="button" id="statement_filter" data-toggle="dropdown" aria-haspopup="true">
						<span class="text" data-default="View All">
							<?php
								if( isset($_GET['page']) && $_GET['page'] == 'trends' && isset($_GET['id']) && $_GET['id'] ) {
									$get_category_slug = $_GET['id'];
									echo $categories[$get_category_slug];
								} else {
									echo 'All Categories';
								}
							?>
						</span>
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" aria-labelledby="statement_filter">
						<li<?php $hasId = isset($_GET['id']); if( !$hasId ) echo ' class="active"'; ?>><a href="#trends">All Categories</a></li>
						<?php
							foreach( $categories AS $category_slug => $category_name ) {
								?>
								<li<?php if( isset($_GET['id']) && $_GET['id'] == $category_slug ) echo ' class="active"'; ?>>
									<a href="#<?= $category_slug ?>"><?= $categories[$category_slug] ?></a>
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
						if( isset($_GET['page']) && $_GET['page'] == 'trends' && isset($_GET['id']) && $_GET['id'] ) {
							// // statement total
							// $statement_date = $_GET['id'];
							// $subtotal = 0;
							// foreach($transactions[$statement_date] AS $transaction) {
							// 	$subtotal += $transaction['debit'];
							// }
							// echo 'Statement Total <strong>$' . number_format($subtotal, 2) . '</strong><br>';
							// // print income amount
							// echo 'Income <strong>$' . number_format($monthly_income, 2) . '</strong>';
							// // calculate debt or savings
							// if( $subtotal > $monthly_income ) {
							// 	$total_debt = ($subtotal - $monthly_income);
							// 	echo '<br><span style="color:#f00;">Debt Incurred</span> <strong>$' . number_format($total_debt, 2) . '</strong>';
							// } else {
							// 	$total_savings = ($monthly_income - $subtotal);
							// 	echo '<br><span style="color:#080;">Amount Saved</span> <strong>$' . number_format($total_savings, 2) . '</strong>';
							// }
						// year-to-date
						} else {
							// year-to-date statement total
							echo 'Year-To-Date Total <strong>$' . number_format($total, 2) . '</strong><br>';
							// year-to-date income
							$current_month = date('n');
							$ytd_income = ($current_month * $monthly_income);
							foreach( $one_time_incomes AS $date => $income ) {
								$ytd_income += $income['amount'];
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
				<canvas id="trendsChart" width="400" height="400"></canvas>
			</div>
		</div>
	</div>
</div>
