<?php

namespace Database\Seeders;

use App\Models\Support\FaqEntry;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            ['question' => '¿Cómo publico una propiedad?', 'answer' => 'Regístrate, verifica tu correo y activa una suscripción. Luego completa el formulario de publicación con tipo, precio, fotos y dirección.', 'category' => 'publicar', 'sort_order' => 1],
            ['question' => '¿Cuántas fotos puedo subir?', 'answer' => 'Máximo 5 fotos por propiedad. Recomendamos imágenes nítidas y bien iluminadas.', 'category' => 'publicar', 'sort_order' => 2],
            ['question' => '¿Cómo contacto a un arrendador?', 'answer' => 'En la ficha de la propiedad usa el botón "Contactar" para abrir el chat con el ofertante.', 'category' => 'mensajes', 'sort_order' => 1],
        ];

        foreach ($entries as $entry) {
            FaqEntry::firstOrCreate(
                ['question' => $entry['question']],
                array_merge($entry, ['is_published' => true])
            );
        }
    }
}
