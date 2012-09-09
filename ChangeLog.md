Conductor 1.1.0
===============

Planned items for Conductor 1.1.0

* Common install script behaviors/wrappers
* Refinements to converter, as needed

Conductor 1.0.3
---------------

* PEAR Converter: Added support for setting composer package name via 
  config file.
* PEAR Converter: Don't add config bin-dir to composer.json unless package
  provides executables.
* PEAR Converter: Added support for additional optional dependencies.

Conductor 1.0.2
---------------

* Added BoostrapSymfonyStandardEdition task.


Conductor 1.0.1
---------------

* Added an autoloader.
* Made it easier to run a PEAR Package conversion to Composer via
  command-line tools and a config file.
  

Conductor 1.0.0
---------------

* Initial release
* Implemented PEAR Package File v2 reader, and initial Converter
* Stubbed initial Conductor Script Installer