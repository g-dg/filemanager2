# Garnet DeGelder's File Manager 2.0 - Developer Documentation

## Namespace
> `GarnetDG\FileManager`

## Basic Directory Structure
```
root_dir
	`- _doc
	`- _extensions
	`- _res
	`- _setup
		`- upgrade_scripts
	`- _system
	`- _config.php
	`- index.php
```

## Application Lifecycle
1) Start index.php
2) Basic startup things
	- set initial timeout
	- manage error reporting
	- define filemanager version
3) Include basic files
	1) Include config.php
	2) Include log.php
	3) Include loader.php
4) Run base system loader
	1) Include all files in `_system` directory
		- Register classes, functions, etc.
		- Register init functions
	2) Run registered inits
		- Register hooks, pages, resources, etc.
5) Run extension loader
	1) Include main files for all extensions in `_extensions` directory
		- Include other files
		- Register classes, functions, etc.
		- Register init functions
		- Register hooks, pages, resources, etc.
	2) Run registered inits
		- Register hooks, pages, resources, etc.
6) Route to the current page
	1) Run current page if registered, else show 404 page


## Extensions
Stored in `_extensions` directory, main file is named like this: `_extensions/<extension name>/<extension name>.php`
