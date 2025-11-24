<?php
// Start output buffering to catch any warnings
ob_start();
header('Content-Type: application/json');

try {
    // Debug: Log what we received
    $debugInfo = [
        'files_received' => isset($_FILES['product_image']),
        'post_received' => isset($_POST['user_id']),
        'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown'
    ];
    
    // Check if file was uploaded
    if (!isset($_FILES['product_image'])) {
        echo json_encode([
            'success' => false,
            'message' => 'No file was selected. Please choose an image file to upload.',
            'debug' => $debugInfo
        ]);
        ob_end_flush();
        exit;
    }
    
    // Check for upload errors
    $uploadError = $_FILES['product_image']['error'];
    if ($uploadError !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive in php.ini (max: ' . ini_get('upload_max_filesize') . ')',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive in HTML form',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        
        $errorMsg = $errorMessages[$uploadError] ?? 'Unknown upload error (code: ' . $uploadError . ')';
        echo json_encode([
            'success' => false,
            'message' => 'Upload error: ' . $errorMsg,
            'error_code' => $uploadError
        ]);
        ob_end_flush();
        exit;
    }
    
    $file = $_FILES['product_image'];
    $user_id = (int)($_POST['user_id'] ?? 1); // Default to user 1 if not provided
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.'
        ]);
        exit;
    }
    
    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        echo json_encode([
            'success' => false,
            'message' => 'File size too large. Maximum size is 5MB.'
        ]);
        exit;
    }
    
    // Create upload directory structure
    $uploadDir = __DIR__ . '/../uploads/';
    $userDir = $uploadDir . 'u' . $user_id . '/';
    
    // For new products (product_id = 0), use a temp directory
    // For existing products, use product-specific directory
    if ($product_id > 0) {
        $productDir = $userDir . 'p' . $product_id . '/';
    } else {
        $productDir = $userDir . 'temp/';
    }
    
    // Create directories if they don't exist
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory. Please check permissions.');
        }
    }
    
    if (!is_dir($userDir)) {
        if (!mkdir($userDir, 0755, true)) {
            throw new Exception('Failed to create user directory. Please check permissions.');
        }
    }
    
    if (!is_dir($productDir)) {
        if (!mkdir($productDir, 0755, true)) {
            throw new Exception('Failed to create product directory. Please check permissions.');
        }
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'product_' . time() . '_' . uniqid() . '.' . $fileExtension;
    $filePath = $productDir . $fileName;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Return relative path from uploads directory
        if ($product_id > 0) {
            $relativePath = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $fileName;
        } else {
            $relativePath = 'uploads/u' . $user_id . '/temp/' . $fileName;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'file_path' => $relativePath,
            'file_name' => $fileName
        ]);
    } else {
        $errorMsg = 'Failed to move uploaded file';
        if (!is_writable($productDir)) {
            $errorMsg .= '. Directory is not writable. Please check permissions.';
        }
        echo json_encode([
            'success' => false,
            'message' => $errorMsg
        ]);
    }
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error uploading image: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    ob_end_flush();
}
