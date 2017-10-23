<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
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

	public static function emergency($message)
	{
		self::log(self::EMERG, $message);
	}
	public static function alert($message)
	{
		self::log(self::ALERT, $message);
	}
	public static function critical($message)
	{
		self::log(self::CRIT, $message);
	}
	public static function error($message)
	{
		self::log(self::ERR, $message);
	}
	public static function warning($message)
	{
		self::log(self::WARNING, $message);
	}
	public static function notice($message)
	{
		self::log(self::NOTICE, $message);
	}
	public static function info($message)
	{
		self::log(self::INFO, $message);
	}
	public static function debug($message)
	{
		self::log(self::DEBUG, $message);
	}

	public static function log($level, $message)
	{
		if ($level <= Config::get('log_level')) {
			switch ($level) {
				case self::EMERGE:
					$pretty_level = 'Emergency';
					break;
				case self::ALERT:
					$pretty_level = 'Alert';
					break;
				case self::CRIT:
					$pretty_level = 'Critical';
					break;
				case self::ERR:
					$pretty_level = 'Error';
					break;
				case self::WARNING:
					$pretty_level = 'Warning';
					break;
				case self::NOTICE:
					$pretty_level = 'Notice';
					break;
				case self::INFO:
					$pretty_level = 'Info';
					break;
				case self::DEBUG:
					$pretty_level = 'Debug';
					break;
				default:
					throw new \Exception('Invalid log level: ' . $level);
					break;
			}

			$log_string = '[' . date('Y-m-d H:i:s') . '] ' . $pretty_level . ': ' . $message . ' (' . $_SERVER['REQUEST_URI'] . ')' . PHP_EOL;
			
			if (!$fh = fopen(Config::get('log_file'), 'a')) {
				throw new \Exception('Could not open log file for writing');
			}
			if (fwrite($fh, $log_string) === FALSE) {
				throw new \Exception('Could not write to log file');
			}
			
			fclose($fh);
		}
	}
}
