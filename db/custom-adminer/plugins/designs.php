<?php

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

function verify_token()
{
	return isset($_POST['token']) && isset($_SESSION['token']) && hash_equals($_SESSION['token'], $_POST['token']);
}

/** Allow switching designs */
class AdminerDesigns
{
	var $designs;

	function __construct()
	{
		$this->designs = [
			//"https://raw.githubusercontent.com/vrana/adminer/master/designs/default.css" => "Default",
			//"https://raw.githubusercontent.com/vrana/adminer/master/designs/nice.css" => "Nice",
			//"https://raw.githubusercontent.com/vrana/adminer/master/designs/flat.css" => "Flat",
			//"https://raw.githubusercontent.com/vrana/adminer/master/designs/light.css" => "Light",
			"https://github.com/vrana/adminer/blob/master/designs/flat/adminer.css" => "Dark"
		];
	}

	function headers()
	{
		if (isset($_POST["design"]) && verify_token()) {
			$_SESSION["design"] = $_POST["design"];
			header("Location: " . $_SERVER["REQUEST_URI"]);
			exit;
		}
	}

	function css()
	{
		$return = array();
		if (isset($_SESSION["design"]) && array_key_exists($_SESSION["design"], $this->designs)) {
			$return[] = $_SESSION["design"];
		}
		return $return;
	}

	function navigation($missing)
	{
		echo "<form action='' method='post' style='position: fixed; bottom: .5em; right: .5em;'>";
		echo "<select name='design' onchange='this.form.submit();'>";
		echo "<option value=''> (design) </option>";
		foreach ($this->designs as $url => $name) {
			$selected = ($url == ($_SESSION["design"] ?? '')) ? "selected" : "";
			echo "<option value='$url' $selected>$name</option>";
		}
		echo "</select>";

		if (!isset($_SESSION['token'])) {
			$_SESSION['token'] = bin2hex(random_bytes(32));
		}

		echo '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
		echo "</form>\n";
	}
}