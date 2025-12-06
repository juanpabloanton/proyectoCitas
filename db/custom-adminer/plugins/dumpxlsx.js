/**
 * Adminer plugin
 * Download select result as XLSX format.
 * 
 * Install AdminerDumpXlsx to Adminer,
 * and place this file to the plugin directory.
 *
 * Install to Adminer on http://www.adminer.org/plugins/
 * @author Tom Higuchi, http://tom-gs.com/
 */
(function (window, document) {
    /**
     * Create dummy table tag from select result.
     * 
     * @param {HTMLTableElement} tblElem 
     * @param {String} id 
     * @returns {String}
     */
    var createDummyTable = function (tblElem, id) {
        var trs = tblElem.querySelectorAll('tr');
        var html = '<table id="' + id + '" class="table-to-export" data-sheet-name="' + id + '">';
        trs.forEach(function (tr) {
            var ths = tr.querySelectorAll('th');
            var tds = tr.querySelectorAll('td');
            if (ths.length) {
                html += '<tr>';
                ths.forEach(function (th, index) {
                    html += '<th>' + getCellValue(th) + '</th>';
                });
                html += '</tr>';
            } else if (tds.length) {
                html += '<tr>';
                tds.forEach(function (td, index) {
                    if (skipFirstCell(tblElem, index)) {
                        return;
                    }
                    html += '<td>' + getCellValue(td) + '</td>';
                });
                html += '</tr>';
            }
        });
        html += '</table>';
        return html;
    };

    /**
     * Check if the skippable cell or not.
     * 
     * @param {HTMLTableElement} tblElem 
     * @param {Number} index 
     * @returns {Boolean}
     */
    var skipFirstCell = function (tblElem, index) {
        return 'table' == tblElem.id && 0 === index;
    };



    /**
     * Get plain value in cell. 
     * 
     * @param {HTMLTableElement} cell 
     * @returns {String}
     */
    var getCellValue = function (cell) {
        if ('th' == cell.tagName.toLowerCase()) {
            var ret = cell.id.replace(/th\[(.*)\]/, '$1');
            if (!ret) {
                var titleAttr = cell.getAttribute('title');  // Obtiene el atributo title
                if (titleAttr) {
                    ret = titleAttr.split('.').slice(-1)[0]; // Solo ejecuta split si title no es null
                } else {
                    ret = cell.innerText.trim(); // Usa el texto del encabezado como última opción
                }
            }
            return ret;
        } else if ('td' == cell.tagName.toLowerCase()) {
            var a = cell.querySelector('a');
            if (a) {
                return a.innerHTML;
            }
            return cell.innerHTML;
        }
    };


    /**
   * Add dump button with improved styling.
   *
   * @param {HTMLElement} parent, @param {Number} index
   */
    var addDumpButton = function (parent, index) {
        var id = 'xlsx-' + index;

        // Crear el botón con estilos refinados
        var button = document.createElement('button');
        button.type = 'button';
        button.id = id;
        button.textContent = 'Descargar XLSX';
        button.classList.add('xlsx-download-btn');

        button.addEventListener('click', function () {
            dumpXlsx();
        });

        parent.appendChild(button);
    };

    // Estilos CSS mejorados para un botón más acorde al diseño
    var style = document.createElement('style');
    style.textContent = `
    .xlsx-download-btn {
      background: #65ADC3;
      color: white;
      border: 1px solid #65ADC3;
      padding: 6px 12px;
      font-size: 14px;
      font-weight: 500;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.2s ease-in-out;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
  
    .xlsx-download-btn:hover {
      background: #0056b3;
      border-color: #004494;
    }
  
    .xlsx-download-btn:active {
      transform: scale(0.98);
    }
  `;
    document.head.appendChild(style);


function getTableNameFromURL() {
    let params = new URLSearchParams(window.location.search);
    let selectParam = params.get("select"); // Obtiene el valor de 'select'

    if (selectParam) {
        return selectParam.split(":")[0]; // Extrae el nombre de la tabla antes de ":"
    }
    return "tabla_desconocida"; // Si no se encuentra, usa un nombre genérico
}



/**
 * Create file name for download file.
 * 
 * @returns {String}
 */
var createFileName = function () {
    var tableElement = document.querySelector('table.table-to-export');
    
    // Obtener nombre de la tabla desde la URL
    var params = new URLSearchParams(window.location.search);
    var tableNameFromURL = params.get("select") ? params.get("select").split(":")[0] : null;

    // Obtener nombre de la tabla en este orden de prioridad
    var tableName = tableNameFromURL || // 1️⃣ Intentar desde URL (`select=tb_h_tipoPeriRegu`)
        (tableElement 
            ? tableElement.getAttribute('data-sheet-name') ||  
              tableElement.getAttribute('id') ||  
              tableElement.querySelector('caption')?.innerText || 
              tableElement.querySelector('th')?.innerText || 
              'mi_tabla'  
            : 'mi_tabla');

    // Eliminar espacios y caracteres especiales para evitar problemas en el nombre del archivo
    tableName = tableName.replace(/\s+/g, '_').replace(/[^\w-]/g, '');

    // Crear timestamp
    var date = new Date();
    var year = date.getFullYear();
    var month = ('0' + (date.getMonth() + 1)).slice(-2);
    var day = ('0' + date.getDate()).slice(-2);
    var hours = ('0' + date.getHours()).slice(-2);
    var minutes = ('0' + date.getMinutes()).slice(-2);
    var seconds = ('0' + date.getSeconds()).slice(-2);

    // Generar el nombre del archivo
    var fileName = `${tableName}_${year}_${month}_${day}_${hours}_${minutes}.xlsx`;

    return fileName;
};




    /**
     * Dump table data to XLSX.
     */

var dumpXlsx = function () {
  var wopts = {
    bookType: 'xlsx',
    bookSST: false,
    type: 'binary'
  };

  var workbook = { SheetNames: [], Sheets: {} };

  document.querySelectorAll('table.table-to-export').forEach(function (currentValue, index) {
    var tableName = currentValue.getAttribute('data-sheet-name') || 'Sheet' + index;
    workbook.SheetNames.push(tableName);
    workbook.Sheets[tableName] = XLSX.utils.table_to_sheet(currentValue, wopts);
  });

  var wbout = XLSX.write(workbook, wopts);
  var tableName = document.querySelector('table.table-to-export')?.getAttribute('data-sheet-name') || 'exported_data';
  saveAs(new Blob([s2ab(wbout)], { type: 'application/octet-stream' }), createFileName(tableName));
};

    /**
     * Convert string to ArrayBuffer.
     * 
     * @param {String} s 
     * @returns {ArrayBuffer}
     */
    var s2ab = function (s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i = 0; i != s.length; ++i) {
            view[i] = s.charCodeAt(i) & 0xFF;
        }
        return buf;
    };

    window.addEventListener('load', function () {
        var div = document.createElement('div');
        div.id = 'dummy-table-area';
        div.style.display = 'none';
        div.style.visibility = 'hidden';
        document.body.appendChild(div);

        var table = document.getElementById('table');
        if (table) {
            div.innerHTML += createDummyTable(table, 'table-0');
            addDumpButton(document.getElementById('fieldset-export'), 0);
        }

        for (var i = 1; ; i++) {
            var sql = document.getElementById('sql-' + i);
            if (!sql) {
                break;
            }
            var table = sql.nextElementSibling.querySelector('table');
            if (table) {
                div.innerHTML += createDummyTable(table, 'table-' + i);
                addDumpButton(document.getElementById('export-' + i), i);
            }
        }
    }, false);
})(window, window.document);
