<?php

/**
 * HTTP Headers
 */
if(
	( isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/' )
	|| ( isset($_GET['page']) && $_GET['page'] == 'transactions' )
	|| ( isset($_GET['page']) && $_GET['page'] == 'charts' )
) {
	$valid = true;
} else {
	$valid = false;
}
if( !$valid ) {
	if( isset($_SERVER["SERVER_PROTOCOL"]) && $_SERVER["SERVER_PROTOCOL"] ) {
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
	} else {
		header('HTTP/1.0 404 Not Found', true, 404);
	}
}

/**
 * Configuration
 */

require_once('config.php');

/**
 * Functions
 */

require_once('functions.php');

/**
 * Procedures
 */

// prepare empty array to store transactions
$transactions = array();

// prepare empty array to store year-to-date chart data (totals by category)
$ytdChartData = array();

// prepare empty array to store per-statement chart data (totals by category)
$statementsChartData = array();

// prepare directory name
$dirname = dirname(__FILE__) . $csv_directory;

// get an array of the filenames
$filenames = getFiles( $dirname );

// process each file
foreach( $filenames AS $file ) {

	// parse statement date from filename
	$statement_date = parseStatementDate($file);

	// map CSV values from this file into an array
	$csv = array_map('str_getcsv', file( $dirname . $file ));

	// loop through the transactions from this statement
	$i = 0;
	$statement = array();
	foreach( $csv AS $transaction ) {

		// skip the titles row
		if( $i > 0 ) {

			// prepare variables
			$stage = $transaction[0];
			$date = $transaction[1];
			$posted_date = $transaction[2];
			$card_number = $transaction[3];
			$description = $transaction[4];
			$category = $transaction[5];
			$debit = $transaction[6];
			$credit = $transaction[7];
			$timestamp = strtotime($transaction[1]);

			foreach($searches AS $search => $new_category) {
				if( contains($search, $description) === true ) {
					$category = $new_category;
				}
			}

			// if this transaction is a debit
			if( isset($debit) && $debit ) {
				// save CSV values from this transaction into the statement array
				$statement[] = array(
					'stage'       => $stage,
					'date'        => $date,
					'timestamp'   => $timestamp,
					'posted_date' => $posted_date,
					'card_number' => $card_number,
					'description' => $description,
					'category'    => $category,
					'debit'       => $debit,
					'credit'      => $credit
				);
			}

		}

		// set up year-to-date chart data
		if( isset($category) && isset($debit) ) {
			if( array_key_exists($category, $ytdChartData) ) {
				$current_total = $ytdChartData[$category];
				$ytdChartData[$category] = ($current_total + $debit);
			} else {
				$ytdChartData[$category] = $debit;
			}
		}

		$i++;

	}

	// set an entry for rent
	$statement[] = array(
		'stage'       => 'POSTED',
		'date'        => str_replace('-', '/', $statement_date),
		'timestamp'   => strtotime($statement_date),
		'posted_date' => $statement_date,
		'card_number' => '',
		'description' => 'RENT',
		'category'    => 'Rent',
		'debit'       => $rent,
		'credit'      => 0
	);
	// add rent to year-to-date chart data
	if( array_key_exists('Rent', $ytdChartData) ) {
		$current_total = $ytdChartData['Rent'];
		$ytdChartData['Rent'] = ($current_total + $rent);
	} else {
		$ytdChartData['Rent'] = $rent;
	}

	// sort the chart data array by the X field
	arsort($ytdChartData);

	// remove categories with no results and tally the grand total
	$total = 0;
	foreach($ytdChartData AS $key => $value) {
		$total += $value;
		if($value == 0) {
			unset($ytdChartData[$key]);
		}
	}

	// sort the statement array by the timestamp field
	$dates = array();
	foreach ($statement as $key => $row) {
	    // replace 0 with the field's index/key
	    $dates[$key]  = $row['timestamp'];
	}
	array_multisort($dates, SORT_ASC, $statement);

	// add the statement transactions to the transactions array
	$transactions[$statement_date] = $statement;

}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Transactions</title>
	<link rel="stylesheet" href="/css/style.min.css">
</head>
<body>

	<div id="content">

		<nav id="nav" class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
			<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/">Transactions</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li<?php if( isPage('home') || isPage('transactions') ) echo ' class="active"'; ?>><a href="/">View All <span class="sr-only">(current)</span></a></li>
						<li<?php echo isPage('charts'); ?>><a href="/charts/">Charts</a></li>
					</ul>
				</div>
			</div>
		</nav>

		<?php
			if( !$valid ) {
				?>
				<div id="main-container" class="error container">
					<div class="row">
						<div class="col-md-12">
							<h2>Page Not Found</h2>
							<p>Sorry, that page could not be found.</p>
						</div>
					</div>
				</div>
				<?php
			}
		?>

		<?php if(
			( isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/' )
			|| ( isset($_GET['page']) && $_GET['page'] == 'transactions' )
		) { ?>
			<div id="main-container" class="home container-fluid">
				<div class="thead sticky container">
					<div class="row filters">
						<div class="col-md-3 col-sm-6 col-xs-12">
							<div class="dropdown">
								<button class="btn btn-default dropdown-toggle" type="button" id="statement_filter" data-toggle="dropdown" aria-haspopup="true">
									<span class="text" data-default="View All">
										<?php
											if( isset($_GET['page']) && $_GET['page'] == 'transactions' ) {
												echo str_replace('-', '/', $_GET['id']);
											} else {
												echo 'View All';
											}
										?>
									</span>
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu" aria-labelledby="statement_filter">
									<li<?php $hasId = isset($_GET['id']); if( !$hasId ) echo ' class="active"'; ?>><a href="#transactions">View All</a></li>
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
									if( isset($_GET['page']) && $_GET['page'] == 'transactions' ) {
										$statement_date = $_GET['id'];
										$subtotal = 0;
										foreach($transactions[$statement_date] AS $transaction) {
											$subtotal += $transaction['debit'];
										}
										echo 'Statement Total <strong>$' . number_format($subtotal, 2) . '</strong>';
									} else {
										echo 'Year-To-Date Total <strong>$' . number_format($total, 2) . '</strong>';
									}
								?>
							</div>
						</div>
					</div>
					<div class="row titles">
						<div class="col-sm-3 col-md-3">Date</div>
						<div class="col-sm-3 col-md-3">Description</div>
						<div class="col-sm-3 col-md-3">Category</div>
						<div class="col-sm-3 col-md-3 price">Amount</div>
					</div>
				</div>
				<div class="container">
					<?php
						if( isset($_GET['page']) && $_GET['page'] == 'transactions' ) {
							$statement_date = $_GET['id'];
							foreach($transactions[$statement_date] AS $transaction) {
								?>
								<div class="row">
									<div class="col-sm-3 col-md-3"><?= $transaction['date'] ?></div>
									<div class="col-sm-3 col-md-3"><?= $transaction['description'] ?></div>
									<div class="col-sm-3 col-md-3"><?= $transaction['category'] ?></div>
									<div class="col-sm-3 col-md-3 price">$<?= number_format($transaction['debit'], 2) ?></div>
								</div>
								<?php
							}
						} else {
							foreach($transactions AS $statement) {
								foreach( $statement AS $transaction ) {
									?>
									<div class="row">
										<div class="col-sm-3 col-md-3"><?= $transaction['date'] ?></div>
										<div class="col-sm-3 col-md-3"><?= $transaction['description'] ?></div>
										<div class="col-sm-3 col-md-3"><?= $transaction['category'] ?></div>
										<div class="col-sm-3 col-md-3 price">$<?= number_format($transaction['debit'], 2) ?></div>
									</div>
									<?php
								}
							}
						}
					?>
				</div>
			</div>
		<?php } else if( isset($_GET['page']) && $_GET['page'] == 'charts' ) { ?>
			<?php
				$labels = '';
				$debits = '';
				if( isset($_GET['page']) && $_GET['page'] == 'charts' ) {
					foreach($ytdChartData as $label => $value) {
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
									<span class="text" data-default="View All">View All</span>
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
							<div class="total">Total $<?= number_format($total, 2) ?></div>
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
		<?php } ?>

	</div>

	<div id="footer">

		<div class="container">
			<div class="row">
				<div class="col-md-12">
				&copy; <script>document.write(new Date().getFullYear())</script> <a href="http://websightdesigns.com/" target="_blank">webSIGHTdesigns</a>
				</div>
			</div>
		</div>

	</div>

	<script src="/js/script.min.js"></script>
	<?php if( isset($_GET['page']) && $_GET['page'] == 'charts' ) { ?>
		<script type="text/javascript">
			var ctx = $("#myChart");
			var myChart = new Chart(ctx, {
				type: 'horizontalBar',
				responsive: true,
				options: {
					scales: {
						yAxes: [{
							ticks: {
								beginAtZero: true
							}
						}]
					},
					legend: {
						display: false
					}
				},
				data: {
					labels: [<?php echo $labels; ?>],
					datasets: [{
						data: [<?php echo $debits; ?>],
						backgroundColor: [
							'rgba(255, 99, 132, 0.2)',
							'rgba(54, 162, 235, 0.2)',
							'rgba(255, 206, 86, 0.2)',
							'rgba(75, 192, 192, 0.2)',
							'rgba(153, 102, 255, 0.2)',
							'rgba(255, 159, 64, 0.2)',
							'rgba(255, 99, 132, 0.2)',
							'rgba(54, 162, 235, 0.2)',
							'rgba(255, 206, 86, 0.2)',
							'rgba(75, 192, 192, 0.2)',
							'rgba(153, 102, 255, 0.2)',
							'rgba(255, 159, 64, 0.2)',
							'rgba(255, 99, 132, 0.2)',
							'rgba(54, 162, 235, 0.2)',
							'rgba(255, 206, 86, 0.2)',
							'rgba(75, 192, 192, 0.2)',
							'rgba(153, 102, 255, 0.2)',
							'rgba(255, 159, 64, 0.2)',
							'rgba(255, 99, 132, 0.2)',
							'rgba(54, 162, 235, 0.2)',
							'rgba(255, 206, 86, 0.2)',
							'rgba(75, 192, 192, 0.2)',
							'rgba(153, 102, 255, 0.2)',
							'rgba(255, 159, 64, 0.2)',
							'rgba(255, 99, 132, 0.2)',
							'rgba(54, 162, 235, 0.2)',
							'rgba(255, 206, 86, 0.2)',
							'rgba(75, 192, 192, 0.2)',
							'rgba(153, 102, 255, 0.2)',
							'rgba(255, 159, 64, 0.2)',
							'rgba(255, 99, 132, 0.2)',
							'rgba(54, 162, 235, 0.2)',
							'rgba(255, 206, 86, 0.2)',
							'rgba(75, 192, 192, 0.2)',
							'rgba(153, 102, 255, 0.2)',
							'rgba(255, 159, 64, 0.2)'
						],
						borderColor: [
							'rgba(255,99,132,1)',
							'rgba(54, 162, 235, 1)',
							'rgba(255, 206, 86, 1)',
							'rgba(75, 192, 192, 1)',
							'rgba(153, 102, 255, 1)',
							'rgba(255, 159, 64, 1)',
							'rgba(255,99,132,1)',
							'rgba(54, 162, 235, 1)',
							'rgba(255, 206, 86, 1)',
							'rgba(75, 192, 192, 1)',
							'rgba(153, 102, 255, 1)',
							'rgba(255, 159, 64, 1)',
							'rgba(255,99,132,1)',
							'rgba(54, 162, 235, 1)',
							'rgba(255, 206, 86, 1)',
							'rgba(75, 192, 192, 1)',
							'rgba(153, 102, 255, 1)',
							'rgba(255, 159, 64, 1)',
							'rgba(255,99,132,1)',
							'rgba(54, 162, 235, 1)',
							'rgba(255, 206, 86, 1)',
							'rgba(75, 192, 192, 1)',
							'rgba(153, 102, 255, 1)',
							'rgba(255, 159, 64, 1)',
							'rgba(255,99,132,1)',
							'rgba(54, 162, 235, 1)',
							'rgba(255, 206, 86, 1)',
							'rgba(75, 192, 192, 1)',
							'rgba(153, 102, 255, 1)',
							'rgba(255, 159, 64, 1)',
							'rgba(255,99,132,1)',
							'rgba(54, 162, 235, 1)',
							'rgba(255, 206, 86, 1)',
							'rgba(75, 192, 192, 1)',
							'rgba(153, 102, 255, 1)',
							'rgba(255, 159, 64, 1)'
						],
						borderWidth: 1
					}]
				}
			});
		</script>
	<?php } ?>

</body>
</html>