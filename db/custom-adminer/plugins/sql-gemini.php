<?php

/** AI prompt in SQL command generating the queries with Google Gemini
 * Beware that this sends your whole database structure (not data) to Google Gemini.
 * @link https://gemini.google.com/
 * @link https://www.adminer.org/plugins/#use
 * @author Jakub Vrana, https://www.vrana.cz/
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */
class AdminerSqlGemini extends Adminer\Plugin
{
	private $apiKey;
	private $model;

	/**
	 * @param string $apiKey The default key is shared with all users and may run out of quota; get your own API key at: https://aistudio.google.com/apikey
	 * @param string $model Available models: https://ai.google.dev/gemini-api/docs/models#available-models
	 */
	function __construct($apiKey = 'AIzaSyDWDbPjmvH9_hphsnY_yJGdue42qRMG3do', $model = "gemini-2.0-flash")
	{
		$this->apiKey = $apiKey;
		$this->model = $model;
	}

	function headers()
	{
		if (isset($_POST["gemini"]) && !isset($_POST["query"])) {
			$prompt = "I have a " . Adminer\get_driver(Adminer\DRIVER) . " database with this structure:\n\n";
			foreach (Adminer\tables_list() as $table => $type) {
				$prompt .= Adminer\create_sql($table, false, "CREATE") . ";\n\n";
			}
			$prompt .= "Prefer returning relevant columns including primary key.\n\n";
			$prompt .= "Give me this SQL query and nothing else:\n\n$_POST[gemini]\n\n";
			$context = stream_context_create(array(
				"http" => array(
					"method" => "POST",
					"header" => array("User-Agent: AdminerSqlGemini/" . Adminer\VERSION, "Content-Type: application/json"),
					"content" => '{"contents": [{"parts":[{"text": ' . json_encode($prompt) . '}]}]}',
					"ignore_errors" => true,
				)
			));
			$response = json_decode(file_get_contents("https://generativelanguage.googleapis.com/v1beta/models/$this->model:generateContent?key=$this->apiKey", false, $context));
			if (isset($response->error)) {
				echo "-- " . $response->error->message;
			} else {
				$text = $response->candidates[0]->content->parts[0]->text;
				$text = preg_replace('~(\n|^)```sql\n(.+)\n```(\n|$)~sU', "*/\n\n\\2\n\n/*", "/*\n$text*/\n");
				echo preg_replace('~/\*\s*\*/\n*~', '', $text);
			}
			exit;
		}
	}

	function sqlPrintAfter()
	{
		echo "<p><textarea name='gemini' rows='5' cols='50' placeholder='" . $this->lang('Gemini') . "' class='gemini-textarea'>" . Adminer\h($_POST["gemini"]) . "</textarea>\n";
		?>
		<p><input type='button' value='🧠 Gemini' id="gemini-button">
			<script <?php echo Adminer\nonce(); ?>>
				const geminiText = qsl('textarea');
				const geminiButton = qsl('input');

				function setSqlareaValue(value) {
					const sqlarea = qs('textarea.sqlarea');
					sqlarea.value = value;
					sqlarea.onchange && sqlarea.onchange();
				}

				geminiButton.onclick = () => {
					setSqlareaValue('-- <?php echo $this->lang('Just a sec...'); ?>');
					ajax(
						'',
						req => setSqlareaValue(req.responseText),
						'gemini=' + encodeURIComponent(geminiText.value)
					);
				};

				geminiText.onfocus = event => {
					alterClass(findDefaultSubmit(geminiText), 'default');
					alterClass(geminiButton, 'default', true);
					event.stopImmediatePropagation();
				};

				geminiText.onblur = () => {
					alterClass(geminiButton, 'default');
				};

				geminiText.onkeydown = event => {
					if (isCtrl(event) && (event.keyCode == 13 || event.keyCode == 10)) {
						geminiButton.onclick();
						event.stopPropagation();
					}
				};
			</script>


			<style>
				/* --- Textarea: igual que antes --- */
				.gemini-textarea {
					width: 100%;
					padding: 10px;
					font-size: 14px;
					border: 1px solid #ccc;
					border-radius: 5px;
					box-sizing: border-box;
					background-color: #f4f4f4;
					transition: border-color 0.3s ease-in-out;
				}

				.gemini-textarea:focus {
					border-color: #007bff;
					background-color: #fff;
				}

				/* --- Botón Gemini igual a 'Ejecutar' --- */
				#gemini-button {
					background-color: #5faeff;
					/* mismo azul que Ejecutar */
					border: 1px solid #4c9cff;
					/* borde ligeramente más oscuro */
					color: white;
					/* texto blanco */
					padding: 2px 8px;
					/* mismo acolchado que Ejecutar */
					font-size: 12px;
					/* mismo tamaño de fuente */
					line-height: 1.4;
					/* similar al botón de Adminer */
					border-radius: 3px;
					/* misma curvatura */
					cursor: pointer;
					transition: background-color 0.2s ease-in-out;
				}

				#gemini-button:hover {
					background-color: #4c9cff;
					/* hover idéntico a Ejecutar */
				}

				#gemini-button:active {
					background-color: #3a88d1;
					/* estado presionado */
				}

				/* Contenedor Flex */
				.form-container {
					display: flex;
					flex-direction: row;
					align-items: center;
					gap: 8px;
					/* separación igual a la del layout de Adminer */
					width: 100%;
				}
			</style>

			<?php
	}

	function screenshot()
	{
		return "https://www.adminer.org/static/plugins/sql-gemini.gif";
	}

	// use the phrases from https://gemini.google.com/
	protected $translations = array(
		'cs' => array(
			'' => 'Generování SQL příkazů pomocí umělé inteligence Google Gemini',
			'Gemini' => 'Zeptat se Gemini',
			'Just a sec...' => 'Chviličku...',
		),
		'pl' => array(
			'Gemini' => 'Zapytaj Gemini',
			'Just a sec...' => 'Chwileczkę...',
		),
		'de' => array(
			'' => 'KI-Eingabeaufforderung im SQL-Befehl zur Erstellung der Abfragen mit Google Gemini',
			'Gemini' => 'Gemini fragen',
			'Just a sec...' => 'Einen Moment...',
		),
		'ja' => array(
			'' => 'Google Gemini AI を用いて SQL 文を生成',
			'Gemini' => 'Gemini に聞く',
			'Just a sec...' => 'しばらくお待ち下さい...',
		),
	);
}

?>