<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait CompressesImages
{
    /**
     * Compress and resize an image.
     *
     * @param UploadedFile $file
     * @param string $path
     * @param int $quality
     * @param int $maxWidth
     * @return string
     */
    public function compressAndStore(UploadedFile $file, string $path, int $quality = 75, int $maxWidth = 1000): string
    {
        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
        $width = imagesx($image);
        $height = imagesy($image);

        // Resize if needed
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = floor($height * ($maxWidth / $width));
            $resized = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG/GIF
            imagealphablending($resized, false);
            imagesavealpha($resized, true);

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        // Generate filename
        $filename = $file->hashName();
        $fullPath = $path . '/' . $filename;

        // Save to temporary stream
        $tempStream = fopen('php://temp', 'r+');

        // Save as JPEG for compression (unless transparency is critical, but for photos JPEG is best)
        // If original is PNG with transparency, we might want to keep it PNG but compress.
        // For simplicity and max compression, we'll convert to JPEG unless it's a logo.
        // But for product images, let's try to respect the extension or default to JPEG.

        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['jpg', 'jpeg'])) {
            imagejpeg($image, $tempStream, $quality); // 0-100
        } elseif ($extension === 'png') {
            // PNG compression is 0-9 (0 is no compression). We map 75 quality to roughly 6.
            imagepng($image, $tempStream, 6);
        } else {
            imagejpeg($image, $tempStream, $quality);
        }

        rewind($tempStream);

        Storage::disk('public')->put($fullPath, $tempStream);

        fclose($tempStream);
        imagedestroy($image);

        return $fullPath;
    }
}
