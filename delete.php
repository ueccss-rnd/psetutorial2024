<?php

include "connect.php";

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $query = "DELETE FROM supplies WHERE supply_id = '$id'";

    if ($conn->query($query)) {
        $conn->close();
        header("Location: index.php");
    } else {
        echo "Unable to delete record.";
    }
}