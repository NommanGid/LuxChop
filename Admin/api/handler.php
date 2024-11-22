<?php
require_once '../includes/config.php';

// Check authentication and CSRF token
Auth::requireLogin();
if (!Security::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

try {
    $action = $_POST['action'] ?? '';
    $response = ['success' => false];

    switch ($action) {
        case 'update_logo':
            $response = handleLogoUpdate();
            break;
            
        case 'update_whatsapp':
            $response = handleWhatsAppUpdate();
            break;
            
        case 'manage_promotions':
            $response = handlePromotions();
            break;
            
        case 'manage_sales':
            $response = handleSales();
            break;
            
        case 'manage_events':
            $response = handleEvents();
            break;
            
        case 'manage_gallery':
            $response = handleGallery();
            break;
            
        case 'manage_testimonials':
            $response = handleTestimonials();
            break;
            
        case 'manage_faq':
            $response = handleFAQ();
            break;
            
        case 'manage_orders':
            $response = handleOrders();
            break;
            
        default:
            throw new Exception('Invalid action');
    }

    echo json_encode($response);
} catch (Exception $e) {
    Logger::log($e->getMessage(), 'ERROR');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// Handler functions
function handleLogoUpdate() {
    if (!isset($_FILES['logo'])) {
        throw new Exception('No file uploaded');
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $errors = Security::validateFile($_FILES['logo'], $allowedTypes);
    
    if (!empty($errors)) {
        throw new Exception(implode(', ', $errors));
    }

    $filename = 'logo_' . time() . '_' . basename($_FILES['logo']['name']);
    $filepath = UPLOAD_DIR . $filename;

    if (!move_uploaded_file($_FILES['logo']['tmp_name'], $filepath)) {
        throw new Exception('Failed to move uploaded file');
    }

    global $conn;
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = 'logo'");
    $stmt->bind_param("s", $filepath);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update database');
    }

    Logger::log("Logo updated: $filepath");
    return ['success' => true, 'filepath' => $filepath];
}

function handleWhatsAppUpdate() {
    $contact = Security::sanitize($_POST['contact'] ?? '');
    $group = Security::sanitize($_POST['group'] ?? '');

    if (!preg_match('/^\+?[1-9]\d{1,14}$/', $contact)) {
        throw new Exception('Invalid phone number format');
    }

    global $conn;
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = 'whatsapp_contact'");
        $stmt->bind_param("s", $contact);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_name = 'whatsapp_group'");
        $stmt->bind_param("s", $group);
        $stmt->execute();

        $conn->commit();
        Logger::log("WhatsApp settings updated");
        return ['success' => true];
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

// Add similar handler functions for other features...
?> 