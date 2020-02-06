# Health check

## About

This module is compatible with PrestaShop >= 1.7.8

It allows you to check if your instance of Prestashop have all the requirements to run properly.

The module provides security configuration to protect the URL from not allowed requests. To do so, you have to configure a token and a list of authorized IP addresses (separated by commas).

The default configuration allows `127.0.0.1, ::1` and set a random token.

Curl example to get instance status :

`curl -H "X-HEALTHCHECK-TOKEN: {TOKEN}" -X GET http(s)://BASE_URL/ADMIN_DIR/modules/healthcheck`

The response will be 200 when all checks pass and 400 if not.

The built-in checks are
- connection to database
- write permissions for cache directory
- composer up to date

Some checks (like php version) are not made on purpose because without them the instance won't run at all

The module allows you to add your own checks

## Adding custom checks

Some checks are already made. You don't need to declare them again

To add your custom checks, you need to declare them as service and add a tag to identify them

The tag is admin.prestashop.healthcheck

Your service can be declared in any services config file like the global one `app/config/services.yml`. However its better to create a file healthcheck_services.yml and import it in the global services file

The service class must implement `Laminas\Diagnostics\Check\CheckInterface`

You already have some standard checks classes https://github.com/laminas/laminas-diagnostics/tree/master/src/Check or you can create your own to fit your need

Here is an example of service to check that a yaml file is well formatted

```yaml
  prestashop.bundle.healthcheck:
    class: Laminas\Diagnostics\Check\YamlFile
    arguments:
      - ['%kernel.project_dir%/app/config/services.yml']
    tags:
      - 'admin.prestashop.healthcheck'
```

## Reporting issues

You can report issues with this module in the main PrestaShop repository. [Click here to report an issue][report-issue]. 

## Contributing

PrestaShop modules are open source extensions to the PrestaShop e-commerce solution. Everyone is welcome and even encouraged to contribute with their own improvements.

### Requirements

Contributors **must** follow the following rules:

* **Make your Pull Request on the "dev" branch**, NOT the "master" branch.
* Do not update the module's version number.
* Follow [the coding standards][1].

### Process in details

Contributors wishing to edit a module's files should follow the following process:

1. Create your GitHub account, if you do not have one already.
2. Fork this project to your GitHub account.
3. Clone your fork to your local machine in the ```/modules``` directory of your PrestaShop installation.
4. Create a branch in your local clone of the module for your changes.
5. Change the files in your branch. Be sure to follow the [coding standards][1]!
6. Push your changed branch to your fork in your GitHub account.
7. Create a pull request for your changes **on the _'dev'_ branch** of the module's project. Be sure to follow the [contribution guidelines][2] in your pull request. If you need help to make a pull request, read the [GitHub help page about creating pull requests][3].
8. Wait for one of the core developers either to include your change in the codebase, or to comment on possible improvements you should make to your code.

That's it: you have contributed to this open source project! Congratulations!

## License

This module is released under the [Academic Free License 3.0][AFL-3.0] 

[report-issue]: https://github.com/PrestaShop/PrestaShop/issues/new/choose
[1]: https://devdocs.prestashop.com/1.7/development/coding-standards/
[2]: https://devdocs.prestashop.com/1.7/contribute/contribution-guidelines/
[3]: https://help.github.com/articles/using-pull-requests
[AFL-3.0]: https://opensource.org/licenses/AFL-3.0
