<?php
// Start output buffering to prevent any warnings/errors from breaking JSON
ob_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/category_controller.php';

try {
    $categories = get_categories_ctr();
    
    // Clear any output that might have been generated
    ob_clean();
    
    if ($categories !== false) {
        echo json_encode([
            'success' => true,
            'data' => $categories,
            'message' => 'Categories fetched successfully'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'data' => [],
            'message' => 'No categories found'
        ]);
    }
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch categories: ' . $e->getMessage()
    ]);
}

ob_end_flush();
?>
