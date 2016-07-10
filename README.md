## Transactions

Transactions is a web application to visualize spending based on credit card statement transactions.

Currently Transactions only supports CSV exports of statements from Capital One. You must rename each downloaded CSV file to `Transactions-Statement-MM-DD-YYY.csv` (using the statement date) and copy it to the `downloads` directory.

### To Do

- Instead of just a field for rent, set it up so that an array can be used to add in any monthly transactions
- Allow filtering by statement of the Transactions List and Transactions Categories views

### Development

This project uses GruntJS to build the JavaScript and stylesheet files. You'll need node.js and npm installed.

To run grunt just change into the directory and run:

    grunt watch
