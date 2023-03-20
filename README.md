# drupal-csv-import-module
Drupal module to import news from .csv file

1. Get imported CSV file path
  1.1. Get the file id from the form state
  1.2. Get the file name from the database using the file id
  1.3. Create a path to the file using the file name
2. Get CSV file content
  2.1. Open the file using fopen() function
3. Parse the file
  3.1. Set a starting row to 1 (first row)
  3.2. While there are rows in the CSV file, get each row and store it in an array called $getData using fgetcsv() function
    3.2.1 If it's a first row, skip it and go to next row (continue) because it's a header row and we don't need it for our nodes
    3.2.2 If it's not a first row, get each column value and store it in a variable (e.g $naslov = $getData[0]) so we can use them later when creating nodes
4. Create nodes
  4.1 Create an array with node data (title, text, image etc.) using variables from step 3 ($naslov, $tekst etc.) as values for keys (title, text etc.) in an array that will be used to create nodes using Node::create() function
