<?php
/**
 * Virtual Try-On Page
 * AR experience for trying on jewellery using MediaPipe
 */
require_once '../settings/core.php';

// Get product info from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product_image = isset($_GET['image']) ? urldecode($_GET['image']) : '';
$product_title = isset($_GET['title']) ? urldecode($_GET['title']) : 'Jewellery';
$product_category = isset($_GET['category']) ? urldecode($_GET['category']) : '';

// Determine jewellery type for positioning
$jewellery_type = 'earring'; // default
$category_lower = strtolower($product_category);
if (strpos($category_lower, 'ring') !== false) {
    $jewellery_type = 'ring';
} elseif (strpos($category_lower, 'necklace') !== false || strpos($category_lower, 'chain') !== false || strpos($category_lower, 'pendant') !== false) {
    $jewellery_type = 'necklace';
} elseif (strpos($category_lower, 'bracelet') !== false || strpos($category_lower, 'bangle') !== false) {
    $jewellery_type = 'bracelet';
} elseif (strpos($category_lower, 'earring') !== false) {
    $jewellery_type = 'earring';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Try-On - <?php echo htmlspecialchars($product_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: white;
        }
        
        .tryon-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .tryon-header {
            text-align: center;
            padding: 20px 0;
        }
        
        .tryon-header h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .tryon-header p {
            color: #888;
            font-size: 0.95rem;
        }
        
        .ar-workspace {
            display: flex;
            gap: 30px;
            margin-top: 30px;
        }
        
        .video-container {
            flex: 1;
            position: relative;
            background: #000;
            border-radius: 20px;
            overflow: hidden;
            aspect-ratio: 4/3;
            max-height: 70vh;
        }
        
        #photoCanvas {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
        
        #overlayCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            transform: scaleX(-1); /* Mirror to match photo */
        }
        
        .upload-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
            border: 3px dashed #667eea;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .upload-area:hover {
            background: rgba(102, 126, 234, 0.1);
        }
        
        .upload-area.active {
            display: flex;
        }
        
        .upload-area svg {
            width: 60px;
            height: 60px;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .upload-area p {
            color: #888;
            margin-bottom: 15px;
        }
        
        .controls-panel {
            width: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 25px;
        }
        
        .product-preview {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .product-preview img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 15px;
            background: white;
            padding: 10px;
        }
        
        .product-preview h3 {
            font-size: 1rem;
            margin-top: 15px;
            color: #ddd;
        }
        
        .product-preview .badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-top: 8px;
            display: inline-block;
        }
        
        .control-group {
            margin-bottom: 20px;
        }
        
        .control-group label {
            display: block;
            margin-bottom: 8px;
            color: #aaa;
            font-size: 0.9rem;
        }
        
        .control-group input[type="range"] {
            width: 100%;
            accent-color: #667eea;
        }
        
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 25px;
        }
        
        .btn-action {
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-capture {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-capture:hover {
            transform: scale(1.02);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-download {
            background: #28a745;
            color: white;
        }
        
        .btn-download:hover {
            background: #218838;
        }
        
        .btn-back {
            background: transparent;
            border: 2px solid #666;
            color: #888;
        }
        
        .btn-back:hover {
            border-color: #667eea;
            color: #667eea;
        }
        
        .status-message {
            text-align: center;
            padding: 15px;
            margin-top: 15px;
            border-radius: 10px;
            font-size: 0.9rem;
        }
        
        .status-loading {
            background: rgba(102, 126, 234, 0.2);
            color: #667eea;
        }
        
        .status-success {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        .status-error {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }
        
        .jewellery-type-info {
            background: rgba(102, 126, 234, 0.1);
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            color: #aaa;
        }
        
        .jewellery-type-info strong {
            color: #667eea;
        }
        
        /* Responsive */
        @media (max-width: 900px) {
            .ar-workspace {
                flex-direction: column;
            }
            
            .controls-panel {
                width: 100%;
            }
            
            .video-container {
                aspect-ratio: 1/1;
            }
        }
        
        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Hidden file input */
        #fileInput {
            display: none;
        }
    </style>
</head>
<body>
    <div class="tryon-container">
        <div class="tryon-header">
            <h1>✨ Virtual Try-On Experience</h1>
            <p>See how "<?php echo htmlspecialchars($product_title); ?>" looks on you!</p>
            <p style="font-size: 0.85rem; color: #667eea; margin-top: 5px;">📸 Upload a photo to see how this jewellery looks on you!</p>
        </div>
        
        <div class="ar-workspace">
            <!-- Photo Display -->
            <div class="video-container" id="videoContainer">
                <canvas id="photoCanvas"></canvas>
                <canvas id="overlayCanvas"></canvas>
                
                <!-- Upload Area (shown by default) -->
                <div class="upload-area active" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p>Click or drag to upload your photo</p>
                    <span style="color: #667eea;">Supports JPG, PNG</span>
                    <p style="margin-top: 15px; font-size: 0.85rem; color: #888;">
                        💡 Tip: For best results, use a well-lit photo showing your face/hands clearly
                    </p>
                </div>
                <input type="file" id="fileInput" accept="image/*" onchange="handleFileUpload(event)">
            </div>
            
            <!-- Controls Panel -->
            <div class="controls-panel">
                <div class="product-preview">
                    <?php if ($product_image): ?>
                    <img src="../<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo htmlspecialchars($product_title); ?>" id="productImage">
                    <?php else: ?>
                    <div style="width: 150px; height: 150px; background: #333; border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                        <span style="color: #666;">No Image</span>
                    </div>
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($product_title); ?></h3>
                    <span class="badge"><?php echo htmlspecialchars($product_category ?: 'Jewellery'); ?></span>
                </div>
                
                <div class="jewellery-type-info">
                    <strong>Detection Mode:</strong> 
                    <?php 
                    $type_labels = [
                        'earring' => '👂 Ear detection for earrings',
                        'necklace' => '📿 Neck detection for necklaces',
                        'ring' => '💍 Hand detection for rings',
                        'bracelet' => '⌚ Wrist detection for bracelets'
                    ];
                    echo $type_labels[$jewellery_type] ?? '💎 General jewellery';
                    ?>
                </div>
                
                <div class="control-group">
                    <label>Size Adjustment</label>
                    <input type="range" id="sizeSlider" min="0.5" max="2" step="0.1" value="1" onchange="updateSize(this.value)">
                </div>
                
                <div class="control-group">
                    <label>Position Fine-tune (Vertical)</label>
                    <input type="range" id="positionYSlider" min="-50" max="50" step="1" value="0" onchange="updatePositionY(this.value)">
                </div>
                
                <div class="control-group">
                    <label>Position Fine-tune (Horizontal)</label>
                    <input type="range" id="positionXSlider" min="-50" max="50" step="1" value="0" onchange="updatePositionX(this.value)">
                </div>
                
                <div class="action-buttons">
                    <button class="btn-action btn-download" id="downloadBtn" onclick="downloadPhoto()" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Photo
                    </button>
                    
                    <button class="btn-action btn-capture" id="uploadNewBtn" onclick="document.getElementById('fileInput').click()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Upload New Photo
                    </button>
                    
                    <a href="single_product.php?id=<?php echo $product_id; ?>" class="btn-action btn-back">
                        ← Back to Product
                    </a>
                </div>
                
                <div id="statusMessage" class="status-message status-loading">
                    <span class="spinner"></span> Loading AR models...
                </div>
            </div>
        </div>
    </div>

    <!-- MediaPipe Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils/drawing_utils.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/hands/hands.js" crossorigin="anonymous"></script>
    
    <script>
        // Configuration
        const CONFIG = {
            productImage: '<?php echo $product_image ? "../" . addslashes($product_image) : ""; ?>',
            productTitle: '<?php echo addslashes($product_title); ?>',
            jewelleryType: '<?php echo $jewellery_type; ?>',
            productId: <?php echo $product_id; ?>
        };
        
        // State
        let faceMesh = null;
        let hands = null;
        let isModelLoaded = false;
        let sizeMultiplier = 1;
        let positionOffsetX = 0;
        let positionOffsetY = 0;
        let jewelleryImg = new Image();
        let currentImageData = null;
        
        // DOM Elements
        const photoCanvas = document.getElementById('photoCanvas');
        const overlayCanvas = document.getElementById('overlayCanvas');
        const uploadArea = document.getElementById('uploadArea');
        const statusMessage = document.getElementById('statusMessage');
        const downloadBtn = document.getElementById('downloadBtn');
        
        const overlayCtx = overlayCanvas.getContext('2d');
        const photoCtx = photoCanvas.getContext('2d');
        
        // Load jewellery image
        if (CONFIG.productImage) {
            jewelleryImg.crossOrigin = 'anonymous';
            jewelleryImg.src = CONFIG.productImage;
            jewelleryImg.onerror = () => {
                console.error('Failed to load product image');
                showStatus('Could not load product image', 'error');
            };
        }
        
        // Initialize AR models
        async function initializeAR() {
            showStatus('Loading AR models...', 'loading');
            
            try {
                if (CONFIG.jewelleryType === 'ring' || CONFIG.jewelleryType === 'bracelet') {
                    await initializeHands();
                } else {
                    await initializeFaceMesh();
                }
                
                isModelLoaded = true;
                showStatus('Ready! Upload a photo to try on the jewellery.', 'success');
                
            } catch (error) {
                console.error('AR initialization error:', error);
                showStatus('Failed to load AR models. Please refresh.', 'error');
            }
        }
        
        // Initialize Face Mesh for earrings/necklaces
        async function initializeFaceMesh() {
            faceMesh = new FaceMesh({
                locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`
            });
            
            faceMesh.setOptions({
                maxNumFaces: 1,
                refineLandmarks: true,
                minDetectionConfidence: 0.5,
                minTrackingConfidence: 0.5
            });
            
            faceMesh.onResults(onFaceMeshResults);
            await faceMesh.initialize();
        }
        
        // Initialize Hands for rings/bracelets
        async function initializeHands() {
            hands = new Hands({
                locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/hands/${file}`
            });
            
            hands.setOptions({
                maxNumHands: 2,
                modelComplexity: 1,
                minDetectionConfidence: 0.5,
                minTrackingConfidence: 0.5
            });
            
            hands.onResults(onHandsResults);
            await hands.initialize();
        }
        
        // Face Mesh results handler
        function onFaceMeshResults(results) {
            overlayCtx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);
            
            if (results.multiFaceLandmarks && results.multiFaceLandmarks.length > 0) {
                const landmarks = results.multiFaceLandmarks[0];
                
                if (CONFIG.jewelleryType === 'earring') {
                    drawEarrings(landmarks);
                } else if (CONFIG.jewelleryType === 'necklace') {
                    drawNecklace(landmarks);
                }
                showStatus('Jewellery applied! Adjust size/position if needed.', 'success');
            } else {
                showStatus('No face detected. Try a clearer photo with your face visible.', 'error');
            }
        }
        
        // Hands results handler
        function onHandsResults(results) {
            overlayCtx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);
            
            if (results.multiHandLandmarks && results.multiHandLandmarks.length > 0) {
                for (const landmarks of results.multiHandLandmarks) {
                    if (CONFIG.jewelleryType === 'ring') {
                        drawRing(landmarks);
                    } else if (CONFIG.jewelleryType === 'bracelet') {
                        drawBracelet(landmarks);
                    }
                }
                showStatus('Jewellery applied! Adjust size/position if needed.', 'success');
            } else {
                showStatus('No hand detected. Try a clearer photo with your hand visible.', 'error');
            }
        }
        
        // Draw earrings at ear positions
        function drawEarrings(landmarks) {
            if (!jewelleryImg.complete) return;
            
            const leftEar = landmarks[234];  // Left ear tragion
            const rightEar = landmarks[454]; // Right ear tragion
            
            // Calculate size based on face width
            const faceWidth = Math.abs(landmarks[234].x - landmarks[454].x) * overlayCanvas.width;
            const earringSize = faceWidth * 0.25 * sizeMultiplier;
            
            // Draw left earring
            const leftX = leftEar.x * overlayCanvas.width + positionOffsetX;
            const leftY = leftEar.y * overlayCanvas.height + earringSize * 0.3 + positionOffsetY;
            
            overlayCtx.save();
            overlayCtx.translate(leftX, leftY);
            overlayCtx.drawImage(jewelleryImg, -earringSize/2, 0, earringSize, earringSize);
            overlayCtx.restore();
            
            // Draw right earring
            const rightX = rightEar.x * overlayCanvas.width + positionOffsetX;
            const rightY = rightEar.y * overlayCanvas.height + earringSize * 0.3 + positionOffsetY;
            
            overlayCtx.save();
            overlayCtx.translate(rightX, rightY);
            overlayCtx.drawImage(jewelleryImg, -earringSize/2, 0, earringSize, earringSize);
            overlayCtx.restore();
        }
        
        // Draw necklace at neck position
        function drawNecklace(landmarks) {
            if (!jewelleryImg.complete) return;
            
            // Get chin and shoulder area points
            const chin = landmarks[152];
            const leftJaw = landmarks[234];
            const rightJaw = landmarks[454];
            
            // Calculate necklace position (below chin)
            const centerX = chin.x * overlayCanvas.width + positionOffsetX;
            const centerY = (chin.y * overlayCanvas.height) + 30 + positionOffsetY;
            
            // Calculate size based on face width
            const faceWidth = Math.abs(leftJaw.x - rightJaw.x) * overlayCanvas.width;
            const necklaceWidth = faceWidth * 1.2 * sizeMultiplier;
            const necklaceHeight = necklaceWidth * (jewelleryImg.height / jewelleryImg.width);
            
            overlayCtx.save();
            overlayCtx.translate(centerX, centerY);
            overlayCtx.drawImage(jewelleryImg, -necklaceWidth/2, 0, necklaceWidth, necklaceHeight);
            overlayCtx.restore();
        }
        
        // Draw ring on finger
        function drawRing(landmarks) {
            if (!jewelleryImg.complete) return;
            
            // Ring finger base (landmark 13) and tip (landmark 14)
            const ringBase = landmarks[13];
            const ringMid = landmarks[14];
            
            const x = ringMid.x * overlayCanvas.width + positionOffsetX;
            const y = ringMid.y * overlayCanvas.height + positionOffsetY;
            
            // Calculate size based on finger width
            const fingerWidth = Math.abs(landmarks[13].x - landmarks[14].x) * overlayCanvas.width;
            const ringSize = Math.max(fingerWidth * 3, 40) * sizeMultiplier;
            
            overlayCtx.save();
            overlayCtx.translate(x, y);
            overlayCtx.drawImage(jewelleryImg, -ringSize/2, -ringSize/2, ringSize, ringSize);
            overlayCtx.restore();
        }
        
        // Draw bracelet on wrist
        function drawBracelet(landmarks) {
            if (!jewelleryImg.complete) return;
            
            // Wrist point (landmark 0)
            const wrist = landmarks[0];
            
            const x = wrist.x * overlayCanvas.width + positionOffsetX;
            const y = wrist.y * overlayCanvas.height + positionOffsetY;
            
            // Calculate size
            const braceletSize = 80 * sizeMultiplier;
            
            overlayCtx.save();
            overlayCtx.translate(x, y);
            overlayCtx.drawImage(jewelleryImg, -braceletSize/2, -braceletSize/4, braceletSize, braceletSize/2);
            overlayCtx.restore();
        }
        
        // Handle file upload
        async function handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            if (!isModelLoaded) {
                showStatus('AR models still loading. Please wait...', 'loading');
                return;
            }
            
            showStatus('Processing image...', 'loading');
            
            const img = new Image();
            img.onload = async () => {
                // Set canvas size to image size
                photoCanvas.width = img.width;
                photoCanvas.height = img.height;
                overlayCanvas.width = img.width;
                overlayCanvas.height = img.height;
                
                // Draw image on canvas (mirrored for selfie-style)
                photoCtx.save();
                photoCtx.scale(-1, 1);
                photoCtx.drawImage(img, -img.width, 0);
                photoCtx.restore();
                
                // Store original image for re-processing
                currentImageData = img;
                
                // Show photo canvas, hide upload area
                photoCanvas.style.display = 'block';
                uploadArea.classList.remove('active');
                
                // Process with MediaPipe
                await processImage();
                
                downloadBtn.style.display = 'flex';
            };
            
            img.src = URL.createObjectURL(file);
        }
        
        // Process the current image with AR
        async function processImage() {
            if (!currentImageData || !isModelLoaded) return;
            
            showStatus('Detecting features...', 'loading');
            
            // Create a temp canvas for detection (non-mirrored)
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = currentImageData.width;
            tempCanvas.height = currentImageData.height;
            const tempCtx = tempCanvas.getContext('2d');
            tempCtx.drawImage(currentImageData, 0, 0);
            
            if (CONFIG.jewelleryType === 'ring' || CONFIG.jewelleryType === 'bracelet') {
                if (hands) await hands.send({ image: tempCanvas });
            } else {
                if (faceMesh) await faceMesh.send({ image: tempCanvas });
            }
        }
        
        // Download photo with jewellery overlay
        function downloadPhoto() {
            if (!currentImageData) {
                showStatus('Please upload a photo first', 'error');
                return;
            }
            
            // Create combined canvas
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = photoCanvas.width;
            tempCanvas.height = photoCanvas.height;
            const tempCtx = tempCanvas.getContext('2d');
            
            // Draw photo
            tempCtx.drawImage(photoCanvas, 0, 0);
            
            // Draw overlay (mirrored to match)
            tempCtx.save();
            tempCtx.scale(-1, 1);
            tempCtx.drawImage(overlayCanvas, -tempCanvas.width, 0);
            tempCtx.restore();
            
            // Download
            const link = document.createElement('a');
            link.download = `tryon_${CONFIG.productTitle.replace(/\s+/g, '_')}_${Date.now()}.png`;
            link.href = tempCanvas.toDataURL('image/png');
            link.click();
            
            showStatus('Photo downloaded!', 'success');
        }
        
        // Update size from slider
        function updateSize(value) {
            sizeMultiplier = parseFloat(value);
            if (currentImageData) processImage();
        }
        
        // Update position from sliders
        function updatePositionY(value) {
            positionOffsetY = parseInt(value);
            if (currentImageData) processImage();
        }
        
        function updatePositionX(value) {
            positionOffsetX = parseInt(value);
            if (currentImageData) processImage();
        }
        
        // Show status message
        function showStatus(message, type) {
            statusMessage.className = `status-message status-${type}`;
            if (type === 'loading') {
                statusMessage.innerHTML = `<span class="spinner"></span> ${message}`;
            } else {
                statusMessage.textContent = message;
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            if (CONFIG.productImage) {
                initializeAR();
            } else {
                showStatus('No product image available', 'error');
            }
        });
    </script>
</body>
</html>
