<?php
/**
 * This file is part of the Conductor package.
 *
 * Generated using:
 * phpab --var namespace=Conductor --template Conductor/Autoload.php.in --output Conductor/Autoload.php Conductor
 * 
 * @copyright 2012 Clay Loveless <clay@php.net>
 * @license   http://claylo.mit-license.org/2012/ MIT License
 */
namespace Conductor;

class Autoload
{
    /**
     * Whether or not we've been registered already
     * 
     * @var bool
     */
    protected static $registered = false;
    
    /**
     * Class map
     * 
     * @var array
     */
    protected static $classes = array();
    
    /**
     * File path
     * 
     * @var string
     */
    protected static $path;
    
    /**
     * Loader using classmap
     * 
     */
    public static function load($class)
    {
    
        if (empty(self::$classes)) {
            self::$classes = array(
                'conductor\\autoload' => '/Autoload.php',
                'conductor\\converter\\package2xmltocomposer' => '/Converter/Package2XmlToComposer.php',
                'conductor\\scriptinstaller' => '/ScriptInstaller.php',
                'conductor\\tasks\\bootstrapsymfonystandardedition' => '/Tasks/BootstrapSymfonyStandardEdition.php',
                'conductor\\util\\pearpackagefilev2' => '/Util/PEARPackageFilev2.php'
            );
        }
        
        $cn = strtolower($class);
        if (isset(self::$classes[$cn])) {
            require __DIR__ . self::$classes[$cn];
        }
    }
    
    /**
     * Register autoload class with spl_autoload_register
     * 
     * @return void
     */
    public static function register()
    {
        if (! self::$registered) {
            $autoload = spl_autoload_functions();
            $loader = __CLASS__ . '::load';
            spl_autoload_register($loader);
            if (function_exists('__autolaod') && $autoload === false) {
                // __autoload() was being used, but would now be ignored
                // add it back
                spl_autoload_register('__autoload');
            }
        }
        self::$registered = true;
    }    
}
Autoload::register();