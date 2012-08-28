Conductor
=========

Set of tools surrounding composer-installed packages.

## PEAR Package Conversion

The Package2XmlToComposer class can read most of a package2.xml file 
and generate a valid, readable composer.json file from it.



## Example Conversion
```php
require_once 'Conductor/Util/PEARPackageFilev2.php';
require_once 'Conductor/Converter/Package2XmlToComposer.php';
use Conductor\Converter\Package2XmlToComposer;

$converter = new Package2XmlToComposer(__DIR__.'/package.xml');

$converter
    ->setKeywords(array('phpunit', 'xunit', 'testing'))
    ->setLicense('BSD-3-Clause')
    ->setHomepage('http://www.phpunit.de/')
    ->setDependencyMap(array(
        'pear.phpunit.de/File_Iterator'         => 'phpunit/php-file-iterator',
        'pear.phpunit.de/Text_Template'         => 'phpunit/php-text-template',
        'pear.phpunit.de/PHP_CodeCoverage'      => 'phpunit/php-code-coverage',
        'pear.phpunit.de/PHP_Timer'             => 'phpunit/php-timer',
        'pear.phpunit.de/PHPUnit_MockObject'    => 'phpunit/phpunit-mock-objects',
        'pear.phpunit.de/PHP_Invoker'           => 'phpunit/php-invoker'    
    ))
    ->setSupportInfo(array(
        'issues' => 'https://github.com/sebastianbergmann/phpunit/issues',
        'irc' => 'irc://irc.freenode.com/phpunit'
    ))
    ->setAutoload(array(
        'psr-0' => array(
            'PHPUnit_Runner_Version' => 'composer/'
        ),
        'files' => array(
            "PHPUnit/Autoload.php"
        )
    ))
    ->setIncludePath(array(
        null,
        "../php-code-coverage/",
        "../php-file-iterator/",
        "../php-invoker/",
        "../php-text-template/",
        "../php-timer/",
        "../php-token-stream/",
        "../phpunit-mock-objects/",
        "../phpunit-selenium/",
        "../phpunit-story/",
        "../../pear-pear.symfony-project.com/YAML/SymfonyComponents/"
    ))
    ->setBinFiles(array(
        'composer/bin/phpunit',
        'phpunit.bat'
    ));

// dump to a composer.json file in this directory
//$converter->convert(true);

echo $converter->convert();
```