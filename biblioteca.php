<?php

class Libro {
    public $nombre;
    private $autor;
    private $categoria;
    private $disponible;
    private $eliminado;
    static $libros = [];

    public function __construct($nombre, $autor, $categoria, $disponible = true, $eliminado = false) {
        $this->nombre = $nombre;
        $this->autor = $autor;
        $this->categoria = $categoria;
        $this->disponible = $disponible;
        $this->eliminado = $eliminado;
    }

    public function getAutor() {
        return $this->autor;
    }

    public function getCategoria() {
        return $this->categoria;
    }

    public function isDisponible() {
        return $this->disponible;
    }

    public function isEliminado() {
        return $this->eliminado;
    }

   
    static function agregarLibro($libro){
        array_push(self::$libros, $libro);
    }

  //Inicializamos la biblioteca con algunos libros predefinidos
    public static function inicializarLibros() {
        self::agregarLibro(new Libro("Las Bratz", "Maria Lopez", "entretenimiento", false));
        self::agregarLibro(new Libro("Barbies", "Ruth Handler", "entretenimiento", true));
        self::agregarLibro(new Libro("Harry Potter", "Rowling", "magia", true));
        self::agregarLibro(new Libro("El Señor de los Anillos", "Alex", "aventura", true));
    }

    public static function buscarLibro($criterio) {
        $criterio = strtolower($criterio);
        $resultados = array_filter(self::$libros, function($libro) use ($criterio) {
            return strpos(strtolower($libro->nombre), $criterio) !== false ||
                   strpos(strtolower($libro->autor), $criterio) !== false ||
                   strpos(strtolower($libro->categoria), $criterio) !== false;
        });
        return $resultados;
    }

    public static function eliminarLibro($criterio) {
        foreach (self::$libros as $libro) {
            if (strpos(strtolower($libro->nombre), strtolower($criterio)) !== false) {
                $libro->eliminado = true; // Marcar el libro como eliminado
            }
        }
    }


    public static function editarLibro($criterio, $nuevoNombre, $nuevoAutor, $nuevaCategoria, $nuevaDisponibilidad) {
        $libroEditado = false;
        foreach (self::$libros as $libro) {
            if (strpos(strtolower($libro->nombre), strtolower($criterio)) !== false) {
                if ($libro->isEliminado()) {
                    echo "El libro '{$libro->nombre}' está eliminado y no puede ser editado.\n";
                    return false;
                }
                $libro->nombre = $nuevoNombre;
                $libro->autor = $nuevoAutor;
                $libro->categoria = $nuevaCategoria;
                $libro->disponible = $nuevaDisponibilidad;
                $libroEditado = true;
                break;
            }
        }
        
        if ($libroEditado) {
            echo "Libro editado exitosamente.\n";
            echo "Libros disponibles:\n";
            foreach (self::$libros as $libro) {
                if ($libro->isDisponible() && !$libro->isEliminado()) {
                    echo "Título: " . $libro->nombre . " - Autor: " . $libro->getAutor() . " - Categoria: " . $libro->getCategoria() . "\n";
                }
            }
        } else {
            echo "No se encontró el libro para editar.\n";
        }
        return $libroEditado;
    }


    public static function prestarLibro($criterio) {
        foreach (self::$libros as $libro) {
            if (strpos(strtolower($libro->nombre), strtolower($criterio)) !== false) {
                if ($libro->isEliminado()) {
                    echo "El libro '{$libro->nombre}' está eliminado y no puede ser prestado.\n";
                    return false;
                }
                if (!$libro->isDisponible()) {
                    echo "El libro '{$libro->nombre}' no está disponible para préstamo.\n";
                    return false;
                }
                $libro->disponible = false;
                echo "El libro '{$libro->nombre}' ha sido prestado exitosamente.\n";
                return true;
            }
        }
        echo "No se encontró el libro para prestar.\n";
        return false;
    }
}

// Función para mostrar el menú y capturar la elección del usuario
function mostrarMenu() {
    echo "Menú:\n";
    echo "1. Agregar libro\n";
    echo "2. Buscar libro\n";
    echo "3. Editar libro\n";
    echo "4. Eliminar libro\n";
    echo "5. Prestar libro\n"; 
    echo "6. Salir\n";
    $opcion = readline("Seleccione una opción: ");
    return $opcion;
}

// Inicializar libros
Libro::inicializarLibros();

// Bucle para mostrar el menú y ejecutar la opción seleccionada
do {
    $opcion = mostrarMenu();

    switch ($opcion) {
        case 1:
            $titulo = readline("Ingrese el título: ");
            $autor = readline("Ingrese el nombre del autor: ");
            $categoria = readline("Ingrese la categoria: ");
            $disponible = readline("Ingrese si está disponible (si/no): ") === 'si';
            $libro = new Libro($titulo, $autor, $categoria, $disponible);
            Libro::agregarLibro($libro);
            echo "Libro agregado exitosamente.\n";
            break;

        case 2:
            $criterio = readline("Ingrese el criterio de búsqueda: ");
            $resultados = Libro::buscarLibro($criterio);
            if (count($resultados) > 0) {
                echo "Libros encontrados:\n";
                foreach ($resultados as $libro) {
                    if (!$libro->isEliminado()) {
                        echo "Título: " . $libro->nombre . " - Autor: " . $libro->getAutor() . " - Categoria: " . $libro->getCategoria() . " - Disponible: " . ($libro->isDisponible() ? 'Sí' : 'No') . "\n";
                    }
                }
            } else {
                echo "No se encontraron libros.\n";
            }
            break;

        case 3:
            $criterioEditar = readline("Ingrese el título del libro que desea editar: ");
            // Verificar si el libro está eliminado antes de solicitar más información
            $libroEncontrado = false;
            foreach (Libro::$libros as $libro) {
                if (strpos(strtolower($libro->nombre), strtolower($criterioEditar)) !== false) {
                    $libroEncontrado = true;
                    if ($libro->isEliminado()) {
                        echo "El libro '{$libro->nombre}' está eliminado y no puede ser editado.\n";
                        break;
                    }
                    $nuevoNombre = readline("Ingrese el nuevo título: ");
                    $nuevoAutor = readline("Ingrese el nuevo autor: ");
                    $nuevaCategoria = readline("Ingrese la nueva categoría: ");
                    $nuevoDisponible = readline("Ingrese si está disponible (si/no): ") === 'si';
                    if (Libro::editarLibro($criterioEditar, $nuevoNombre, $nuevoAutor, $nuevaCategoria, $nuevoDisponible)) {
                        echo "Libro editado exitosamente.\n";
                    } else {
                        echo "No se encontró el libro para editar.\n";
                    }
                    break;
                }
            }
            if (!$libroEncontrado) {
                echo "No se encontró el libro para editar.\n";
            }
            break;

        case 4:
            $criterioEliminar = readline("Ingrese el título del libro que desea eliminar: ");
            Libro::eliminarLibro($criterioEliminar);
            echo "Libro eliminado exitosamente.\n";
            break;

        case 5:
            $criterioPrestar = readline("Ingrese el título del libro que desea prestar: ");
            Libro::prestarLibro($criterioPrestar);
            break;

        case 6:
            echo "Saliendo del sistema.\n";
            break;

        default:
            echo "Opción no válida. Intente nuevamente.\n";
            break;
    }
    
} while ($opcion != 6);

?>
