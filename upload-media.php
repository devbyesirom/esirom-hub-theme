<?php
/**
 * Media upload handler for the Admin Panel data import.
 *
 * Only reachable through the `upload_media` admin-ajax action registered in
 * functions.php, which has already verified the caller is a Hub administrator
 * (WordPress session or a Hub JWT validated against the backend).
 *
 * Files are stored under wp-content/media/<brand-slug>/<media-type>/<month>/.
 */

if (!defined('ABSPATH')) {
    http_response_code(403);
    exit;
}

if (!defined('ESIROM_HUB_UPLOAD_AUTHORISED') || !ESIROM_HUB_UPLOAD_AUTHORISED) {
    wp_send_json_error(array('message' => 'Unauthorised'), 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['mediaFiles'])) {
    wp_send_json_error(array('message' => 'No files were received.'), 400);
}

/**
 * Reduce a user-supplied path segment to a safe slug (no traversal, no separators).
 */
$hub_segment = static function ($value, $fallback = '') {
    $value = is_string($value) ? wp_unslash($value) : '';
    $slug = sanitize_title($value);
    $slug = preg_replace('/[^a-z0-9\-_]/', '', $slug);
    return $slug !== '' ? $slug : $fallback;
};

$clientSlug = $hub_segment($_POST['clientName'] ?? '');
$mediaType = $hub_segment($_POST['mediaType'] ?? 'reels', 'reels');
$monthFolder = $hub_segment($_POST['monthFolder'] ?? '');

if ($clientSlug === '') {
    wp_send_json_error(array('message' => 'Brand name is required.'), 400);
}

if (!in_array($mediaType, array('reels', 'posts', 'stories', 'profile', 'videos', 'photos', 'media'), true)) {
    wp_send_json_error(array('message' => 'Unsupported media folder "' . $mediaType . '".'), 400);
}

$baseDir = WP_CONTENT_DIR . '/media/' . $clientSlug . '/' . $mediaType;
if ($monthFolder !== '') {
    $baseDir .= '/' . $monthFolder;
}

if (!file_exists($baseDir) && !wp_mkdir_p($baseDir)) {
    wp_send_json_error(array('message' => 'Failed to create the upload directory.'), 500);
}

$allowedTypes = array('image/jpeg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'video/quicktime');
$uploadedFiles = array();
$errors = array();

$files = $_FILES['mediaFiles'];
$fileCount = is_array($files['name']) ? count($files['name']) : 1;

for ($i = 0; $i < $fileCount; $i++) {
    $fileName = is_array($files['name']) ? $files['name'][$i] : $files['name'];
    $fileTmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
    $fileError = is_array($files['error']) ? $files['error'][$i] : $files['error'];
    $fileSize = is_array($files['size']) ? $files['size'][$i] : $files['size'];

    if ($fileError !== UPLOAD_ERR_OK || !is_uploaded_file($fileTmpName)) {
        $errors[] = "Error uploading {$fileName}";
        continue;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileTmpName);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes, true)) {
        $errors[] = "{$fileName} is not an allowed file type";
        continue;
    }

    $fileName = sanitize_file_name($fileName);
    if ($fileName === '') {
        $errors[] = 'A file had an invalid name and was skipped';
        continue;
    }
    $targetPath = $baseDir . '/' . $fileName;

    if (move_uploaded_file($fileTmpName, $targetPath)) {
        $uploadedFiles[] = array(
            'name' => $fileName,
            'path' => str_replace(WP_CONTENT_DIR, '/wp-content', $targetPath),
            'size' => $fileSize,
        );
    } else {
        $errors[] = "Failed to save {$fileName}";
    }
}

wp_send_json_success(array(
    'message' => count($uploadedFiles) . ' file(s) uploaded',
    'uploaded' => $uploadedFiles,
    'errors' => $errors,
    'uploadPath' => str_replace(WP_CONTENT_DIR, '/wp-content', $baseDir),
));
