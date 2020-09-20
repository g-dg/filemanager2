<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class HttpRange
{
	public static function send($full_path, $force_download = false)
	{
		$has_32bit = PHP_INT_MAX == 2147483647;

		if ($has_32bit && !function_exists('bcadd')) {
			Log::error('Cannot send file on 32-bit system without BC Math extension');
			return false;
		}

		set_time_limit(0);
		if (Filesystem::is_file($full_path) && Filesystem::is_readable($full_path)) {
			if ($has_32bit)
				$content_length = Filesystem::filesize_actual($full_path);
			else
				$content_length = Filesystem::filesize($full_path);
			$content_disposition = basename($full_path);
			$content_type = Filesystem::getContentType($full_path);

			if (ini_get('zlib.output_compression')) {
				ini_set('zlib.output_compression', 'Off');
			}

			ob_end_clean();

			header('Accept-Ranges: bytes');
			header('Content-Type: ' . $content_type);
			if ($force_download) {
				header('Content-Disposition: attachment; filename="' . $content_disposition . '"');
			}
			
			if (isset($_SERVER['HTTP_RANGE'])) {
				list($start, $end) = explode("-", explode(",", explode("=", $_SERVER['HTTP_RANGE'], 2)[1], 2)[0]);
				if ($end == '') {
					if ($has_32bit)
						$end = bcsub($content_length, 1);
					else
						$end = $content_length - 1;
				} else {
					if (!$has_32bit)
						$end = (int)$end;
				}
				if ($has_32bit)
					$send_length = bcsub($end, bcadd($start, 1));
				else
					$send_length = $end - (int)$start + 1;

				if ($has_32bit)
					$invalid_request_range = bccomp($start, $content_length) == 1 || bccomp($end, $content_length) == 1;
				else
					$invalid_request_range = $start > $content_length || $end > $content_length;
				if ($invalid_request_range) {
					Log::warning('Invalid requested range (start: ' . $start . ', end: ' . $end . ', filesize: ' . $content_length . ')');
					http_response_code(416); // Requested Range Not Satisfiable
					header('Content-Length: ' . $content_length);
					exit();
				}

				http_response_code(206); // Partial Length
				header('Content-Length: ' . $send_length);
				header('Content-Range: bytes ' . $start . '-' . $end . '/' . $content_length);
			} else {
				header('Content-Length: ' . $content_length);
				$send_length = $content_length;
			}

			// send the file
			if ($fd = Filesystem::fopen($full_path, 'rb')) {
				if (isset($start)) {
					if ($has_32bit) {
						if (bccomp($start, 2147483647 - 1) == 1) { // if start is larger than max
							fseek($fd, 2147483647 - 1); // jump to just before max
							$seeking_remaining = bcsub($start, 2147483647 - 1); // calculate seeking remaining
							$mb_blocks_remaining = bcdiv($seeking_remaining, 1024 * 1024, 0); // calculate how many 1MB blocks to jump
							$kb_blocks_remaining = bcdiv(bcsub($seeking_remaining, bcmul($mb_blocks_remaining, 1024 * 1024)), 1024, 0); // calculate how many 1KB blocks to jump
							$bytes_remaining = bcsub($seeking_remaining, bcadd(bcmul($mb_blocks_remaining, 1024 * 1024), bcmul($kb_blocks_remaining, 1024))); // calculate remainder to jump after jumping blocks
							Log::notice("HTTP range: " . $_SERVER['HTTP_RANGE'] . ", filesize: " . $content_length . ", requested start: " . $start . ", send length: " . $send_length . ", seeked to 2147483646, seek remaining: " . $seeking_remaining . ", mb remaining: " . $mb_blocks_remaining . ", kb remaining: " . $kb_blocks_remaining . ", bytes remaining: " . $bytes_remaining);
							for ($i = 0; $i < $mb_blocks_remaining; $i++) { // jump in 1M blocks
								fread($fd, 1024 * 1024);
							}
							for ($i = 0; $i < $kb_blocks_remaining; $i++) { // jump in 1K blocks
								fread($fd, 1024);
							}
							for ($i = 0; $i < $bytes_remaining; $i++) { // jump in bytes
								fread($fd, 1);
							}
						} else {
							fseek($fd, (int)$start);
						}
					} else {
						fseek($fd, $start);
					}
				}

				$sent = 0;
				$buffersize = (int)GlobalSettings::get('_httprange.buffersize', 4096);
				while (!feof($fd) && !connection_aborted() && ($has_32bit ? bccomp($sent, $send_length) == -1 : $sent < $send_length)) {
					$buffer = fread($fd, $buffersize);
					echo $buffer;
					flush();
					if ($has_32bit)
						$sent = bcadd($sent, strlen($buffer));
					else
						$sent += strlen($buffer);
				}

				fclose($fd);
			} else {
				Log::notice('Could not open file "' . $full_path . '" to send to "' . Auth::getCurrentUserName() . '"');
				http_response_code(404);
				exit();
			}
		} else {
			Log::notice('Could not send file "' . $full_path . '" to "' . Auth::getCurrentUserName() . '", possibly they don\'t have the necessary permissions, or the file does not exist');
			http_response_code(404);
			exit();
		}
	}
}
