<?php
echo "⏳ Script started at " . date("H:i:s") . "<br>";
echo "Waiting for 10 minutes...<br>";
flush(); // Push output

// Wait for 10 minutes (600 seconds)
sleep(10);//

echo "⏰ 10 minutes passed. Sending reminder email...<br>";
include("reminder-cron.php"); // This sends the reminder
