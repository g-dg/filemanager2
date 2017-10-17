<?php
namespace GarnetDG\FileManager2;

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

	protected static $log_file = '_log.txt';
	protected static $log_level = 6;

	public static function setUp()
	{
		self::$log_file = Config::get('log_file');
		self::$log_level = Config::get('log_level');
	}

	public static function log($level, $message)
	{
		if ($level <= self::$log_level) {
			$log_string = '[' . date('Y-m-d H:i:s') . '] ' . $pretty_level . ': ' . $message . ' ( ' . $_SERVER['REQUEST_URI'] . ' )\n';

			if (!$fh = fopen(self::$log_file, 'a')) {
				throw new Exception('Could not open log file!');
			}
			if (fwrite($fh, $log_string === FALSE)) {
				throw new Exception('Could not write to log file!');
			}
			fclose($fh);
		}
	}
}
