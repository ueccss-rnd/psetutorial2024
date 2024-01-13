<?php

$conn = new mysqli('localhost', 'root', '', 'manubay_pse');

if (!$conn) {
    echo "Connection failed!";
    die(mysqli_error($conn));
}