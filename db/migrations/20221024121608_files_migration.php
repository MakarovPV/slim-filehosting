<?php
declare(strict_types=1);

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

final class FilesMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $files = $this->table('files');
        $files->addColumn('filename', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR])
            ->addColumn('format', 'string', ['limit' => MysqlAdapter::TEXT_TINY])
            ->addColumn('size', 'integer')
            ->addColumn('date', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }
}
