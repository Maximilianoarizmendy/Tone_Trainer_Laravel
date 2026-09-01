<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected ?Cloudinary $cloudinary = null;

    public function __construct()
    {
        $cloudName = config('cloudinary.cloud_name', env('CLOUDINARY_CLOUD_NAME'));
        $apiKey    = config('cloudinary.api_key', env('CLOUDINARY_API_KEY'));
        $apiSecret = config('cloudinary.api_secret', env('CLOUDINARY_API_SECRET'));

        if (!empty($cloudName) && !empty($apiKey) && !empty($apiSecret)) {
            $this->cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => $cloudName,
                    'api_key'    => $apiKey,
                    'api_secret' => $apiSecret,
                ],
                'url' => [
                    'secure' => true,
                ],
            ]);
        }
    }

    /**
     * Subir un archivo o imagen a Cloudinary y retornar la URL pública HTTPS.
     *
     * @param UploadedFile|string $file
     * @param string $folder
     * @return string|null
     */
    public function uploadFile($file, string $folder = 'profile_photos'): ?string
    {
        try {
            if (!$this->cloudinary) {
                Log::warning('CloudinaryService: Faltan credenciales de Cloudinary.');
                return null;
            }

            $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

            $result = $this->cloudinary->uploadApi()->upload($filePath, [
                'folder' => 'tone_trainer/' . ltrim($folder, '/'),
                'resource_type' => 'auto',
            ]);

            return $result['secure_url'] ?? $result['url'] ?? null;
        } catch (\Exception $e) {
            Log::error('Error al subir archivo a Cloudinary: ' . $e->getMessage());
            return null;
        }
    }
}
