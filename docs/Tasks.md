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
        "conductor/conductor": "1.0.*"
    },
    ...
    "scripts": {
        "post-install-cmd": [
            "Conductor\\Tasks\\BootstrapSymfonyStandardEdition::postInstall"
        ]
    },
    ...
    "repositories": [
        { "type": "git", "url": "http://github.com/claylo/conductor.git" }
    ]
}
```

This approach assumes that you've based your custom setup off of 
Symfony Standard Edition's [base `composer.json`](https://github.com/symfony/symfony-standard/blob/master/composer.json).

