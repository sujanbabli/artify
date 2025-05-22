<?php
// Get testimonial model
$testimonialModel = new TestimonialModel();

// Check if approve or delete request
if (isset($_GET['approve'])) {
    $testimonialId = (int)$_GET['approve'];
    
    if ($testimonialModel->approveTestimonial($testimonialId)) {
        Session::setFlash('success', 'Testimonial approved successfully');
    } else {
        Session::setFlash('error', 'Failed to approve testimonial');
    }
    
    header('Location: ' . BASE_URL . '/admin/index.php?page=testimonials');
    exit;
} elseif (isset($_GET['delete'])) {
    $testimonialId = (int)$_GET['delete'];
    
    if ($testimonialModel->deleteTestimonial($testimonialId)) {
        Session::setFlash('success', 'Testimonial deleted successfully');
    } else {
        Session::setFlash('error', 'Failed to delete testimonial');
    }
    
    header('Location: ' . BASE_URL . '/admin/index.php?page=testimonials');
    exit;
}

// Get filter
$filter = isset($_GET['filter']) ? Validation::sanitize($_GET['filter']) : 'pending';

// Get testimonials based on filter
switch ($filter) {
    case 'approved':
        $testimonials = $testimonialModel->getApprovedTestimonials();
        break;
    case 'all':
        $testimonials = $testimonialModel->getAllTestimonials();
        break;
    case 'pending':
    default:
        $testimonials = $testimonialModel->getPendingTestimonials();
        break;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Manage Testimonials</h4>
    <div class="btn-group">
        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=testimonials&filter=pending" class="btn <?php echo $filter == 'pending' ? 'btn-primary' : 'btn-outline-primary'; ?>">
            Pending
            <?php 
            $pendingCount = count($testimonialModel->getPendingTestimonials());
            if ($pendingCount > 0): 
            ?>
                <span class="badge bg-danger"><?php echo $pendingCount; ?></span>
            <?php endif; ?>
        </a>
        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=testimonials&filter=approved" class="btn <?php echo $filter == 'approved' ? 'btn-primary' : 'btn-outline-primary'; ?>">Approved</a>
        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=testimonials&filter=all" class="btn <?php echo $filter == 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">All</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($testimonials)): ?>
            <div class="text-center p-4">
                <p class="text-muted">No <?php echo $filter; ?> testimonials found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Rating</th>
                            <th>Testimonial</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testimonials as $testimonial): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($testimonial->Name); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($testimonial->Email); ?></small>
                                </td>
                                <td>
                                    <div class="text-warning">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $testimonial->Rating): ?>
                                                <i class="fas fa-star"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                    $text = htmlspecialchars($testimonial->Text);
                                    echo (strlen($text) > 100) ? substr($text, 0, 100) . '...' : $text;
                                    ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($testimonial->Date)); ?></td>
                                <td>
                                    <?php if ($testimonial->Approved): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewTestimonialModal<?php echo $testimonial->TestimonialNo; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if (!$testimonial->Approved): ?>
                                            <a href="<?php echo BASE_URL; ?>/admin/index.php?page=testimonials&approve=<?php echo $testimonial->TestimonialNo; ?>" class="btn btn-outline-success">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=testimonials&delete=<?php echo $testimonial->TestimonialNo; ?>" class="btn btn-outline-danger confirm-delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                    
                                    <!-- Testimonial Modal -->
                                    <div class="modal fade" id="viewTestimonialModal<?php echo $testimonial->TestimonialNo; ?>" tabindex="-1" aria-labelledby="viewTestimonialModalLabel<?php echo $testimonial->TestimonialNo; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="viewTestimonialModalLabel<?php echo $testimonial->TestimonialNo; ?>">Testimonial Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Name</label>
                                                        <p><?php echo htmlspecialchars($testimonial->Name); ?></p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Email</label>
                                                        <p><?php echo htmlspecialchars($testimonial->Email); ?></p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Rating</label>
                                                        <div class="text-warning">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <?php if ($i <= $testimonial->Rating): ?>
                                                                    <i class="fas fa-star"></i>
                                                                <?php else: ?>
                                                                    <i class="far fa-star"></i>
                                                                <?php endif; ?>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Date</label>
                                                        <p><?php echo date('F j, Y', strtotime($testimonial->Date)); ?></p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Testimonial</label>
                                                        <div class="p-3 bg-light rounded">
                                                            <?php echo nl2br(htmlspecialchars($testimonial->Text)); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <?php if (!$testimonial->Approved): ?>
                                                        <a href="<?php echo BASE_URL; ?>/admin/index.php?page=testimonials&approve=<?php echo $testimonial->TestimonialNo; ?>" class="btn btn-success">
                                                            <i class="fas fa-check me-1"></i> Approve
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?php echo BASE_URL; ?>/admin/index.php?page=testimonials&delete=<?php echo $testimonial->TestimonialNo; ?>" class="btn btn-danger confirm-delete">
                                                        <i class="fas fa-trash me-1"></i> Delete
                                                    </a>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
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
