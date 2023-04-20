## Documentation

CustomDimensionsManager provides some administrative commands that can be used to manage custom dimensions configuration of multiple sites. The commands are executed using Matomo's [console](https://matomo.org/faq/general/faq_21827/).

### The 'synchronize' Command

This is the main function with the purpose of synchronizing custom dimensions configurations of sites.

The command `customdimensionsmanager:synchronize` allows you to copy configuration of custom dimensions from a specific site to another in a Matomo installation. The source site could be considered a template for other sites.

You can also use `*` as the target site to copy configuration of the source site to all other sites. This makes it possible to e.g. run the process regularly as a cron task.

The `--dry-run` option can be used to test what the command would do without actually making any changes.

### The 'delete' Command

The command `customdimensionsmanager:delete` allows you to delete all custom dimensions configuration of a single site.
