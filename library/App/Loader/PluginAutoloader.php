<?php
class App_Loader_PluginAutoloader
extends Zend_Loader_PluginLoader
implements Zend_Loader_Autoloader_Interface {

    public function autoload($class) {

        $registry = $this->getPaths();
        $registry  = array_reverse($registry, true);

        foreach($registry as $prefix => $paths) {

            if (strpos($class, $prefix, 0) !==0) {
                continue;
            }

            $classFile = substr($class, strlen($prefix));
            $classFile = str_replace('_', DIRECTORY_SEPARATOR, $classFile);
            $classFile = $classFile.'.php';

            $paths = array_reverse($paths, true);

            foreach ($paths as $path) {
                $loadFile = $path . $classFile;
                if (Zend_Loader::isReadable($loadFile)) {
                    include_once $loadFile;
                    if (!class_exists($class, false)) {
                        throw new Exception('Class not found: '.$class. ' in '.$loadFile);
                    }

                    return true;
                }
            }
        }

        return false;
    }
}