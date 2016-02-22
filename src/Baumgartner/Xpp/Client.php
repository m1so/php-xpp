<?php

namespace Baumgartner\Xpp;

class Client
{
    /**
     * @var string|null
     */
    private $odeFilePath;

    private $resultFilePath;

    private $logFilePath;

    private $pathToXpp;

    private $baseDir;

    private $options;

    private $parser;

    /**
     * Client constructor.
     */
    public function __construct($odePath = null, $options = [])
    {
        $defaultOptions = [
            'base_dir' => __DIR__,
            'xpp_path' => __DIR__,
        ];

        $this->options = array_merge($defaultOptions, $options);

        $this->odeFilePath = $odePath ? $odePath : $this->options['base_dir'].'model.ode';
        $this->resultFilePath = $this->options['base_dir'].'result.dat';
        $this->logFilePath = $this->options['base_dir'].'xpp.log';

        $this->pathToXpp = $this->options['xpp_path'];

        $this->parser = new Parser($this->odeFilePath);
    }

    /**
     * Run XPP executable
     *
     * @return mixed Status code returned from command
     */
    public function run()
    {
        $this->parser->parse();

        $command = sprintf(
            "%s %s -silent -outfile %s -logfile %s",
            $this->pathToXpp,
            $this->odeFilePath,
            $this->resultFilePath,
            $this->logFilePath
        );

        // We don't need output, since we are using silent flag and specifying log file path
        exec($command, $output, $status);

        return $status;
    }

    public function getParser()
    {
        return $this->parser;
    }

    public function getOdeFilePath()
    {
        return $this->odeFilePath;
    }

    public function setOdeFilePath($odeFilePath)
    {
        $this->odeFilePath = $odeFilePath;
        $this->parser->setFilePath($odeFilePath);
    }

    public function getPathToXpp()
    {
        return $this->pathToXpp;
    }

    public function setPathToXpp($pathToXpp)
    {
        $this->pathToXpp = $pathToXpp;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function getBaseDir()
    {
        return $this->baseDir;
    }

    public function setBaseDir($baseDir)
    {
        $this->baseDir = $baseDir;
        $this->logFilePath = $baseDir.'xpp.log';
        $this->resultFilePath = $baseDir.'result.dat';
    }
}