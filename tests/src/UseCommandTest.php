<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\ComposerInitApplication;
use CL\ComposerInit\UseCommand;
use Symfony\Component\Console\Tester\CommandTester;
use CL\EnvBackup\DirectoryParam;

class UseCommandTest extends AbstractTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->application = new ComposerInitApplication();

        $this->output = new DummyOutput();
    }

    public function testGetDestination()
    {
        $use = new UseCommand('use');

        $this->assertEquals('.', $use->getDestination());
    }

    /**
     * @covers CL\ComposerInit\UseCommand::moveFiles
     */
    public function testMoveFiles()
    {
        $dirFrom = dirname(__FILE__).'/../test_dir1';
        $dirTo = dirname(__FILE__).'/../test_dir2';

        $this->env
            ->add(
                new DirectoryParam($dirFrom, array(
                    'test1' => 'test1',
                    'dir1' => array(
                        'test2' => 'new',
                    )
                ))
            )
            ->add(
                new DirectoryParam($dirTo, array(
                    'dir1' => array(
                        'test2' => 'old',
                        'test4' => 'test4',
                    )
                ))
            )
            ->apply();

        $use = new UseCommand('use');

        $use->moveFiles($dirFrom, $dirTo);

        $this->assertFileExists($dirTo.'/test1');
        $this->assertFileExists($dirTo.'/dir1/test2');
        $this->assertFileExists($dirTo.'/dir1/test4');
        $this->assertEquals('new', file_get_contents($dirTo.'/dir1/test2'));
    }

    /**
     * @covers CL\ComposerInit\UseCommand::deleteDir
     */
    public function testDeleteDir()
    {
        $dir = dirname(__FILE__).'/../testVariables';

        $this->env
            ->add(
                new DirectoryParam($dir, array(
                    'dir1' => array(
                        'test2' => 'test',
                        'dir2' => array(
                            'test' => 'test',
                        ),
                    )
                ))
            )
            ->apply();

        $use = new UseCommand('use');

        $use->deleteDir($dir.'/dir1');

        $this->assertFileNotExists($dir.'/dir1/test2');
        $this->assertFileNotExists($dir.'/dir1/test2/test');
    }

    /**
     * @covers CL\ComposerInit\UseCommand::setTemplateVariables
     */
    public function testSetTemplateVariables()
    {
        $dir = dirname(__FILE__).'/../testVariables';

        $this->env
            ->add(
                new DirectoryParam($dir, array(
                    'test' => 'some place {%for%} template',
                    'dir1' => array(
                        'test2' => 'another {%place%} {%for%} template',
                    )
                ))
            )
            ->apply();

        $use = new UseCommand('use');

        $use->setTemplateVariables($dir, array(
            'for' => 'v1',
            'place' => 'v2',
            'missing' => 'v3',
        ));

        $this->assertEquals('some place v1 template', file_get_contents($dir.'/test'));
        $this->assertEquals('another v2 v1 template', file_get_contents($dir.'/dir1/test2'));
    }

    /**
     * @covers CL\ComposerInit\UseCommand::execute
     */
    public function testExecute()
    {
        $zipFile = dirname(__FILE__).'/../test.zip';
        $testDir = dirname(__FILE__).'/../testdir';

        $this->env
            ->add(new DirectoryParam($testDir, array()))
            ->apply();

        $use = $this->getMock(
            'CL\ComposerInit\UseCommand',
            array('getDistUrl', 'getDestination')
        );

        $dialog = $this->getMock(
            'Symfony\Component\Console\Helper\DialogHelper',
            array('ask', 'askConfirmation')
        );

        $template = $this->getMock(
            'CL\ComposerInit\TemplateHelper',
            array('getRepoField')
        );

        $helperSet = $this->application->getHelperSet();
        $helperSet->set($dialog);
        $helperSet->set($template);

        $use->setHelperSet($helperSet);

        $tester = new CommandTester($use);

        $use
            ->expects($this->any())
            ->method('getDestination')
            ->will($this->returnValue($testDir));

        $use
            ->expects($this->once())
            ->method('getDistUrl')
            ->with($this->equalTo('test-package'), $this->equalTo('dev-test'))
            ->will($this->returnValue('file:://'.$zipFile));

        $template
            ->expects($this->any())
            ->method('getRepoField')
            ->will($this->returnValue('testrepo'));

        $template
            ->expects($this->any())
            ->method('getGitConfig')
            ->will($this->returnValue('author 1'));

        $dialog
            ->expects($this->once())
            ->method('askConfirmation')
            ->will($this->returnValue('Y'));

        $dialog
            ->expects($this->any())
            ->method('ask')
            ->will($this->onConsecutiveCalls('test name', 'author 2'));

        $tester->execute(array('package' => 'test-package', 'release' => 'dev-test'));

        $this->assertFileExists($testDir.'/README.md');
        $this->assertFileExists($testDir.'/.gitignore');
        $this->assertFileExists($testDir.'/src/Init.php');

        $this->assertContains('# Test Project Named test name', file_get_contents(($testDir.'/README.md')));
        $this->assertContains('@author    author 2', file_get_contents(($testDir.'/src/Init.php')));
    }
}
