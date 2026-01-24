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

/**
 * Determine jewellery type for AR positioning
 * Checks both category name AND product title for keywords
 */
function detectJewelleryType($category, $title) {
    // Combine category and title for searching
    $search_text = strtolower($category . ' ' . $title);
    
    // Order matters - check more specific terms first
    // Rings (but not "earrings")
    if (preg_match('/\bring\b|\brings\b/i', $search_text) && strpos($search_text, 'earring') === false) {
        return 'ring';
    }
    
    // Necklaces, chains, pendants
    if (strpos($search_text, 'necklace') !== false || 
        strpos($search_text, 'chain') !== false || 
        strpos($search_text, 'pendant') !== false ||
        strpos($search_text, 'choker') !== false) {
        return 'necklace';
    }
    
    // Bracelets, bangles
    if (strpos($search_text, 'bracelet') !== false || 
        strpos($search_text, 'bangle') !== false ||
        strpos($search_text, 'anklet') !== false) {
        return 'bracelet';
    }
    
    // Earrings (check last since "ring" is a substring)
    if (strpos($search_text, 'earring') !== false || 
        strpos($search_text, 'ear ring') !== false ||
        strpos($search_text, 'stud') !== false ||
        strpos($search_text, 'hoop') !== false ||
        strpos($search_text, 'drop earring') !== false) {
        return 'earring';
    }
    
    // Default to earring if no match (most common jewellery type)
    return 'earring';
}

$jewellery_type = detectJewelleryType($product_category, $product_title);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Try-On - <?php echo htmlspecialchars($product_title); ?> | Golden Aura</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #FFFFFF 0%, #F9F5EC 100%);
            min-height: 100vh;
            color: #2B2B2B;
        }
        
        .tryon-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .tryon-header {
            text-align: center;
            padding: 20px 0 30px;
        }
        
        .tryon-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin-bottom: 10px;
            color: #2B2B2B;
        }
        
        .tryon-header h1 span {
            color: #D4AF37;
        }
        
        .tryon-header p {
            color: #666666;
            font-size: 1rem;
        }
        
        .tryon-header .tip-badge {
            display: inline-block;
            background: #F9F5EC;
            border: 1px solid #D4AF37;
            border-radius: 20px;
            padding: 8px 20px;
            font-size: 0.85rem;
            color: #7A5C3E;
            margin-top: 15px;
        }
        
        .ar-workspace {
            display: flex;
            gap: 30px;
            margin-top: 20px;
        }
        
        .video-container {
            flex: 1;
            position: relative;
            background: #FFFFFF;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 4/3;
            max-height: 65vh;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #F9F5EC;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .canvas-wrapper {
            position: relative;
            display: inline-block;
            max-width: 100%;
            max-height: 100%;
        }
        
        #photoCanvas {
            display: none;
            max-width: 100%;
            max-height: 100%;
            background: #1a1a1a;
        }
        
        #overlayCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        .upload-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 30px;
            border: 2px dashed #D4AF37;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #FFFFFF;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 10;
        }
        
        .upload-area:hover {
            background: #F9F5EC;
            border-color: #7A5C3E;
        }
        
        .upload-area .upload-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #D4AF37 0%, #7A5C3E 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .upload-area svg {
            width: 35px;
            height: 35px;
            color: white;
        }
        
        .upload-area h3 {
            font-family: 'Playfair Display', serif;
            color: #2B2B2B;
            font-size: 1.3rem;
            margin-bottom: 8px;
        }
        
        .upload-area p {
            color: #666666;
            margin-bottom: 5px;
            font-size: 0.95rem;
        }
        
        .upload-area .formats {
            color: #D4AF37;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .upload-area .tip-text {
            margin-top: 20px;
            padding: 12px 20px;
            background: #F9F5EC;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #7A5C3E;
        }
        
        .controls-panel {
            width: 320px;
            background: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 25px;
            border: 1px solid #F9F5EC;
        }
        
        .product-preview {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #F9F5EC;
        }
        
        .product-preview img {
            max-width: 140px;
            max-height: 140px;
            border-radius: 12px;
            border: 2px solid #D4AF37;
            padding: 8px;
            background: #FFFFFF;
        }
        
        .product-preview h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            margin-top: 15px;
            color: #2B2B2B;
        }
        
        .product-preview .badge {
            display: inline-block;
            background: #F9F5EC;
            color: #7A5C3E;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-top: 10px;
            font-weight: 500;
            border: 1px solid #D4AF37;
        }
        
        .control-group {
            margin-bottom: 20px;
        }
        
        .control-group label {
            display: block;
            margin-bottom: 8px;
            color: #2B2B2B;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .control-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #D4AF37;
            border-radius: 8px;
            background: #FFFFFF;
            color: #2B2B2B;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            cursor: pointer;
            outline: none;
            transition: all 0.3s ease;
        }
        
        .control-group select:hover {
            border-color: #7A5C3E;
        }
        
        .control-group select:focus {
            border-color: #7A5C3E;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
        }
        
        .control-group input[type="range"] {
            width: 100%;
            height: 6px;
            -webkit-appearance: none;
            background: #F9F5EC;
            border-radius: 3px;
            outline: none;
        }
        
        .control-group input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            background: #D4AF37;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(212, 175, 55, 0.4);
        }
        
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 25px;
        }
        
        .btn-action {
            padding: 14px 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-capture {
            background: #D4AF37;
            color: white;
        }
        
        .btn-capture:hover {
            background: #B8941F;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }
        
        .btn-download {
            background: #7A5C3E;
            color: white;
        }
        
        .btn-download:hover {
            background: #5D4429;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(122, 92, 62, 0.3);
        }
        
        .btn-back {
            background: #F9F5EC;
            border: 1px solid #D4AF37;
            color: #7A5C3E;
        }
        
        .btn-back:hover {
            background: #D4AF37;
            color: white;
        }
        
        .status-message {
            text-align: center;
            padding: 15px;
            margin-top: 20px;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        
        .status-loading {
            background: #F9F5EC;
            color: #7A5C3E;
            border: 1px solid #D4AF37;
        }
        
        .status-success {
            background: #F9F5EC;
            color: #7A5C3E;
            border: 1px solid #D4AF37;
        }
        
        .status-error {
            background: #FDF2F2;
            color: #9B2C2C;
            border: 1px solid #FEB2B2;
        }
        
        .jewellery-type-info {
            background: #F9F5EC;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            color: #666666;
            border-left: 3px solid #D4AF37;
        }
        
        .jewellery-type-info strong {
            color: #7A5C3E;
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
            width: 18px;
            height: 18px;
            border: 2px solid #F9F5EC;
            border-radius: 50%;
            border-top-color: #D4AF37;
            animation: spin 1s linear infinite;
            margin-right: 8px;
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
            <h1>✨ Virtual <span>Try-On</span> Experience</h1>
            <p>See how "<?php echo htmlspecialchars($product_title); ?>" looks on you!</p>
            <div class="tip-badge">📸 Upload a photo to try on this beautiful piece</div>
        </div>
        
        <div class="ar-workspace">
            <!-- Photo Display -->
            <div class="video-container" id="videoContainer">
                <div class="canvas-wrapper" id="canvasWrapper">
                    <canvas id="photoCanvas"></canvas>
                    <canvas id="overlayCanvas"></canvas>
                </div>
                
                <!-- Upload Area (shown by default) -->
                <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                    <div class="upload-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3>Upload Your Photo</h3>
                    <p>Click or drag to upload</p>
                    <span class="formats">Supports JPG, PNG</span>
                    <div class="tip-text">
                        💡 For best results, use a well-lit photo showing your face or hands clearly
                    </div>
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
                    <span id="detectionModeText">
                    <?php 
                    $type_labels = [
                        'earring' => '👂 Ear detection for earrings',
                        'necklace' => '📿 Neck detection for necklaces',
                        'ring' => '💍 Hand detection for rings',
                        'bracelet' => '⌚ Wrist detection for bracelets'
                    ];
                    echo $type_labels[$jewellery_type] ?? '💎 General jewellery';
                    ?>
                    </span>
                </div>
                
                <div class="control-group">
                    <label>Jewellery Type</label>
                    <select id="jewelleryTypeSelect" onchange="changeJewelleryType(this.value)">
                        <option value="earring" <?php echo $jewellery_type === 'earring' ? 'selected' : ''; ?>>👂 Earrings</option>
                        <option value="necklace" <?php echo $jewellery_type === 'necklace' ? 'selected' : ''; ?>>📿 Necklace / Pendant</option>
                        <option value="ring" <?php echo $jewellery_type === 'ring' ? 'selected' : ''; ?>>💍 Ring</option>
                        <option value="bracelet" <?php echo $jewellery_type === 'bracelet' ? 'selected' : ''; ?>>⌚ Bracelet / Bangle</option>
                    </select>
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
            showStatus('Loading AR models... This may take a moment.', 'loading');
            
            try {
                // Only load the model we need initially (faster startup)
                const needsFace = CONFIG.jewelleryType === 'earring' || CONFIG.jewelleryType === 'necklace';
                const needsHands = CONFIG.jewelleryType === 'ring' || CONFIG.jewelleryType === 'bracelet';
                
                console.log('Initializing AR for type:', CONFIG.jewelleryType);
                console.log('Needs face:', needsFace, 'Needs hands:', needsHands);
                
                // Add timeout wrapper
                const timeoutPromise = (promise, ms, name) => {
                    return Promise.race([
                        promise,
                        new Promise((_, reject) => 
                            setTimeout(() => reject(new Error(`${name} loading timeout after ${ms/1000}s`)), ms)
                        )
                    ]);
                };
                
                const loadPromises = [];
                
                if (needsFace) {
                    loadPromises.push(
                        timeoutPromise(initializeFaceMesh(), 30000, 'Face Mesh')
                            .then(() => { console.log('Face Mesh loaded successfully'); return 'face'; })
                            .catch(err => { console.error('Face Mesh error:', err); return null; })
                    );
                }
                
                if (needsHands) {
                    loadPromises.push(
                        timeoutPromise(initializeHands(), 30000, 'Hands')
                            .then(() => { console.log('Hands model loaded successfully'); return 'hands'; })
                            .catch(err => { console.error('Hands error:', err); return null; })
                    );
                }
                
                // Also pre-load the other model in background for type switching
                if (!needsFace) {
                    initializeFaceMesh().catch(err => console.log('Background face mesh load failed:', err));
                }
                if (!needsHands) {
                    initializeHands().catch(err => console.log('Background hands load failed:', err));
                }
                
                const results = await Promise.all(loadPromises);
                const successfulLoads = results.filter(r => r !== null);
                
                console.log('Load results:', results);
                
                if (successfulLoads.length > 0) {
                    isModelLoaded = true;
                    showStatus('Ready! Upload a photo to try on the jewellery.', 'success');
                    
                    // If user already uploaded an image while waiting, process it now
                    if (currentImageData) {
                        await processImage();
                    }
                } else {
                    throw new Error('Required model failed to load');
                }
                
            } catch (error) {
                console.error('AR initialization error:', error);
                showStatus('AR models failed to load. You can still view your photo, but try-on won\'t work. Refresh to retry.', 'error');
                // Still allow viewing photos without AR
                isModelLoaded = false;
            }
        }
        
        // Initialize Face Mesh for earrings/necklaces
        async function initializeFaceMesh() {
            console.log('Starting Face Mesh initialization...');
            faceMesh = new FaceMesh({
                locateFile: (file) => {
                    console.log('Loading face mesh file:', file);
                    return `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`;
                }
            });
            
            faceMesh.setOptions({
                maxNumFaces: 1,
                refineLandmarks: true,
                minDetectionConfidence: 0.5,
                minTrackingConfidence: 0.5
            });
            
            faceMesh.onResults(onFaceMeshResults);
            console.log('Calling faceMesh.initialize()...');
            await faceMesh.initialize();
            console.log('Face Mesh initialization complete');
        }
        
        // Initialize Hands for rings/bracelets
        async function initializeHands() {
            console.log('Starting Hands initialization...');
            hands = new Hands({
                locateFile: (file) => {
                    console.log('Loading hands file:', file);
                    return `https://cdn.jsdelivr.net/npm/@mediapipe/hands/${file}`;
                }
            });
            
            hands.setOptions({
                maxNumHands: 2,
                modelComplexity: 1,
                minDetectionConfidence: 0.5,
                minTrackingConfidence: 0.5
            });
            
            hands.onResults(onHandsResults);
            console.log('Calling hands.initialize()...');
            await hands.initialize();
            console.log('Hands initialization complete');
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
            console.log('drawEarrings called, image complete:', jewelleryImg.complete, 'natural size:', jewelleryImg.naturalWidth, 'x', jewelleryImg.naturalHeight);
            if (!jewelleryImg.complete || jewelleryImg.naturalWidth === 0) {
                console.error('Jewellery image not loaded properly');
                return;
            }
            
            const leftEar = landmarks[234];  // Left ear tragion
            const rightEar = landmarks[454]; // Right ear tragion
            
            console.log('Left ear:', leftEar, 'Right ear:', rightEar);
            console.log('Overlay canvas size:', overlayCanvas.width, 'x', overlayCanvas.height);
            
            // Calculate size based on face width
            const faceWidth = Math.abs(landmarks[234].x - landmarks[454].x) * overlayCanvas.width;
            const earringSize = faceWidth * 0.25 * sizeMultiplier;
            
            console.log('Face width:', faceWidth, 'Earring size:', earringSize);
            
            // Draw left earring
            const leftX = leftEar.x * overlayCanvas.width + positionOffsetX;
            const leftY = leftEar.y * overlayCanvas.height + earringSize * 0.3 + positionOffsetY;
            
            console.log('Drawing left earring at:', leftX, leftY);
            
            overlayCtx.save();
            overlayCtx.translate(leftX, leftY);
            overlayCtx.drawImage(jewelleryImg, -earringSize/2, 0, earringSize, earringSize);
            overlayCtx.restore();
            
            // Draw right earring
            const rightX = rightEar.x * overlayCanvas.width + positionOffsetX;
            const rightY = rightEar.y * overlayCanvas.height + earringSize * 0.3 + positionOffsetY;
            
            console.log('Drawing right earring at:', rightX, rightY);
            
            overlayCtx.save();
            overlayCtx.translate(rightX, rightY);
            overlayCtx.drawImage(jewelleryImg, -earringSize/2, 0, earringSize, earringSize);
            overlayCtx.restore();
            
            console.log('Earrings drawn');
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
            
            console.log('File selected:', file.name, file.type, file.size);
            
            showStatus('Processing image...', 'loading');
            
            const img = new Image();
            
            img.onerror = (err) => {
                console.error('Failed to load image:', err);
                showStatus('Failed to load image. Try another file.', 'error');
            };
            
            img.onload = async () => {
                console.log('Image loaded:', img.width, 'x', img.height);
                
                try {
                    // Set canvas size to image size
                    photoCanvas.width = img.width;
                    photoCanvas.height = img.height;
                    overlayCanvas.width = img.width;
                    overlayCanvas.height = img.height;
                    
                    // Draw image on canvas (NOT mirrored - draw normally)
                    photoCtx.drawImage(img, 0, 0);
                    
                    // Store original image for re-processing
                    currentImageData = img;
                    
                    // Show photo canvas, hide upload area
                    console.log('Showing canvas, hiding upload area');
                    uploadArea.style.display = 'none';
                    photoCanvas.style.display = 'block';
                    
                    console.log('Canvas display:', photoCanvas.style.display);
                    console.log('Upload area display:', uploadArea.style.display);
                    console.log('Canvas dimensions:', photoCanvas.width, 'x', photoCanvas.height);
                    
                    // Process with MediaPipe if model is loaded
                    if (isModelLoaded) {
                        await processImage();
                    } else {
                        showStatus('Photo displayed. AR models still loading...', 'loading');
                    }
                    
                    downloadBtn.style.display = 'flex';
                } catch (err) {
                    console.error('Error processing image:', err);
                    showStatus('Error processing image. Please try again.', 'error');
                }
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
            
            // Draw overlay directly on top
            tempCtx.drawImage(overlayCanvas, 0, 0);
            
            // Download
            const link = document.createElement('a');
            link.download = `GoldenAura_TryOn_${Date.now()}.png`;
            link.href = tempCanvas.toDataURL('image/png');
            link.click();
            
            showStatus('✨ Photo downloaded!', 'success');
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
        
        // Change jewellery type dynamically
        async function changeJewelleryType(newType) {
            const oldType = CONFIG.jewelleryType;
            CONFIG.jewelleryType = newType;
            
            // Update detection mode text
            const modeLabels = {
                'earring': '👂 Ear detection for earrings',
                'necklace': '📿 Neck detection for necklaces',
                'ring': '💍 Hand detection for rings',
                'bracelet': '⌚ Wrist detection for bracelets'
            };
            document.getElementById('detectionModeText').textContent = modeLabels[newType] || '💎 General jewellery';
            
            // Check if we need to switch detection models
            const needsFace = (newType === 'earring' || newType === 'necklace');
            const hadFace = (oldType === 'earring' || oldType === 'necklace');
            
            if (needsFace !== hadFace) {
                // Need to load different model
                showStatus('Switching detection mode...', 'loading');
                isModelLoaded = false;
                
                try {
                    if (needsFace) {
                        if (!faceMesh) {
                            await initializeFaceMesh();
                        }
                    } else {
                        if (!hands) {
                            await initializeHands();
                        }
                    }
                    isModelLoaded = true;
                    showStatus('Detection mode changed! Re-processing...', 'success');
                } catch (error) {
                    console.error('Error switching models:', error);
                    showStatus('Failed to switch detection mode', 'error');
                    CONFIG.jewelleryType = oldType; // Revert
                    document.getElementById('jewelleryTypeSelect').value = oldType;
                    return;
                }
            }
            
            // Clear overlay and reprocess if we have an image
            overlayCtx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);
            if (currentImageData && isModelLoaded) {
                await processImage();
            } else {
                showStatus('Type changed to ' + modeLabels[newType].split(' ')[0] + '. Upload a photo to try it on!', 'success');
            }
        }
        
        // Reset upload area (for uploading new photo)
        function resetUpload() {
            uploadArea.style.display = 'flex';
            photoCanvas.style.display = 'none';
            downloadBtn.style.display = 'none';
            currentImageData = null;
            overlayCtx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);
            photoCtx.clearRect(0, 0, photoCanvas.width, photoCanvas.height);
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
        
<button onclick="startARTryOn(<?= $product['id'] ?>)">
    Try AR
</button>

    </script>
</body>
</html>
