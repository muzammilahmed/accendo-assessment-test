<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use \Log;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;

class S3Helper
{
    public static function init()
    {
        return new S3Client([
            'version' => 'latest',
            'region' => config('app.s3_region'),
            'credentials' => [
                'key' => config('app.s3_key'),
                'secret' => config('app.s3_secret'),
            ],
        ]);
    }

    public static function upload($file)
    {
        Log::info("File uploading...");
        try {
            $result = self::init()->putObject([
                'Bucket' => config('app.s3_bucket'),
                'Key' => 'org.csv',
                'SourceFile' => $file->getPathname(),
            ]);
        } catch(\Exception $e) {
            Log::info("File uploading failed. ".$e->getMessage());
            $result = $e->getMessage();
        }

        return $result;
    }

    public static function download()
    { 
        Log::info("File downloading...");
        return Storage::get('org.csv');
    }
}
