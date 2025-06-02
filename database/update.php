<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artify Database Update</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Artify Database Update</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!isset($_POST['update_db'])): ?>
                            <div class="alert alert-warning">
                                <h4 class="alert-heading">Warning!</h4>
                                <p>This tool will update your database structure to match the latest schema. Make sure you have a backup of your database before proceeding.</p>
                            </div>
                            <form method="post">
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="confirmBackup" name="confirm_backup" required>
                                    <label class="form-check-label" for="confirmBackup">I confirm that I have backed up my database</label>
                                </div>
                                <button type="submit" name="update_db" class="btn btn-primary">Update Database</button>
                            </form>
                        <?php else: ?>
                            <div class="update-results">
                                <?php 
                                // Include the update script
                                require_once 'update_db.php';
                                ?>
                                
                                <div class="mt-4">
                                    <a href="../public/index.php" class="btn btn-success">Go to Website</a>
                                    <a href="../admin/index.php" class="btn btn-secondary ms-2">Go to Admin Panel</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
