<?php
// Get news model
$newsModel = new NewsModel();

// Check if in edit mode
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$isEdit = $editId > 0;

// Initialize news data
$news = [
    'id' => 0,
    'title' => '',
    'text' => '',
    'date' => date('Y-m-d'),
    'active' => true
];

// If editing, get news data
if ($isEdit) {
    $newsObj = $newsModel->getNewsById($editId);
    
    if (!$newsObj) {
        Session::setFlash('error', 'News item not found');
        header('Location: ' . BASE_URL . '/admin/index.php?page=news');
        exit;
    }
    
    $news = [
        'id' => $newsObj->NewsNo,
        'title' => $newsObj->Title,
        'text' => $newsObj->Text,
        'date' => $newsObj->Date,
        'active' => $newsObj->Active
    ];
}

// Check if delete request
if (isset($_GET['delete'])) {
    $newsId = (int)$_GET['delete'];
    
    if ($newsModel->deleteNews($newsId)) {
        Session::setFlash('success', 'News item deleted successfully');
    } else {
        Session::setFlash('error', 'Failed to delete news item');
    }
    
    header('Location: ' . BASE_URL . '/admin/index.php?page=news');
    exit;
}

// Initialize errors array
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_news'])) {
    // Validate form data
    $validation = new Validation();
    
    // Validate required fields
    if ($validation->required($_POST['title'], 'Title')) {
        $news['title'] = Validation::sanitize($_POST['title']);
    } else {
        $errors['title'] = $validation->getError('Title');
    }
    
    if ($validation->required($_POST['text'], 'Text')) {
        $news['text'] = Validation::sanitize($_POST['text']);
    } else {
        $errors['text'] = $validation->getError('Text');
    }
    
    if ($validation->required($_POST['date'], 'Date')) {
        if ($validation->date($_POST['date'], 'Date')) {
            $news['date'] = $_POST['date'];
        } else {
            $errors['date'] = $validation->getError('Date');
        }
    } else {
        $errors['date'] = $validation->getError('Date');
    }
    
    // Optional fields
    $news['active'] = isset($_POST['active']) ? true : false;
    
    // If validation passed, save news
    if (empty($errors)) {
        if ($isEdit) {
            // Update news
            $result = $newsModel->updateNews([
                'id' => $editId,
                'title' => $news['title'],
                'text' => $news['text'],
                'date' => $news['date'],
                'active' => $news['active']
            ]);
            
            if ($result) {
                Session::setFlash('success', 'News item updated successfully');
                header('Location: ' . BASE_URL . '/admin/index.php?page=news');
                exit;
            } else {
                Session::setFlash('error', 'Failed to update news item');
            }
        } else {
            // Add new news
            $result = $newsModel->addNews([
                'title' => $news['title'],
                'text' => $news['text'],
                'date' => $news['date'],
                'active' => $news['active']
            ]);
            
            if ($result) {
                Session::setFlash('success', 'News item added successfully');
                header('Location: ' . BASE_URL . '/admin/index.php?page=news');
                exit;
            } else {
                Session::setFlash('error', 'Failed to add news item');
            }
        }
    }
}

// Get all news
$allNews = $newsModel->getAllNews();

// Sort news by date (most recent first)
usort($allNews, function($a, $b) {
    return strtotime($b->Date) - strtotime($a->Date);
});
?>

<div class="row">
    <!-- News Form -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><?php echo $isEdit ? 'Edit News' : 'Add News'; ?></h5>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/admin/index.php?page=news<?php echo $isEdit ? '&edit=' . $editId : ''; ?>" method="post">
                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" id="title" name="title" value="<?php echo htmlspecialchars($news['title']); ?>" required>
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['title']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Text -->
                    <div class="mb-3">
                        <label for="text" class="form-label">Content</label>
                        <textarea class="form-control <?php echo isset($errors['text']) ? 'is-invalid' : ''; ?>" id="text" name="text" rows="5" required><?php echo htmlspecialchars($news['text']); ?></textarea>
                        <?php if (isset($errors['text'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['text']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Date -->
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control <?php echo isset($errors['date']) ? 'is-invalid' : ''; ?>" id="date" name="date" value="<?php echo htmlspecialchars($news['date']); ?>" required>
                        <?php if (isset($errors['date'])): ?>
                            <div class="invalid-feedback">
                                <?php echo $errors['date']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Status -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active" name="active" <?php echo $news['active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="active">Active</label>
                        </div>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" name="save_news" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo $isEdit ? 'Update News' : 'Add News'; ?>
                        </button>
                        
                        <?php if ($isEdit): ?>
                            <a href="<?php echo BASE_URL; ?>/admin/index.php?page=news" class="btn btn-outline-secondary mt-2">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- News List -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">All News</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($allNews)): ?>
                    <div class="text-center p-4">
                        <p class="text-muted">No news items found. Add your first news item using the form.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allNews as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($item->Title); ?></div>
                                            <small class="text-muted">
                                                <?php 
                                                $text = htmlspecialchars($item->Text);
                                                echo (strlen($text) > 80) ? substr($text, 0, 80) . '...' : $text;
                                                ?>
                                            </small>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($item->Date)); ?></td>
                                        <td>
                                            <?php if ($item->Active): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo BASE_URL; ?>/admin/index.php?page=news&edit=<?php echo $item->NewsNo; ?>" class="btn btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/admin/index.php?page=news&delete=<?php echo $item->NewsNo; ?>" class="btn btn-outline-danger confirm-delete" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
