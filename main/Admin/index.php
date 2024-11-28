<?php
require_once 'includes/config.php';
Auth::requireLogin();
 
// Fetch current settings
$sql = "SELECT * FROM settings";
$result = $conn->query($sql);
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_name']] = $row['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LuxChop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="#logo"><i class="fas fa-image"></i> Change Logo</a></li>
            <li><a href="#contact"><i class="fab fa-whatsapp"></i> WhatsApp Settings</a></li>
            <li><a href="#promotions"><i class="fas fa-percentage"></i> Manage Promotions</a></li>
            <li><a href="#sales"><i class="fas fa-chart-line"></i> Sales</a></li>
            <li><a href="#events"><i class="fas fa-calendar"></i> Event Catering</a></li>
            <li><a href="#gallery"><i class="fas fa-images"></i> Gallery</a></li>
            <li><a href="#testimonials"><i class="fas fa-comments"></i> Testimonials</a></li>
            <li><a href="#faq"><i class="fas fa-question-circle"></i> FAQ</a></li>
            <li><a href="#orders"><i class="fas fa-shopping-cart"></i> Track Orders</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <!-- Logo Section -->
        <section id="logo" class="section">
            <h3>Change Logo</h3>
            <form id="logoForm" class="mb-4">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                <div class="mb-3">
                    <label class="form-label">Current Logo</label>
                    <img src="<?php echo htmlspecialchars($settings['logo'] ?? ''); ?>" class="img-thumbnail d-block" style="max-width: 200px;">
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload New Logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Logo</button>
            </form>
        </section>

        <!-- WhatsApp Settings Section -->
        <section id="contact" class="section">
            <h3>WhatsApp Settings</h3>
            <form id="whatsappForm">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                <div class="mb-3">
                    <label class="form-label">WhatsApp Contact Number</label>
                    <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($settings['whatsapp_contact'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">WhatsApp Group Link</label>
                    <input type="url" name="group" class="form-control" value="<?php echo htmlspecialchars($settings['whatsapp_group'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Update WhatsApp Settings</button>
            </form>
        </section>

        <!-- Add similar sections for other features -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
</body>
</html> 