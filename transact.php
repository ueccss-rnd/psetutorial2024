<?php

include "connect.php";

$errors = [];

$id = null;
$quantity = null;
$cash = null;

$name = null;
$stock = null;
$price = null;

$service_charge = null;
$subtotal = null;
$total = null;
$change = null;

if (isset($_POST['transact'])) {
    $id = trim(htmlspecialchars(stripslashes($_POST['id'])));
    $quantity = trim(htmlspecialchars(stripslashes($_POST['quantity'])));
    $cash = trim(htmlspecialchars(stripslashes($_POST['cash'])));

    if (empty($id)) {
        $errors['id'] = 'Please enter a supply ID.';
    }

    if (empty($quantity)) {
        $errors['quantity'] = 'Please enter an amount.';
    } else if ($quantity <= 0) {
        $errors['quantity'] = "Quantity cannot be negative or zero.";
    }
    
    $money_pattern = '/^-?\d+(?:\.\d{1,2})?$/';

    if (empty($cash)) {
        $errors['cash'] = 'Please enter cash amount.';
    } else if ($cash <= 0) {
        $errors['cash'] = 'Cash cannot be negative or zero.';
    } else if (!preg_match($money_pattern, $cash)) {
        $errors['cash'] = 'Please enter valid cash amount.';
    }

    

    if (count($errors) == 0) {
        $query = "SELECT * FROM supplies where supply_id = '$id'";

        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $name = $row['supply_name'];
            $stock = $row['stock'];
            $price = $row['price'];
        } else {
            echo "Error: " . $conn->error;
        }
        
        // If quantity > stock amount
        if ($quantity > $stock) {
            $errors['transaction'] = "Not enough stock.";
        } else {
            $subtotal = $quantity * $price;

            // Calculate service charge
            if ($quantity >= 1 && $quantity <= 5) {
                $service_charge = $subtotal * 0.015;
            } else if ($quantity >= 6 && $quantity <= 15) {
                $service_charge = $subtotal * 0.03;
            } else if ($quantity > 15) {
                $service_charge = $subtotal * 0.05;
            }

            // Calculate total
            $total = $subtotal + $service_charge;

            // If payment < total amount
            if ($cash < $total) {
                $errors['transaction'] = "Insufficient payment.";
            } else {
                $change = $cash - $total;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRANSACT</title>
</head>
<body>
    <a href="index.php">Go Back</a>
    <h1>TRANSACTION</h1>
    <form action="transact.php" method="post">
        <div>
            <label for="id">Supply ID</label>
            <input value="<?= $id ?>" type="text" name="id" id="id">
            <?php
            if (isset($errors['id'])) {
                ?>
                <p><?= $errors['id'] ?></p>
                <?php
            }
            ?>
        </div>
        <div>
            <label for="quantity">Quantity</label>
            <input value="<?= $quantity ?>" type="text" name="quantity" id="quantity">
            <?php
            if (isset($errors['quantity'])) {
                ?>
                <p><?= $errors['quantity'] ?></p>
                <?php
            }
            ?>
        </div>
        <div>
            <label for="cash">Cash Amount</label>
            <input value="<?= $cash ?>" type="number" step="any" name="cash" id="cash">
            <?php
            if (isset($errors['cash'])) {
                ?>
                <p><?= $errors['cash'] ?></p>
                <?php
            }
            ?>
        </div>
        <button name="transact" type="submit">Print</button>
    </form>
    <div>
        <?php
        if (!isset($errors['transaction'])) {
            $remaining = $stock - $quantity;

            $query = "UPDATE supplies SET stock = '$remaining' WHERE supply_id = '$id'";

            if ($conn->query($query)) {
                $conn->close();
                ?>
                <table>
                    <tr>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                    <tr>
                        <td><?= $name ?></td>
                        <td><?= $quantity ?></td>
                        <td>Php <?= round($price, 2) ?></td>
                        <td>Php <?= round($subtotal, 2) ?></td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td>Service Charge</td>
                        <td>Php <?= round($service_charge, 2) ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total Amount</strong></td>
                        <td><strong>Php <?= round($total, 2) ?></strong></td>
                    </tr>
                    <tr>
                        <td>Cash</td>
                        <td>Php <?= round($cash, 2) ?></td>
                    </tr>
                    <tr>
                        <td>Change</td>
                        <td>Php <?= round($change, 2) ?></td>
                    </tr>
                </table>
                <?php
            } else {
                echo "Error: " . $conn->error;
            }
        } else {
            ?>
            <p><?= $errors['transaction'] ?></p>
            <?php
        }
        ?>
    </div>
</body>
</html>