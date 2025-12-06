<?php
class AdminerAutocomplete {
    function head() {
        ?>
        <!-- Incluir CodeMirror -->
        <link rel="stylesheet" href="./plugins/codemirror/codemirror.min.css">
        <link rel="stylesheet" href="./plugins/codemirror/material.min.css">
        <link rel="stylesheet" href="./plugins/codemirror/show-hint.min.css">
        <script src="./plugins/codemirror/codemirror.min.js"></script>
        <script src="./plugins/codemirror/sql.min.js"></script>
        <script src="./plugins/codemirror/show-hint.min.js"></script>
        <script src="./plugins/codemirror/sql-hint.min.js"></script>

        <?php
        // Conectar a la base de datos y obtener tablas y columnas
        $tables = [];

        $mysqli = connection(); // Conexión a la BD en Adminer
        if ($mysqli) {
            // Intentar obtener nombres de tablas
            $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_CATALOG = DB_NAME()";
            $result = $mysqli->query($sql);

            if (!$result) {
                error_log("Error en la consulta SQL: " . $mysqli->error);
            } else {
                while ($row = $result->fetch_assoc()) {
                    $tables[$row["TABLE_NAME"]] = [];
                }

                // Obtener las columnas de cada tabla
                foreach ($tables as $table => &$columns) {
                    $colSql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table'";
                    $colResult = $mysqli->query($colSql);

                    if (!$colResult) {
                        error_log("Error obteniendo columnas de $table: " . $mysqli->error);
                    } else {
                        while ($colRow = $colResult->fetch_assoc()) {
                            $columns[] = $colRow["COLUMN_NAME"];
                        }
                    }
                }
            }
        } else {
            error_log("Error: No se pudo establecer conexión a la base de datos.");
        }
        ?>

        <script <?php echo nonce(); ?>>
        document.addEventListener('DOMContentLoaded', function() {
            let textarea = document.querySelector('textarea[name="query"]');
            if (textarea) {
                let editor = CodeMirror.fromTextArea(textarea, {
                    mode: "text/x-mssql",
                    theme: "material",
                    lineNumbers: true,
                    extraKeys: { "Ctrl-Space": "autocomplete" },
                    hintOptions: { tables: <?php echo json_encode($tables); ?> }
                });

                // "Parche" para redirigir focus al editor
                textarea.focus = function() {
                    editor.focus();
                };

                // Autocompletar con espacio o punto
                editor.on("inputRead", function(instance, event) {
                    if (event.text[0] === " " || event.text[0] === ".") {
                        instance.showHint();
                    }
                });
            }
        });
        </script>
        <?php
    }
}
?>

