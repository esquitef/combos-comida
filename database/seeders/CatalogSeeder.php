<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Ingredient;
use App\Models\Style;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $this->cargarIngredientes();
        $this->cargarEstilos();
        $this->cargarPlatos();
    }

    private function cargarIngredientes(): void
    {
        // categoría => [[nombre, unidad, es_perecedero], ...]
        $catalogo = [
            'Verduras' => [
                ['Tomate', 'pieza', true], ['Cebolla', 'pieza', false], ['Ajo', 'diente', false],
                ['Papa', 'pieza', false], ['Zanahoria', 'pieza', true], ['Lechuga', 'pieza', true],
                ['Chile', 'pieza', true], ['Pimiento', 'pieza', true], ['Aguacate', 'pieza', true],
                ['Champiñones', 'g', true], ['Pepino', 'pieza', true], ['Berenjena', 'pieza', true],
                ['Jengibre', 'g', false], ['Col', 'pieza', true],
            ],
            'Carnes y pescados' => [
                ['Pollo', 'g', true], ['Carne molida', 'g', true], ['Res', 'g', true],
                ['Cerdo', 'g', true], ['Atún', 'lata', false], ['Jamón', 'g', true],
                ['Chorizo', 'g', true], ['Camarón', 'g', true],
            ],
            'Huevos y lácteos' => [
                ['Huevo', 'pieza', true], ['Leche', 'ml', true], ['Queso', 'g', true],
                ['Crema', 'ml', true], ['Mantequilla', 'g', true], ['Yogur', 'g', true],
            ],
            'Pastas y cereales' => [
                ['Pasta', 'g', false], ['Arroz', 'g', false], ['Tortilla', 'pieza', true],
                ['Pan', 'pieza', true], ['Avena', 'g', false], ['Harina', 'g', false],
            ],
            'Legumbres' => [
                ['Frijol', 'g', false], ['Lenteja', 'g', false], ['Garbanzo', 'g', false],
            ],
            'Frutas' => [
                ['Plátano', 'pieza', true], ['Limón', 'pieza', true], ['Manzana', 'pieza', true],
                ['Fresa', 'g', true], ['Naranja', 'pieza', true],
            ],
            'Condimentos y básicos' => [
                ['Salsa de soya', 'ml', false], ['Azúcar', 'g', false], ['Canela', 'g', false],
                ['Café', 'g', false], ['Té', 'bolsa', false], ['Aceituna', 'g', false],
                ['Pimentón', 'g', false], ['Alga nori', 'hoja', false],
                ['Vinagre de arroz', 'ml', false], ['Miel', 'ml', false],
            ],
        ];

        foreach ($catalogo as $nombreCategoria => $ingredientes) {
            $categoria = Category::firstOrCreate(['nombre' => $nombreCategoria]);

            foreach ($ingredientes as [$nombre, $unidad, $perecedero]) {
                Ingredient::updateOrCreate(
                    ['nombre' => $nombre],
                    [
                        'category_id'   => $categoria->id,
                        'unidad_medida' => $unidad,
                        'es_perecedero' => $perecedero,
                    ]
                );
            }
        }
    }

    private function cargarEstilos(): void
    {
        $estilos = [
            ['Mexicano', 'México'],
            ['Italiano', 'Italia'],
            ['Japonés', 'Japón'],
            ['Chino', 'China'],
            ['Griego', 'Grecia'],
            ['Español', 'España'],
            ['Peruano', 'Perú'],
            ['Argentino', 'Argentina'],
        ];

        foreach ($estilos as [$nombre, $pais]) {
            Style::updateOrCreate(['nombre' => $nombre], ['pais_origen' => $pais]);
        }
    }

    private function cargarPlatos(): void
    {
        $estilos = Style::pluck('id', 'nombre');
        $ingredientes = Ingredient::pluck('id', 'nombre');

        // [nombre, tipo, minutos, costo (MXN aprox.), estilo, objetivo, esenciales, opcionales]
        $platos = [
            // ---------- MEXICANO ----------
            ['Tacos de pollo', 'principal', 25, 70, 'Mexicano', 'rapida', ['Pollo', 'Tortilla', 'Cebolla'], ['Aguacate', 'Limón', 'Chile']],
            ['Huevos a la mexicana', 'principal', 15, 35, 'Mexicano', 'rapida', ['Huevo', 'Tomate', 'Cebolla'], ['Chile']],
            ['Chilaquiles', 'principal', 20, 45, 'Mexicano', 'economica', ['Tortilla', 'Tomate', 'Chile'], ['Queso', 'Crema', 'Huevo', 'Cebolla']],
            ['Quesadillas', 'principal', 10, 40, 'Mexicano', 'rapida', ['Tortilla', 'Queso'], ['Champiñones', 'Pollo']],
            ['Arroz rojo', 'acompanamiento', 30, 20, 'Mexicano', 'economica', ['Arroz', 'Tomate', 'Cebolla'], ['Ajo', 'Zanahoria']],
            ['Frijoles de la olla', 'acompanamiento', 45, 15, 'Mexicano', 'economica', ['Frijol', 'Cebolla'], ['Ajo', 'Chile']],
            ['Guacamole', 'acompanamiento', 10, 40, 'Mexicano', 'ligera', ['Aguacate', 'Limón'], ['Tomate', 'Cebolla', 'Chile']],
            ['Agua de limón', 'bebida', 5, 10, 'Mexicano', 'rapida', ['Limón', 'Azúcar'], []],
            ['Arroz con leche', 'postre', 40, 30, 'Mexicano', 'economica', ['Arroz', 'Leche', 'Azúcar'], ['Canela']],

            // ---------- ITALIANO ----------
            ['Pasta con tomate', 'principal', 20, 40, 'Italiano', 'economica', ['Pasta', 'Tomate', 'Ajo'], ['Cebolla', 'Queso']],
            ['Pasta carbonara casera', 'principal', 20, 60, 'Italiano', 'rapida', ['Pasta', 'Huevo', 'Jamón'], ['Queso', 'Ajo']],
            ['Pasta con champiñones', 'principal', 25, 65, 'Italiano', 'rapida', ['Pasta', 'Champiñones', 'Crema'], ['Ajo', 'Queso', 'Cebolla']],
            ['Pollo a la cacciatora', 'principal', 40, 80, 'Italiano', 'alta_proteina', ['Pollo', 'Tomate', 'Cebolla'], ['Pimiento', 'Ajo']],
            ['Ensalada caprese', 'acompanamiento', 10, 45, 'Italiano', 'ligera', ['Tomate', 'Queso'], ['Lechuga']],
            ['Pan con ajo', 'acompanamiento', 10, 20, 'Italiano', 'rapida', ['Pan', 'Ajo', 'Mantequilla'], []],
            ['Café latte', 'bebida', 5, 25, 'Italiano', 'rapida', ['Café', 'Leche'], ['Azúcar']],
            ['Fresas con crema', 'postre', 10, 40, 'Italiano', 'rapida', ['Fresa', 'Crema'], ['Azúcar']],

            // ---------- JAPONÉS ----------
            ['Onigiri de atún', 'principal', 15, 35, 'Japonés', 'rapida', ['Arroz', 'Atún', 'Alga nori'], ['Salsa de soya']],
            ['Oyakodon', 'principal', 25, 60, 'Japonés', 'alta_proteina', ['Pollo', 'Huevo', 'Arroz', 'Cebolla'], ['Salsa de soya', 'Jengibre']],
            ['Pollo teriyaki', 'principal', 30, 75, 'Japonés', 'alta_proteina', ['Pollo', 'Salsa de soya', 'Azúcar'], ['Jengibre', 'Ajo']],
            ['Yakisoba', 'principal', 20, 55, 'Japonés', 'rapida', ['Pasta', 'Col', 'Salsa de soya'], ['Zanahoria', 'Cebolla', 'Cerdo']],
            ['Tamagoyaki', 'acompanamiento', 15, 25, 'Japonés', 'rapida', ['Huevo', 'Azúcar'], ['Salsa de soya']],
            ['Ensalada de pepino', 'acompanamiento', 10, 20, 'Japonés', 'ligera', ['Pepino', 'Vinagre de arroz'], ['Azúcar', 'Salsa de soya']],
            ['Té verde', 'bebida', 5, 8, 'Japonés', 'ligera', ['Té'], []],

            // ---------- CHINO ----------
            ['Arroz frito', 'principal', 20, 45, 'Chino', 'rapida', ['Arroz', 'Huevo', 'Cebolla'], ['Zanahoria', 'Salsa de soya', 'Pollo', 'Ajo']],
            ['Pollo agridulce', 'principal', 30, 75, 'Chino', 'alta_proteina', ['Pollo', 'Pimiento', 'Azúcar'], ['Cebolla', 'Vinagre de arroz', 'Jengibre']],
            ['Chow mein', 'principal', 25, 60, 'Chino', 'rapida', ['Pasta', 'Col', 'Zanahoria'], ['Salsa de soya', 'Ajo', 'Pollo']],
            ['Res salteada con pimiento', 'principal', 25, 85, 'Chino', 'alta_proteina', ['Res', 'Pimiento', 'Cebolla'], ['Salsa de soya', 'Jengibre', 'Ajo']],
            ['Sopa de huevo', 'acompanamiento', 15, 25, 'Chino', 'ligera', ['Huevo', 'Cebolla'], ['Salsa de soya', 'Zanahoria']],
            ['Verduras salteadas', 'acompanamiento', 15, 30, 'Chino', 'ligera', ['Col', 'Zanahoria', 'Pimiento'], ['Salsa de soya', 'Ajo', 'Jengibre']],
            ['Té de jengibre', 'bebida', 10, 10, 'Chino', 'ligera', ['Jengibre'], ['Miel', 'Limón']],
            ['Plátano caramelizado', 'postre', 10, 25, 'Chino', 'rapida', ['Plátano', 'Azúcar'], ['Canela']],

            // ---------- GRIEGO ----------
            ['Pollo al limón con papas', 'principal', 45, 85, 'Griego', 'alta_proteina', ['Pollo', 'Limón', 'Papa'], ['Ajo', 'Cebolla']],
            ['Moussaka sencilla', 'principal', 60, 90, 'Griego', 'alta_proteina', ['Berenjena', 'Carne molida', 'Tomate'], ['Papa', 'Cebolla', 'Queso', 'Leche']],
            ['Souvlaki de cerdo', 'principal', 30, 80, 'Griego', 'alta_proteina', ['Cerdo', 'Limón', 'Ajo'], ['Cebolla', 'Tomate', 'Pan']],
            ['Ensalada griega', 'acompanamiento', 10, 40, 'Griego', 'ligera', ['Tomate', 'Pepino', 'Queso'], ['Cebolla', 'Aceituna']],
            ['Tzatziki', 'acompanamiento', 10, 30, 'Griego', 'ligera', ['Yogur', 'Pepino'], ['Ajo', 'Limón']],
            ['Papas al limón', 'acompanamiento', 40, 25, 'Griego', 'economica', ['Papa', 'Limón'], ['Ajo']],
            ['Frappé', 'bebida', 5, 15, 'Griego', 'rapida', ['Café', 'Azúcar'], ['Leche']],
            ['Yogur con miel', 'postre', 5, 30, 'Griego', 'ligera', ['Yogur', 'Miel'], ['Fresa', 'Manzana', 'Canela']],

            // ---------- ESPAÑOL ----------
            ['Tortilla española', 'principal', 30, 40, 'Español', 'economica', ['Huevo', 'Papa'], ['Cebolla']],
            ['Paella sencilla', 'principal', 50, 95, 'Español', 'alta_proteina', ['Arroz', 'Pollo', 'Pimiento'], ['Camarón', 'Tomate', 'Ajo', 'Pimentón']],
            ['Lentejas con chorizo', 'principal', 45, 60, 'Español', 'economica', ['Lenteja', 'Chorizo', 'Zanahoria'], ['Papa', 'Cebolla', 'Pimentón', 'Ajo']],
            ['Garbanzos con chorizo', 'principal', 40, 55, 'Español', 'economica', ['Garbanzo', 'Chorizo'], ['Cebolla', 'Pimentón', 'Ajo', 'Papa']],
            ['Albóndigas en salsa', 'principal', 40, 70, 'Español', 'alta_proteina', ['Carne molida', 'Tomate', 'Cebolla'], ['Ajo', 'Huevo', 'Pan']],
            ['Gambas al ajillo', 'principal', 15, 100, 'Español', 'rapida', ['Camarón', 'Ajo'], ['Pimentón', 'Limón', 'Pan']],
            ['Patatas bravas', 'acompanamiento', 30, 25, 'Español', 'economica', ['Papa', 'Tomate'], ['Pimentón', 'Ajo', 'Chile']],
            ['Gazpacho', 'acompanamiento', 15, 35, 'Español', 'ligera', ['Tomate', 'Pepino', 'Pimiento'], ['Ajo', 'Cebolla', 'Pan']],
            ['Pan con tomate', 'acompanamiento', 5, 15, 'Español', 'rapida', ['Pan', 'Tomate'], ['Ajo']],
            ['Zumo de naranja', 'bebida', 5, 20, 'Español', 'ligera', ['Naranja'], []],
            ['Natillas', 'postre', 30, 30, 'Español', 'economica', ['Leche', 'Huevo', 'Azúcar'], ['Canela']],

            // ---------- PERUANO ----------
            ['Lomo saltado', 'principal', 25, 90, 'Peruano', 'alta_proteina', ['Res', 'Cebolla', 'Tomate'], ['Papa', 'Salsa de soya', 'Ajo', 'Chile', 'Arroz']],
            ['Arroz con pollo', 'principal', 45, 75, 'Peruano', 'alta_proteina', ['Arroz', 'Pollo', 'Cebolla'], ['Zanahoria', 'Ajo', 'Chile', 'Pimiento']],
            ['Ají de gallina', 'principal', 40, 70, 'Peruano', 'alta_proteina', ['Pollo', 'Pan', 'Leche'], ['Cebolla', 'Ajo', 'Chile', 'Queso', 'Huevo']],
            ['Causa limeña', 'principal', 30, 55, 'Peruano', 'ligera', ['Papa', 'Atún', 'Aguacate'], ['Limón', 'Huevo', 'Chile']],
            ['Papa a la huancaína', 'acompanamiento', 25, 40, 'Peruano', 'economica', ['Papa', 'Queso', 'Chile'], ['Huevo', 'Leche']],
            ['Salsa criolla', 'acompanamiento', 10, 15, 'Peruano', 'ligera', ['Cebolla', 'Limón'], ['Chile', 'Tomate']],
            ['Limonada', 'bebida', 5, 12, 'Peruano', 'rapida', ['Limón', 'Azúcar'], []],
            ['Avena peruana', 'bebida', 10, 12, 'Peruano', 'economica', ['Avena', 'Leche'], ['Canela', 'Azúcar']],

            // ---------- ARGENTINO ----------
            ['Milanesa a la napolitana', 'principal', 35, 100, 'Argentino', 'alta_proteina', ['Res', 'Huevo', 'Pan'], ['Jamón', 'Queso', 'Tomate']],
            ['Empanadas de carne', 'principal', 50, 70, 'Argentino', 'economica', ['Carne molida', 'Harina', 'Cebolla'], ['Huevo', 'Aceituna', 'Pimentón']],
            ['Bife con papas', 'principal', 30, 110, 'Argentino', 'alta_proteina', ['Res', 'Papa'], ['Ajo', 'Limón']],
            ['Ñoquis de papa', 'principal', 45, 45, 'Argentino', 'economica', ['Papa', 'Harina', 'Tomate'], ['Queso', 'Ajo', 'Cebolla']],
            ['Provoleta', 'acompanamiento', 10, 50, 'Argentino', 'rapida', ['Queso'], ['Tomate', 'Pimentón']],
            ['Ensalada mixta', 'acompanamiento', 10, 25, 'Argentino', 'ligera', ['Lechuga', 'Tomate', 'Cebolla'], ['Zanahoria', 'Limón']],
            ['Puré de papa', 'acompanamiento', 25, 25, 'Argentino', 'economica', ['Papa', 'Mantequilla'], ['Leche']],
            ['Licuado de plátano', 'bebida', 5, 25, 'Argentino', 'rapida', ['Plátano', 'Leche'], ['Azúcar', 'Avena']],
            ['Panqueques', 'postre', 20, 30, 'Argentino', 'economica', ['Harina', 'Huevo', 'Leche'], ['Azúcar', 'Plátano', 'Fresa']],
        ];

        foreach ($platos as [$nombre, $tipo, $minutos, $costo, $estilo, $objetivo, $esenciales, $opcionales]) {
            $plato = Dish::updateOrCreate(
                ['nombre' => $nombre],
                [
                    'tipo'                   => $tipo,
                    'tiempo_preparacion_min' => $minutos,
                    'costo_aproximado'       => $costo,
                    'style_id'               => $estilos[$estilo],
                    'objetivo'               => $objetivo,
                ]
            );

            $pivot = [];
            foreach ($esenciales as $ing) {
                $pivot[$ingredientes[$ing]] = ['cantidad' => 1, 'es_opcional' => false];
            }
            foreach ($opcionales as $ing) {
                $pivot[$ingredientes[$ing]] = ['cantidad' => 1, 'es_opcional' => true];
            }

            $plato->ingredients()->sync($pivot);
        }
    }
}