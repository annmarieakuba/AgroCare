<?php
// Start output buffering to prevent any warnings/errors from breaking JSON
ob_start();

header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/brand_controller.php';

try {
    $brands = get_brands_ctr();
    
    // Clear any output that might have been generated
    ob_clean();
    
    if ($brands !== false) {
        echo json_encode([
            'success' => true,
            'data' => $brands,
            'message' => 'Brands fetched successfully'
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'data' => [],
            'message' => 'No brands found'
        ]);
    }
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching brands: ' . $e->getMessage()
    ]);
}

ob_end_flush();
?>
