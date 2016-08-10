## Transactions

Transactions is a web application to visualize spending based on credit card statement transactions.

### Supported Formats

***Please Note:*** Currently this app only supports CSV exports of statements from Capital One.

In the following examples, the date you should use to name your files is shown as `MM-YYYY`. For example, if the month and year were June of 1969, you would use `06-1969`.

***Capital One***

For Capital One credit card statements, rename each downloaded CSV file to `capitalone-MM-YYYY.csv` and copy it to the `downloads` directory.

### Development

This project uses GruntJS to build the JavaScript and stylesheet files. You'll need node.js and npm installed.

To run grunt just change into the directory and run:

    grunt watch
