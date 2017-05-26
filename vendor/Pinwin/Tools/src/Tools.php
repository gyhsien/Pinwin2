<?php
namespace Pinwin\Tools;

class Tools
{

    static public function BulidToken($length = 16)
    {
        $charlist = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charlist .= strtolower($charlist);
        $charlist .= '0123456789_-';
        return \Zend\Math\Rand::getString($length, $charlist, true);
    }

    static public function minify_html($input)
    {
        return preg_replace_callback('#<\s*([^\/\s]+)\s*(?:>|(\s[^<>]+?)\s*>)#', function ($m) {
            if (isset($m[2])) {
                // Minify inline CSS declaration(s)
                if (stripos($m[2], ' style=') !== false) {
                    $m[2] = preg_replace_callback('#( style=)([\'"]?)(.*?)\2#i', function ($m) {
                        return $m[1] . $m[2] . minify_css($m[3]) . $m[2];
                    }, $m[2]);
                }
                return '<' . $m[1] . preg_replace(array(
                    // From `defer="defer"`, `defer='defer'`, `defer="true"`, `defer='true'`, `defer=""` and `defer=''` to `defer` [^1]
                    '#\s(checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped)(?:=([\'"]?)(?:true|\1)?\2)#i',
                    // Remove extra white-space(s) between HTML attribute(s) [^2]
                    '#\s*([^\s=]+?)(=(?:\S+|([\'"]?).*?\3)|$)#',
                    // From `<img />` to `<img/>` [^3]
                    '#\s+\/$#'
                ), array(
                    // [^1]
                    ' $1',
                    // [^2]
                    ' $1$2',
                    // [^3]
                    '/'
                ), str_replace("\n", ' ', $m[2])) . '>';
            }
            return '<' . $m[1] . '>';
        }, $input);
    }

    static public function mkdir_r($dirName, $rights = 0777, $print = false)
    {
        $dirName = str_replace('\\', '/', $dirName);
        $dirs = explode('/', $dirName);
        
        $t_dirs = $dirs;
        if (is_file(implode('/', $t_dirs))) {
            array_pop($t_dirs);
        }
        
        if (is_dir(implode('/', $t_dirs))) {
            return;
        }
        
        $dir = '';
        
        foreach ($dirs as $part) {
            
            $dir .= $part . '/';
            if (! is_dir($dir) && strlen($dir) > 0 && ! is_file($dir)) {
                $result = mkdir($dir, $rights);
                
                if ($result && $print) {
                    echo "\"$dir\" Folder created successfully.\n";
                }
                
                if (! $result && $print) {
                     echo "\"$dir\" Folder created failed.\n";
                }
            }
        }
    }

    static public function copy($src, $dst, $print = false)
    {
        $srcIterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        
        $dstTailFolder = end(explode('/', $dst));
        $srcTailFolder = end(explode('/', $src));
        
        foreach ($srcIterator as $item) {
            // $item = new \SplFileInfo();
            if ($item->isDir()) {
                
                // $item->getFilename();
                // $item->getPath()
                $mkFolderName = str_replace($srcTailFolder, $dstTailFolder, $item->getPathname());
                if (! is_dir($mkFolderName)) {
                    self::mkdir_r($mkFolderName, 0777, true);
                }
            } else {
                // echo $item->getPath().'/'.$item->getFilename()."\n";
                $source = $item->getPath() . '/' . $item->getFilename();
                $dest = str_replace($srcTailFolder, $dstTailFolder, $source);
                $result = copy($source, $dest);
                
                if ($result && $print) {
                    echo "\"$source\" copy to \"$dest\" successfully.\n";
                }
                
                if (! $result && $print) {
                    echo "\"$source\" copy to \"$dest\" failed.\n";
                }
            }
        }
    }
}