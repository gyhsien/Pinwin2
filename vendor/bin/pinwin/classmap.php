<?php
//新增PSR4 Classmap 到 composer.json內，須在專案資料夾內操作
//php vendor/bin/pinwin/classmap.php
require 'vendor/autoload.php';
$library = './vendor/pinwin';
$composer_data = json_decode(file_get_contents('./composer.json') ,true);
$addSettings = [];
$libraryFoldes = new \RecursiveDirectoryIterator($library);

//$composer_data['autoload']['psr-4']\\'] = [];

$filter = new Zend\Filter\Word\SeparatorToCamelCase();
$psr4 = [];

foreach ($libraryFoldes as $folder)
{
    //$folder = new \FilesystemIterator();
    $dir_name = $folder->getFilename();
    if($dir_name!= '.' && $dir_name!= '..')
    {
        $namespace = "Pinwin\\".$filter->filter($dir_name)."\\";
        if(!isset($composer_data['autoload']['psr-4'][$namespace]))
        {
            $psr4[$namespace] = 'vendor/pinwin/'.$dir_name.'/src';
        }
    }
}
if(count($psr4) > 0)
{
    $composer_data['autoload']['psr-4']+=$psr4;
    $output = json_encode($composer_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    if(file_put_contents('./composer.json', $output))
    {
        echo "composer.json rewrite success.\n";
    }else{
        echo "composer.json rewrite failed.\n";
    }
}

echo "Done.\n";