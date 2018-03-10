<?php
return function ($class) {
    static $map;
    if (!$map) {
        $map = include __DIR__ . '/autoload_classmap.php';
    }

    if (!isset($map[$class])) {
        return false;
    }
    $classFile=$map[$class];
    // $classFile=str_replace('\\', DS , $map[$class]);
    return include $classFile;
};
