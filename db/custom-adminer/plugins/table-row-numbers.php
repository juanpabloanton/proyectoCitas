<?php

class AdminerTableRowNumbers {

    function head() {
        echo "<style>
            .tblrn { text-align: right; padding-right: 0.5em; color: #999; font-weight: bold; }
        </style>";

        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                let tables = document.querySelectorAll('table');

                tables.forEach((table) => {
                    // Verifica si la tabla es de resultados de consulta (excluye otras tablas como claves externas)
                    if (table.querySelector('thead') && table.querySelector('tbody') && table.rows.length > 1) {
                        let headerRow = table.querySelector('thead tr');
                        let bodyRows = table.querySelectorAll('tbody tr');

                        // Agregar encabezado solo si no existe
                        if (!headerRow.querySelector('.tblrn')) {
                            let th = document.createElement('th');
                            th.textContent = '#';
                            th.classList.add('tblrn');
                            headerRow.insertBefore(th, headerRow.firstChild);
                        }

                        // Agregar numeración a las filas del cuerpo
                        bodyRows.forEach((row, index) => {
                            let firstCell = document.createElement('td');
                            firstCell.textContent = index + 1;
                            firstCell.classList.add('tblrn');
                            row.insertBefore(firstCell, row.firstChild);
                        });
                    }
                });
            });
        </script>";
    }

    function messageQuery(&$query, $time) {
        // Aplica SOLO si es una consulta SELECT y no tiene ya ROW_NUMBER()
        if (stripos(trim($query), "SELECT") === 0 && stripos($query, " ROW_NUMBER() ") === false) {
            // Agrega ROW_NUMBER solo si la consulta sigue el formato "SELECT ..."
            if (preg_match('/^SELECT\s+(DISTINCT\s+)?/i', $query, $matches)) {
                $query = preg_replace('/^SELECT\s+/i', 'SELECT ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) AS RowNum, ', $query, 1);
            }
        }
    }

    function selectColumnsPrint($columns) {
        echo "<th class='tblrn'></th>\n";
    }

    function selectRowPrint($row, $columns) {
        if (isset($row['RowNum'])) {
            echo "<td class='tblrn'>" . $row['RowNum'] . "</td>\n";
        } else {
            static $row_number = 0;
            $row_number++;
            echo "<td class='tblrn'>{$row_number}</td>\n";
        }
    }
}