<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    
    <style>
        .header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include "header.php"; ?>

<div class="container mt-5">
    <h1 class="mb-4">Pesan Customer</h1>

    <!-- Reviews Table -->
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Komentar</th>
                <th scope="col">Kategori</th>
                <th scope="col">Nama</th>
                <th scope="col">Email</th>
                <th scope="col">No. HP</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database connection
            include "config.php";

            // Query to fetch reviews
            $query = "SELECT reviewID, komentar, kategori, nama, email, noHP FROM reviews";
            $result = mysqli_query($conn, $query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['reviewID']}</td>
                        <td>{$row['komentar']}</td>
                        <td>{$row['kategori']}</td>
                        <td>{$row['nama']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['noHP']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No reviews found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<?php include "footer.php"; ?>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

</body>
</html>
