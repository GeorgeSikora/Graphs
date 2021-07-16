<?php

$conn = new mysqli("localhost", "root", "", "graphs");

$fromDate = '2010-01-01';
$toDate = '2012-01-01'; //date('Y-m-d');

$sql = "";
$date = $fromDate;
$years = 21;

$totalDays = $years * 365;
$sql = "INSERT INTO visits (id, date) VALUES ";

$recordIndex = 0;
while ($date < $toDate) {

    for ($i = 0; $i < rand(50, 200); $i++) {
        if ($recordIndex != 0) $sql .= ", ";
        $sql .= "(0,'$date')";
        $recordIndex++;
    }

    $date = date('Y-m-d', strtotime($date.' + 1day'));
}

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

echo 'Hotovo!';