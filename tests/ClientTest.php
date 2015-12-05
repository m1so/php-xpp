<?php
use Baumgartner\Xpp\Client;

class ClientTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_runs_xpp_from_console_and_generates_files()
    {
        $client = new Client(__DIR__.'/stubs/ode/goodwin.ode', [
            'base_dir' => __DIR__.'/output/',
            'xpp_path' => __DIR__.'/../xppaut',
        ]);

        $client->run();

        $this->assertFileExists(__DIR__.'/output/result.dat');
        $this->assertGreaterThan(10000, filesize(__DIR__.'/output/result.dat'));
        $this->assertFileExists(__DIR__.'/output/xpp.log');
    }

    /** @test */
    public function it_checks_alternate_client_setter_syntax()
    {
        $client = new Client();
        $client->setBaseDir(__DIR__.'/output/');
        $client->setOdeFilePath(__DIR__.'/stubs/ode/goodwin.ode');
        $client->setPathToXpp(__DIR__.'/../xppaut');

        $client->run();

        $this->assertFileExists(__DIR__.'/output/result.dat');
        $this->assertGreaterThan(10000, filesize(__DIR__.'/output/result.dat'));
        $this->assertFileExists(__DIR__.'/output/xpp.log');
    }

    public function tearDown()
    {
        $fileToDelete = [
            __DIR__.'/output/result.dat',
            __DIR__.'/output/xpp.log',
        ];

        foreach ($fileToDelete as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

    }
    
}
