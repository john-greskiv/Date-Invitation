<?php

// Accept POST requests only.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}


// Get submitted values.
$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$activity = $_POST['activity'] ?? '';


// Make sure all required values are present.
if ($date === '' || $time === '' || $activity === '') {
    http_response_code(400);
    exit('Missing data');
}


// Store the CSV file in the same directory as this script.
$file = __DIR__ . '/dates.csv';


// Add the current server date and time.
$timestamp = date('Y-m-d H:i:s');


// Prepare the row that will be saved.
$row = [
    $timestamp,
    $date,
    $time,
    $activity
];


// Check whether the CSV file already exists.
$fileExists = file_exists($file);


// Open the file in append mode.
$handle = fopen($file, 'a');


// Stop if the file cannot be opened.
if ($handle === false) {
    http_response_code(500);
    exit('Could not open the file');
}


// Lock the file while writing.
// This prevents two requests from writing at the same time.
if (flock($handle, LOCK_EX)) {

    // Add column names when the file is created for the first time.
    if (!$fileExists) {

        fputcsv($handle, [
            'Saved at',
            'Date',
            'Time',
            'Activity'
        ]);
    }


    // Add the submitted result.
    fputcsv($handle, $row);


    // Release the file lock.
    flock($handle, LOCK_UN);
}


// Close the file.
fclose($handle);


// Tell the browser that everything was saved successfully.
echo 'OK';

?>