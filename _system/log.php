<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER2_VERSION')) {
	http_response_code(403);
	die();
}

class Log
{
	const EMERG = 0;
	const ALERT = 1;
	const CRIT = 2;
	const ERR = 3;
	const WARNING = 4;
	const NOTICE = 5;
	const INFO = 6;
	const DEBUG = 7;

	public static function log($level, $message)
	{
		if ($level <= Config::get('log_level')) {
			switch ($level) {
				case 0:
					$pretty_level = 'Emergency';
					break;
				case 1:
					$pretty_level = 'Alert';
					break;
				case 2:
					$pretty_level = 'Critical';
					break;
				case 3:
					$pretty_level = 'Error';
					break;
				case 4:
					$pretty_level = 'Warning';
					break;
				case 5:
					$pretty_level = 'Notice';
					break;
				case 6:
					$pretty_level = 'Info';
					break;
				case 7:
					$pretty_level = 'Debug';
					break;
				default:
					throw new \Exception('Invalid log level');
					break;
			}

			$log_string = '[' . date('Y-m-d H:i:s') . '] ' . $pretty_level . ': ' . $message . ' (' . $_SERVER['REQUEST_URI'] . ')' . PHP_EOL;
			
			if (!$fh = fopen(Config::get('log_file'), 'a')) {
				throw new \Exception('Could not open log file for writing!');
			}
			if (fwrite($fh, $log_string) === FALSE) {
				throw new \Exception('Could not write to log file!');
			}
			
			fclose($fh);
		}
	}
}
