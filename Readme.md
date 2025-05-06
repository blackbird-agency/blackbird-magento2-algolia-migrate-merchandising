# Blackbird_AlgoliaMigrateMerchandising

The **Blackbird_AlgoliaMigrateMerchandising** module for Magento 2 allows you to migrate Algolia rules from the classic version to the **Algolia Merchandising Studio** version. It adjusts the rules by using a new format that better handles categories with a hierarchical structure.
## Setup

### Get the package

**Zip Package:**

Unzip the package in app/code/Blackbird/CategoryEmptyButton, from the root of your Magento instance.

**Composer Package:**

```
composer require blackbird/module-algolia-migrate-merchandising
```

### Install the module

Go to your Magento root, then run the following Magento command:

```
php bin/magento setup:upgrade
```

**If you are in production mode, do not forget to recompile and redeploy the static resources, or to use the `--keep-generated` option.**

## Functionality

The module transforms the old Algolia rules, where the `rule-context` uses Magento category IDs (e.g., `magento-category-$id`), into the new **Algolia Merchandising Studio** format. In this new format, the `categories` attribute is used to specify the full category **path** in a hierarchical structure.

### Migration Structure

- **Old Version**: Rules contain a `rule-context` that references a Magento category using an ID (e.g., `magento-category-$id`).
- **New Version**: Migrated rules use the `categories` attribute, which holds the full category path in a hierarchical format, reflecting the category structure in **Algolia Merchandising Studio**.

## Migration Command

Once the module is installed, you can run the following command to migrate the Algolia rules.

```bash
bin/magento blackbird:migrate_algolia <json_file> <store_code>
```

## Support

- If you have any issue with this code, feel free to [open an issue](https://github.com/blackbird-agency/magento-2-category-empty-button/issues/new).
- If you want to contribute to this project, feel free to [create a pull request](https://github.com/blackbird-agency/magento-2-category-empty-button/compare).
## Contact

For further information, contact us:

- by email: hello@bird.eu
- or by form: [https://black.bird.eu/en/contacts/](https://black.bird.eu/contacts/)

## Authors

- **Emilie Wittmann** - *Maintainer* - [It's me!](https://github.com/emilie-blackbird)
- **Blackbird Team** - *Contributor* - [They're awesome!](https://github.com/blackbird-agency)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

***That's all folks!***
