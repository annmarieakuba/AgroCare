<?php
/**
 * Quick fix script for uploads directory permissions
 * Access this file in your browser: http://localhost/lab1_e/fix_uploads.php
 */

$uploadDir = __DIR__ . '/uploads/';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Uploads Directory</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2d5016; }
        .success { color: #28a745; padding: 10px; background: #d4edda; border-radius: 5px; margin: 10px 0; }
        .error { color: #dc3545; padding: 10px; background: #f8d7da; border-radius: 5px; margin: 10px 0; }
        .info { color: #0c5460; padding: 10px; background: #d1ecf1; border-radius: 5px; margin: 10px 0; }
        button { background: #2d5016; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #4a7c59; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Uploads Directory</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix'])) {
            echo '<h2>Fixing Uploads Directory...</h2>';
            
            // Create uploads directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                if (mkdir($uploadDir, 0777, true)) {
                    echo '<div class="success">✓ Created uploads directory</div>';
                } else {
                    echo '<div class="error">✗ Failed to create uploads directory</div>';
                }
            } else {
                echo '<div class="info">ℹ Uploads directory already exists</div>';
            }
            
            // Set permissions
            if (is_dir($uploadDir)) {
                // Try different permission levels
                $permissions = [0777, 0755, 0700];
                $success = false;
                
                foreach ($permissions as $perm) {
                    if (chmod($uploadDir, $perm)) {
                        echo '<div class="success">✓ Set permissions to ' . decoct($perm) . ' on uploads directory</div>';
                        $success = true;
                        break;
                    }
                }
                
                if (!$success) {
                    echo '<div class="error">⚠ Could not change permissions automatically</div>';
                }
                
                // Test write
                $testFile = $uploadDir . 'test_' . time() . '.txt';
                if (file_put_contents($testFile, 'test')) {
                    unlink($testFile);
                    echo '<div class="success">✓ Write test successful! Directory is writable.</div>';
                    echo '<div class="success"><strong>✅ Setup complete! You can now upload images.</strong></div>';
                } else {
                    echo '<div class="error">✗ Write test failed. Directory is not writable.</div>';
                    echo '<div class="info">Please manually set permissions: <code>chmod 777 uploads</code></div>';
                }
            }
        } else {
            ?>
            <p>This script will fix the uploads directory permissions so you can upload product images.</p>
            
            <div class="info">
                <strong>Current Status:</strong><br>
                Directory exists: <?php echo is_dir($uploadDir) ? '✓ Yes' : '✗ No'; ?><br>
                Directory writable: <?php echo is_writable($uploadDir) ? '✓ Yes' : '✗ No'; ?><br>
                <?php if (is_dir($uploadDir)): ?>
                    Permissions: <?php echo substr(sprintf('%o', fileperms($uploadDir)), -4); ?>
                <?php endif; ?>
            </div>
            
            <form method="POST">
                <button type="submit" name="fix">Fix Uploads Directory</button>
            </form>
            <?php
        }
        ?>
        
        <hr style="margin: 20px 0;">
        <p><a href="admin/product.php">← Back to Product Management</a></p>
    </div>
</body>
</html>

