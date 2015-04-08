<?php

namespace T4webCalendar\Controller\Console;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Ddl;
use Zend\Db\Sql\Ddl\Column;
use Zend\Db\Sql\Ddl\Constraint;
use Zend\Db\Sql\Sql;
use PDOException;
use League\Flysystem\Filesystem;

class InitController extends AbstractActionController {

    /**
     * @var Adapter
     */
    private $dbAdapter;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    public function __construct(Adapter $dbAdapter, Filesystem $fileSystem){
        $this->dbAdapter = $dbAdapter;
        $this->fileSystem = $fileSystem;
    }

    public function runAction() {

        $this->createTableCalendar();

        $vendorSiteConfigRootPath = dirname(dirname(dirname(dirname(__DIR__))));

        if (!$this->fileSystem->has('/public/js/t4web-calendar/add.js')) {
            $this->fileSystem->symlink(
                $vendorSiteConfigRootPath . '/public/js/t4web-calendar/',
                getcwd() . '/public/js/t4web-calendar'
            );
        }

        return "Success completed" . PHP_EOL;
    }

    private function createTableCalendar() {
        $table = new Ddl\CreateTable('calendar');

        $id = new Column\Integer('id');
        $id->setOption('AUTO_INCREMENT', 1);

        $table->addColumn($id);
        $table->addColumn(new Column\Varchar('name', 50));

        $date = new Column\Date('date');
        $date->setNullable(true);
        $table->addColumn($date);

        $table->addConstraint(new Constraint\PrimaryKey('id'));

        $sql = new Sql($this->dbAdapter);

        try {
            $this->dbAdapter->query(
                $sql->getSqlStringForSqlObject($table),
                Adapter::QUERY_MODE_EXECUTE
            );
        } catch (PDOException $e) {
            return $e->getMessage() . PHP_EOL;
        }
    }

}
