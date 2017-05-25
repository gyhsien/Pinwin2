<?php
use Zend\Mvc\Application;
use Zend\Stdlib\ArrayUtils;

require 'config/constants.php';
ini_set('error_log', 'data/log/php_errors_' . date("Ymd") . '.log');
set_time_limit(0);

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
$loader = include __DIR__ . '/vendor/autoload.php';

//額外加入library的classmap
$browser_class_path = 'library/Browser.php/lib/Browser.php';
if(is_file($browser_class_path))
{
    $loader->addClassMap(['Browser' => $browser_class_path]);
}
unset($browser_class_path);

$urlModules = include __DIR__ . '/config/url_modules.php';
foreach ($urlModules as $namespace => $path) {
    $loader->setPsr4($namespace, $path);
}
$loader->register(true);

if (! class_exists(Application::class)) {
    throw new RuntimeException("Unable to load application.\n" . "- Type `composer install` if you are developing locally.\n" . "- Type `vagrant ssh -c 'composer install'` if you are using Vagrant.\n" . "- Type `docker-compose run zf composer install` if you are using Docker.\n");
}

// Retrieve configuration
$appConfig = require __DIR__ . '/config/application.config.php';
if (file_exists(__DIR__ . '/config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require __DIR__ . '/config/development.config.php');
}

//自動生成前端資料
if(APP_ENV === 'development')
{
	$bootstrapFolders = ['assets/release/bootstrap/css', 'assets/release/bootstrap/fonts', 'assets/release/bootstrap/js'];
	$fontawesoneFolders = ['assets/release/font-awesome/css', 'assets/release/font-awesome/fonts'];
	
	$foldersMerge = array_merge($bootstrapFolders, $fontawesoneFolders);
	
	foreach($foldersMerge as $_folder)
	{
		if(!is_dir($_folder))
		{
			\Pinwin\Tools\Tools::mkdir_r($_folder);	
		}	
		
	}
	
	$bootstrapFiles = [
		'vendor/twbs/bootstrap-sass/assets/javascripts/bootstrap.min.js'=>'assets/release/bootstrap/js/bootstrap.min.js', 
		'vendor/twbs/bootstrap-sass/assets/fonts/bootstrap/glyphicons-halflings-regular.eot'=>'assets/release/bootstrap/fonts/bootstrap/glyphicons-halflings-regular.eot',
		'vendor/twbs/bootstrap-sass/assets/fonts/bootstrap/glyphicons-halflings-regular.svg'=>'assets/release/bootstrap/fonts/bootstrap/glyphicons-halflings-regular.svg',
		'vendor/twbs/bootstrap-sass/assets/fonts/bootstrap/glyphicons-halflings-regular.ttf'=>'assets/release/bootstrap/fonts/bootstrap/glyphicons-halflings-regular.ttf',
		'vendor/twbs/bootstrap-sass/assets/fonts/bootstrap/glyphicons-halflings-regular.woff'=>'assets/release/bootstrap/fonts/bootstrap/glyphicons-halflings-regular.woff',
		'vendor/twbs/bootstrap-sass/assets/fonts/bootstrap/glyphicons-halflings-regular.woff2'=>'assets/release/bootstrap/fonts/bootstrap/glyphicons-halflings-regular.woff2'
	];

	$fontawesoneFiles = [
		'vendor/fortawesome/font-awesome/css/font-awesome.min.css'=>'assets/release/font-awesome/css/font-awesome.min.css', 
		'vendor/fortawesome/font-awesome/fonts/fontawesome-webfont.eot'=>'assets/release/font-awesome/fonts/fontawesome-webfont.eot',
		'vendor/fortawesome/font-awesome/fonts/fontawesome-webfont.svg'=>'assets/release/font-awesome/fonts/fontawesome-webfont.svg',
		'vendor/fortawesome/font-awesome/fonts/fontawesome-webfont.ttf'=>'assets/release/font-awesome/fonts/fontawesome-webfont.ttf',
		'vendor/fortawesome/font-awesome/fonts/fontawesome-webfont.woff'=>'assets/release/font-awesome/fonts/fontawesome-webfont.woff',
		'vendor/fortawesome/font-awesome/fonts/fontawesome-webfont.woff2'=>'assets/release/font-awesome/fonts/fontawesome-webfont.woff2',
		'vendor/fortawesome/font-awesome/fonts/FontAwesome.otf'=>'assets/release/font-awesome/fonts/FontAwesome.otf',
	];
	
	$filesMerge = array_merge($bootstrapFiles, $fontawesoneFiles);
	foreach($filesMerge as $source=>$target)
	{
		
		if(!is_file($target))
		{
			copy($source, $target);
		}else{
			$srcFileInfo = new \SplFileInfo($source);
			$tgeFileInfo = new \SplFileInfo($target);
			
			if($srcFileInfo->getAtime() > $tgeFileInfo->getAtime())
			{
				copy($source, $target);	
			}
		}
	}
	
	if(!is_file('library/dojo/themes/flat/styles/styles.styl'))
	{
		\Pinwin\Tools\Tools::copy('library/flat_replace/styles', 'library/dojo/themes/flat/styles');	
	}	
}

//AutoloaderFactory::factory([ClassMapAutoloader::class => [['Browser' => 'vendor/Browser.php/lib/Browser.php']]]);

// Run the application!
$application = Application::init($appConfig);

$application->run();