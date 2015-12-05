<?php

namespace Baumgartner\Xpp;

use Baumgartner\Xpp\ODE\DifferentialEquationsBag;
use Baumgartner\Xpp\ODE\InitialConditionsBag;
use Baumgartner\Xpp\ODE\OptionsBag;
use Baumgartner\Xpp\ODE\ParametersBag;

class Parser {
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var ParametersBag
     */
    private $parameters;

    /**
     * @var OptionsBag
     */
    private $options;

    /**
     * @var InitialConditionsBag
     */
    private $ic;

    /**
     * @var DifferentialEquationsBag
     */
    private $de;

    /**
     * Parser constructor.
     *
     * @param string $filePath
     * @param ParametersBag|null $parameters
     * @param OptionsBag|null $options
     * @param InitialConditionsBag|null $ic
     * @param DifferentialEquationsBag|null $de
     */
    public function __construct($filePath, $parameters = null, $options = null, $ic = null, $de = null)
    {
        $this->filePath = $filePath;
        $this->parameters = $parameters ?: new ParametersBag();
        $this->options = $options ?: new OptionsBag();
        $this->ic = $ic ?: new InitialConditionsBag();
        $this->de = $de ?: new DifferentialEquationsBag();
    }

    /**
     * Parses given .ode file.
     *
     * @return $this
     */
    public function parse()
    {
        // Open the file for reading
        $file = fopen($this->filePath, 'r');

        // Read line by line
        while (! feof($file)) {
            $this->parseLine(fgets($file));
        }

        // Close the file
        fclose($file);

        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return ParametersBag
     */
    public function getParametersBag()
    {
        return $this->parameters;
    }

    /**
     * @return OptionsBag
     */
    public function getOptionsBag()
    {
        return $this->options;
    }

    /**
     * @return InitialConditionsBag
     */
    public function getInitialConditionsBag()
    {
        return $this->ic;
    }

    /**
     * @return DifferentialEquationsBag
     */
    public function getDifferentialEquationsBag()
    {
        return $this->de;
    }

    /**
     * Parses given line a stores information to bags.
     * http://www.math.pitt.edu/~bard/xpp/help/xppexample.html
     *
     * @param $line
     */
    private function parseLine($line)
    {
        // Remove comment from line
        $line = $this->removeCommentFromLine($line);

        // If line now contains "par" add to parameter bag
        $this->addParametersToBagFromLine($line);

        // If line contains "@" add to options bag
        $this->addOptionsToBagFromLine($line);

        // If line contains "init" add to IC bag
        $this->addInitialConditionsToBagFromLine($line);

        // If line contains "'" or "/dt" or "t+1" add to DE bag
        $this->addDifferentialEquationsToBagFromLine($line);
    }

    /**
     * Returns line without the comment part.
     *
     * @param $line
     * @return string
     */
    private function removeCommentFromLine($line)
    {
        return strstr($line, '#', true) ? strstr($line, '#', true) : $line; // get content before "#" character
    }

    /**
     * @param $line
     */
    private function addParametersToBagFromLine($line)
    {
        $this->addKeywordKeyValuePairsToBagFromLine('parameters', 'par ', $line);
    }

    /**
     * @param $line
     */
    private function addOptionsToBagFromLine($line)
    {
        $this->addKeywordKeyValuePairsToBagFromLine('options', '@', $line);
    }

    /**
     * @param $line
     */
    private function addInitialConditionsToBagFromLine($line)
    {
        $this->addKeywordKeyValuePairsToBagFromLine('ic', 'init ', $line);
    }

    /**
     * Filters key value pairs from line by keyword and saves them to given bag
     *
     * @param $bag
     * @param $keyword
     * @param $line
     */
    private function addKeywordKeyValuePairsToBagFromLine($bag, $keyword, $line)
    {
        if (strpos($line, $keyword) !== FALSE) {
            // Get string after prefix
            $paramsString = substr($line, strpos($line, $keyword) + strlen($keyword), strlen($line));

            // Try to explode comma separated values into an array of keys and values
            $params = explode(',', $paramsString);

            foreach ($params as $parameter) {
                list($key, $value) = explode('=', $parameter, 2);
                $this->{$bag}->add(trim($key), trim($value));
            }
        }
    }

    private function addDifferentialEquationsToBagFromLine($line)
    {
        if (strpos($line, "'") !== false || strpos($line, "/dt") !== false || strpos($line, "t+1") !== false) {
            list($leftSide, $rightSide) = explode('=', $line, 2);
            $this->de->add(trim($leftSide), trim($rightSide));
        }
    }
}
