# How to manage our Database with Doctrine.

## Configure Table schema

- To create a new table:
    1. Create a new class [here](../srv/app/src/Infrastructure/Persistence/Doctrine/DBAL/Table)
    2. Implement `App\Infrastructure\Persistence\Doctrine\DBAL\Table\DoctrineDBALTableSchemaConfigurator`
    3. Run `task app:postgres-db-diff`
    4. Edit the description in the new generated file localized [here](../srv/app/src/Infrastructure/Persistence/Doctrine/Migrations/migrations)
    5. **CHECK** the resulted sql queries:
       - BC breaks (RENAME, DROP, etc...) **should** be avoided 99.99% of the time. 
       - If really needed, they **MUST** be carefully handled in 2 separate deployments (hence 2 separate migrations).
       eg. ADD a new column (first deployment), DROP old column (second deployment).
       - Don't forget to **communicate** the change with your **manager** and the different **consumers.**
    6. Run `task app:postgres-db-migrate` to update the schema in your database.
