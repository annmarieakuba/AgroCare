<?php
/**
 * Setup script to create uploads directory with proper permissions
 * Run this once to set up the uploads directory
 */

$uploadDir = __DIR__ . '/uploads/';

echo "Setting up uploads directory...\n\n";

// Create main uploads directory
if (!is_dir($uploadDir)) {
    $oldUmask = umask(0);
    if (mkdir($uploadDir, 0777, true)) {
        echo "✓ Created uploads directory: $uploadDir\n";
    } else {
        echo "✗ Failed to create uploads directory: $uploadDir\n";
        echo "Please create it manually with: mkdir -p uploads && chmod 755 uploads\n";
        exit(1);
    }
    umask($oldUmask);
} else {
    echo "✓ Uploads directory already exists: $uploadDir\n";
}

// Set permissions
if (chmod($uploadDir, 0755)) {
    echo "✓ Set permissions to 755 on uploads directory\n";
} else {
    echo "⚠ Warning: Could not set permissions. You may need to run: chmod 755 uploads\n";
}

// Create .htaccess to protect directory (optional)
$htaccessFile = $uploadDir . '.htaccess';
if (!file_exists($htaccessFile)) {
    $htaccessContent = "# Allow access to image files\n";
    $htaccessContent .= "<FilesMatch \"\\.(jpg|jpeg|png|gif|webp)$\">\n";
    $htaccessContent .= "    Order Allow,Deny\n";
    $htaccessContent .= "    Allow from all\n";
    $htaccessContent .= "</FilesMatch>\n";
    
    if (file_put_contents($htaccessFile, $htaccessContent)) {
        echo "✓ Created .htaccess file for uploads directory\n";
    }
}

// Test write permissions
$testFile = $uploadDir . 'test_write_' . time() . '.txt';
if (file_put_contents($testFile, 'test')) {
    unlink($testFile);
    echo "✓ Write permissions are working correctly\n";
} else {
    echo "✗ Write permissions test failed. Please check directory permissions.\n";
    echo "Try running: chmod 755 uploads\n";
    exit(1);
}

echo "\n✓ Setup complete! Uploads directory is ready.\n";
echo "You can now upload product images.\n";

