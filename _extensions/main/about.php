<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

Router::registerPage('about', function($subpage) {
	switch ($subpage) {
		case '':
			MainUiTemplate::header('About');
			echo '
<h1>About</h1>
<p>Garnet DeGelder\'s File Manager ' . htmlspecialchars(GARNETDG_FILEMANAGER_VERSION) . ' at ' . htmlspecialchars($_SERVER['SERVER_NAME']) . '</p>

<hr />

<h1>License</h1>

<p>Click <a href="'.Router::getHtmlReadyUri('/about/license').'">here</a> for the full license text.</p>

<p>Garnet DeGelder\'s File Manager '.htmlspecialchars(GARNETDG_FILEMANAGER_VERSION).'</p>
<p>'.GARNETDG_FILEMANAGER_COPYRIGHT.'</p>

<p>This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.</p>

<p>This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.</p>

<p>You should have received a copy of the GNU Lesser General Public License
along with this program.  If not, see &lt;<a href="http://www.gnu.org/licenses/" target="_blank">http://www.gnu.org/licenses/</a>&gt;.</p>

<hr />
';

			Hooks::exec('_main.about.post_license');

			MainUiTemplate::footer();
			break;


		case 'license':
			MainUiTemplate::header('License');
			echo '<a href="'.Router::getHtmlReadyUri('/about').'">&lt; Back</a>'.PHP_EOL;
			$license_text = @file_get_contents('_license.txt');
			if ($license_text !== false) {
				echo '<pre>'.htmlspecialchars($license_text).'</pre>';
			} else {
				echo '<em><strong>Error!</strong> The license file could not be found!</em>';
			}
			MainUiTemplate::footer();
			break;


		default:
			Router::execErrorPage(404);
			break;
	}
});
