# Yireo ExampleDeclarativeSchema
This sample module illustrates the usage of the Magento 2.3 Declarative Schemas. The module can be installed with the following commands:

    bin/magento module:enable Yireo_ExampleDeclarativeSchema
    bin/magento setup:upgrade
    
After this, a new database table `yireo_example` has been created.

## Playing with it
By copying the file `db_schema_test_drop_column_enabled.xml` to `db_schema.xml`, you can modify the schema to remove a column `enabled`. Once the new XML is in place, run `bin/magento setup:upgrade`. You should notice that the column is still there. This is because the module is not whitelisting the table for modification yet.

Now, run the following command:

    bin/magento setup:db-declaration:generate-whitelist --module-name=Yireo_ExampleDeclarativeSchema
    
This adds a file `db_schema_whitelist.json` in the `etc` folder of the module. If you repeat the command `bin/magento setup:upgrade`, the column will be removed as expected.

## Integration tests
The module also ships with Integration Tests, that show further migrations (described in the `db_schema_test_*` files):

- A column is dropped, which does not work unless the module has been whitelisted (`bin/magento setup:db-declaration:generate-whitelist --module-name=Yireo_ExampleDeclarativeSchema`). First, an upgrade without whitelisting is attempted. Then, an upgrade with whitelisting.