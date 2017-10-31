<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class HttpRange
{
	function send($full_path, $force_download = false)
	{
		set_time_limit(0);
		if (Filesystem::is_file($full_path) && Filesystem::is_readable($full_path)) {
			$content_length = Filesystem::filesize($full_path);
			$content_disposition = basename($full_path);
			$content_type = Filesystem::getMimeType($full_path);

			if (ini_get('zlib.output_compression')) {
				ini_set('zlib.output_compression', 'Off');
			}

			ob_end_clean();

			header('Accept-Ranges: bytes');
			header('Content-Type: ' . $content_type);
			if ($force_download) {
				header('Content-Disposition: attachment; filename="' . $content_disposition . '"');
			}
			
			// if a partial download is requested
			if (isset($_SERVER['HTTP_RANGE'])) {
				$range_params = explode("-", explode(",", explode("=", $_SERVER['HTTP_RANGE'], 2)[1], 2)[0]);
				$start = (int)$range_params[0];
				if (isset($range_params[1])) {
					$end = (int)$range_params[1];
				} else {
					$end = $content_length - 1;
				}
				$send_length = $end - $start + 1;
				header('HTTP/1.1 206 Partial Content');
				header('Content-Length: ' . $send_length);
				header('Content-Range: bytes ' . $start . '-' . $end . '/' . $content_length);
			} else {
				header('Content-Length: ' . $content_length);
				$send_length = $content_length;
			}

			// send the file
			if ($fd = Filesystem::fopen($full_path, 'rb')) {
				if (isset($start)) {
					fseek($file, $start);
				}

				$sent = 0;
				while (!feof($file) && !connection_aborted() && $sent < $send_length) {
					$buffer = fread($file, 4096);
					echo $buffer;
					flush();
					$sent += strlen($buffer);
				}

				fclose($fd);
			} else {
				Log::error('Could not open file "' . $full_path . '" to send to "' . Auth::getCurrentUserName() . '"');
				http_response_code(404);
				exit();
			}
		} else {
			Log::error('Could not send file "' . $full_path . '" to "' . Auth::getCurrentUserName() . '", possibly they don\'t have the necessary permissions');
			http_response_code(404);
			exit();
		}
	}
}
