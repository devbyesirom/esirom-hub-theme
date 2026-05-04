<?php
/**
 * Media Upload Handler for Instagram Import
 * This script handles uploading media files from Instagram export
 */

// Load WordPress
if (!defined('ABSPATH')) {
    // Try multiple paths to find wp-load.php
    $wp_load_paths = [
        __DIR__ . '/../../../../../wp-load.php',
        __DIR__ . '/../../../../wp-load.php',
        __DIR__ . '/../../../wp-load.php',
    ];
    
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once($path);
            break;
        }
    }
}

// Set JSON header first
header('Content-Type: application/json');

// Check authentication - either WordPress login or JWT token
$is_authenticated = false;

// Check WordPress login first
if (is_user_logged_in()) {
    $is_authenticated = true;
} 
// Check for JWT token in POST data
else if (isset($_POST['auth_token'])) {
    $token = sanitize_text_field($_POST['auth_token']);
    // Basic token validation - you may want to add JWT verification here
    if (!empty($token)) {
        $is_authenticated = true;
    }
}

if (!$is_authenticated) {
    http_response_code(401);
    echo json_encode(['success' => false, 'data' => ['message' => 'Unauthorized - Please login first']]);
    exit;
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['mediaFiles'])) {
    $clientName = sanitize_text_field($_POST['clientName'] ?? '');
    $mediaType = sanitize_text_field($_POST['mediaType'] ?? 'reels'); // reels, stories, profile
    $monthFolder = sanitize_text_field($_POST['monthFolder'] ?? '');
    
    if (empty($clientName)) {
        wp_send_json_error(['message' => 'Client name is required']);
        exit;
    }
    
    // Create client slug
    $clientSlug = strtolower(str_replace(' ', '-', $clientName));
    
    // Base upload directory
    $baseDir = WP_CONTENT_DIR . '/media/' . $clientSlug . '/' . $mediaType;
    
    // Add month folder if provided
    if (!empty($monthFolder)) {
        $baseDir .= '/' . $monthFolder;
    }
    
    // Create directory if it doesn't exist
    if (!file_exists($baseDir)) {
        if (!wp_mkdir_p($baseDir)) {
            wp_send_json_error(['message' => 'Failed to create upload directory']);
            exit;
        }
    }
    
    $uploadedFiles = [];
    $errors = [];
    
    // Handle multiple files
    $files = $_FILES['mediaFiles'];
    $fileCount = is_array($files['name']) ? count($files['name']) : 1;
    
    for ($i = 0; $i < $fileCount; $i++) {
        $fileName = is_array($files['name']) ? $files['name'][$i] : $files['name'];
        $fileTmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
        $fileError = is_array($files['error']) ? $files['error'][$i] : $files['error'];
        $fileSize = is_array($files['size']) ? $files['size'][$i] : $files['size'];
        
        // Check for upload errors
        if ($fileError !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading {$fileName}";
            continue;
        }
        
        // Validate file type (images and videos only)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/quicktime'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileTmpName);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $errors[] = "{$fileName} is not an allowed file type";
            continue;
        }
        
        // Sanitize filename
        $fileName = sanitize_file_name($fileName);
        $targetPath = $baseDir . '/' . $fileName;
        
        // Move uploaded file
        if (move_uploaded_file($fileTmpName, $targetPath)) {
            $uploadedFiles[] = [
                'name' => $fileName,
                'path' => str_replace(WP_CONTENT_DIR, '/wp-content', $targetPath),
                'size' => $fileSize
            ];
        } else {
            $errors[] = "Failed to save {$fileName}";
        }
    }
    
    wp_send_json_success([
        'message' => count($uploadedFiles) . ' files uploaded successfully',
        'uploaded' => $uploadedFiles,
        'errors' => $errors,
        'uploadPath' => str_replace(WP_CONTENT_DIR, '/wp-content', $baseDir)
    ]);
    
} else {
    wp_send_json_error(['message' => 'No files uploaded or invalid request']);
}
