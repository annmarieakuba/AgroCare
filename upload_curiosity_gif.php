<?php
/**
 * Simple upload helper for Curiosity Box GIF
 * Upload your GIF file using this page
 */

$uploadDir = __DIR__ . '/images/';
$allowedTypes = ['image/gif'];
$maxSize = 10 * 1024 * 1024; // 10MB

// Create images directory if it doesn't exist
if (!is_dir($uploadDir)) {
    $oldUmask = umask(0);
    if (!mkdir($uploadDir, 0777, true)) {
        $message = 'Failed to create images directory. Please create it manually with write permissions.';
        $messageType = 'danger';
    }
    umask($oldUmask);
}

// Ensure directory is writable
if (is_dir($uploadDir) && !is_writable($uploadDir)) {
    // Try to make it writable
    @chmod($uploadDir, 0777);
    if (!is_writable($uploadDir)) {
        $message = 'Images directory is not writable. Please set permissions to 777 on the images/ directory.';
        $messageType = 'warning';
    }
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gif_file'])) {
    $file = $_FILES['gif_file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Validate file type
        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            $message = 'Invalid file type. Please upload a GIF file.';
            $messageType = 'danger';
        } elseif ($file['size'] > $maxSize) {
            $message = 'File too large. Maximum size is 10MB.';
            $messageType = 'danger';
        } else {
            // Try multiple filenames
            $filenames = [
                'curiosity_box.gif',
                'curiosity_box_preview.gif',
                'whats_inside.gif',
                'curiosity_box_animation.gif'
            ];
            
            // Check if directory is writable before attempting upload
            if (!is_writable($uploadDir)) {
                $message = 'Images directory is not writable. Please set permissions to 777 on the images/ directory. Current permissions: ' . substr(sprintf('%o', fileperms($uploadDir)), -4);
                $messageType = 'danger';
            } else {
                $uploaded = false;
                $lastError = '';
                
                foreach ($filenames as $filename) {
                    $targetPath = $uploadDir . $filename;
                    
                    // Try to remove existing file if it exists
                    if (file_exists($targetPath)) {
                        @unlink($targetPath);
                    }
                    
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        @chmod($targetPath, 0644);
                        $message = "GIF uploaded successfully as: <strong>$filename</strong>";
                        $messageType = 'success';
                        $uploaded = true;
                        break;
                    } else {
                        $lastError = error_get_last()['message'] ?? 'Unknown error';
                    }
                }
                
                if (!$uploaded) {
                    $message = 'Failed to upload file. Error: ' . htmlspecialchars($lastError) . '. Please check directory permissions (should be 777).';
                    $messageType = 'danger';
                }
            }
        }
    } else {
        $message = 'Upload error occurred. Please try again.';
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Curiosity Box GIF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .upload-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
            max-width: 600px;
            margin: 0 auto;
        }
        .upload-area {
            border: 3px dashed #2d5016;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .upload-area:hover {
            background: #f8f9fa;
            border-color: #4a7c59;
        }
        .upload-area.dragover {
            background: #e8f5e9;
            border-color: #2d5016;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="upload-container">
            <h2 class="text-center mb-4" style="color: #2d5016;">
                <i class="fas fa-upload me-2"></i>Upload Curiosity Box GIF
            </h2>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <div class="upload-area" id="uploadArea" onclick="document.getElementById('gif_file').click();">
                    <i class="fas fa-cloud-upload-alt fa-3x mb-3" style="color: #2d5016;"></i>
                    <h5 class="mb-2">Click to Upload or Drag & Drop</h5>
                    <p class="text-muted mb-0">Upload your Curiosity Box GIF file</p>
                    <small class="text-muted">(Max size: 10MB)</small>
                </div>
                <input type="file" name="gif_file" id="gif_file" accept="image/gif" style="display: none;" required>
                <div class="mt-3 text-center">
                    <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, #2d5016, #4a7c59); color: white;">
                        <i class="fas fa-upload me-2"></i>Upload GIF
                    </button>
                    <a href="curiosity_box.php" class="btn btn-secondary btn-lg ms-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Curiosity Box
                    </a>
                </div>
            </form>
            
            <div class="mt-4 p-3 bg-light rounded">
                <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-2"></i>Accepted Filenames:</h6>
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-check text-success me-2"></i>curiosity_box.gif</li>
                    <li><i class="fas fa-check text-success me-2"></i>curiosity_box_preview.gif</li>
                    <li><i class="fas fa-check text-success me-2"></i>whats_inside.gif</li>
                    <li><i class="fas fa-check text-success me-2"></i>curiosity_box_animation.gif</li>
                </ul>
            </div>
            
            <?php if (is_dir($uploadDir)): ?>
                <div class="mt-3 p-3 rounded" style="background: <?php echo is_writable($uploadDir) ? '#d4edda' : '#f8d7da'; ?>;">
                    <small class="d-block">
                        <i class="fas fa-<?php echo is_writable($uploadDir) ? 'check-circle text-success' : 'exclamation-triangle text-danger'; ?> me-2"></i>
                        <strong>Directory Status:</strong> 
                        <?php if (is_writable($uploadDir)): ?>
                            <span class="text-success">Writable ✓</span>
                        <?php else: ?>
                            <span class="text-danger">Not Writable ✗</span>
                            <br><small class="text-muted mt-1 d-block">Run: <code>chmod 777 images</code> in terminal</small>
                        <?php endif; ?>
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('gif_file');
        
        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
            }
        });
        
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                uploadArea.innerHTML = `
                    <i class="fas fa-file-image fa-3x mb-3" style="color: #2d5016;"></i>
                    <h5 class="mb-2">${fileName}</h5>
                    <p class="text-muted mb-0">Click to change file</p>
                `;
            }
        });
    </script>
</body>
</html>

