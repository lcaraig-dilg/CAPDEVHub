<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    /**
     * Generate a simple captcha image with colored text and noise.
     */
    public function image(Request $request)
    {
        // Generate random 5-character code (avoid confusing characters)
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        // Store in session for later validation
        $request->session()->put('captcha_text', $code);

        $width = 150;
        $height = 50;

        $image = imagecreatetruecolor($width, $height);

        // Background color (light)
        $backgroundColor = imagecolorallocate($image, 232, 226, 219); // #E8E2DB
        imagefilledrectangle($image, 0, 0, $width, $height, $backgroundColor);

        // Noise colors
        $lineColor = imagecolorallocate($image, 1, 49, 65);   // #013141
        $dotColor = imagecolorallocate($image, 10, 124, 161); // #0A7CA1

        // Draw random lines for distortion
        for ($i = 0; $i < 5; $i++) {
            imageline(
                $image,
                random_int(0, $width),
                random_int(0, $height),
                random_int(0, $width),
                random_int(0, $height),
                $lineColor
            );
        }

        // Draw random dots
        for ($i = 0; $i < 150; $i++) {
            imagesetpixel(
                $image,
                random_int(0, $width - 1),
                random_int(0, $height - 1),
                $dotColor
            );
        }

        // Text color (use palette highlight)
        $textColor = imagecolorallocate($image, 250, 185, 91); // #FAB95B

        // Position text roughly centered, character by character with small jitter
        $fontSize = 5; // built-in font size (1-5)
        $textLength = strlen($code);
        $x = 10;
        $yBase = 15;

        for ($i = 0; $i < $textLength; $i++) {
            $yOffset = random_int(-3, 3);
            imagestring($image, $fontSize, $x, $yBase + $yOffset, $code[$i], $textColor);
            $x += 25;
        }

        // Output image as PNG
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        return response($imageData, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}

