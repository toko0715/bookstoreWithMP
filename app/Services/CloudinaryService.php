<?php

namespace App\Services;

use Cloudinary\Cloudinary;

class CloudinaryService
{
    protected ?Cloudinary $client = null;

    /**
     * ¿Hay credenciales suficientes para hablar con Cloudinary?
     */
    public function configured(): bool
    {
        return filled(config('services.cloudinary.url'))
            || (filled(config('services.cloudinary.cloud_name'))
                && filled(config('services.cloudinary.api_key'))
                && filled(config('services.cloudinary.api_secret')));
    }

    protected function client(): Cloudinary
    {
        if ($this->client instanceof Cloudinary) {
            return $this->client;
        }

        if (filled($url = config('services.cloudinary.url'))) {
            return $this->client = new Cloudinary($url);
        }

        return $this->client = new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key' => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
            'url' => ['secure' => true],
        ]);
    }

    /**
     * Sube una imagen (ruta local o URL remota) y devuelve la secure_url.
     */
    public function upload(string $source, ?string $publicId = null): string
    {
        $response = $this->client()->uploadApi()->upload($source, array_filter([
            'folder' => config('services.cloudinary.folder'),
            'public_id' => $publicId,
            'overwrite' => true,
            'resource_type' => 'image',
        ]));

        return (string) $response['secure_url'];
    }
}
