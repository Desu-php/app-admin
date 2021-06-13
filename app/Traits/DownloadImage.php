<?php


namespace App\Traits;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait DownloadImage
{
    private function findFormat($imageUrl)
    {
        $formats = ['.jpeg', '.jpg', '.webp', '.png'];
        foreach ($formats as $format) {
            if (Str::contains($imageUrl, $format)) {
                return $format;
            }
        }
        return $formats[0];
    }

    private function downloadImage($url)
    {
        $format = $this->findFormat($url);
        try {
            $file = file_get_contents($url);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            Log::error('[IMAGE] - ' . $exception->getMessage());
            return false;
        }

        $dir = 'assets/image/channels/';
        $fileName = $dir . \hash('sha256', $url) . $format;

        $this->fileExists($dir);

        $filePath = public_path($fileName);
        if (file_put_contents($filePath, $file)) {
            return $fileName;
        }
        return false;
    }

    public function fileExists($path){
        if (\File::exists(public_path($path)) == false) {
            \File::makeDirectory(public_path($path), 0777, true, true);
        }
    }
}
