<?php

/**
 * HTTP Headers
 */
if(
	( isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/' )
	|| ( isset($_GET['page']) && $_GET['page'] == 'transactions' )
	|| ( isset($_GET['page']) && $_GET['page'] == 'charts' )
	|| ( isset($_GET['page']) && $_GET['page'] == 'trends' )
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

		// set up per-statement chart data
		if( isset($category) && isset($debit) ) {
			if( isset($statementsChartData[$statement_date][$category]) ) {
				$current_total = $statementsChartData[$statement_date][$category];
				$statementsChartData[$statement_date][$category] = ($current_total + $debit);
			} else {
				$statementsChartData[$statement_date][$category] = $debit;
			}
		}

		$i++;

	}

	// set an entry for rent
	$statement[] = array(
		'stage'       => 'POSTED',
		'date'        => formatRentDate($statement_date, $rent_day),
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

	// add rent to per-statement chart data
	$statementsChartData[$statement_date]['Rent'] = $rent;

	// sort the year-to-date chart data array
	arsort($ytdChartData);

	// sort the per-statement chart data array
	foreach($statementsChartData AS $this_statement_date => $this_statement) {
		arsort($statementsChartData[$this_statement_date]);
	}

	// remove categories with no results from year-to-date chart data, and tally the grand total
	$total = 0;
	foreach($ytdChartData AS $key => $value) {
		$total += $value;
		if($value == 0) {
			unset($ytdChartData[$key]);
		}
	}

	// remove categories with no results from per-statement chart data
	foreach($statementsChartData AS $this_statement_date => $this_statement) {
		foreach($this_statement AS $key => $value) {
			if($value == 0) {
				unset($statementsChartData[$statement_date][$key]);
			}
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

// ob_start(); echo "<pre>"; var_dump( $statementsChartData ); echo "</pre>"; $dump = ob_get_clean(); echo $dump;

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
		) {
			// TRANSACTIONS PAGE
			include('includes/transactions.inc.php');
		} else if( isset($_GET['page']) && $_GET['page'] == 'charts' ) {
			// CHARTS PAGE
			include('includes/charts.inc.php');
		} else if( isset($_GET['page']) && $_GET['page'] == 'trends' ) {
			// TRENDS PAGE
			include('includes/trends.inc.php');
		}
		?>

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