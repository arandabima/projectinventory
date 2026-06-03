<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }

    public function upload(string $filePath, string $folder = 'inventory'): array
    {
        $result = $this->cloudinary->uploadApi()->upload($filePath, [
            'folder'         => $folder,
            'resource_type'  => 'auto', // otomatis detect image/video/file
        ]);

        return [
            'url'       => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    public function delete(string $publicId, string $resourceType = 'image'): void
    {
        $this->cloudinary->uploadApi()->destroy($publicId, [
            'resource_type' => $resourceType,
        ]);
    }
}