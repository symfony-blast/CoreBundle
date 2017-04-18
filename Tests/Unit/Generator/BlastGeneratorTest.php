<?php

namespace Blast\CoreBundle\Generator;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class BlastGeneratorTest extends TestCase
{
    /**
     * @var BlastGenerator
     */
    protected $object;

    private $blastFile;
    private $skeletonDirectory;
    private $modelManager;

    private $root;
    private $file;

    /**
     * @todo check if it is pertinent to cover __construct and do it(or not) in setUp
     * @covers \Blast\CoreBundle\Generator\BlastGenerator::__construct
     */
    protected function setUp()
    {
        // As blast.yml is modified by the test
        $this->root = vfsStream::setup('BlastTestRessources');
        $this->file = vfsStream::newFile('blast.yml');
        $this->root->addChild($this->file);

        /*
         *   @todo test with and/or without original content
         */
        $this->file->setContent(file_get_contents('Resources/config/blast.yml'));

        $this->blastFile = vfsStream::url('BlastTestRessources/blast.yml');

        /*
         * @todo check if it should be tested with other skeleton
         */

        $this->skeletonDirectory = 'Resources/skeleton';

        // Sonata Model Manager is used to launch getExportFields
        // from the method addResource in Blast\CoreBundle\Generator\BlastGenerator
        // $managerType =  'sonata.admin.manager.orm';
        $modelManagerMock = $this->getMockForAbstractClass('Sonata\AdminBundle\Model\ModelManagerInterface');

        $map = array(
            array('Model', array('foo', 'bar', 'not_an_id')),
            array('Ledom', array('id', 'zoo', 'rab')),
         );
        $modelManagerMock
             ->expects($this->any())
             ->method('getExportFields')
             ->will($this->returnValueMap($map));

        $this->modelManager = $modelManagerMock;
        $this->object = new BlastGenerator(
             $this->blastFile,
             $this->modelManager,
             $this->skeletonDirectory
         );
    }

    protected function tearDown()
    {
    }

    /**
     * @covers \Blast\CoreBundle\Generator\BlastGenerator::addResource
     */
    public function testAddResource()
    {
        $this->assertFileExists($this->blastFile);

        $this->object->AddResource('Model');
        $this->object->AddResource('Ledom');

        $content = $this->file->getContent();

        // Model
        $this->assertContains('blast', $content);
        $this->assertContains('foo: ~', $content);
        $this->assertContains('Model:', $content);

        // Ledom
        $this->assertContains('Ledom:', $content);

        /*
         * @todo should be test for '#id: ~' when content is generated by sequence like ORM\GeneratedValue(strategy="AUTO")
         */
        $this->assertContains('id: ~', $content);

        // echo  $this->file->getContent();
    }
}
