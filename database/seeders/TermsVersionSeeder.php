<?php

namespace Database\Seeders;

use App\Models\TermsVersion;
use Illuminate\Database\Seeder;

class TermsVersionSeeder extends Seeder
{
    public function run(): void
    {
        TermsVersion::updateOrCreate(
            ['version' => '1.0'],
            [
                'content' => "TÉRMINOS Y CONDICIONES\n\nVersión 1.0. Al usar esta plataforma aceptas los siguientes términos.\n\n1. Uso del servicio\nEl servicio está destinado a la búsqueda y publicación de propiedades para arriendo.\n\n2. Cuenta y datos\nEres responsable de mantener la confidencialidad de tu cuenta y de las actividades realizadas en ella.\n\n3. Contenido\nNo está permitido publicar contenido ilegal, ofensivo o que infrinja derechos de terceros.\n\n---\n\nPRIVACIDAD (Política de Privacidad)\n\n[id privacidad]\n\nTratamos tus datos personales conforme a la normativa vigente. Los datos se utilizan para gestionar tu cuenta, publicaciones y mensajes dentro de la plataforma.",
                'effective_at' => now(),
            ]
        );
    }
}
