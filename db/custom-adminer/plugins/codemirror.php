<?php

/** Use CodeMirror for SQL query */
class AdminerCodeMirror
{
    function head()
    {
        ?>
        <script src="plugins/codemirror/codemirror.min.js"></script>
        <script src="plugins/codemirror/sql.min.js"></script>
        <script src="plugins/codemirror/show-hint.min.js"></script>
        <script src="plugins/codemirror/sql-hint.min.js"></script>
        <link rel="stylesheet" type="text/css" href="plugins/codemirror/codemirror.css">
        <link rel="stylesheet" type="text/css" href="plugins/codemirror/show-hint.css">
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var ta = document.querySelector("textarea[name='sql']");
                if (ta) {
                    var editor = CodeMirror.fromTextArea(ta, {
                        mode: "text/x-sql",
                        lineNumbers: true,
                        matchBrackets: true,
                        indentWithTabs: true,
                        smartIndent: true,
                        extraKeys: {
                            "Ctrl-Space": "autocomplete"
                        },
                        hintOptions: {
                            tables: []
                        }
                    });

                    editor.on("inputRead", function (editor, change) {
                        if (change.text[0] === ".") {
                            editor.showHint({
                                completeSingle: false
                            });
                        }
                    });
                }
            });
        </script>

        <?php
    }
}