<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageHelper
{
    /**
     * Store image and return the saved image name.
     *
     * @param  \Illuminate\Http\UploadedFile  $image
     * @param  string  $folder
     * @return string
     */
    public static function saveImage(UploadedFile $image, $folder)
    {
        $imageName = self::uniqueFileName($folder, $image);

        Storage::disk('public')->putFileAs($folder, $image, $imageName);

        return $imageName;
    }

    /**
     * Get the URL of image from the file name and folder.
     *
     * @param  string  $imageName
     * @param  string  $folder
     * @return string
     */
    public static function getImageUrl($imageName, $folder)
    {
        return Storage::disk('public')->url($folder . '/' . $imageName);
    }

    /**
     * Delete an image from the file name and folder.
     *
     * @param  string  $imageName
     * @param  string  $folder
     * @return void
     */
    public static function deleteImage($imageName, $folder)
    {
        Storage::disk('public')->delete($folder . '/' . $imageName);
    }

    /**
     * Generate a unique file name for storing.
     *
     * @param  string  $folder
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    private static function uniqueFileName($folder, $file)
    {
        $extension = $file->getClientOriginalExtension();
        $counter = 1;
        $fileName = $file->getClientOriginalName();

        while (Storage::disk('public')->exists($folder . '/' . $fileName)) {
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '(' . $counter . ').' . $extension;
            $counter++;
        }

        return $fileName;
    }
}
