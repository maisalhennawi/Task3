<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
trait FileTrait
{
    public function uploadFile($file, $directory)
    {
        $filename = $this->generateUniqueFileName($file->getClientOriginalExtension());
        $path = $file->store($directory, 'public');
        return $path;
    }

    public function deleteFile($path)
    {
        Storage::disk('public')->delete($path);
    }
    private function generateUniqueFileName($extension)
    {
        return Str::uuid() . '.' . $extension;
    }
}
