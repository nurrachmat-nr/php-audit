<?php
//----------------------------------------------------------------------------------------------------------------------
namespace SetBased\Audit\Test\MySql;

use SetBased\Audit\MySql\Command\AuditCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

//----------------------------------------------------------------------------------------------------------------------
/**
 * Tests for/with minimal configuration.
 */
class MinimalConfigTest extends AuditTestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  public function test01()
  {
    $application = new Application();
    $application->add(new AuditCommand());

    /** @var AuditCommand $command */
    $command = $application->find('audit');
    $command->setRewriteConfigFile(false);
    $commandTester = new CommandTester($command);
    $commandTester->execute(['command'     => $command->getName(),
                             'config file' => 'test/MySql/MinimalConfigTest/audit.json']);

    $this->assertSame(0, $commandTester->getStatusCode());
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------