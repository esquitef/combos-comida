<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Ingredient;
use App\Models\Style;
use Illuminate\Database\Seeder;

/**
 * Catálogo de ingredientes y platos.
 *
 * Los platos están adaptados de los libros de cocina mexicana, italiana, japonesa, china (wok),
 * española y argentina; las aguas frescas, atoles y los platos vegetarianos extra son propios.
 * Cada plato se simplifica a sus ingredientes clave (esenciales) y los secundarios (opcionales).
 * Los tiempos son aproximados y los costos son estimaciones en pesos mexicanos.
 */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $this->retirarEstilosAnteriores();
        $this->cargarIngredientes();
        $this->cargarEstilos();
        $this->cargarPlatos();
    }

    /** Estilos que ya no forman parte del catálogo (sus platos se eliminan en cascada). */
    private function retirarEstilosAnteriores(): void
    {
        Style::whereIn('nombre', ['Griego', 'Peruano'])->delete();
    }

    private function cargarIngredientes(): void
    {
        // categoría => [[nombre, unidad, es_perecedero], ...]
        $catalogo = [
            'Verduras' => [
                ['Tomate', 'pieza', true], ['Cebolla', 'pieza', false], ['Cebolleta', 'pieza', true],
                ['Ajo', 'diente', false], ['Papa', 'pieza', false], ['Camote', 'pieza', false],
                ['Zanahoria', 'pieza', true], ['Lechuga', 'pieza', true], ['Pimiento', 'pieza', true],
                ['Aguacate', 'pieza', true], ['Champiñones', 'g', true], ['Pepino', 'pieza', true],
                ['Berenjena', 'pieza', true], ['Jengibre', 'g', false], ['Col', 'pieza', true],
                ['Calabacita', 'pieza', true], ['Calabaza', 'g', true], ['Elote', 'pieza', true],
                ['Espinaca', 'g', true], ['Acelga', 'g', true], ['Brócoli', 'pieza', true],
                ['Coliflor', 'pieza', true], ['Jícama', 'pieza', true], ['Betabel', 'pieza', true],
                ['Tomatillo', 'pieza', true], ['Chícharos', 'g', false],
            ],
            'Chiles, hierbas y especias' => [
                ['Chile', 'pieza', true], ['Chile poblano', 'pieza', true], ['Chile chipotle', 'pieza', false],
                ['Chile guajillo', 'pieza', false], ['Chile ancho', 'pieza', false], ['Cilantro', 'g', true],
                ['Perejil', 'g', true], ['Hierbabuena', 'g', true], ['Albahaca', 'g', true],
                ['Orégano', 'g', false], ['Laurel', 'hoja', false], ['Comino', 'g', false],
                ['Pimentón', 'g', false],
            ],
            'Carnes y aves' => [
                ['Pollo', 'g', true], ['Carne molida', 'g', true], ['Res', 'g', true],
                ['Cerdo', 'g', true], ['Jamón', 'g', true], ['Chorizo', 'g', true],
                ['Tocino', 'g', true],
            ],
            'Pescados y mariscos' => [
                ['Atún', 'lata', false], ['Camarón', 'g', true], ['Pescado blanco', 'g', true],
                ['Bacalao', 'g', false], ['Salmón', 'g', true],
            ],
            'Huevos y lácteos' => [
                ['Huevo', 'pieza', true], ['Leche', 'ml', true], ['Queso', 'g', true],
                ['Crema', 'ml', true], ['Mantequilla', 'g', true], ['Yogur', 'g', true],
                ['Queso crema', 'g', true], ['Leche condensada', 'ml', false],
            ],
            'Pastas, cereales y panes' => [
                ['Pasta', 'g', false], ['Arroz', 'g', false], ['Tortilla', 'pieza', true],
                ['Pan', 'pieza', true], ['Avena', 'g', false], ['Harina', 'g', false],
                ['Maicena', 'g', false], ['Fideos', 'g', false], ['Masa para empanadas', 'pieza', true],
            ],
            'Legumbres y soya' => [
                ['Frijol', 'g', false], ['Lenteja', 'g', false], ['Garbanzo', 'g', false],
                ['Habas', 'g', false], ['Tofu', 'g', true],
            ],
            'Frutas' => [
                ['Plátano', 'pieza', true], ['Limón', 'pieza', true], ['Manzana', 'pieza', true],
                ['Fresa', 'g', true], ['Naranja', 'pieza', true], ['Piña', 'pieza', true],
                ['Mango', 'pieza', true], ['Melón', 'pieza', true], ['Sandía', 'pieza', true],
                ['Guayaba', 'pieza', true], ['Papaya', 'pieza', true], ['Tamarindo', 'g', false],
                ['Coco', 'pieza', true], ['Uva', 'g', true],
            ],
            'Frutos secos, semillas y flores' => [
                ['Nuez', 'g', false], ['Chía', 'g', false], ['Ajonjolí', 'g', false],
                ['Jamaica', 'g', false], ['Lavanda', 'g', false],
            ],
            'Condimentos y básicos' => [
                ['Azúcar', 'g', false], ['Canela', 'g', false], ['Café', 'g', false],
                ['Té', 'bolsa', false], ['Aceituna', 'g', false], ['Miel', 'ml', false],
                ['Salsa de soya', 'ml', false], ['Vinagre', 'ml', false], ['Vainilla', 'ml', false],
                ['Chocolate', 'g', false], ['Dulce de leche', 'g', false], ['Membrillo', 'g', false],
                ['Alga nori', 'hoja', false], ['Miso', 'g', false], ['Vinagre de arroz', 'ml', false],
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

        // Categorías de versiones anteriores del catálogo que quedaron sin ingredientes.
        Category::doesntHave('ingredients')->delete();
    }

    private function cargarEstilos(): void
    {
        $estilos = [
            ['Mexicano', 'México'],
            ['Italiano', 'Italia'],
            ['Japonés', 'Japón'],
            ['Chino', 'China'],
            ['Español', 'España'],
            ['Argentino', 'Argentina'],
            ['Vegetariano', null],
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
            ['Chilaquiles con huevo', 'principal', 20, 45, 'Mexicano', 'economica', ['Tortilla', 'Tomate', 'Huevo'], ['Chile', 'Cebolla', 'Queso', 'Crema']],
            ['Huevos a la mexicana', 'principal', 15, 35, 'Mexicano', 'rapida', ['Huevo', 'Tomate', 'Cebolla'], ['Chile']],
            ['Huevos motuleños', 'principal', 25, 50, 'Mexicano', 'rapida', ['Huevo', 'Tortilla', 'Frijol'], ['Plátano', 'Tomate', 'Queso', 'Chícharos', 'Jamón']],
            ['Enchiladas de queso', 'principal', 35, 60, 'Mexicano', 'economica', ['Tortilla', 'Queso', 'Tomate'], ['Cebolla', 'Ajo', 'Chile ancho', 'Chile chipotle', 'Crema']],
            ['Quesadillas', 'principal', 10, 40, 'Mexicano', 'rapida', ['Tortilla', 'Queso'], ['Champiñones', 'Pollo', 'Chile poblano', 'Jamón']],
            ['Tacos de pollo', 'principal', 25, 70, 'Mexicano', 'rapida', ['Pollo', 'Tortilla', 'Cebolla'], ['Aguacate', 'Limón', 'Chile', 'Cilantro']],
            ['Tacos de carne y verdura', 'principal', 25, 60, 'Mexicano', 'economica', ['Tortilla', 'Carne molida', 'Tomate'], ['Cebolla', 'Queso', 'Frijol', 'Cilantro', 'Chile', 'Chile chipotle']],
            ['Tacos de pescado a la cerveza', 'principal', 35, 95, 'Mexicano', 'alta_proteina', ['Pescado blanco', 'Tortilla', 'Col'], ['Limón', 'Cebolla', 'Cilantro', 'Harina', 'Huevo', 'Aguacate']],
            ['Ceviche de pescado', 'principal', 20, 90, 'Mexicano', 'ligera', ['Pescado blanco', 'Limón', 'Cebolla'], ['Aguacate', 'Naranja', 'Cilantro', 'Camarón']],
            ['Pollo adobado a la lima', 'principal', 50, 85, 'Mexicano', 'alta_proteina', ['Pollo', 'Limón', 'Ajo'], ['Naranja', 'Cilantro', 'Pimentón', 'Comino']],
            ['Pollo en chile color', 'principal', 50, 80, 'Mexicano', 'alta_proteina', ['Pollo', 'Chile ancho', 'Tomate'], ['Cebolla', 'Ajo', 'Comino', 'Orégano', 'Canela']],
            ['Pollo a la crema', 'principal', 35, 75, 'Mexicano', 'alta_proteina', ['Pollo', 'Crema', 'Cebolla'], ['Elote', 'Pimiento', 'Ajo', 'Mantequilla']],
            ['Mole verde de Zacatecas con pollo', 'principal', 60, 95, 'Mexicano', 'alta_proteina', ['Pollo', 'Tomatillo', 'Cilantro'], ['Cebolla', 'Ajo', 'Chile', 'Laurel']],
            ['Picadillo', 'principal', 35, 65, 'Mexicano', 'economica', ['Carne molida', 'Papa', 'Tomate'], ['Cebolla', 'Ajo', 'Plátano', 'Canela', 'Laurel']],
            ['Cerdo en chile guajillo', 'principal', 80, 100, 'Mexicano', 'alta_proteina', ['Cerdo', 'Chile guajillo', 'Tomate'], ['Cebolla', 'Ajo', 'Laurel']],
            ['Huarache con carne asada', 'principal', 50, 95, 'Mexicano', 'alta_proteina', ['Harina', 'Res', 'Cebolla'], ['Queso', 'Tomate', 'Chile', 'Frijol']],
            ['Arroz blanco', 'acompanamiento', 25, 12, 'Mexicano', 'economica', ['Arroz'], ['Mantequilla']],
            ['Arroz al cilantro y limón', 'acompanamiento', 30, 20, 'Mexicano', 'ligera', ['Arroz', 'Cilantro', 'Limón'], ['Mantequilla']],
            ['Arroz rojo', 'acompanamiento', 30, 20, 'Mexicano', 'economica', ['Arroz', 'Tomate', 'Cebolla'], ['Ajo', 'Zanahoria']],
            ['Frijoles negros refritos', 'acompanamiento', 45, 18, 'Mexicano', 'economica', ['Frijol', 'Cebolla'], ['Ajo', 'Queso', 'Tortilla', 'Hierbabuena', 'Perejil']],
            ['Guacamole', 'acompanamiento', 10, 40, 'Mexicano', 'ligera', ['Aguacate', 'Limón'], ['Tomate', 'Cebolla', 'Chile', 'Cilantro']],
            ['Ensalada de jícama', 'acompanamiento', 15, 35, 'Mexicano', 'ligera', ['Jícama', 'Naranja', 'Limón'], ['Zanahoria', 'Betabel']],
            ['Ensalada con sabor a México', 'acompanamiento', 15, 35, 'Mexicano', 'ligera', ['Jícama', 'Pepino', 'Tomate'], ['Pimiento', 'Cilantro', 'Cebolla']],
            ['Papas con rajas', 'acompanamiento', 30, 30, 'Mexicano', 'economica', ['Papa', 'Chile poblano', 'Cebolla'], ['Ajo']],
            ['Crema de maíz', 'acompanamiento', 30, 35, 'Mexicano', 'ligera', ['Elote', 'Cebolla'], ['Chile', 'Yogur', 'Queso', 'Cilantro']],
            ['Escabeche de cebolla roja', 'acompanamiento', 20, 15, 'Mexicano', 'ligera', ['Cebolla', 'Vinagre'], ['Orégano', 'Ajo', 'Comino']],
            ['Café dulce con canela', 'bebida', 10, 12, 'Mexicano', 'rapida', ['Café', 'Canela'], ['Azúcar']],
            ['Refresco de coco y limón', 'bebida', 10, 25, 'Mexicano', 'rapida', ['Coco', 'Limón', 'Azúcar'], ['Mango', 'Papaya', 'Guayaba', 'Hierbabuena']],
            ['Ponche de frutas', 'bebida', 30, 30, 'Mexicano', 'economica', ['Piña', 'Naranja', 'Manzana'], ['Uva', 'Limón', 'Canela', 'Azúcar']],
            ['Agua de jamaica', 'bebida', 15, 10, 'Mexicano', 'economica', ['Jamaica', 'Azúcar'], ['Limón']],
            ['Agua de tamarindo', 'bebida', 20, 10, 'Mexicano', 'economica', ['Tamarindo', 'Azúcar'], []],
            ['Horchata de arroz', 'bebida', 20, 15, 'Mexicano', 'economica', ['Arroz', 'Canela', 'Azúcar'], ['Leche', 'Vainilla']],
            ['Agua de limón', 'bebida', 5, 10, 'Mexicano', 'rapida', ['Limón', 'Azúcar'], []],
            ['Limonada con chía', 'bebida', 10, 12, 'Mexicano', 'ligera', ['Limón', 'Chía', 'Azúcar'], []],
            ['Agua de pepino con limón', 'bebida', 10, 12, 'Mexicano', 'ligera', ['Pepino', 'Limón'], ['Hierbabuena', 'Azúcar']],
            ['Agua de melón', 'bebida', 10, 14, 'Mexicano', 'rapida', ['Melón'], ['Azúcar', 'Limón']],
            ['Agua de sandía', 'bebida', 10, 14, 'Mexicano', 'rapida', ['Sandía'], ['Limón', 'Azúcar']],
            ['Agua de piña', 'bebida', 10, 14, 'Mexicano', 'rapida', ['Piña'], ['Azúcar', 'Canela']],
            ['Agua de fresa', 'bebida', 10, 20, 'Mexicano', 'rapida', ['Fresa', 'Azúcar'], ['Limón']],
            ['Agua de mango', 'bebida', 10, 18, 'Mexicano', 'rapida', ['Mango'], ['Limón', 'Azúcar']],
            ['Agua de guayaba', 'bebida', 10, 15, 'Mexicano', 'rapida', ['Guayaba'], ['Azúcar', 'Limón']],
            ['Agua de papaya', 'bebida', 10, 15, 'Mexicano', 'rapida', ['Papaya'], ['Limón', 'Azúcar', 'Naranja']],
            ['Agua de naranja con hierbabuena', 'bebida', 10, 14, 'Mexicano', 'ligera', ['Naranja', 'Hierbabuena'], ['Azúcar']],
            ['Agua de lavanda con limón', 'bebida', 15, 18, 'Mexicano', 'ligera', ['Lavanda', 'Limón', 'Azúcar'], ['Miel']],
            ['Agua de crema de coco', 'bebida', 10, 25, 'Mexicano', 'rapida', ['Coco', 'Leche'], ['Azúcar', 'Canela', 'Vainilla']],
            ['Agua de piña y coco', 'bebida', 10, 22, 'Mexicano', 'rapida', ['Piña', 'Coco'], ['Azúcar']],
            ['Atole de vainilla', 'bebida', 15, 15, 'Mexicano', 'economica', ['Maicena', 'Leche', 'Vainilla'], ['Canela', 'Azúcar']],
            ['Chocolate caliente', 'bebida', 10, 20, 'Mexicano', 'rapida', ['Chocolate', 'Leche'], ['Canela', 'Azúcar']],
            ['Flan cremoso de caramelo', 'postre', 60, 35, 'Mexicano', 'economica', ['Huevo', 'Leche', 'Azúcar'], ['Queso crema', 'Leche condensada', 'Vainilla']],
            ['Budín de elote', 'postre', 60, 40, 'Mexicano', 'economica', ['Elote', 'Huevo', 'Leche condensada'], ['Queso crema', 'Mantequilla']],
            ['Capirotada', 'postre', 45, 35, 'Mexicano', 'economica', ['Pan', 'Queso', 'Azúcar'], ['Nuez', 'Canela', 'Leche', 'Mantequilla']],
            ['Burritos de manzana con canela', 'postre', 25, 30, 'Mexicano', 'rapida', ['Tortilla', 'Manzana', 'Canela'], ['Naranja', 'Nuez', 'Azúcar', 'Limón']],
            ['Plátano con miel y nueces', 'postre', 15, 30, 'Mexicano', 'rapida', ['Plátano', 'Miel'], ['Limón', 'Mantequilla', 'Nuez']],
            ['Pastel de tres leches', 'postre', 60, 55, 'Mexicano', 'economica', ['Harina', 'Huevo', 'Leche'], ['Azúcar', 'Vainilla', 'Crema', 'Leche condensada']],
            ['Arroz con leche', 'postre', 40, 30, 'Mexicano', 'economica', ['Arroz', 'Leche', 'Azúcar'], ['Canela']],
            // ---------- ITALIANO ----------
            ['Pasta con tomate y albahaca', 'principal', 25, 40, 'Italiano', 'economica', ['Pasta', 'Tomate', 'Ajo'], ['Albahaca', 'Cebolla', 'Queso']],
            ['Pasta arrabbiata', 'principal', 60, 40, 'Italiano', 'economica', ['Pasta', 'Tomate', 'Chile', 'Ajo'], ['Albahaca', 'Queso']],
            ['Espagueti carbonara', 'principal', 25, 60, 'Italiano', 'rapida', ['Pasta', 'Huevo', 'Tocino'], ['Queso', 'Ajo']],
            ['Pasta con champiñones y crema', 'principal', 25, 65, 'Italiano', 'rapida', ['Pasta', 'Champiñones', 'Crema'], ['Ajo', 'Queso', 'Cebolla']],
            ['Risotto a la parmesana con limón', 'principal', 35, 60, 'Italiano', 'economica', ['Arroz', 'Queso', 'Limón'], ['Cebolla', 'Mantequilla']],
            ['Canelones de carne', 'principal', 120, 85, 'Italiano', 'alta_proteina', ['Pasta', 'Carne molida', 'Tomate'], ['Cebolla', 'Zanahoria', 'Leche', 'Queso', 'Jamón']],
            ['Lasaña de carne', 'principal', 90, 90, 'Italiano', 'alta_proteina', ['Pasta', 'Carne molida', 'Queso'], ['Cebolla', 'Zanahoria', 'Pimiento', 'Tomate', 'Tocino']],
            ['Pollo a la cacciatora', 'principal', 45, 80, 'Italiano', 'alta_proteina', ['Pollo', 'Tomate', 'Cebolla'], ['Pimiento', 'Ajo']],
            ['Escalopines de res al balsámico', 'principal', 25, 100, 'Italiano', 'alta_proteina', ['Res', 'Ajo', 'Perejil'], ['Vinagre']],
            ['Tosta de salmón y naranja', 'principal', 15, 90, 'Italiano', 'ligera', ['Salmón', 'Naranja', 'Pan'], ['Limón']],
            ['Ensalada caprese', 'acompanamiento', 10, 45, 'Italiano', 'ligera', ['Tomate', 'Queso'], ['Albahaca', 'Lechuga']],
            ['Carpaccio de tomate con parmesano y pesto', 'acompanamiento', 20, 55, 'Italiano', 'ligera', ['Tomate', 'Queso', 'Albahaca'], ['Ajo', 'Nuez']],
            ['Pan con ajo', 'acompanamiento', 10, 20, 'Italiano', 'rapida', ['Pan', 'Ajo', 'Mantequilla'], ['Perejil']],
            ['Ensalada verde', 'acompanamiento', 10, 30, 'Italiano', 'ligera', ['Lechuga', 'Tomate'], ['Zanahoria', 'Limón']],
            ['Café latte', 'bebida', 5, 25, 'Italiano', 'rapida', ['Café', 'Leche'], ['Azúcar']],
            ['Limonata italiana', 'bebida', 5, 12, 'Italiano', 'rapida', ['Limón', 'Azúcar'], ['Albahaca', 'Hierbabuena']],
            ['Tiramisú', 'postre', 30, 70, 'Italiano', 'rapida', ['Huevo', 'Queso crema', 'Café'], ['Azúcar', 'Chocolate']],
            ['Panna cotta', 'postre', 25, 40, 'Italiano', 'ligera', ['Crema', 'Azúcar', 'Leche'], ['Queso crema', 'Vainilla', 'Fresa']],
            ['Fresas con crema', 'postre', 10, 40, 'Italiano', 'rapida', ['Fresa', 'Crema'], ['Azúcar']],
            // ---------- JAPONÉS ----------
            ['Donburi de pollo', 'principal', 30, 65, 'Japonés', 'alta_proteina', ['Arroz', 'Pollo', 'Salsa de soya'], ['Jengibre', 'Champiñones', 'Miso', 'Cebolleta']],
            ['Oyakodon', 'principal', 25, 60, 'Japonés', 'alta_proteina', ['Arroz', 'Pollo', 'Huevo', 'Cebolla'], ['Salsa de soya', 'Jengibre']],
            ['Pollo teriyaki', 'principal', 30, 75, 'Japonés', 'alta_proteina', ['Pollo', 'Salsa de soya', 'Azúcar'], ['Jengibre', 'Ajo']],
            ['Yakisoba', 'principal', 30, 55, 'Japonés', 'rapida', ['Fideos', 'Col', 'Salsa de soya'], ['Zanahoria', 'Cebolla', 'Cerdo', 'Ajo', 'Cebolleta']],
            ['Okonomiyaki', 'principal', 30, 45, 'Japonés', 'economica', ['Col', 'Harina', 'Huevo'], ['Cebolleta', 'Tocino', 'Salsa de soya']],
            ['Rollitos de sushi con aguacate', 'principal', 45, 70, 'Japonés', 'ligera', ['Arroz', 'Alga nori', 'Aguacate'], ['Pepino', 'Zanahoria', 'Vinagre de arroz']],
            ['Onigiri de atún', 'principal', 15, 35, 'Japonés', 'rapida', ['Arroz', 'Atún', 'Alga nori'], ['Salsa de soya']],
            ['Gyozas de verduras', 'principal', 60, 50, 'Japonés', 'ligera', ['Harina', 'Col', 'Champiñones'], ['Cebolla', 'Jengibre', 'Ajo', 'Salsa de soya']],
            ['Caja bento', 'principal', 40, 60, 'Japonés', 'ligera', ['Arroz', 'Huevo', 'Brócoli'], ['Camote', 'Berenjena', 'Alga nori', 'Miso']],
            ['Tamagoyaki', 'acompanamiento', 15, 25, 'Japonés', 'rapida', ['Huevo', 'Azúcar'], ['Salsa de soya', 'Cebolleta']],
            ['Ensalada de pepino japonesa', 'acompanamiento', 10, 20, 'Japonés', 'ligera', ['Pepino', 'Vinagre de arroz'], ['Azúcar', 'Salsa de soya', 'Ajonjolí']],
            ['Sopa de miso', 'acompanamiento', 15, 30, 'Japonés', 'ligera', ['Miso', 'Tofu', 'Cebolleta'], ['Alga nori', 'Champiñones']],
            ['Verduras al vapor con ajonjolí', 'acompanamiento', 15, 30, 'Japonés', 'ligera', ['Brócoli', 'Zanahoria', 'Ajonjolí'], ['Salsa de soya']],
            ['Té verde', 'bebida', 5, 8, 'Japonés', 'ligera', ['Té'], ['Limón', 'Miel']],
            ['Té helado de limón', 'bebida', 10, 10, 'Japonés', 'rapida', ['Té', 'Limón'], ['Azúcar', 'Miel']],
            ['Helado de mango', 'postre', 5, 30, 'Japonés', 'rapida', ['Mango'], ['Yogur', 'Miel']],
            ['Purin japonés', 'postre', 40, 30, 'Japonés', 'economica', ['Huevo', 'Leche', 'Azúcar'], ['Vainilla']],
            // ---------- CHINO ----------
            ['Arroz frito', 'principal', 20, 45, 'Chino', 'rapida', ['Arroz', 'Huevo', 'Cebolleta'], ['Zanahoria', 'Salsa de soya', 'Pollo', 'Ajo', 'Chícharos']],
            ['Pollo al jengibre con miel', 'principal', 30, 75, 'Chino', 'alta_proteina', ['Pollo', 'Jengibre', 'Miel'], ['Salsa de soya', 'Ajo', 'Cebolleta']],
            ['Pollo al limón estilo chino', 'principal', 30, 75, 'Chino', 'alta_proteina', ['Pollo', 'Limón', 'Maicena'], ['Cebolleta', 'Ajo', 'Huevo', 'Azúcar']],
            ['Chow mein', 'principal', 25, 60, 'Chino', 'rapida', ['Fideos', 'Col', 'Zanahoria'], ['Salsa de soya', 'Pollo', 'Cebolleta', 'Ajo', 'Cerdo']],
            ['Chop suey de pollo con ajo', 'principal', 25, 70, 'Chino', 'alta_proteina', ['Pollo', 'Ajo', 'Cebolleta'], ['Jengibre', 'Salsa de soya', 'Tomate', 'Col', 'Zanahoria']],
            ['Res salteada con ajonjolí', 'principal', 25, 95, 'Chino', 'alta_proteina', ['Res', 'Ajonjolí', 'Ajo'], ['Salsa de soya', 'Chile', 'Azúcar', 'Cebolleta']],
            ['Tofu con verduras salteadas', 'principal', 20, 50, 'Chino', 'ligera', ['Tofu', 'Brócoli', 'Salsa de soya'], ['Elote', 'Limón', 'Azúcar', 'Chile']],
            ['Potstickers de camarón', 'principal', 50, 90, 'Chino', 'alta_proteina', ['Camarón', 'Harina', 'Cebolleta'], ['Ajo', 'Jengibre', 'Salsa de soya', 'Col']],
            ['Sopa de huevo', 'acompanamiento', 15, 25, 'Chino', 'ligera', ['Huevo', 'Cebolleta'], ['Salsa de soya', 'Zanahoria']],
            ['Verduras salteadas al wok', 'acompanamiento', 15, 30, 'Chino', 'ligera', ['Champiñones', 'Zanahoria', 'Col'], ['Salsa de soya', 'Azúcar']],
            ['Setas salteadas con jengibre', 'acompanamiento', 15, 35, 'Chino', 'ligera', ['Champiñones', 'Jengibre', 'Ajo'], ['Cebolleta', 'Chile', 'Salsa de soya']],
            ['Arroz al vapor', 'acompanamiento', 20, 12, 'Chino', 'economica', ['Arroz'], []],
            ['Té de jengibre', 'bebida', 10, 10, 'Chino', 'ligera', ['Jengibre'], ['Miel', 'Limón']],
            ['Té caliente con limón', 'bebida', 5, 8, 'Chino', 'rapida', ['Té', 'Limón'], ['Azúcar']],
            ['Plátano caramelizado', 'postre', 10, 25, 'Chino', 'rapida', ['Plátano', 'Azúcar'], ['Canela']],
            ['Pastelitos de manzana frita y coco', 'postre', 35, 40, 'Chino', 'economica', ['Manzana', 'Coco', 'Harina'], ['Azúcar', 'Canela']],
            // ---------- ESPAÑOL ----------
            ['Tortilla española', 'principal', 30, 40, 'Español', 'economica', ['Huevo', 'Papa'], ['Cebolla']],
            ['Patatas a la riojana', 'principal', 40, 55, 'Español', 'economica', ['Papa', 'Chorizo', 'Cebolla'], ['Ajo', 'Pimiento', 'Pimentón']],
            ['Zarangollo murciano', 'principal', 30, 40, 'Español', 'economica', ['Calabacita', 'Huevo', 'Cebolla'], ['Papa', 'Orégano']],
            ['Migas extremeñas', 'principal', 35, 50, 'Español', 'economica', ['Pan', 'Ajo', 'Chorizo'], ['Tocino', 'Pimentón', 'Pimiento']],
            ['Pollo al salmorejo', 'principal', 50, 80, 'Español', 'alta_proteina', ['Pollo', 'Ajo', 'Pimentón'], ['Comino', 'Orégano', 'Laurel']],
            ['Albóndigas en salsa', 'principal', 40, 70, 'Español', 'alta_proteina', ['Carne molida', 'Tomate', 'Cebolla'], ['Ajo', 'Huevo', 'Pan']],
            ['Paella sencilla', 'principal', 50, 95, 'Español', 'alta_proteina', ['Arroz', 'Pollo', 'Pimiento'], ['Camarón', 'Tomate', 'Ajo', 'Pimentón']],
            ['Bacalao al pil pil', 'principal', 30, 110, 'Español', 'alta_proteina', ['Bacalao', 'Ajo', 'Chile'], []],
            ['Ensalada murciana', 'principal', 15, 55, 'Español', 'ligera', ['Tomate', 'Atún', 'Huevo'], ['Cebolla', 'Aceituna']],
            ['Pisto a la bilbaína', 'acompanamiento', 40, 45, 'Español', 'ligera', ['Calabacita', 'Pimiento', 'Cebolla'], ['Tomate', 'Huevo']],
            ['Patatas bravas', 'acompanamiento', 30, 25, 'Español', 'economica', ['Papa', 'Tomate'], ['Pimentón', 'Ajo', 'Chile']],
            ['Gazpacho', 'acompanamiento', 15, 35, 'Español', 'ligera', ['Tomate', 'Pepino', 'Pimiento'], ['Ajo', 'Cebolla', 'Pan']],
            ['Pan con tomate', 'acompanamiento', 5, 15, 'Español', 'rapida', ['Pan', 'Tomate'], ['Ajo']],
            ['Habas con jamón', 'acompanamiento', 30, 45, 'Español', 'economica', ['Habas', 'Jamón', 'Cebolleta'], []],
            ['Zumo de naranja', 'bebida', 5, 20, 'Español', 'ligera', ['Naranja'], []],
            ['Café con leche', 'bebida', 5, 15, 'Español', 'rapida', ['Café', 'Leche'], ['Azúcar']],
            ['Natillas conventuales', 'postre', 30, 30, 'Español', 'economica', ['Leche', 'Huevo', 'Azúcar'], ['Canela', 'Maicena', 'Vainilla']],
            ['Brazo de gitano', 'postre', 50, 45, 'Español', 'economica', ['Huevo', 'Azúcar', 'Harina'], ['Crema', 'Fresa']],
            ['Pestiños', 'postre', 40, 30, 'Español', 'economica', ['Harina', 'Miel'], ['Ajonjolí', 'Naranja']],
            ['Torrijas', 'postre', 25, 30, 'Español', 'economica', ['Pan', 'Leche', 'Huevo'], ['Canela', 'Miel', 'Azúcar']],
            // ---------- ARGENTINO ----------
            ['Milanesa a caballo con papas fritas', 'principal', 40, 100, 'Argentino', 'alta_proteina', ['Res', 'Huevo', 'Pan'], ['Papa', 'Leche', 'Perejil']],
            ['Empanadas de carne', 'principal', 50, 70, 'Argentino', 'economica', ['Carne molida', 'Masa para empanadas', 'Cebolla'], ['Huevo', 'Aceituna', 'Pimentón', 'Comino']],
            ['Empanadas de pollo al verdeo', 'principal', 50, 75, 'Argentino', 'alta_proteina', ['Pollo', 'Masa para empanadas', 'Cebolleta'], ['Queso', 'Crema']],
            ['Empanadas de jamón y queso', 'principal', 35, 55, 'Argentino', 'rapida', ['Jamón', 'Queso', 'Masa para empanadas'], []],
            ['Empanadas de atún', 'principal', 40, 60, 'Argentino', 'economica', ['Atún', 'Masa para empanadas', 'Cebolla'], ['Aceituna', 'Papa', 'Pimiento', 'Ajo']],
            ['Guiso de lentejas', 'principal', 50, 45, 'Argentino', 'economica', ['Lenteja', 'Zanahoria', 'Cebolla'], ['Tocino', 'Chorizo', 'Papa', 'Tomate', 'Pimiento']],
            ['Asado de tira con chimichurri', 'principal', 60, 120, 'Argentino', 'alta_proteina', ['Res', 'Perejil', 'Ajo'], ['Limón', 'Orégano', 'Vinagre']],
            ['Bife con papas al plomo', 'principal', 35, 110, 'Argentino', 'alta_proteina', ['Res', 'Papa'], ['Ajo', 'Perejil']],
            ['Pastel de papas', 'principal', 60, 65, 'Argentino', 'economica', ['Papa', 'Carne molida', 'Cebolla'], ['Huevo', 'Leche', 'Mantequilla', 'Queso']],
            ['Ñoquis de papa', 'principal', 45, 45, 'Argentino', 'economica', ['Papa', 'Harina', 'Huevo'], ['Queso', 'Tomate', 'Ajo']],
            ['Chimichurri', 'acompanamiento', 10, 15, 'Argentino', 'rapida', ['Perejil', 'Ajo'], ['Orégano', 'Vinagre', 'Chile', 'Limón']],
            ['Humitas en chala', 'acompanamiento', 60, 45, 'Argentino', 'economica', ['Elote', 'Cebolla', 'Queso'], ['Pimiento', 'Leche', 'Ajo']],
            ['Ensalada mixta', 'acompanamiento', 10, 25, 'Argentino', 'ligera', ['Lechuga', 'Tomate', 'Cebolla'], ['Zanahoria', 'Limón']],
            ['Puré de papa', 'acompanamiento', 25, 25, 'Argentino', 'economica', ['Papa', 'Mantequilla'], ['Leche']],
            ['Provoleta', 'acompanamiento', 15, 50, 'Argentino', 'rapida', ['Queso'], ['Tomate', 'Orégano']],
            ['Licuado de banana con leche', 'bebida', 5, 25, 'Argentino', 'rapida', ['Plátano', 'Leche'], ['Azúcar', 'Avena']],
            ['Mate cocido con leche', 'bebida', 10, 10, 'Argentino', 'economica', ['Té', 'Leche'], ['Azúcar']],
            ['Delicia de arroz con leche', 'postre', 40, 30, 'Argentino', 'economica', ['Arroz', 'Leche', 'Azúcar'], ['Canela', 'Vainilla', 'Dulce de leche']],
            ['Flan con dulce de leche', 'postre', 60, 40, 'Argentino', 'economica', ['Huevo', 'Leche', 'Azúcar'], ['Dulce de leche', 'Vainilla']],
            ['Alfajorcitos de maicena', 'postre', 50, 40, 'Argentino', 'economica', ['Maicena', 'Harina', 'Mantequilla'], ['Azúcar', 'Huevo', 'Dulce de leche', 'Vainilla']],
            ['Pasta frola de membrillo', 'postre', 70, 40, 'Argentino', 'economica', ['Harina', 'Membrillo', 'Mantequilla'], ['Huevo', 'Azúcar']],
            ['Panqueques con dulce de leche', 'postre', 25, 30, 'Argentino', 'rapida', ['Harina', 'Huevo', 'Leche'], ['Dulce de leche', 'Azúcar', 'Plátano']],
            ['Budín de pan con manzanas', 'postre', 70, 35, 'Argentino', 'economica', ['Pan', 'Manzana', 'Leche'], ['Huevo', 'Azúcar', 'Canela', 'Nuez']],
            // ---------- VEGETARIANO ----------
            ['Tacos de verduras con frijoles', 'principal', 30, 45, 'Vegetariano', 'ligera', ['Tortilla', 'Calabaza', 'Frijol'], ['Tomate', 'Yogur', 'Limón', 'Chile']],
            ['Lasaña mexicana de frijoles y espinaca', 'principal', 45, 55, 'Vegetariano', 'economica', ['Tortilla', 'Frijol', 'Espinaca'], ['Queso', 'Cilantro', 'Cebolleta', 'Chile']],
            ['Calabacitas con queso', 'principal', 30, 45, 'Vegetariano', 'economica', ['Calabacita', 'Queso', 'Tomate'], ['Cebolla', 'Chile']],
            ['Berenjenas capeadas', 'principal', 30, 45, 'Vegetariano', 'economica', ['Berenjena', 'Huevo', 'Harina'], ['Chile', 'Cebolla', 'Lechuga']],
            ['Tarta pascualina de acelga y calabaza', 'principal', 60, 50, 'Vegetariano', 'economica', ['Acelga', 'Huevo', 'Harina'], ['Calabaza', 'Queso', 'Cebolla', 'Mantequilla']],
            ['Tarta soufflé de espinacas', 'principal', 55, 50, 'Vegetariano', 'economica', ['Espinaca', 'Huevo', 'Harina'], ['Cebolla', 'Queso', 'Mantequilla', 'Leche']],
            ['Canelones de espinaca con bechamel', 'principal', 70, 60, 'Vegetariano', 'economica', ['Pasta', 'Espinaca', 'Leche'], ['Queso', 'Mantequilla', 'Harina', 'Nuez']],
            ['Empanadas de elote', 'principal', 40, 50, 'Vegetariano', 'economica', ['Elote', 'Masa para empanadas', 'Queso'], ['Cebolla', 'Pimiento', 'Mantequilla']],
            ['Patatas riojanas vegetarianas', 'principal', 40, 35, 'Vegetariano', 'economica', ['Papa', 'Cebolla', 'Pimiento'], ['Ajo', 'Pimentón', 'Chile', 'Laurel', 'Perejil']],
            ['Garbanzos con espinacas', 'principal', 35, 40, 'Vegetariano', 'economica', ['Garbanzo', 'Espinaca'], ['Ajo', 'Pimentón', 'Comino']],
            ['Tomates asados con pesto', 'principal', 30, 55, 'Vegetariano', 'ligera', ['Tomate', 'Albahaca', 'Pan'], ['Ajo', 'Queso', 'Nuez']],
            ['Ensalada de lechuga y aguacate', 'acompanamiento', 10, 40, 'Vegetariano', 'ligera', ['Lechuga', 'Aguacate'], ['Tomate', 'Limón', 'Pepino']],
            ['Brócoli al ajo', 'acompanamiento', 15, 30, 'Vegetariano', 'ligera', ['Brócoli', 'Ajo'], ['Limón', 'Queso']],
            ['Coliflor al horno', 'acompanamiento', 35, 30, 'Vegetariano', 'ligera', ['Coliflor'], ['Ajo', 'Queso', 'Pimentón']],
            ['Ensalada de lentejas', 'acompanamiento', 20, 30, 'Vegetariano', 'economica', ['Lenteja', 'Tomate', 'Cebolla'], ['Pepino', 'Limón', 'Perejil']],
            ['Arroz con verduras', 'acompanamiento', 30, 25, 'Vegetariano', 'economica', ['Arroz', 'Zanahoria', 'Chícharos'], ['Cebolla', 'Ajo', 'Elote']],
            ['Smoothie de mango y yogur', 'bebida', 5, 28, 'Vegetariano', 'ligera', ['Mango', 'Yogur'], ['Miel', 'Naranja']],
            ['Licuado de plátano con avena', 'bebida', 5, 22, 'Vegetariano', 'economica', ['Plátano', 'Avena', 'Leche'], ['Miel', 'Canela']],
            ['Yogur con miel y fruta', 'postre', 5, 30, 'Vegetariano', 'ligera', ['Yogur', 'Miel'], ['Fresa', 'Manzana', 'Plátano', 'Nuez']],
            ['Manzanas asadas con canela', 'postre', 30, 25, 'Vegetariano', 'ligera', ['Manzana', 'Canela'], ['Miel', 'Nuez', 'Mantequilla']],
            ['Ensalada de frutas con limón y miel', 'postre', 10, 35, 'Vegetariano', 'ligera', ['Manzana', 'Plátano'], ['Fresa', 'Naranja', 'Limón', 'Miel', 'Piña']],
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