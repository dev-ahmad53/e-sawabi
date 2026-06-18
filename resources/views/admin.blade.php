<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - eSwabi Event Frame Generator</title>
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
            background: radial-gradient(circle at top right, #111424, #0b0c10 60%);
            color: #ffffff;
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
        }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .form-control-premium {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 10px;
            padding: 10px 14px;
            transition: all 0.3s ease;
        }

        .form-control-premium:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: #ff3344;
            box-shadow: 0 0 0 3px rgba(255, 51, 68, 0.25);
            color: white;
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

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(229, 45, 39, 0.6);
            color: white;
        }

        /* Configurator Canvas Setup */
        .config-workspace-container {
            position: relative;
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            aspect-ratio: 819 / 1024;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.6);
            background-color: #12131a;
            user-select: none;
        }

        .base-poster-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            pointer-events: none;
        }

        /* Interactive overlays */
        .workspace-photo-ring {
            position: absolute;
            border: 2px dashed #ff3344;
            background: rgba(255, 51, 68, 0.15);
            border-radius: 50%;
            cursor: move;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 10px rgba(255, 51, 68, 0.4);
            z-index: 10;
        }

        .workspace-photo-ring::before {
            content: 'Photo Area';
            font-size: 11px;
            font-weight: 700;
            color: #ffffff;
            background: rgba(0, 0, 0, 0.6);
            padding: 2px 8px;
            border-radius: 8px;
            pointer-events: none;
            white-space: nowrap;
        }

        /* Resize handle on photo ring */
        .resize-handle {
            position: absolute;
            width: 14px;
            height: 14px;
            background-color: #ffffff;
            border: 2px solid #ff3344;
            border-radius: 50%;
            bottom: 0;
            right: 0;
            cursor: se-resize;
            transform: translate(-15%, -15%);
            z-index: 15;
        }

        .workspace-name-box {
            position: absolute;
            border: 2px dashed #00ccff;
            background: rgba(0, 192, 255, 0.15);
            padding: 6px 12px;
            cursor: move;
            border-radius: 6px;
            color: #ffffff;
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            text-align: center;
            white-space: nowrap;
            z-index: 10;
            box-shadow: 0 0 10px rgba(0, 192, 255, 0.4);
            transform: translate(-50%, -50%);
        }

        .workspace-name-box::before {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            background-color: #00ccff;
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        .back-button {
            transition: all 0.3s ease;
        }

        .back-button:hover {
            color: #ff3344 !important;
            transform: translateX(-3px);
        }
    </style>
</head>
<body class="py-4 py-md-5">

    <div class="container">
        
        <!-- Header -->
        <header class="d-flex flex-wrap align-items-center justify-content-between mb-5 gap-3">
            <div>
                <a href="{{ route('frame.index') }}" class="text-secondary text-decoration-none fw-semibold back-button d-inline-flex align-items-center gap-2 mb-2">
                    <i class="fa-solid fa-arrow-left-long"></i> Back to Public Generator
                </a>
                <h1 class="fw-extrabold mb-0 d-flex align-items-center gap-3">
                    <i class="fa-solid fa-screwdriver-wrench text-danger"></i> Frame Configurator
                </h1>
            </div>
            <div>
                <span class="badge bg-dark border border-secondary-subtle px-3 py-2 text-secondary">
                    Laravel 12 / PHP 8+
                </span>
            </div>
        </header>

        <!-- Status Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 p-3 mb-4 shadow" role="alert" style="background: rgba(25, 135, 84, 0.2); color: #38ef7d; border: 1px solid rgba(25, 135, 84, 0.3) !important;">
                <i class="fa-regular fa-circle-check me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4 p-3 mb-4 shadow" role="alert" style="background: rgba(220, 53, 69, 0.2); color: #ff6b6b; border: 1px solid rgba(220, 53, 69, 0.3) !important;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> <strong>Please correct the errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Layout Grid -->
        <div class="row g-4">
            
            <!-- Left Side: Inputs -->
            <div class="col-lg-6 col-md-12">
                <form action="{{ route('admin.save') }}" method="POST" enctype="multipart/form-data" class="h-100">
                    @csrf
                    
                    <div class="glass-card p-4 p-md-5 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h4 class="fw-bold mb-4 border-bottom border-secondary pb-2">
                                <i class="fa-solid fa-sliders text-danger me-2"></i> Numerical Settings
                            </h4>

                            <!-- File upload for Base Poster -->
                            <div class="mb-4">
                                <label for="posterInput" class="form-label fw-bold text-secondary">UPLOAD BASE POSTER</label>
                                <input type="file" name="base_poster" id="posterInput" class="form-control form-control-premium" accept="image/png, image/jpeg, image/jpg">
                                <div class="form-text text-muted">Leave empty to keep the current base poster image. Recommended resolution: 819 x 1024.</div>
                            </div>

                            <div class="row g-3">
                                <!-- Photo Config Section -->
                                <div class="col-12 mt-4">
                                    <h5 class="fw-semibold text-danger border-bottom border-secondary pb-1 mb-3">
                                        <i class="fa-solid fa-circle-user me-2"></i> User Photo Configuration
                                    </h5>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-secondary">X Coordinate (px)</label>
                                    <input type="number" name="photo_x" id="photo_x" class="form-control form-control-premium" value="{{ $settings['photo_x'] }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-secondary">Y Coordinate (px)</label>
                                    <input type="number" name="photo_y" id="photo_y" class="form-control form-control-premium" value="{{ $settings['photo_y'] }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-secondary">Diameter (px)</label>
                                    <input type="number" name="photo_size" id="photo_size" class="form-control form-control-premium" value="{{ $settings['photo_size'] }}" required>
                                </div>

                                <!-- Name Config Section -->
                                <div class="col-12 mt-4">
                                    <h5 class="fw-semibold text-info border-bottom border-secondary pb-1 mb-3">
                                        <i class="fa-solid fa-font me-2"></i> Name Tag Configuration
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary">Horizontal Center X (px)</label>
                                    <input type="number" name="name_x" id="name_x" class="form-control form-control-premium" value="{{ $settings['name_x'] }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary">Vertical Baseline Y (px)</label>
                                    <input type="number" name="name_y" id="name_y" class="form-control form-control-premium" value="{{ $settings['name_y'] }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary">Font Size (pt)</label>
                                    <input type="number" name="font_size" id="font_size" class="form-control form-control-premium" value="{{ $settings['font_size'] }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary">Font Color (Hex)</label>
                                    <div class="input-group">
                                        <input type="color" id="font_color_picker" class="form-control form-control-premium p-1" style="max-width: 50px; height: 45px;" value="{{ $settings['font_color'] }}">
                                        <input type="text" name="font_color" id="font_color" class="form-control form-control-premium border-start-0" value="{{ $settings['font_color'] }}" placeholder="#ffffff" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-3">
                            <button type="submit" class="btn btn-premium w-100 btn-lg shadow">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Configurations
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Right Side: Interactive Workspace -->
            <div class="col-lg-6 col-md-12">
                <div class="glass-card p-4 p-md-5 text-center">
                    <h4 class="fw-bold mb-2 text-start">
                        <i class="fa-solid fa-crosshairs text-danger me-2"></i> Visual Drag & Place
                    </h4>
                    <p class="text-secondary text-start fs-7 mb-4">
                        Drag the elements directly on the poster to set coordinates. Drag the white circle's bottom-right corner to resize. Numerical fields will update in real-time.
                    </p>

                    <div class="config-workspace-container" id="workspaceContainer">
                        
                        <!-- Base Poster image -->
                        <img src="{{ asset('storage/' . $settings['base_poster']) }}" alt="Base Event Poster" class="base-poster-img" id="workspacePoster">

                        <!-- Photo Area Overlay (Circle) -->
                        <div class="workspace-photo-ring" id="dragPhoto">
                            <div class="resize-handle" id="resizePhotoHandle"></div>
                        </div>

                        <!-- Name Box Overlay -->
                        <div class="workspace-name-box" id="dragName">
                            NAME TAG
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Visual drag configurator logic -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const workspace = document.getElementById('workspaceContainer');
            const poster = document.getElementById('workspacePoster');
            
            // Overlays
            const dragPhoto = document.getElementById('dragPhoto');
            const dragName = document.getElementById('dragName');
            const resizeHandle = document.getElementById('resizePhotoHandle');

            // Inputs
            const inputPhotoX = document.getElementById('photo_x');
            const inputPhotoY = document.getElementById('photo_y');
            const inputPhotoSize = document.getElementById('photo_size');
            const inputNameX = document.getElementById('name_x');
            const inputNameY = document.getElementById('name_y');
            const inputFontSize = document.getElementById('font_size');
            const inputFontColor = document.getElementById('font_color');
            const colorPicker = document.getElementById('font_color_picker');

            // Constants
            const ORIGINAL_WIDTH = 819;
            const ORIGINAL_HEIGHT = 1024;

            // Sync Color Picker with hex input
            colorPicker.addEventListener('input', (e) => {
                inputFontColor.value = e.target.value;
            });
            inputFontColor.addEventListener('input', (e) => {
                if(/^#[a-fA-F0-9]{6}$/.test(e.target.value)) {
                    colorPicker.value = e.target.value;
                }
            });

            // Calculate scaling factor between display workspace and original image pixels
            function getScale() {
                const rect = workspace.getBoundingClientRect();
                return ORIGINAL_WIDTH / rect.width;
            }

            // Sync Overlay positions from numerical Input fields
            function syncOverlaysFromInputs() {
                const rect = workspace.getBoundingClientRect();
                if (rect.width === 0) return; // Wait for layout

                const scale = rect.width / ORIGINAL_WIDTH;

                // Photo Overlay
                const px = parseFloat(inputPhotoX.value) * scale;
                const py = parseFloat(inputPhotoY.value) * scale;
                const pSize = parseFloat(inputPhotoSize.value) * scale;

                dragPhoto.style.left = `${px}px`;
                dragPhoto.style.top = `${py}px`;
                dragPhoto.style.width = `${pSize}px`;
                dragPhoto.style.height = `${pSize}px`;

                // Name Overlay
                const nx = parseFloat(inputNameX.value) * scale;
                const ny = parseFloat(inputNameY.value) * scale;

                dragName.style.left = `${nx}px`;
                dragName.style.top = `${ny}px`;
            }

            // Update inputs from Overlay state
            function updateInputsFromOverlays() {
                const scale = getScale();

                // Photo overlay values
                const px = Math.round(dragPhoto.offsetLeft * scale);
                const py = Math.round(dragPhoto.offsetTop * scale);
                const pSize = Math.round(dragPhoto.offsetWidth * scale);

                inputPhotoX.value = px;
                inputPhotoY.value = py;
                inputPhotoSize.value = pSize;

                // Name overlay values
                const nx = Math.round(dragName.offsetLeft * scale);
                const ny = Math.round(dragName.offsetTop * scale);

                inputNameX.value = nx;
                inputNameY.value = ny;
            }

            // Drag behavior implementation
            function makeDraggable(element, onDragCallback) {
                let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

                element.addEventListener('mousedown', dragMouseDown);

                function dragMouseDown(e) {
                    // Ignore drag events when clicking on the resize handle
                    if (e.target.id === 'resizePhotoHandle') return;

                    e.preventDefault();
                    pos3 = e.clientX;
                    pos4 = e.clientY;
                    document.addEventListener('mouseup', closeDragElement);
                    document.addEventListener('mousemove', elementDrag);
                }

                function elementDrag(e) {
                    e.preventDefault();
                    pos1 = pos3 - e.clientX;
                    pos2 = pos4 - e.clientY;
                    pos3 = e.clientX;
                    pos4 = e.clientY;

                    // New positions in displaying px
                    let newTop = element.offsetTop - pos2;
                    let newLeft = element.offsetLeft - pos1;

                    // Bound checks
                    const containerWidth = workspace.clientWidth;
                    const containerHeight = workspace.clientHeight;

                    if (element.id === 'dragPhoto') {
                        newLeft = Math.max(0, Math.min(newLeft, containerWidth - element.offsetWidth));
                        newTop = Math.max(0, Math.min(newTop, containerHeight - element.offsetHeight));
                    } else if (element.id === 'dragName') {
                        // Center is anchored, bounding checks can be looser
                        newLeft = Math.max(0, Math.min(newLeft, containerWidth));
                        newTop = Math.max(0, Math.min(newTop, containerHeight));
                    }

                    element.style.top = `${newTop}px`;
                    element.style.left = `${newLeft}px`;

                    onDragCallback();
                }

                function closeDragElement() {
                    document.removeEventListener('mouseup', closeDragElement);
                    document.removeEventListener('mousemove', elementDrag);
                }
            }

            // Resize behavior for the Photo Overlay
            function makeResizable(element, handle, onResizeCallback) {
                let startWidth = 0, startHeight = 0, startX = 0, startY = 0;

                handle.addEventListener('mousedown', initResize);

                function initResize(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    startWidth = element.offsetWidth;
                    startHeight = element.offsetHeight;
                    startX = e.clientX;
                    startY = e.clientY;

                    document.addEventListener('mouseup', stopResize);
                    document.addEventListener('mousemove', startResizing);
                }

                function startResizing(e) {
                    e.preventDefault();
                    
                    const dx = e.clientX - startX;
                    const dy = e.clientY - startY;

                    // Ensure proportional scaling for a perfect square photo area
                    const delta = Math.max(dx, dy);
                    const newSize = Math.max(30, startWidth + delta);

                    // Bounding validation
                    const containerWidth = workspace.clientWidth;
                    const containerHeight = workspace.clientHeight;
                    
                    if (element.offsetLeft + newSize <= containerWidth && element.offsetTop + newSize <= containerHeight) {
                        element.style.width = `${newSize}px`;
                        element.style.height = `${newSize}px`;
                        onResizeCallback();
                    }
                }

                function stopResize() {
                    document.removeEventListener('mouseup', stopResize);
                    document.removeEventListener('mousemove', startResizing);
                }
            }

            // Setup listeners
            makeDraggable(dragPhoto, updateInputsFromOverlays);
            makeDraggable(dragName, updateInputsFromOverlays);
            makeResizable(dragPhoto, resizeHandle, updateInputsFromOverlays);

            // Sync values from inputs when manual values are entered
            [inputPhotoX, inputPhotoY, inputPhotoSize, inputNameX, inputNameY].forEach(input => {
                input.addEventListener('input', syncOverlaysFromInputs);
            });

            // Initial load delay to allow image rendering and dimensions loading
            poster.onload = () => {
                syncOverlaysFromInputs();
            };
            
            // Double check if already loaded
            if (poster.complete) {
                setTimeout(syncOverlaysFromInputs, 500);
            }
            
            window.addEventListener('resize', syncOverlaysFromInputs);
        });
    </script>
</body>
</html>
