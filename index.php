<?php

include "connect.php";

$search = null;
$query = null;

if (isset($_GET['search']) && strlen($_GET['search']) > 0) {
    $search = trim(htmlspecialchars(stripslashes($_GET['search'])));

    $query = "SELECT * FROM supplies WHERE supply_id LIKE '%$search%' OR supply_name LIKE '%$search%'";
} else {
    $query = "SELECT * FROM supplies";
}

$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>
        <a href="create.php">Add Supply</a>
        <a href="transact.php">Transaction</a>
    </header>
    <main>
        <form action="index.php" method='get'>
            <input type="text" value="<?= $search ?>" name="search" id="search">
            <button type="submit">Search</button>
        </form>
        <table>
            <thead>
                <th>Supply ID</th>
                <th>Supply Name</th>
                <th>Supply Category</th>
                <th>Supply Price</th>
                <th>Supply Stock</th>
                <th>Receive Date</th>
                <th>Actions</th>
            </thead>
            <tbody>
                <?php
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td><?= $row['supply_id'] ?></td>
                            <td><?= $row['supply_name'] ?></td>
                            <td><?= $row['category'] ?></td>
                            <td><?= $row['price'] ?></td>
                            <td><?= $row['stock'] ?></td>
                            <td><?= date_format(date_create($row['receive_date']), 'F j, Y') ?></td>
                            <td>
                                <a href="edit.php?id=<?=$row['supply_id']?>">Edit</a>
                                <form action="delete.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $row['supply_id'] ?>">
                                    <button name="delete" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>