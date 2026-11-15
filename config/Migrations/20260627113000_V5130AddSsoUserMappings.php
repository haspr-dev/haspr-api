<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class V5130AddSsoUserMappings extends AbstractMigration
{
    /**
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('sso_user_mappings', ['id' => false, 'primary_key' => ['id']]);
        $table
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('user_id', 'uuid', ['null' => false])
            ->addColumn('provider', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('claim_name', 'string', ['limit' => 128, 'null' => false])
            ->addColumn('claim_value', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('created', 'datetime', ['null' => false])
            ->addColumn('modified', 'datetime', ['null' => false])
            ->addIndex(['user_id'])
            ->addIndex(['provider', 'claim_name', 'claim_value'], ['unique' => true])
            ->create();
    }
}
