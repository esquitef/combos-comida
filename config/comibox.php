<?php

/*
 * Ajustes propios de ComiBox.
 *
 * Los símbolos (emojis) se eligen entre los de Unicode 12 o anteriores para que se vean bien en Windows 10, Android y Linux.
 * Un ingrediente que no aparece en 'emoji_ingredientes' simplemente se muestra sin símbolo.
 */

return [

    // Símbolo de cada rol de plato dentro de un combo.
    'emoji_roles' => [
        'principal' => '🍽️',
        'acompanamiento' => '🥗',
        'bebida' => '🥤',
        'postre' => '🍰',
    ],

    // Símbolo de cada categoría de ingredientes (si falta alguna, la vista usa 🍽️).
    'emoji_categorias' => [
        'Verduras' => '🥦',
        'Chiles, hierbas y especias' => '🌶️',
        'Carnes y aves' => '🍗',
        'Pescados y mariscos' => '🐟',
        'Huevos y lácteos' => '🥚',
        'Pastas, cereales y panes' => '🍝',
        'Legumbres y soya' => '🌱',
        'Frutas' => '🍎',
        'Frutos secos, semillas y flores' => '🥜',
        'Condimentos y básicos' => '🧂',
    ],

    // Símbolo de cada ingrediente (clave = nombre exacto del ingrediente).
    'emoji_ingredientes' => [
        'Tomate' => '🍅',
        'Cebolla' => '🧅',
        'Ajo' => '🧄',
        'Papa' => '🥔',
        'Camote' => '🍠',
        'Zanahoria' => '🥕',
        'Lechuga' => '🥬',
        'Aguacate' => '🥑',
        'Champiñones' => '🍄',
        'Pepino' => '🥒',
        'Berenjena' => '🍆',
        'Col' => '🥬',
        'Espinaca' => '🥬',
        'Acelga' => '🥬',
        'Calabaza' => '🎃',
        'Elote' => '🌽',
        'Brócoli' => '🥦',
        'Chile' => '🌶️',
        'Chile poblano' => '🌶️',
        'Chile chipotle' => '🌶️',
        'Chile guajillo' => '🌶️',
        'Chile ancho' => '🌶️',
        'Cilantro' => '🌿',
        'Perejil' => '🌿',
        'Hierbabuena' => '🌿',
        'Albahaca' => '🌿',
        'Orégano' => '🌿',
        'Laurel' => '🍃',
        'Pollo' => '🍗',
        'Carne molida' => '🥩',
        'Res' => '🥩',
        'Cerdo' => '🐖',
        'Jamón' => '🍖',
        'Chorizo' => '🌭',
        'Tocino' => '🥓',
        'Atún' => '🐟',
        'Camarón' => '🦐',
        'Pescado blanco' => '🐟',
        'Bacalao' => '🐟',
        'Salmón' => '🐟',
        'Huevo' => '🥚',
        'Leche' => '🥛',
        'Queso' => '🧀',
        'Queso crema' => '🧀',
        'Pasta' => '🍝',
        'Arroz' => '🍚',
        'Tortilla' => '🌮',
        'Pan' => '🍞',
        'Avena' => '🥣',
        'Harina' => '🌾',
        'Fideos' => '🍜',
        'Masa para empanadas' => '🥟',
        'Plátano' => '🍌',
        'Limón' => '🍋',
        'Manzana' => '🍎',
        'Fresa' => '🍓',
        'Naranja' => '🍊',
        'Piña' => '🍍',
        'Mango' => '🥭',
        'Melón' => '🍈',
        'Sandía' => '🍉',
        'Coco' => '🥥',
        'Uva' => '🍇',
        'Nuez' => '🌰',
        'Jamaica' => '🌺',
        'Lavanda' => '💐',
        'Café' => '☕',
        'Té' => '🍵',
        'Miel' => '🍯',
        'Chocolate' => '🍫',
    ],

];
