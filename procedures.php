<?php

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

		// set up various chart data
		if( isset($category) && isset($debit) ) {
			// set up per-statement chart data
			if( isset($statementsChartData[$statement_date][$category]) ) {
				// if the category already exists, add to it
				$current_total = $statementsChartData[$statement_date][$category];
				$statementsChartData[$statement_date][$category] = ($current_total + $debit);
			} else {
				// if the category does not already exist, create it
				$statementsChartData[$statement_date][$category] = $debit;
			}
			// set up monthly totals chart data
			if( isset($statementsChartData[$statement_date][$category]) ) {
				// if the category already exists, add to it
				$current_total = $statementsChartData[$statement_date][$category];
				$statementsChartData[$statement_date][$category] = ($current_total + $debit);
			} else {
				// if the category does not already exist, create it
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

ob_start(); echo "<pre>"; var_dump( $statementsChartData ); echo "</pre>"; $dump = ob_get_clean(); echo $dump;
