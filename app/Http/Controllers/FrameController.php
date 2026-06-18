<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FrameController extends Controller
{
    /**
     * Show the public generator interface.
     */
    public function index()
    {
        $basePoster = Setting::getVal('base_poster', 'base_poster.jpg');
        return view('generator', compact('basePoster'));
    }

    /**
     * Generate the event frame and return preview details.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
            'name' => 'required|string|max:50',
        ]);

        try {
            $name = strtoupper($request->input('name'));
            $userPhoto = $request->file('photo');

            // Load Configuration
            $photoX = (int) Setting::getVal('photo_x', 275);
            $photoY = (int) Setting::getVal('photo_y', 435);
            $photoSize = (int) Setting::getVal('photo_size', 270);
            
            $nameX = (int) Setting::getVal('name_x', 410);
            $nameY = (int) Setting::getVal('name_y', 750);
            $fontSize = (int) Setting::getVal('font_size', 28);
            $fontColor = Setting::getVal('font_color', '#ffffff');
            $fontFamily = Setting::getVal('font_family', 'Arial-Bold.ttf');

            // Paths
            $basePosterName = Setting::getVal('base_poster', 'base_poster.jpg');
            $basePosterPath = storage_path('app/public/' . $basePosterName);

            if (!file_exists($basePosterPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Base event poster is missing. Please contact the administrator.',
                ], 422);
            }

            // Init Image Manager
            $manager = new ImageManager(new Driver());

            // 1. Read Base Poster and User Photo
            $baseImage = $manager->decode($basePosterPath);
            $userImage = $manager->decode($userPhoto->getRealPath());

            // 2. Crop User Photo to Circle
            $circlePhoto = $this->cropToCircle($manager, $userImage, $photoSize);

            // 3. Get native GdImage for Base Poster to draw name and overlay photo
            // Using native GD handles alpha blending and text sizing perfectly
            $gdBase = $baseImage->core()->native();
            $gdPhoto = $circlePhoto->core()->native();

            // Enable alpha blending on base image
            imagealphablending($gdBase, true);
            imagesavealpha($gdBase, true);

            // 4. Overlay User Photo on Base Poster
            imagecopyresampled(
                $gdBase,
                $gdPhoto,
                $photoX,
                $photoY,
                0,
                0,
                $photoSize,
                $photoSize,
                $photoSize,
                $photoSize
            );

            // 5. Draw User Name centered at X position
            $fontPath = storage_path('app/fonts/' . $fontFamily);
            if (!file_exists($fontPath)) {
                // Fallback to local Arial-Bold if not found
                $fontPath = storage_path('app/fonts/Arial-Bold.ttf');
            }

            if (file_exists($fontPath)) {
                // Parse Color
                $hex = str_replace('#', '', $fontColor);
                if (strlen($hex) == 3) {
                    $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
                }
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                
                $textColor = imagecolorallocate($gdBase, $r, $g, $b);

                // Calculate bounding box for centering
                $bbox = imagettfbbox($fontSize, 0, $fontPath, $name);
                $textWidth = abs($bbox[4] - $bbox[0]);
                
                // Centered X coordinate
                $startX = $nameX - ($textWidth / 2);
                $startY = $nameY;

                imagettftext($gdBase, $fontSize, 0, $startX, $startY, $textColor, $fontPath, $name);
            }

            // 6. Save final frame as PNG to temporary directory
            $tempDir = storage_path('app/public/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $tempFileName = 'frame_' . uniqid() . '_' . time() . '.png';
            $tempFilePath = $tempDir . '/' . $tempFileName;

            // Output base image as PNG
            imagepng($gdBase, $tempFilePath);
            // GD resources are auto-freed by PHP 8+ GC

            return response()->json([
                'success' => true,
                'preview_url' => asset('storage/temp/' . $tempFileName),
                'download_url' => route('frame.download', ['filename' => $tempFileName]),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating your frame: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle download of the generated frame.
     */
    public function download($filename)
    {
        // Prevent directory traversal attacks
        $filename = basename($filename);
        $filePath = storage_path('app/public/temp/' . $filename);

        if (file_exists($filePath)) {
            // Clean up old temporary files (older than 30 minutes)
            $this->cleanTempFiles();

            return response()->download($filePath, 'eSwabi_Event_Frame.png')->deleteFileAfterSend(false);
        }

        abort(404, 'Generated frame not found or expired.');
    }

    /**
     * Circular cropping logic using native PHP GD
     */
    private function cropToCircle(ImageManager $manager, $image, $size)
    {
        // Resize and cover into square
        $image->cover($size, $size);

        $gdImg = $image->core()->native();

        // Create transparent square canvas
        $newGdImg = imagecreatetruecolor($size, $size);
        imagealphablending($newGdImg, false);
        imagesavealpha($newGdImg, true);
        $transparent = imagecolorallocatealpha($newGdImg, 0, 0, 0, 127);
        imagefill($newGdImg, 0, 0, $transparent);

        $r = $size / 2;
        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                $dx = $x - $r;
                $dy = $y - $r;
                $dist_sq = $dx * $dx + $dy * $dy;
                $r_sq = $r * $r;

                if ($dist_sq <= $r_sq) {
                    $dist = sqrt($dist_sq);
                    $color = imagecolorat($gdImg, $x, $y);

                    // Extract RGBA channels
                    $red = ($color >> 16) & 0xFF;
                    $green = ($color >> 8) & 0xFF;
                    $blue = $color & 0xFF;
                    $orig_alpha = ($color >> 24) & 0x7F;

                    // Apply anti-aliasing at the border (outer 1.5 pixels)
                    if ($dist > $r - 1.5) {
                        $ratio = ($r - $dist) / 1.5;
                        $ratio = max(0, min(1, $ratio));
                        $new_alpha = 127 - ((127 - $orig_alpha) * $ratio);
                        $new_alpha = max(0, min(127, (int) $new_alpha));
                    } else {
                        $new_alpha = $orig_alpha;
                    }

                    $allocatedColor = imagecolorallocatealpha($newGdImg, $red, $green, $blue, $new_alpha);
                    imagesetpixel($newGdImg, $x, $y, $allocatedColor);
                }
            }
        }

        // Return as Intervention Image
        return $manager->decode($newGdImg);
    }

    /**
     * Clean up old generated temporary files.
     */
    private function cleanTempFiles()
    {
        $tempDir = storage_path('app/public/temp');
        if (!file_exists($tempDir)) return;

        $files = glob($tempDir . '/*.png');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                // Delete if file is older than 30 minutes (1800 seconds)
                if ($now - filemtime($file) > 1800) {
                    @unlink($file);
                }
            }
        }
    }
}
