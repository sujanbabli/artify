<?php
include 'connect.php'; // DB connection

// Fetch all products ordered by Id ascending (to keep S.N. in ascending order)
$result = $conn->query("SELECT Id, Name, Price, Image FROM products ORDER BY Id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ArtNest | View Products</title>
  <link rel="stylesheet" href="css/styles.css" />
  <style>
    table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  border-radius: 10px;
  overflow: hidden;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

thead {
  background: linear-gradient(90deg, #4b6cb7 0%, #182848 100%);
  color: #fff;
  font-weight: 700;
}

th, td {
  padding: 14px 20px;
  text-align: left;
}

tbody tr:nth-child(even) {
  background-color: #f9f9f9;
}

tbody tr:hover {
  background-color: #d6e4ff;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

img {
  max-width: 100px;
  height: auto;
  display: block;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.action-btn {
  padding: 8px 14px;
  margin-right: 6px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  text-decoration: none;
  transition: background-color 0.3s ease;
  display: inline-block;
  font-size: 0.9rem;
  user-select: none;
}

.update-btn {
  background-color: #4CAF50;
  color: white;
}

.update-btn:hover {
  background-color: #45a049;
}

.delete-btn {
  background-color: #f44336;
  color: white;
}

.delete-btn:hover {
  background-color: #d7372a;
}

@media (max-width: 768px) {
  table, thead, tbody, th, td, tr {
    display: block;
  }

  thead tr {
    display: none;
  }

  tbody tr {
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 15px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }

  tbody td {
    padding: 10px 15px;
    text-align: right;
    position: relative;
    padding-left: 50%;
    border: none;
    border-bottom: 1px solid #eee;
  }

  tbody td::before {
    position: absolute;
    top: 50%;
    left: 15px;
    width: 45%;
    padding-right: 10px;
    white-space: nowrap;
    transform: translateY(-50%);
    font-weight: 700;
    text-align: left;
    color: #333;
  }

  tbody td:nth-of-type(1)::before { content: "S.N."; }
  tbody td:nth-of-type(2)::before { content: "Product Name"; }
  tbody td:nth-of-type(3)::before { content: "Price"; }
  tbody td:nth-of-type(4)::before { content: "Image"; }
  tbody td:nth-of-type(5)::before { content: "Action"; }

  .action-btn {
    margin: 5px 5px 0 0;
  }
}

  </style>
</head>
<body>

<?php include('header.php'); ?>

<section class="container">
  <h2>All Products</h2>
  <?php if ($result && $result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>S.N.</th>

          <th>Product Name</th>
          <th>Price ($)</th>
          <th>Image</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $sn = 1; // Serial Number starts at 1
        while ($product = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $sn++ ?></td> <!-- Increment S.N -->
            <td><?= htmlspecialchars($product['Name']) ?></td>
            <td><?= number_format($product['Price'], 2) ?></td>
            <td>
              <img src="images/<?= htmlspecialchars($product['Image']) ?>" alt="<?= htmlspecialchars($product['Name']) ?>" />
            </td>
            <td>
              <a href="update.php?id=<?= $product['Id'] ?>" class="action-btn update-btn">Update</a>
              <a href="delete.php?id=<?= $product['Id'] ?>" 
                 class="action-btn delete-btn" 
                 onclick="return confirm('Are you sure you want to delete this product?');">
                 Delete
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No products found.</p>
  <?php endif; ?>
</section>

<footer class="footer">
  <p>&copy; 2025 ArtNest. Handcrafted with heart in Darwin.</p>
</footer>

<script src="js/main.js"></script>
</body>
</html>
