<?php
use Baumgartner\Xpp\Parser;

class ParserTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_loads_all_parameters_from_ode_file()
    {
        $parser = $this->setUpParser("/stubs/ode/only/parameters.ode");
        $bag = $parser->getParametersBag();

        $this->assertEquals(1, $bag->get('a'));
        $this->assertEquals(2, $bag->get('b'));
        $this->assertEquals(3, $bag->get('c'));
        $this->assertEquals(4, $bag->get('d'));
    }

    /** @test */
    public function it_loads_all_options_from_ode_file()
    {
        $parser = $this->setUpParser("/stubs/ode/only/options.ode");
        $bag = $parser->getOptionsBag();

        $this->assertEquals(40, $bag->get('total'));
        $this->assertEquals('u', $bag->get('xp'));
        $this->assertEquals('v', $bag->get('yp'));
    }

    /** @test */
    public function it_loads_all_initial_conditions_from_ode_file()
    {
        $parser = $this->setUpParser("/stubs/ode/only/initial-conditions.ode");
        $bag = $parser->getInitialConditionsBag();

        $this->assertEquals(1, $bag->get('a'));
        $this->assertEquals(2, $bag->get('b'));
        $this->assertEquals(3, $bag->get('c'));
    }

    /** @test */
    public function it_loads_all_differential_equations_from_ode_file()
    {
        $parser = $this->setUpParser("/stubs/ode/only/differential-equations.ode");
        $bag = $parser->getDifferentialEquationsBag();

        $this->assertCount(5, $bag->keys());
        $this->assertEquals('1', $bag->get('dx/dt'));
        $this->assertEquals('-u+f(aee*u-aie*v-ze+i_e(t))', $bag->get("u'"));
        $this->assertEquals('(-v+f(aei*u-aii*v-zi+i_i(t)))/tau', $bag->get("v'"));
        $this->assertEquals('2', $bag->get('g(t+1)'));
        $this->assertEquals('3', $bag->get('h[t+1]'));
    }

    /** @test */
    public function it_parses_complex_ode_file()
    {
        $parser = $this->setUpParser("/stubs/ode/complex.ode");

        $params = $parser->getParametersBag();
        $options = $parser->getOptionsBag();
        $ics = $parser->getInitialConditionsBag();
        $des = $parser->getDifferentialEquationsBag();

        $this->assertCount(12, $params->keys());
        $this->assertEquals(1, $params->get('tau'));

        $this->assertCount(7, $options->keys());
        $this->assertEquals(40, $options->get('total'));

        $this->assertCount(2, $ics->keys());
        $this->assertEquals(0.1, $ics->get('u'));

        $this->assertCount(2, $des->keys());
        $this->assertEquals('-u+f(aee*u-aie*v-ze+i_e(t))', $des->get("u'"));
    }

    private function setUpParser($relativeFilePath)
    {
        return (new Parser(__DIR__.$relativeFilePath))->parse();
    }
}
