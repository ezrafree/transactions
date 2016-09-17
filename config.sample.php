<?php

// set the webroot path
$webroot = '/';

// set the directory path to the csv files
$csv_directory = "/downloads/";

// enter the dollar amount of your monthly rent payments
$rent = 0;

// enter the numeral of the day of the month on which you pay rent
$rent_day = 15;

// enter the dollar amount of your monthly income
$monthly_income = 1000;

// enter any one-time incomes
$one_time_incomes = array(
    '1969-01-01' => array(
        'amount' => '100',
        'description' => 'DESCRIPTION',
    ),
);

// map new categories for certain transaction descriptions
$searches = array(
	'DESCRIPTION' => 'Category',
);

// enter your desired budget for each category
$category_limits = array(
    'Rent'        => $rent,
    'Groceries'   => '400',
    'Dining'      => '80',
    'Merchandise' => '200',
    'Phone/Cable' => '160',
    'Utilities'   => '150',
);
