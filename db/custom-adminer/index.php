<?php

use Adminer\AdminerPlugin;
// Desactivar la visualización de los errores en pantalla
error_reporting(0);  // Desactiva todos los errores

// Alternativamente, puedes configurar solo los warnings
// error_reporting(E_ALL & ~E_WARNING);  // Desactiva solo los warnings

ini_set('display_errors', 0);  // No mostrar errores en la pantalla

require_once __DIR__ . "/plugins/AdminerPlugin.php";
require_once __DIR__ . "/adminer.php";

function adminer_object()
{



    // Mapeo manual de archivos a nombres de clases
    $pluginFiles = [
        "struct-comments.php" => "AdminerStructComments",
        "AdminerShowColumnComment.php" => "AdminerShowColumnComment",
        "slugify.php" => "AdminerSlugify",
        //"tables-filter.php" => "AdminerTablesFilter",
        "AdminerDumpXlsx.php" => 'AdminerDumpXlsx',
        //"table-row-numbers.php" => "AdminerTableRowNumbers",
        "sql-gemini.php" => "AdminerSqlGemini",
        "email-table.php" => "AdminerEmailTable",
        "table-structure.php" => "AdminerTableStructure",

    ];

    $plugins = [];

    error_log("Adminer object function is being executed");

    foreach ($pluginFiles as $file => $class) {
        $filePath = "./plugins/" . $file;

        if (file_exists($filePath)) {
            require_once $filePath;

            if (class_exists($class)) {
                $plugins[] = new $class();
            } else {
                error_log("❌ ERROR: La clase <strong>$class</strong> no está definida en <strong>$file</strong>.");
            }
        } else {
            error_log("❌ ERROR: No se encontró el archivo del plugin <strong>$file</strong>.");
        }
    }



    class AdminerCustom extends AdminerPlugin
    {


        function head()
        {
            echo '<link rel="stylesheet" type="text/css" href="adminer.css">';

            echo '<style>
    #form > p:nth-child(1) > select {
        padding: 8px 14px;
        font-size: 15px;
        border: 1px solid #aaa;
        border-radius: 8px;
        background-color: #fff;
        color: #333;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        width: 80%;
        min-width: 350px;
        max-width: 250px;
        height: auto;
    }

    #form > p:nth-child(1) {
        max-width: 100%;
        margin-bottom: 1rem;
    }

    /* Cuando se enfoca, resalta */
    #form > p:nth-child(1) > select:focus {
        border-color: #7a57d1;
        box-shadow: 0 0 8px rgba(122, 87, 209, 0.4);
    }
</style>';
            // Estilos en línea para mejorar la consola sin ocultarla
            echo '<style>
                /* Estilo general para la consola SQL */
                #sql-command,
                textarea[name="query"] {
                    background: #2d2d2d;
                    color: #ccc;
                    font-family: "Fira Code", "Courier New", monospace;
                    padding: 12px;
                    border-radius: 6px;
                    box-shadow: inset 0 0 8px rgba(0,0,0,0.5);
                    max-height: 400px;
                    overflow-y: auto;
                    margin-bottom: 1em;
                    width: 100%;
                    line-height: 1.6;
                    font-size: 14px;
                }
        
                /* Título o encabezado en el área de consola */
                #sql-command:before,
                textarea[name="query"]:before {
                    content: "SQL Console";
                    display: block;
                    font-weight: bold;
                    color: #fff;
                    margin-bottom: 8px;
                }
        
                /* Aumentar la visibilidad y el tamaño del textarea de consulta SQL */
                textarea.sqlarea {
                    width: 100%;
                    height: 250px;  /* Mayor altura para hacer más visible */
                    background: #1e1e1e;
                    color: #fff;
                    border: 1px solid #444;
                    border-radius: 4px;
                    padding: 8px;
                    font-family: "Fira Code", "Courier New", monospace;
                    font-size: 14px;
                    box-sizing: border-box;
                    resize: vertical;
                    margin-bottom: 8px;
                    overflow-y: auto;
                }
        
                /* Estilo al enfocar el textarea */
                textarea.sqlarea:focus {
                    border-color: #5faeff;
                    outline: none;
                }
        
                /* Estilo para los botones */
                .console input[type="submit"],
                .console input[type="button"] {
                    background: #5faeff;
                    border: 1px solid #4c9cff;
                    color: #fff;
                    padding: 6px 12px;
                    font-size: 13px;
                    border-radius: 4px;
                    cursor: pointer;
                    transition: background-color 0.2s;
                    margin-right: 6px;
                }
                .console input[type="submit"]:hover,
                .console input[type="button"]:hover {
                    background: #4c9cff;
                }
                .console input[type="submit"]:active,
                .console input[type="button"]:active {
                    background: #3a88d1;
                }
        
                /* Scrollbar personalizado */
                #sql-command::-webkit-scrollbar,
                textarea[name="query"]::-webkit-scrollbar {
                    width: 6px;
                }
                #sql-command::-webkit-scrollbar-thumb,
                textarea[name="query"]::-webkit-scrollbar-thumb {
                    background: rgba(255,255,255,0.2);
                    border-radius: 3px;
                }
        
                /* Mejorar el espacio visible de las tablas */
                .tables-list {
                    background: #333;
                    color: #fff;
                    padding: 15px;
                    border-radius: 5px;
                    margin-top: 15px;
                    max-height: 300px;
                    overflow-y: scroll;
                    font-family: "Courier New", monospace;
                    font-size: 13px;
                }
        
                .tables-list pre {
                    margin: 0;
                    line-height: 1.5;
                    font-size: 14px;
                    white-space: pre-wrap;
                    word-wrap: break-word;
                }
        
                /* Resaltar palabras clave de SQL */
                textarea.sqlarea {
                    color: #ccc;
                }
        
                .sql-keywords {
                    font-weight: bold;
                    color: #ff5c5c;
                }
        
            </style>';

            parent::head();
        }


        // Evita la llamada a `create_sql()` redefiniendo `dumpTable()`
        function dumpTable($table, $style, $is_view = 0)
        {
            echo "-- Exportación de la tabla `$table`.\n";
        }


        function csp()
        {
            return "";
        }

    }

    return new AdminerCustom($plugins);



}

?>