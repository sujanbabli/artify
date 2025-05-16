<?php
include 'connect.php'; // DB connection

if (!isset($_GET['id'])) {
    die("Product ID is required.");
}

$id = intval($_GET['id']);
$name = $price = $image = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);

    // Get old image filename from hidden input
    $oldImage = $_POST['old_image'] ?? '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'images/';
        $tmpName = $_FILES['image']['tmp_name'];
        $originalName = basename($_FILES['image']['name']);
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);

        // Generate a unique filename to avoid conflicts
        $newImageName = uniqid('prod_', true) . '.' . $ext;

        // Validate file type (optional but recommended)
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($ext), $allowedExt)) {
            $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } else {
            $destination = $uploadDir . $newImageName;
            if (move_uploaded_file($tmpName, $destination)) {
                // Delete old image file if it exists and is not empty
                if ($oldImage && file_exists($uploadDir . $oldImage)) {
                    unlink($uploadDir . $oldImage);
                }
                $image = $newImageName; // use new uploaded image
            } else {
                $error = "Failed to upload the image.";
            }
        }
    } else {
        // No new image uploaded, keep old image
        $image = $oldImage;
    }

    if (!$error) {
        if ($name === "" || $price <= 0) {
            $error = "Please fill all fields correctly.";
        } else {
            $stmt = $conn->prepare("UPDATE products SET Name = ?, Price = ?, Image = ? WHERE Id = ?");
            if ($stmt === false) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("sdsi", $name, $price, $image, $id);

            if ($stmt->execute()) {
                header("Location: view.php?msg=Product+updated+successfully");
                exit;
            } else {
                $error = "Update failed: " . $stmt->error;
            }
            $stmt->close();
        }
    }
} else {
    $stmt = $conn->prepare("SELECT Name, Price, Image FROM products WHERE Id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt->bind_result($name, $price, $image);

    if (!$stmt->fetch()) {
        die("Product not found.");
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Update Product</title>
<style>
  body { font-family: Arial, sans-serif; padding: 20px; }
  form { max-width: 400px; margin: auto; background: #f7f7f7; padding: 20px; border-radius: 8px; }
  label { display: block; margin-top: 15px; font-weight: bold; }
  input[type="text"], input[type="number"], input[type="file"] {
    width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box;
  }
  .error { color: red; margin-top: 10px; }
  button {
    margin-top: 20px; padding: 10px 15px; background-color: #4caf50;
    border: none; color: white; font-weight: bold; border-radius: 5px; cursor: pointer;
  }
  button:hover { background-color: #45a049; }
  img {
    margin-top: 10px;
    max-width: 150px;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  }
  a { display: inline-block; margin-top: 15px; color: #555; text-decoration: none; }
  a:hover { text-decoration: underline; }
</style>
</head>
<body>

<h2>Update Product</h2>

<?php if ($error): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="" enctype="multipart/form-data">
  <label for="name">Product Name</label>
  <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" required />

  <label for="price">Price ($)</label>
  <input type="number" step="0.01" min="0" name="price" id="price" value="<?= htmlspecialchars($price) ?>" required />

  <label for="image">Product Image (upload to replace)</label>
  <input type="file" name="image" id="image" accept="image/*" />
  
  <?php if ($image): ?>
    <img src="images/<?= htmlspecialchars($image) ?>" alt="Current Image" />
  <?php endif; ?>

  <!-- Pass old image filename so we can delete if replaced -->
  <input type="hidden" name="old_image" value="<?= htmlspecialchars($image) ?>" />

  <button type="submit">Update Product</button>
</form>

<p><a href="view.php">← Back to Products</a></p>

</body>
</html>
