<?php

namespace App\Jobs;

use App\Models\Property\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Procesa fotos de una propiedad: resize/optimización.
 * En producción integrar con Intervention Image o similar.
 */
class ProcessPhotosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Property $property
    ) {}

    public function handle(): void
    {
        foreach ($this->property->photos as $photo) {
            $path = Storage::path($photo->path);
            if (! file_exists($path)) {
                continue;
            }
            // Placeholder: en producción aquí resize/optimize y guardar en disco o S3
            Log::info('ProcessPhotosJob: procesando foto', ['property_id' => $this->property->id, 'photo_id' => $photo->id]);
        }
    }
}
