<?php
declare(strict_types=1);

namespace SetBased\Audit\Test\MySql\AuditCommand\NewTable;

use SetBased\Audit\MySql\AuditDataLayer;
use SetBased\Audit\Test\MySql\AuditCommand\AuditCommandTestCase;
use SetBased\Stratum\Helper\RowSetHelper;
use SetBased\Stratum\MySql\StaticDataLayer;

/**
 * Tests for running audit with a new table.
 */
class NewTableTest extends AuditCommandTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * @inheritdoc
   */
  public static function setUpBeforeClass(): void
  {
    self::$dir = __DIR__;

    parent::setUpBeforeClass();
  }

  //--------------------------------------------------------------------------------------------------------------------
  public function test01(): void
  {
    $this->runAudit();

    // TABLE1 MUST and TABLE2 MUST not exist.
    $tables = AuditDataLayer::getTablesNames(self::$auditSchema);
    self::assertNotNull(RowSetHelper::searchInRowSet($tables, 'table_name', 'TABLE1'));
    self::assertNull(RowSetHelper::searchInRowSet($tables, 'table_name', 'TABLE2'));

    // TABLE1 MUST have triggers.
    $triggers = AuditDataLayer::getTableTriggers(self::$dataSchema, 'TABLE1');
    self::assertNotNull(RowSetHelper::searchInRowSet($triggers, 'trigger_name', 'trg_audit_t1_insert'));
    self::assertNotNull(RowSetHelper::searchInRowSet($triggers, 'trigger_name', 'trg_audit_t1_update'));
    self::assertNotNull(RowSetHelper::searchInRowSet($triggers, 'trigger_name', 'trg_audit_t1_delete'));

    // Create new table TABLE2.
    StaticDataLayer::executeMulti(file_get_contents(__DIR__.'/config/create_new_table.sql'));

    $this->runAudit();

    // TABLE1 and TABLE2 MUST exist.
    $tables = AuditDataLayer::getTablesNames(self::$auditSchema);
    self::assertNotNull(RowSetHelper::searchInRowSet($tables, 'table_name', 'TABLE1'));
    self::assertNotNull(RowSetHelper::searchInRowSet($tables, 'table_name', 'TABLE2'));

    // TABLE1 and TABLE2 MUST have triggers.
    $triggers = AuditDataLayer::getTableTriggers(self::$dataSchema, 'TABLE1');
    self::assertNotNull(RowSetHelper::searchInRowSet($triggers, 'trigger_name', 'trg_audit_t1_insert'));
    self::assertNotNull(RowSetHelper::searchInRowSet($triggers, 'trigger_name', 'trg_audit_t1_update'));
    self::assertNotNull(RowSetHelper::searchInRowSet($triggers, 'trigger_name', 'trg_audit_t1_delete'));
    $triggers = AuditDataLayer::getTableTriggers(self::$dataSchema, 'TABLE2');
    self::assertNotNull(RowSetHelper::searchInRowSet($triggers, 'trigger_name', 'trg_audit_t2_insert'));
    self::assertNotNull(RowSetHelper::searchInRowSet($triggers, 'trigger_name', 'trg_audit_t2_update'));
    self::assertNotNull(RowSetHelper::searchInRowSet($triggers, 'trigger_name', 'trg_audit_t2_delete'));
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
