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
		$dest_share_path = $dest;
		$source = self::mapSharePathToFilesystemPath($source);
		$dest = self::mapSharePathToFilesystemPath($dest);
		if (!is_null($source) && !is_null($dest)) {
			if (self::isPathWritable($dest_share_path)) {
				return @copy($source, $dest);
			}
		}
		return false;
	}
	public static function file_exists($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @file_exists($filename);
		}
		return false;
	}
	public static function file_get_contents($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @file_get_contents($filename);
		}
		return false;
	}
	public static function file_put_contents($filename, $data)
	{
		$filename_share_path = $filename;
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			if (self::isPathWritable($filename_share_path)) {
				return @file_put_contents($filename, $data);
			}
		}
		return false;
	}
	public static function fileatime($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @fileatime($filename);
		}
		return false;
	}
	public static function filemtime($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @filemtime($filename);
		}
		return false;
	}
	/**
	 * Finds file size on platforms with 32-bit integers
	 * From https://www.php.net/manual/en/function.filesize.php#121406 by Damien Dussouillez
	 * 
	 * Return file size (even for file > 2 Gb)
	 * For file size over PHP_INT_MAX (2 147 483 647), PHP filesize function loops from -PHP_INT_MAX to PHP_INT_MAX.
	 *
	 * @param string $path Path of the file
	 * @return mixed File size or false if error
	 */
	private static function filesize_32bit($path)
	{
		if (!function_exists('bcadd')) {
			Log::error('Cannot get filesize without BC Math extension');
			return false;
		}

		if (!file_exists($path))
			return false;

		$size = filesize($path);
	
		if (!($file = fopen($path, 'rb')))
			return false;
	
		if ($size >= 0)
		{//Check if it really is a small file (< 2 GB)
			if (fseek($file, 0, SEEK_END) === 0)
			{//It really is a small file
				fclose($file);
				return $size;
			}
		}
	
		//Quickly jump the first 2 GB with fseek. After that fseek is not working on 32 bit php (it uses int internally)
		$size = PHP_INT_MAX - 1;
		if (fseek($file, PHP_INT_MAX - 1) !== 0)
		{
			fclose($file);
			return false;
		}
	
		$length = 1024 * 1024;
		while (!feof($file))
		{//Read the file until end
			$read = fread($file, $length);
			$size = bcadd($size, $length);
		}
		$size = bcsub($size, $length);
		$size = bcadd($size, strlen($read));
	
		fclose($file);
		return $size;
	}
	public static function filesize_actual($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			if (PHP_INT_MAX != 2147483647) { // check for 64-bit support
				return (string)@filesize($filename);
			} else {
				return @self::filesize_32bit($filename);
			}
		}
		return false;
	}
	public static function filesize($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			$size = @filesize($filename);
			if ($size < 0) {
				$size = null;
			}
			return $size;
		}
		return false;
	}
	public static function filetype($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @filetype($filename);
		}
		return false;
	}
	// The built-in php file functions can be used on the returned handle.
	// Requires read permission on the share
	public static function fopen($filename, $mode)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @fopen($filename, $mode);
		}
		return false;
	}
	public static function is_dir($filename)
	{
		if (self::sanitizePath($filename) === '/') {
			return true;
		}
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @is_dir($filename);
		}
		return false;
	}
	public static function is_file($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @is_file($filename);
		}
		return false;
	}
	public static function is_readable($filename)
	{
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			return @is_readable($filename);
		}
		return false;
	}
	public static function is_writable($filename)
	{
		$share_filename = $filename;
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			if (self::isPathWritable($share_filename)) {
				return @is_writable($filename);
			}
		}
		return false;
	}
	public static function mkdir($filename)
	{
		$filename_share_path = $filename;
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			if (self::isPathWritable($filename_share_path)) {
				return @mkdir($filename);
			}
		}
		return false;
	}
	public static function rename($oldname, $newname)
	{
		$oldname_share_path = $oldname;
		$newname_share_path = $newname;
		$oldname = self::mapSharePathToFilesystemPath($oldname);
		$newname = self::mapSharePathToFilesystemPath($newname);
		if (!is_null($oldname) && !is_null($newname)) {
			if (self::isPathWritable($oldname_share_path) && self::isPathWritable($newname_share_path)) {
				return @rename($oldname, $newname);
			}
		}
		return false;
	}
	public static function rmdir($dirname)
	{
		$dirname_share_path = $dirname;
		$dirname = self::mapSharePathToFilesystemPath($dirname);
		if (!is_null($dirname)) {
			if (self::isPathWritable($dirname_share_path)) {
				return @rmdir($dirname);
			}
		}
		return false;
	}
	public static function touch($filename)
	{
		$filename_share_path = $filename;
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			if (self::isPathWritable($filename_share_path)) {
				return @touch($filename);
			}
		}
		return false;
	}
	public static function unlink($filename)
	{
		$filename_share_path = $filename;
		$filename = self::mapSharePathToFilesystemPath($filename);
		if (!is_null($filename)) {
			if (self::isPathWritable($filename_share_path)) {
				return @unlink($filename);
			}
		}
		return false;
	}

	public static function scandir($path)
	{
		if (self::isPathToRoot($path)) {
			$share_ids = Shares::getAllAccessible(Auth::getCurrentUserId());
			$share_names = ['.'];
			foreach ($share_ids as $share_id) {
				$share_names[] = Shares::getname($share_id);
			}
			return $share_names;
		}

		$path = self::mapSharePathToFilesystemPath($path);
		if (!is_null($path)) {
			return @scandir($path);
		}

		return false;
	}
	public static function fileCount($path)
	{
		if (self::isPathToRoot($path)) {
			return count(Shares::getAllAccessible(Auth::getCurrentUserId()));
		}

		$path = self::mapSharePathToFilesystemPath($path);
		if (!is_null($path)) {
			if (is_dir($path) && ($dh = @opendir($path))) {
				$count = 0;
				while (($entry = readdir($dh)) !== false) {
					if (substr($entry, 0, 1) !== '.') {
						$count++;
					}
				}
				closedir($dh);
				return $count;
			}
		}
		return false;
	}

	public static function processFileUpload($form_upload_element_name, $dest_filename)
	{
		$dest_share_path = $dest_filename;
		$dest_filename = self::mapSharePathToFilesystemPath($dest_filename);
		if (!is_null($dest_filename)) {
			if (self::isPathWritable($dest_share_path)) {
				if (move_uploaded_file($_FILES[$form_upload_element_name]['tmp_name'], $dest_filename)) {
					return true;
				}
			}
		}
		return false;
	}

	protected static $extension_mime_types = [];
	public static function getContentType($filename, $is_filesystem_path = false, $cache = false)
	{
		if (!$is_filesystem_path) {
			$filename = self::mapSharePathToFilesystemPath($filename);
		}
		if (is_null($filename)) {
			return false;
		}

		// check the extension
		if (isset(pathinfo($filename)['extension'])) {
			$extension = strtolower(pathinfo($filename)['extension']);
			if ($cache && count(self::$extension_mime_types) > 0) {
				if (isset(self::$extension_mime_types[$extension])) {
					return self::$extension_mime_types[$extension];
				}
			} else {
				$fh = fopen('_res/mime.types', 'r');
				while (($line = fgets($fh)) !== false) {
					if (strlen($line) > 0 &&
							substr($line, 0, 1) !== '#' &&
							preg_match_all('/([^\s]+)/', $line, $record) &&
							isset($record[1]) &&
							($count = count($record[1])) > 1) {
						for ($i = 1; $i < $count; $i++) {
							if ($cache) {
								self::$extension_mime_types[$record[1][$i]] = $record[1][0];
							} else {
								if ($record[1][$i] == $extension) {
									return $record[1][0];
								}
							}
						}
						if (isset(self::$extension_mime_types[$extension])) {
							return self::$extension_mime_types[$extension];
						}
					}
				}
				fclose($fh);
			}
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
				return preg_replace('/^(.+);.*$/', '$1', $unprocessed_content_type);
			}
		}

		// last ditch
		return 'application/octet-stream';
	}

	public static function sanitizePath($path)
	{
		$raw_path_array = explode('/', $path);
		$path_array = [];
		foreach ($raw_path_array as $raw_path_part) {
			if (strlen($raw_path_part) > 0 && $raw_path_part !== '.') {
				if ($raw_path_part === '..') {
					if (count($path_array) > 0) {
						array_pop($path_array);
					}
				} else {
					array_push($path_array, $raw_path_part);
				}
			}
		}
		return '/' . implode('/', $path_array);
	}


	protected static function isPathToRoot($path)
	{
		$path = self::sanitizePath($path);
		$path_array = explode('/', trim($path, '/'));
		if (isset($path_array[0])) {
			return ($path_array[0] === '');
		}
		return false;
	}

	// path doesn't have to exist (the share does)
	protected static function mapSharePathToFilesystemPath($share_path)
	{
		$share_path_array = explode('/', trim($share_path, '/'));
		if (isset($share_path_array[0])) {
			$share_id = Shares::getId($share_path_array[0]);
			if (Shares::canRead($share_id, Auth::getCurrentUserId())) {
				$path_to_share = Shares::getPath($share_id);
				if (!is_null($path_to_share)) {
					$path_array = $share_path_array;
					array_shift($path_array);
					$path = implode('/', $path_array);
					if ($path !== '') {
						return $path_to_share . '/' . $path;
					} else {
						return $path_to_share;
					}
				}
			}
		}
		return null;
	}

	// looks for the file or directory's name in the directory listing of dirname()
	protected static function fileActuallyExists($filename)
	{

	}

	protected static function isPathReadable($share_path)
	{
		$share_path_array = explode('/', trim($share_path, '/'));
		if (isset($share_path_array[0])) {
			return Shares::canRead(Shares::getId($share_path_array[0]), Auth::getCurrentUserId());
		} else {
			return false;
		}
	}
	protected static function isPathWritable($share_path)
	{
		$share_path_array = explode('/', trim($share_path, '/'));
		if (isset($share_path_array[0])) {
			return Shares::canWrite(Shares::getId($share_path_array[0]), Auth::getCurrentUserId());
		} else {
			return false;
		}
	}
}
