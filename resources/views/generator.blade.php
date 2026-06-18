@php
    $photoX = (int) \App\Models\Setting::getVal('photo_x', 275);
    $photoY = (int) \App\Models\Setting::getVal('photo_y', 435);
    $photoSize = (int) \App\Models\Setting::getVal('photo_size', 270);
    $nameX = (int) \App\Models\Setting::getVal('name_x', 410);
    $nameY = (int) \App\Models\Setting::getVal('name_y', 750);
    $fontSize = (int) \App\Models\Setting::getVal('font_size', 28);
    $fontColor = \App\Models\Setting::getVal('font_color', '#ffffff');
    $basePoster = \App\Models\Setting::getVal('base_poster', 'base_poster.jpg');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eSwabi Event Frame Generator</title>
    <meta name="description" content="Generate your personalized eSwabi Conference frame. Upload your profile picture, enter your name, and instantly download a high-quality frame.">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #0b0c10;
            --card-bg: rgba(255, 255, 255, 0.03);
            --card-border: rgba(255, 255, 255, 0.08);
            --primary-gradient: linear-gradient(135deg, #e52d27 0%, #b31217 100%);
            --accent-orange: #ff9900;
            --text-muted: #8a90a6;
            --theme-red: #ff3344;
        }

        body {
            background: radial-gradient(circle at top right, #1a0f1a, #0b0c10 60%);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: 1px;
            background: linear-gradient(to right, #ffffff, #ff4e50);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.5rem;
        }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-card:hover {
            border-color: rgba(255, 255, 255, 0.15);
        }

        .btn-premium {
            background: var(--primary-gradient);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 12px;
            padding: 12px 24px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(229, 45, 39, 0.4);
        }

        .btn-premium:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(229, 45, 39, 0.6);
            color: white;
        }

        .btn-premium:active {
            transform: translateY(0);
        }

        .btn-outline-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-weight: 500;
            border-radius: 12px;
            padding: 12px 24px;
            transition: all 0.3s ease;
        }

        .btn-outline-glass:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff;
        }

        .form-control-premium {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control-premium:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: #ff3344;
            box-shadow: 0 0 0 3px rgba(255, 51, 68, 0.25);
            color: white;
        }

        /* Drag & Drop Upload Style */
        .upload-area {
            border: 2px dashed rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 30px 20px;
            text-align: center;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.01);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .upload-area:hover, .upload-area.dragover {
            border-color: #ff3344;
            background: rgba(255, 51, 68, 0.03);
        }

        .upload-icon {
            font-size: 2.5rem;
            color: var(--text-muted);
            margin-bottom: 12px;
            transition: color 0.3s ease;
        }

        .upload-area:hover .upload-icon {
            color: #ff3344;
        }

        /* Live Preview Container */
        .poster-preview-wrapper {
            position: relative;
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            aspect-ratio: 819 / 1024;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            background-color: #12131a;
        }

        .base-poster-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Dynamic CSS overlay matching configured coordinates */
        .overlay-photo {
            position: absolute;
            left: calc(({{ $photoX }} / 819) * 100%);
            top: calc(({{ $photoY }} / 1024) * 100%);
            width: calc(({{ $photoSize }} / 819) * 100%);
            height: calc(({{ $photoSize }} / 819) * 100%);
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 10;
        }

        .overlay-name {
            position: absolute;
            left: calc(({{ $nameX }} / 819) * 100%);
            top: calc(({{ $nameY }} / 1024) * 100%);
            transform: translate(-50%, -50%);
            text-align: center;
            font-weight: 800;
            text-transform: uppercase;
            color: {{ $fontColor }};
            white-space: nowrap;
            z-index: 20;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
            font-family: Arial, Helvetica, sans-serif;
            pointer-events: none;
        }

        .final-result-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 30;
            display: none;
            animation: fadeIn 0.5s ease forwards;
        }

        /* Sparkle details */
        .glow-title {
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 50%, #ff4b2b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .admin-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 100;
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }

        .admin-link:hover {
            opacity: 1;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 40;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .spinner-glow {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 51, 68, 0.1);
            border-left-color: #ff3344;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            box-shadow: 0 0 15px rgba(255, 51, 68, 0.3);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="py-4 py-md-5">

    <!-- Admin configuration button -->
    <a href="{{ route('admin.index') }}" class="admin-link btn btn-outline-glass rounded-circle p-3 shadow" title="Admin Panel">
        <i class="fa-solid fa-gear fa-xl"></i>
    </a>

    <div class="container">
        <!-- Logo and Header -->
        <header class="text-center mb-5">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <span class="badge bg-danger px-3 py-2 rounded-pill text-uppercase tracking-wider fs-10" style="background: var(--primary-gradient) !important;">
                    Creator's Con Pakistan
                </span>
            </div>
            <h1 class="glow-title display-4 mb-2">eSwabi Event Frame</h1>
            <p class="text-secondary mx-auto" style="max-width: 600px;">
                Create your customized gifting partner poster instantly. Upload your picture, write your name, and generate high-resolution event frames.
            </p>
        </header>

        <!-- Main Content Grid -->
        <div class="row g-4 align-items-center">
            
            <!-- Left Panel: Form Input -->
            <div class="col-lg-5 col-md-12">
                <div class="glass-card p-4 p-md-5">
                    <h3 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles text-danger"></i> Configure Frame
                    </h3>

                    <form id="frameForm" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Name input -->
                        <div class="mb-4">
                            <label for="nameInput" class="form-label fw-semibold text-secondary">YOUR FULL NAME</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary border-end-0 text-secondary">
                                    <i class="fa-regular fa-user"></i>
                                </span>
                                <input type="text" 
                                       name="name" 
                                       id="nameInput" 
                                       class="form-control form-control-premium border-start-0" 
                                       placeholder="Enter your name" 
                                       maxlength="50" 
                                       required>
                            </div>
                            <div class="form-text text-muted text-end mt-1" id="nameCount">0 / 50 characters</div>
                        </div>

                        <!-- Photo upload -->
                        <div class="mb-5">
                            <label class="form-label fw-semibold text-secondary">YOUR PROFILE PHOTO</label>
                            <input type="file" name="photo" id="photoInput" accept="image/png, image/jpeg, image/jpg" class="d-none" required>
                            
                            <div class="upload-area" id="uploadArea">
                                <div class="upload-content" id="uploadPrompt">
                                    <div class="upload-icon">
                                        <i class="fa-regular fa-image"></i>
                                    </div>
                                    <p class="mb-1 fw-bold">Drag and drop your photo here</p>
                                    <p class="text-secondary mb-0 fs-7">Supports JPEG, JPG, PNG (Max 5MB)</p>
                                </div>
                                <div class="upload-preview d-none text-center" id="uploadPreview">
                                    <div class="position-relative d-inline-block">
                                        <img src="" id="userPhotoPreview" class="rounded-circle border border-3 border-danger shadow" style="width: 100px; height: 100px; object-fit: cover;">
                                        <button type="button" id="removePhotoBtn" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle" style="transform: translate(30%, -30%);">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                    <p class="mb-0 mt-2 text-danger fw-semibold" id="fileNameText"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="d-grid gap-3">
                            <button type="submit" id="generateBtn" class="btn btn-premium btn-lg">
                                <i class="fa-solid fa-circle-check me-2"></i> Generate Frame
                            </button>
                            
                            <a id="downloadBtn" href="" class="btn btn-success btn-lg d-none" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none;">
                                <i class="fa-solid fa-circle-arrow-down me-2"></i> Download Event Frame
                            </a>

                            <button type="button" id="resetBtn" class="btn btn-outline-glass btn-lg d-none">
                                <i class="fa-solid fa-rotate-left me-2"></i> Generate Another
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Panel: Live Preview -->
            <div class="col-lg-7 col-md-12 text-center">
                <div class="mb-3 d-flex align-items-center justify-content-center gap-2">
                    <span class="fs-7 text-secondary">
                        <i class="fa-regular fa-eye text-danger"></i> Live Preview Area
                    </span>
                </div>

                <div class="poster-preview-wrapper" id="previewContainer">
                    
                    <!-- Base Poster -->
                    <img src="{{ asset('storage/' . $basePoster) }}" alt="Base Event Poster" class="base-poster-img" id="basePosterImg">

                    <!-- Realtime User Photo overlay -->
                    <div class="overlay-photo" id="photoOverlay"></div>

                    <!-- Realtime Name text overlay -->
                    <div class="overlay-name" id="nameOverlay"></div>

                    <!-- Final Compiled Result Image (hidden initially) -->
                    <img src="" alt="Final Generated Frame" class="final-result-img" id="resultImg">

                    <!-- Loading State overlay -->
                    <div class="loading-overlay" id="loadingOverlay">
                        <div class="spinner-glow mb-3"></div>
                        <h5 class="fw-bold mb-1">Generating Frame...</h5>
                        <p class="text-secondary fs-7 px-4">Processing image overlays & rendering typography...</p>
                    </div>
                </div>

                <div class="mt-4">
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2">
                        <i class="fa-solid fa-shield-halved text-warning me-1"></i> Original poster graphics are preserved in high quality
                    </span>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Script for AJAX and Real-time overlay preview -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('frameForm');
            const nameInput = document.getElementById('nameInput');
            const nameCount = document.getElementById('nameCount');
            const photoInput = document.getElementById('photoInput');
            const uploadArea = document.getElementById('uploadArea');
            const uploadPrompt = document.getElementById('uploadPrompt');
            const uploadPreview = document.getElementById('uploadPreview');
            const userPhotoPreview = document.getElementById('userPhotoPreview');
            const fileNameText = document.getElementById('fileNameText');
            const removePhotoBtn = document.getElementById('removePhotoBtn');
            
            const generateBtn = document.getElementById('generateBtn');
            const downloadBtn = document.getElementById('downloadBtn');
            const resetBtn = document.getElementById('resetBtn');

            const previewContainer = document.getElementById('previewContainer');
            const photoOverlay = document.getElementById('photoOverlay');
            const nameOverlay = document.getElementById('nameOverlay');
            const resultImg = document.getElementById('resultImg');
            const loadingOverlay = document.getElementById('loadingOverlay');

            // Set default font configurations
            let originalFontSize = {{ $fontSize }};
            
            // Adjust Font Size dynamically based on container size to maintain aspect ratio scaling
            function adjustOverlayFontSize() {
                const containerWidth = previewContainer.clientWidth;
                // Base width of the poster config is 819px
                const scale = containerWidth / 819;
                const scaledSize = Math.max(6, Math.round(originalFontSize * scale));
                nameOverlay.style.fontSize = `${scaledSize}px`;
            }

            // Adjust on window resize and initial load
            window.addEventListener('resize', adjustOverlayFontSize);
            // Wait slightly for layouts
            setTimeout(adjustOverlayFontSize, 200);

            // Handle name input real-time sync
            nameInput.addEventListener('input', (e) => {
                const text = e.target.value.toUpperCase();
                nameOverlay.textContent = text;
                nameCount.textContent = `${text.length} / 50 characters`;
                adjustOverlayFontSize();
            });

            // Drag and Drop Upload logic
            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    uploadArea.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                }, false);
            });

            uploadArea.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length) {
                    photoInput.files = files;
                    handlePhotoSelect(files[0]);
                }
            });

            uploadArea.addEventListener('click', () => {
                if (photoInput.files.length === 0) {
                    photoInput.click();
                }
            });

            photoInput.addEventListener('change', (e) => {
                if (e.target.files.length) {
                    handlePhotoSelect(e.target.files[0]);
                }
            });

            function handlePhotoSelect(file) {
                if (!file.type.match('image.*')) {
                    alert('Please select an image file (JPG, JPEG, PNG).');
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('File size exceeds the 5MB limit.');
                    return;
                }

                fileNameText.textContent = file.name;
                
                // Show in Form Preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    userPhotoPreview.src = e.target.result;
                    uploadPrompt.classList.add('d-none');
                    uploadPreview.classList.remove('d-none');
                    
                    // Show in Overlay Preview
                    photoOverlay.style.backgroundImage = `url('${e.target.result}')`;
                    photoOverlay.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }

            removePhotoBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                photoInput.value = '';
                uploadPrompt.classList.remove('d-none');
                uploadPreview.classList.add('d-none');
                
                photoOverlay.style.backgroundImage = '';
                photoOverlay.style.display = 'none';
            });

            // Form Submit (AJAX generation)
            form.addEventListener('submit', (e) => {
                e.preventDefault();

                if (photoInput.files.length === 0) {
                    alert('Please upload a photo first.');
                    return;
                }

                // Show loading state
                loadingOverlay.style.display = 'flex';
                generateBtn.disabled = true;

                const formData = new FormData(form);

                fetch('{{ route("frame.generate") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    loadingOverlay.style.display = 'none';
                    generateBtn.disabled = false;

                    if (data.success) {
                        // Display compiled image
                        resultImg.src = data.preview_url;
                        resultImg.style.display = 'block';

                        // Show download button
                        downloadBtn.href = data.download_url;
                        downloadBtn.classList.remove('d-none');
                        
                        // Show reset/generate another button
                        resetBtn.classList.remove('d-none');
                        generateBtn.classList.add('d-none');
                        
                        // Disable fields
                        nameInput.disabled = true;
                        uploadArea.style.pointerEvents = 'none';
                    } else {
                        alert(data.message || 'An error occurred during frame generation.');
                    }
                })
                .catch(error => {
                    loadingOverlay.style.display = 'none';
                    generateBtn.disabled = false;
                    console.error('Error:', error);
                    alert('A connection error occurred. Please try again.');
                });
            });

            // Reset form for generating another
            resetBtn.addEventListener('click', () => {
                // Clear inputs
                nameInput.disabled = false;
                nameInput.value = '';
                nameOverlay.textContent = '';
                nameCount.textContent = '0 / 50 characters';
                
                photoInput.value = '';
                uploadArea.style.pointerEvents = 'auto';
                uploadPrompt.classList.remove('d-none');
                uploadPreview.classList.add('d-none');
                
                // Reset overlays
                photoOverlay.style.backgroundImage = '';
                photoOverlay.style.display = 'none';
                
                resultImg.src = '';
                resultImg.style.display = 'none';

                // Reset buttons
                generateBtn.classList.remove('d-none');
                downloadBtn.classList.add('d-none');
                resetBtn.classList.add('d-none');
            });
        });
    </script>
</body>
</html>
