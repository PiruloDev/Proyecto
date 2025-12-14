<?php

namespace App\Helpers;

class ProductImageHelper
{
    private static $imageMap = [
        // Por nombre de producto (en minúsculas)
        'kumiss natural' => 'kumiss.1.png',
        'yogurt fresa litro' => 'yogurt1.jpg',
        'galletas de avena y pasas' => 'galletas-de-avena-con-pasas-y-nuez.jpg',
        'galletas surtidas de mantequilla' => 'galletasmantequilla.jpg',
        'bizcochos de achira' => 'achiras.jpg',
        'galleta de tres ojos' => 'galletatresojos.png',
        'avena la lechera (500ml)' => 'avena.jpg',
        'pan de hamburguesa' => 'panhamburguesa.jpg',
        'pan blandito' => 'panblando.jpeg',
        'pan de bono pequeno' => 'pandebono.png',
        'pan blanco de molde' => 'pan-de-molde.jpg',
        'pan tajado integral' => 'pan-integral.jpg',
        'pan artesanal de masa madre' => 'pan-con-masa-madre-.jpg',
        'pan campesino grande' => 'pancampesino.png',
        'baguette clasica' => 'baguette.jpg',
        'ponque de naranja (porcion)' => 'naranjaponque.jpg',
        'mogolla chicharrona' => 'chicharrona.png',
        'milhoja de arequipe' => 'miloha_de_arequipe.jpg',
        'cheesecake de frutos rojos' => 'cheesecake-frutos-rojos.jpg',
        'tamales' => 'tamal1.jpg',
        'postre de tres leches' => 'postreleche.jpg',
        'empanadas' => 'empanadas.png',
        'brazo de reina' => 'brazoreina1.jpg',
        'hojaldre' => 'hojaldre1.jpg',
        'hojaldres' => 'hojaldre1.jpg',
        'pan grande' => 'pangrande1.jpg',
        'panes grandes' => 'pangrande1.jpg',
        'brownie' => 'brownie1.jpg',
        'brownies' => 'brownie1.jpg',
        'croissant' => 'croissants.jpg',
        'croissants' => 'croissants.jpg',
        'muffin' => 'muffins1.jpg',
        'muffins' => 'muffins1.jpg',
        'cupcake' => 'cupcake.jpg',
        'cupcakes' => 'cupcake.jpg',
        'pan' => 'pan.jpg',
        'pastel' => 'pastel.jpg',
        'torta' => 'pastel.jpg',
        'jugo' => 'jugo.jpg',
        'jugo de naranja natural' => 'naranja.jpg',
        'panadero' => 'panadero.jpg',
        'preparacion' => 'preparacion.jpg',
        'producto' => 'productoab.jpg',
        'app' => 'appmobile.jpg',
        'aplicacion' => 'appmobile.jpg',

        // Por categoría
        'panaderia' => 'pan.jpg',
        'postres' => 'cupcake.jpg',
        'reposteria' => 'pastel.jpg',
        'bebidas' => 'jugo.jpg',
    ];

        /**
     * Obtiene el nombre de la imagen correspondiente para un producto dado
     * @param string $nombreProducto Nombre del producto
     * @param int|null $idProducto ID del producto (opcional)
     * @param string|null $nombreCategoria Nombre de la categoría (opcional)
     * @return string Nombre del archivo de imagen
        */
    public static function getImage($nombreProducto, $idProducto = null, $nombreCategoria = null)
    {
        $nombreLower = strtolower(trim($nombreProducto));

        // 1. Buscar coincidencia exacta por nombre
        if (isset(self::$imageMap[$nombreLower])) {
            return self::$imageMap[$nombreLower];
        }

        // 2. Buscar coincidencia parcial por palabras clave
        foreach (self::$imageMap as $keyword => $image) {
            if (strpos($nombreLower, $keyword) !== false) {
                return $image;
            }
        }

        // 3. Buscar por categoría si está disponible
        if ($nombreCategoria) {
            $categoriaLower = strtolower(trim($nombreCategoria));
            if (isset(self::$imageMap[$categoriaLower])) {
                return self::$imageMap[$categoriaLower];
            }
        }

        // 4. Imagen por defecto
        return 'trabajador.jpg';
    }

    /**
     * Verifica si una imagen existe en el directorio public/images
     *
     * @param string $imageName Nombre del archivo de imagen
     * @return bool
     */
    public static function imageExists($imageName)
    {
        return file_exists(public_path('images/' . $imageName));
    }

    /**
     * Obtiene la ruta completa de la imagen con validación
     *
     * @param string $imageName Nombre del archivo de imagen
     * @return string URL completa de la imagen
     */
    public static function getImageUrl($imageName)
    {
        if (self::imageExists($imageName)) {
            return asset('images/' . $imageName);
        }
        return asset('images/trabajador.jpg'); // Imagen por defecto
    }
}
