<?php

/** Show comments of sql structure in more places (mainly where you edit things)
 * @link https://www.adminer.org/plugins/#use
 * @author Adam Kuśmierz, http://kusmierz.be/
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */
class AdminerStructComments
{

	function fieldName(&$field, $order = 0)
	{
		// Usar htmlspecialchars en lugar de h()
		return '<span title="' . htmlspecialchars($field["full_type"]) .
			(!empty($field["comment"]) ? ': ' . htmlspecialchars($field["comment"]) : '') . '">' .
			htmlspecialchars($field["field"]) . '</span>';
	}
}