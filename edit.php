<?php

include "connect.php";

$supply_id = $_GET['id'];

$errors = [];

$name = null;
$category = null;
$price = null;
$stock = null;
$receive_date = null;

if (!isset($_POST['edit'])) {
    $query = "SELECT * FROM supplies WHERE supply_id = '$supply_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $name = $row['supply_name'];
        $category = $row['category'];
        $price = $row['price'];
        $stock = intval($row['stock']);
        $receive_date = $row['receive_date'];
    }
} else {
    $id = $_POST['id'];
    $name = trim(htmlspecialchars(stripslashes($_POST['name'])));
    $category = trim(htmlspecialchars(stripslashes($_POST['category'])));
    $price = trim(htmlspecialchars(stripslashes($_POST['price'])));
    $stock = trim(htmlspecialchars(stripslashes($_POST['stock'])));
    $receive_date = trim(htmlspecialchars(stripslashes($_POST['receive-date'])));

    if (empty($name)) {
        $errors['name'] = "Please enter a supply name.";
    }

    if (empty($category)) {
        $errors["category"] = "Please enter a category.";
    }

    if (empty($price)) {
        $errors['price'] = "Please enter a price.";
    } else if (floatval($price) <= 0) {
        $errors["price"] = "Price must not be negative or zero.";
    }

    if (empty($stock)) {
        $errors['stock'] = "Please enter a stock amount.";
    } else if (intval($stock) <= 0) {
        $errors['stock'] = "Stock amount must not be negative or zero.";
    }

    $receive_date_timestamp = strtotime($receive_date);

    if (empty($receive_date)) {
        $errors['receive-date'] = "Please enter a receive date.";
    } else if (!$receive_date_timestamp) {
        $errors['receive-date'] = "Please enter a valid receive date.";
    } else if ($receive_date_timestamp >= time()) {
        $errors['receive-date'] = "Date cannot be in the future.";
    }

    if (count($errors) == 0) {
        $date_format = new DateTime($receive_date);
        $date = $date_format->format("Y-m-d");

        $query = "UPDATE supplies SET supply_name = '$name', category = '$category', price = '$price', stock = '$stock', receive_date = '$date' WHERE supply_id = '$id'";

        if ($conn->query($query)) {
            $conn->close();
            header("Location: index.php");
        } else {
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT</title>
</head>
<body>
    <a href="index.php">Go Back</a>
    <h1>Edit Supply</h1>
    <form action="edit.php" method="post">
        <input type="hidden" name="id" value="<?= $supply_id ?>">
        <div>
            <label for="name">Supply Name</label>
            <input type="text" value="<?= $name ?>"  name='name' id='name'>
            <?php
            if (isset($errors['name'])) {
                ?>
                <p><?= $errors['name'] ?></p>
                <?php
            }
            ?>
        </div>
        <div>
            <label for="category">Category</label>
            <input type="text" value="<?= $category ?>"  name='category' id='category'>
            <?php
            if (isset($errors['category'])) {
                ?>
                <p><?= $errors['category'] ?></p>
                <?php
            }
            ?>
        </div>
        <div>
            <label for="price">Price</label>
            <input type="number" value="<?= $price ?>" step='any'  name='price' id='price'>
            <?php
            if (isset($errors['price'])) {
                ?>
                <p><?= $errors['price'] ?></p>
                <?php
            }
            ?>
        </div>
        <div>
            <label for="stock">Stock</label>
            <input type="number" value="<?= $stock ?>"  name='stock' id='stock'>
            <?php
            if (isset($errors['stock'])) {
                ?>
                <p><?= $errors['stock'] ?></p>
                <?php
            }
            ?>
        </div>
        <div>
            <label for="receive-date">Receive Date</label>
            <input type="date" value="<?= $receive_date ?>"  name='receive-date' id='receive-date'>
            <?php
            if (isset($errors['receive-date'])) {
                ?>
                <p><?= $errors['receive-date'] ?></p>
                <?php
            }
            ?>
        </div>

        <button name='edit' type="submit">Edit</button>
    </form>
</body>
</html>