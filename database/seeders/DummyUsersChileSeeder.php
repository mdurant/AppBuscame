<?php

namespace Database\Seeders;

use App\Enums\VerificationStatus;
use App\Models\Profile;
use App\Enums\PropertyStatus;
use App\Models\Property\Property;
use App\Models\Property\PropertyAddress;
use App\Models\Property\PropertyView;
use App\Models\Property\Source;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crea 50 usuarios dummy en Chile con distintas regiones, ciudades y fechas.
 * Algunos tienen propiedades publicadas y vistas (consultas) para el admin.
 */
class DummyUsersChileSeeder extends Seeder
{
    private array $regionsCities = [
        'Metropolitana' => ['Santiago', 'Puente Alto', 'Maipú', 'La Florida', 'Las Condes', 'Providencia', 'Ñuñoa', 'Vitacura'],
        'Valparaíso' => ['Valparaíso', 'Viña del Mar', 'Quilpué', 'Villa Alemana', 'San Antonio', 'Quillota'],
        'Biobío' => ['Concepción', 'Talcahuano', 'Chiguayante', 'Los Ángeles', 'Coronel'],
        'La Araucanía' => ['Temuco', 'Villarrica', 'Pucón', 'Angol', 'Padre Las Casas'],
        'Los Lagos' => ['Puerto Varas', 'Puerto Montt', 'Osorno', 'Frutillar', 'Castro'],
        'Coquimbo' => ['La Serena', 'Coquimbo', 'Ovalle', 'Illapel'],
        'Antofagasta' => ['Antofagasta', 'Calama', 'San Pedro de Atacama', 'Tocopilla'],
        'Maule' => ['Talca', 'Curicó', 'Linares', 'Constitución'],
        'O\'Higgins' => ['Rancagua', 'Rengo', 'San Fernando', 'Machalí'],
        'Tarapacá' => ['Iquique', 'Alto Hospicio', 'Pica'],
        'Los Ríos' => ['Valdivia', 'La Unión', 'Río Bueno', 'Panguipulli'],
        'Arica y Parinacota' => ['Arica', 'Putre'],
        'Atacama' => ['Copiapó', 'Vallenar', 'Caldera'],
        'Aysén' => ['Coyhaique', 'Puerto Aysén', 'Chile Chico'],
        'Magallanes' => ['Punta Arenas', 'Puerto Natales', 'Porvenir'],
    ];

    public function run(): void
    {
        $source = Source::where('slug', 'buscame')->first();
        if (! $source) {
            return;
        }

        $firstNames = ['Juan', 'María', 'Carlos', 'Ana', 'Pedro', 'Carmen', 'Luis', 'Patricia', 'Diego', 'Francisca', 'Jorge', 'Isabel', 'Ricardo', 'Elena', 'Andrés', 'Claudia', 'Francisco', 'Daniela', 'Miguel', 'Carolina', 'José', 'Valentina', 'Fernando', 'Camila', 'Roberto', 'Javiera', 'Pablo', 'Antonella', 'Sergio', 'Martina', 'Rodrigo', 'Sofía', 'Gonzalo', 'Florencia', 'Cristian', 'Constanza', 'Sebastián', 'Amanda', 'Felipe', 'Loreto', 'Nicolás', 'Trinidad', 'Víctor', 'Rocío', 'Héctor', 'Bárbara', 'Raúl', 'Paula', 'Eduardo', 'Andrea'];
        $lastNames = ['González', 'Muñoz', 'Rojas', 'Díaz', 'Pérez', 'Soto', 'Contreras', 'Silva', 'Martínez', 'Sepúlveda', 'Morales', 'Rodríguez', 'López', 'Fuentes', 'Hernández', 'Torres', 'Araya', 'Flores', 'Valenzuela', 'Castillo', 'Ramírez', 'Reyes', 'Gutiérrez', 'Castro', 'Vargas', 'Fernández', 'Vera', 'Molina', 'Sandoval', 'Sánchez', 'Carrasco', 'Figueroa', 'Ruiz', 'Herrera', 'Medina', 'Correa', 'Rivas', 'Miranda', 'Vega', 'Espinoza', 'Rivera', 'Tapia', 'Lagos', 'Moreno', 'Campos', 'Cortés', 'Núñez', 'Ortega', 'Pino', 'Bustamante'];

        for ($i = 0; $i < 50; $i++) {
            $region = array_rand($this->regionsCities);
            $cities = $this->regionsCities[$region];
            $city = $cities[array_rand($cities)];

            $firstName = $firstNames[$i % count($firstNames)];
            $lastName = $lastNames[$i % count($lastNames)];
            $name = "{$firstName} {$lastName}";
            $email = strtolower(\Illuminate\Support\Str::slug($firstName.'.'.$lastName)).'.'.($i + 1).'@ejemplo.cl';

            $createdAt = now()->subDays(rand(5, 400))->subHours(rand(0, 23));

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'email_verified_at' => $createdAt,
                'verification_status' => VerificationStatus::FullyVerified,
                'role' => 'usuario',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            Profile::create([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'region' => $region,
                'city' => $city,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Aproximadamente 60% tienen al menos una propiedad publicada
            if (rand(1, 100) <= 60) {
                $pubCount = rand(1, 3);
                for ($p = 0; $p < $pubCount; $p++) {
                    $pubCreated = $createdAt->copy()->addDays(rand(1, 90));
                    $property = Property::create([
                        'user_id' => $user->id,
                        'source_id' => $source->id,
                        'type' => ['casa', 'departamento', 'cabaña', 'estancia'][rand(0, 3)],
                        'status' => PropertyStatus::Published,
                        'rental_type' => 'diario',
                        'cost_amount' => rand(35000, 250000),
                        'cost_currency' => 'CLP',
                        'completeness_percent' => rand(70, 100),
                        'published_at' => $pubCreated,
                        'created_at' => $pubCreated,
                        'updated_at' => $pubCreated,
                    ]);
                    PropertyAddress::create([
                        'property_id' => $property->id,
                        'address_line' => fake()->streetAddress(),
                        'city' => $city,
                        'region' => $region,
                        'country' => 'CL',
                        'latitude' => -33.4 + (rand(-500, 500) / 100),
                        'longitude' => -70.6 + (rand(-500, 500) / 100),
                    ]);
                    // Consultas (vistas) en el portal
                    $viewsCount = rand(0, 25);
                    for ($v = 0; $v < $viewsCount; $v++) {
                        $viewedAt = $pubCreated->copy()->addDays(rand(0, 120));
                        PropertyView::create([
                            'property_id' => $property->id,
                            'user_id' => rand(0, 3) === 0 ? User::where('role', 'cliente')->inRandomOrder()->first()?->id : null,
                            'viewed_at' => $viewedAt,
                        ]);
                    }
                }
            }
        }
    }
}
