## Conductor Tasks

There are some tasks that Composer users may find useful which aren't 
needed often enough to bother bulking up Composer itself to support them.

When these tasks become evident, they'll be added here if it makes sense.

### BootstrapSymfonyStandardEdition

The BootstrapSymfonyStandardEdition task is intended for use when customized
Symfony projects are defined through their dependencies in a `composer.json`
file, and then passed around to developers within an organization. 

In these scenarios, the two recommended ways of installing Symfony 2 
(download a .zip/.tgz or use `composer create-project --dev --prefer-source symfony/symfony MyProjectDir`) both 
wind up being somewhat cumbersome. 

To install a customized Symfony that's set up how you want it right out of
`composer install --dev`, add the following to your `composer.json`:

```json
{
    ...
    "require": {
        ...
        "symfony/framework-standard-edition": "*",
        "conductor/conductor": "1.0.*"
    },
    ...
    "scripts": {
        "post-install-cmd": [
            "Conductor\\Tasks\\BootstrapSymfonyStandardEdition::postInstall",
            "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",
            ... other tasks ...
        ]
    },
    ...
    "repositories": [
        { "type": "git", "url": "http://github.com/symfony/symfony-standard.git" },
        { "type": "git", "url": "http://github.com/claylo/conductor.git" }
    ]
}
```

This approach assumes that you've based your custom setup off of 
Symfony Standard Edition's [base `composer.json`](https://github.com/symfony/symfony-standard/blob/master/composer.json),
_and_ that you've also included a requirement for the Symfony Standard package
itself.

**NOTE:** The `"Conductor\\Tasks\\BootstrapSymfonyStandardEdition::postInstall"` command
needs to be the _first_ `post-install-cmd`, as the others are dependent on the work
that it does.

## What's wrong with composer create-project?

For many things, nothing. 

However, after a few of these, and a while spent on creating a Satis repo just
for a customized installation, it's just not worth it for me. 

```
clay:test clay$ composer create-project --dev --prefer-source symfony/framework-standard-edition test-project 
Installing symfony/framework-standard-edition (dev-master 506ffaab8d8474db2512fca879ca4b9877616a1e)
  - Installing symfony/framework-standard-edition (dev-master master)
    Cloning master

Created project in test-project
Loading composer repositories with package information
Installing dependencies from lock file
Your lock file is out of sync with your composer.json, run "composer.phar update" to update dependencies
Your requirements could not be resolved to an installable set of packages.

  Problem 1
    - Installation request for symfony/symfony == 2.1.9999999.9999999-dev -> satisfiable by symfony/symfony 2.1.x-dev.
    - Can only install one of: symfony/symfony dev-master, symfony/symfony 2.1.x-dev.
    - Installation request for symfony/symfony == 9999999-dev -> satisfiable by symfony/symfony dev-master.
```