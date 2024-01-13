<?php

include "connect.php";

$errors = [];

$name = null;
$category = null;
$price = null;
$stock = null;
$receive_date = null;

if (isset($_POST['create'])) {
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
        // Generate ID
        $supply_id = "";
        // First three letters of category
        $supply_id .= substr($category, 0, 3) . "-";
        // First five letters of supply name
        $supply_id .= substr($name, 0, 5) . "-";
        // Random 4-digit integer
        $supply_id .= rand(1000, 9999) . "-";
        // Receive date of supply (ddMMMyyyy)
        $date_format = new DateTime($receive_date);
        $id_date = $date_format->format("dMY");
        $supply_id .= $id_date;
        $supply_id = strtoupper($supply_id);

        $date = $date_format->format('Y-m-d');

        $query = "INSERT INTO supplies (supply_id, supply_name, category, price, stock, receive_date) VALUES ('$supply_id', '$name', '$category', '$price', '$stock', '$date')";
        if ($conn->query($query)) {
            $conn->close();
            header("Location: index.php");
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREATE</title>
</head>
<body>
    <a href="index.php">Go Back</a>
    <h1>Add Supply</h1>
    <form action="create.php" method="post">
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

        <button name='create' type="submit">Add</button>
    </form>
</body>
</html>