<?php

namespace App\Services;

use Cloudinary\Cloudinary;

class CloudinaryService
{
    private Cloudinary $cloudinary;

    public function __construct()
    {
        $url = env('CLOUDINARY_URL');
        if ($url) { $this->cloudinary = new Cloudinary($url); return; }
        $this->cloudinary = new Cloudinary(['cloud' => [
            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
            'api_key' => env('CLOUDINARY_API_KEY'),
            'api_secret' => env('CLOUDINARY_API_SECRET'),
        ]]);
    }

    public function upload(string $path): array
    {
        $result = $this->cloudinary->uploadApi()->upload($path, ['folder' => 'sistem-integrasi/products', 'resource_type' => 'image']);
        return ['url' => $result['secure_url'], 'public_id' => $result['public_id']];
    }

    public function delete(string $publicId): void { $this->cloudinary->uploadApi()->destroy($publicId); }
}
