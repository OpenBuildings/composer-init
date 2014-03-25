<?php

namespace CL\ComposerInit\Test;

use CL\ComposerInit\TemplateHelper;
use Symfony\Component\Console\Application;
use stdClass;

class TemplateHelperTest extends AbstractTestCase
{
    public $template;
    public $application;
    public $output;

    public function setUp()
    {
        parent::setUp();

        $this->template = new TemplateHelper();
        $this->application = new Application();
        $this->application->getHelperSet()->set($this->template);
        $this->template->setHelperSet($this->application->getHelperSet());
        $this->output = new DummyOutput();
    }

    /**
     * @covers CL\ComposerInit\TemplateHelper::getApplication
     * @covers CL\ComposerInit\TemplateHelper::setApplication
     */
    public function testApplication()
    {
        $application = new Application();

        $this->assertNull($this->template->getApplication());

        $this->template->setApplication($application);

        $this->assertSame($application, $this->template->getApplication());
    }

    /**
     * @covers CL\ComposerInit\TemplateHelper::retrieveParams
     */
    public function testRetrieveParams()
    {
        $template = $this->getMock('CL\ComposerInit\TemplateHelper', array('getRepoField'));

        $template
            ->expects($this->exactly(2))
            ->method('getRepoField')
            ->with($this->equalTo('full_name'))
            ->will($this->returnValue('tmp'));

        $values = $template->retrieveParams($this->output, array());
        $expected = array('repository_name' => 'tmp');

        $this->assertEquals($expected, $values);

        $values = $template->retrieveParams($this->output, array(new DummyPrompt()));
        $expected = array('repository_name' => 'tmp', 'dummy2' => 'dummy2', 'dummy' => 'dummy');

        $this->assertEquals($expected, $values);
    }

    /**
     * @covers CL\ComposerInit\TemplateHelper::download
     */
    public function testDownload()
    {
        $this->template->download($this->output, $this->getTestsDir().'test_downloaded.txt', 'file://'.$this->getTestsDir().'test.txt');

        $this->assertFileEquals($this->getTestsDir().'test.txt', $this->getTestsDir().'test_downloaded.txt');

        unlink($this->getTestsDir().'test_downloaded.txt');

        $expectedOutput = <<<OUTPUT
Downloading: file:///vagrant/libs/composer-init/tests/test.txt
Done.

OUTPUT;

        $this->assertEquals($expectedOutput, $this->output->output);
    }

    /**
     * @covers CL\ComposerInit\TemplateHelper::confirmValues
     */
    public function testConfirmValues()
    {
        $dialog = $this->template->getHelperSet()->get('dialog');

        $dialog->setInputStream($this->getInputStream("\n"));

        $this->template->confirmValues($this->output, array('test' => 'My Test', 'dummy' => 'Test Dummy'));

        $expectedOutput = <<<OUTPUT
Use These Variables:
  test: My Test
  dummy: Test Dummy
Confirm? (Y/n):
OUTPUT;

        $this->assertEquals($expectedOutput, $this->output->output);
    }

    /**
     * @covers CL\ComposerInit\TemplateHelper::getRepo
     */
    public function testGetRepo()
    {
        $expected = array('repo');
        $template = $this->getMock('CL\ComposerInit\TemplateHelper', array('getGitConfig', 'showGithubRepo'));

        $template
            ->expects($this->once())
            ->method('getGitConfig')
            ->with($this->equalTo('remote.origin.url'))
            ->will($this->returnValue('git@github.com:clippings/composer-init.git'));

        $template
            ->expects($this->once())
            ->method('showGithubRepo')
            ->with($this->equalTo('clippings'), $this->equalTo('composer-init'))
            ->will($this->returnValue($expected));

        $repo = $template->getRepo();

        $this->assertEquals($expected, $repo);
        $this->assertEquals($expected, $repo);
    }

    /**
     * @covers CL\ComposerInit\TemplateHelper::getGithub
     */
    public function testGetGithub()
    {
        $expected = new stdClass();
        $application = $this->getMock('Symfony\Component\Console\Application', array('getGithub'));

        $application
            ->expects($this->once())
            ->method('getGithub')
            ->will($this->returnValue($expected));

        $this->template->setApplication($application);

        $github = $this->template->getGithub();

        $this->assertSame($expected, $github);
    }

    /**
     * @covers CL\ComposerInit\TemplateHelper::getRepoField
     */
    public function testGetRepoField()
    {
        $template = $this->getMock('CL\ComposerInit\TemplateHelper', array('getRepo'));

        $template
            ->expects($this->once())
            ->method('getRepo')
            ->will($this->returnValue(array('repo' => 'test')));

        $this->assertEquals('test', $template->getRepoField('repo'));
    }

    /**
     * @covers CL\ComposerInit\TemplateHelper::showGithubRepo
     */
    public function testShowGithubRepo()
    {
        $expected = array('repo');

        $template = $this->getMock('CL\ComposerInit\TemplateHelper', array('getGithub'));
        $github = $this->getMock('Github\Client', array('api'));
        $api = $this->getMock('Github\Api\Repo', array('show'), array($github));

        $template
            ->expects($this->once())
            ->method('getGithub')
            ->will($this->returnValue($github));

        $github
            ->expects($this->once())
            ->method('api')
            ->with($this->equalTo('repo'))
            ->will($this->returnValue($api));

        $api
            ->expects($this->once())
            ->method('show')
            ->with($this->equalTo('owner_name'), $this->equalTo('repo_name'))
            ->will($this->returnValue($expected));

        $this->assertEquals($expected, $template->showGithubRepo('owner_name', 'repo_name'));
    }

    /**
     * @covers CL\ComposerInit\TemplateHelper::showGithubUser
     */
    public function testShowGithubUser()
    {
        $expected = array('user');

        $template = $this->getMock('CL\ComposerInit\TemplateHelper', array('getGithub'));
        $github = $this->getMock('Github\Client', array('api'));
        $api = $this->getMock('Github\Api\User', array('show'), array($github));

        $template
            ->expects($this->once())
            ->method('getGithub')
            ->will($this->returnValue($github));

        $github
            ->expects($this->once())
            ->method('api')
            ->with($this->equalTo('user'))
            ->will($this->returnValue($api));

        $api
            ->expects($this->once())
            ->method('show')
            ->with($this->equalTo('login_name'))
            ->will($this->returnValue($expected));

        $this->assertEquals($expected, $template->showGithubUser('login_name'));
    }

    /**
     * @covers CL\ComposerInit\TemplateHelper::getOrganization
     */
    public function testGetOrganization()
    {
        $expected = array('user');
        $template = $this->getMock('CL\ComposerInit\TemplateHelper', array('getRepoField', 'showGithubUser'));

        $template
            ->expects($this->once())
            ->method('getRepoField')
            ->with($this->equalTo('organization'))
            ->will($this->returnValue(array('login' => 'test')));

        $template
            ->expects($this->once())
            ->method('showGithubUser')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($expected));

        $organization = $template->getOrganization();

        $this->assertEquals($expected, $organization);
        $this->assertEquals($expected, $organization);
    }

    /**
     * @covers CL\ComposerInit\TemplateHelper::getOwner
     */
    public function testGetOwner()
    {
        $expected = array('user');
        $template = $this->getMock('CL\ComposerInit\TemplateHelper', array('getRepoField', 'showGithubUser'));

        $template
            ->expects($this->once())
            ->method('getRepoField')
            ->with($this->equalTo('owner'))
            ->will($this->returnValue(array('login' => 'test')));

        $template
            ->expects($this->once())
            ->method('showGithubUser')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($expected));

        $owner = $template->getOwner();

        $this->assertEquals($expected, $owner);
        $this->assertEquals($expected, $owner);
    }

}
