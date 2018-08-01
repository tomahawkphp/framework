<?php

namespace Tomahawk\Generator\Tests;

use Tomahawk\HttpKernel\Bundle\BundleInterface;
use Tomahawk\Generator\ModelGenerator;

class ModelGeneratorTest extends GeneratorTest
{
    public function testGenerateModel()
    {
        $generator = $this->getGenerator();

        $generator->generate($this->tmpDir .'/App/User', 'App\User', 'User');

        $files = [
            'App/User/User.php',
        ];

        foreach ($files as $file) {
            $this->assertTrue(file_exists($this->tmpDir.'/'.$file), sprintf('%s has been generated', $file));
        }

        $content = file_get_contents($this->tmpDir.'/App/User/User.php');
        $strings = array(
            'namespace App\\User',
            'class User',
        );
        foreach ($strings as $string) {
            $this->assertContains($string, $content);
        }
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGenerateModelFileExists()
    {
        $this->filesystem->mkdir($this->tmpDir.'/App/Page');
        $this->filesystem->touch($this->tmpDir.'/App/Page/Page.php');

        $this->getGenerator()->generate($this->tmpDir .'/App/Page', 'App\Page', 'Page');

    }

    /**
     * @return ModelGenerator
     */
    protected function getGenerator()
    {
        $generator = new ModelGenerator($this->filesystem);
        $generator->setSkeletonDirs([__DIR__.'/../Resources/skeleton']);

        return $generator;
    }

}
