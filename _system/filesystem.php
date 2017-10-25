<?php
namespace GarnetDG\FileManager;

if (!defined('GARNETDG_FILEMANAGER_VERSION')) {
	http_response_code(403);
	die();
}

class Filesystem
{
	// the paths to the public methods are share-relative paths ("/share/directory/file")

	public static function copy($source, $dest)
	{

	}
	public static function file_exists($filename)
	{

	}
	public static function file_get_contents($filename)
	{

	}
	public static function file_put_contents($filename, $data, $flags = 0)
	{

	}
	public static function fileatime($filename)
	{

	}
	public static function filemtime($filename)
	{

	}
	public static function filesize($filename)
	{

	}
	public static function filetype($filename)
	{

	}
	// The built-in php file functions can be used on the returned handle.
	// Does not check user's permissions
	public static function fopen($filename, $mode)
	{

	}
	public static function is_dir($filename)
	{

	}
	public static function is_file($filename)
	{

	}
	public static function is_readable($filename)
	{

	}
	public static function is_writable($filename)
	{

	}
	public static function mkdir($filename)
	{

	}
	public static function rename($oldname, $newname)
	{

	}
	public static function rmdir($dirname)
	{

	}
	public static function touch($filename)
	{

	}
	public static function unlink($filename)
	{

	}

	// The built-in php directory functions can be used on the returned handle.
	// Does not check user's permissions
	public static function opendir($path)
	{

	}
	public static function scandir($path, $sorting_order = SCANDIR_SORT_ASCENDING)
	{

	}

	public static function processFileUpload($form_upload_element_name, $dest_filename)
	{

	}

	public static function getMimeType($filename, $is_filesystem_path = false)
	{
		// check the extension
		
		if (isset(pathinfo($filename)['extension'])) {
			$fh = fopen('_res/mime.types', 'r');
			while (($line = fgets($fh)) !== false) {
				if (strlen($line) > 0 &&
						substr($line, 0, 1) !== '#' &&
						preg_match_all('/([^\s]+)/', $line, $record) &&
						isset($record[1]) &&
						($count = count($record[1])) > 1) {
					for ($i = 1; $i < $count; $i++) {
						if ($record[1][$i] == pathinfo($filename)['extension']) {
							return $record[1][0];
						}
					}
				}
			}
			fclose($fh);
		}

		if (!$is_filesystem_path) {
			$filename = self::mapSharePathToFilesystemPath($filename);
		}

		// try checking the content
		if (function_exists('finfo_open')) {
			if ($finfo = @finfo_open(FILEINFO_MIME_TYPE)) {
				if ($mimetype = @finfo_file($finfo, $filename)) {
					finfo_close($finfo);
					return $mimetype;
				}
			}
		}
		if (function_exists('mime_content_type')) {
			if ($unprocessed_content_type = @mime_content_type($filename)) {
				return preg_replace('~^(.+);.*$~', '$1', $unprocessed_content_type);
			}
		}

		// last ditch
		return 'application/octet-stream';
	}

	public static function cpRecursive($source, $destination)
	{

	}

	public static function rmRecursive($filename)
	{

	}


	// path doesn't have to exist (the share does)
	protected static function mapSharePathToFilesystemPath($share_path)
	{

	}

	protected static function sanitizeSharePath($path)
	{

	}

	protected static function getShareName($share_path)
	{

	}
	// looks for the file or directory's name in the directory listing of dirname()
	protected static function fileActuallyExists($fs_path)
	{

	}
}
