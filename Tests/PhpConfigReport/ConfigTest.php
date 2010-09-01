<?php
/**
 * Copyright (c) 2010, Jean-Marc Fontaine
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package Tests
 * @author Jean-Marc Fontaine <jm@jmfontaine.net>
 * @copyright 2010 Jean-Marc Fontaine <jm@jmfontaine.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

class PhpConfigReport_ConfigTest
    extends PHPUnit_Framework_TestCase
{
    /*
     * Methods
     */

   /**
     * @test
     */
    public function passingConfigurationFilePathToConstructorloadsConfigurationFromFile()
    {
        $config = new PhpConfigReport_Config(FILES_PATH . '/valid.ini');

        $this->assertSame('test_value_1', $config->getDirective('display_errors'));
        $this->assertSame('test_value_2', $config->getDirective('log_errors'));
    }

   /**
     * @test
     */
    public function passingConfigurationStringToConstructorloadsConfigurationFromString()
    {
        $string = "
        display_errors = test_value_1\n
        log_errors = test_value_2";

        $config = new PhpConfigReport_Config($string);

        $this->assertSame('test_value_1', $config->getDirective('display_errors'));
        $this->assertSame('test_value_2', $config->getDirective('log_errors'));
    }

   /**
     * @test
     */
    public function passingNothingToConstructorloadsConfigurationFromSystem()
    {
        $oldDisplayErrors = ini_set('display_errors', 'test_value_1');
        $oldLogErrors     = ini_set('log_errors'    , 'test_value_2');

        $config = new PhpConfigReport_Config();

        $this->assertSame('test_value_1', $config->getDirective('display_errors'));
        $this->assertSame('test_value_2', $config->getDirective('log_errors'));

        if (false !== $oldDisplayErrors) {
            ini_set('display_errors', $oldDisplayErrors);
        }
        if (false !== $oldLogErrors) {
            ini_set('log_errors', $oldLogErrors);
        }
    }

   /**
     * @test
     */
    public function loadsConfigurationFromFile()
    {
        $config = new PhpConfigReport_Config();
        $config->loadFromFile(FILES_PATH . '/valid.ini');

        $this->assertSame('test_value_1', $config->getDirective('display_errors'));
        $this->assertSame('test_value_2', $config->getDirective('log_errors'));
    }

   /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function failsLoadingMissingConfigurationFile()
    {
        $config = new PhpConfigReport_Config();
        $config->loadFromFile(FILES_PATH . '/missing.ini');
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     */
    public function failsLoadingInvalidConfigurationFile()
    {
        $config = new PhpConfigReport_Config();
        $config->loadFromFile(FILES_PATH . '/invalid.ini');
    }

    /**
     * @test
     */
    public function loadsConfigurationFromString()
    {
        $string = "
        display_errors = test_value_1\n
        log_errors = test_value_2";

        $config = new PhpConfigReport_Config();
        $config->loadFromString($string);

        $this->assertSame('test_value_1', $config->getDirective('display_errors'));
        $this->assertSame('test_value_2', $config->getDirective('log_errors'));
    }

    /**
     * @test
     * @expectedException UnexpectedValueException
     */
    public function failsLoadingInvalidConfigurationString()
    {
        $string = '<?xml version="1.0" encoding="UTF-8"?>
                   <root>
                       <node></node>
                   </root>';

        $config = new PhpConfigReport_Config();
        $config->loadFromString($string);
    }

    /**
     * @test
     */
    public function loadsConfigurationFromSystem()
    {
        $oldDisplayErrors = ini_set('display_errors', 'test_value_1');
        $oldLogErrors     = ini_set('log_errors'    , 'test_value_2');

        $config = new PhpConfigReport_Config();
        $config->loadFromSystem();

        $this->assertSame('test_value_1', $config->getDirective('display_errors'));
        $this->assertSame('test_value_2', $config->getDirective('log_errors'));

        if (false !== $oldDisplayErrors) {
            ini_set('display_errors', $oldDisplayErrors);
        }
        if (false !== $oldLogErrors) {
            ini_set('log_errors', $oldLogErrors);
        }
    }

    /**
     * @test
     */
    public function canCheckIfADirectiveIsDisabled()
    {
        $string = "
        display_errors = 0\n
        log_errors = off";

        $config = new PhpConfigReport_Config($string);

        $this->assertTrue($config->isDirectiveDisabled('display_errors'));
        $this->assertTrue($config->isDirectiveDisabled('log_errors'));
    }

    /**
     * @test
     */
    public function canCheckIfADirectiveIsEnabled()
    {
        $string = "
        display_errors = 1\n
        log_errors = on";

        $config = new PhpConfigReport_Config($string);

        $this->assertTrue($config->isDirectiveEnabled('display_errors'));
        $this->assertTrue($config->isDirectiveEnabled('log_errors'));
    }

    /**
     * @test
     */
    public function canCheckIfADirectiveIsSet()
    {
        $string = "
        display_errors = 1";

        $config = new PhpConfigReport_Config($string);

        $this->assertTrue($config->isDirectiveSet('display_errors'));
        $this->assertFalse($config->isDirectiveSet('log_errors'));
    }

    /**
     * @test
     */
    public function canRetrieveFlagDirectiveValue()
    {
        $string = "
        dummy1  = 1\n
        dummy2  = '1'\n
        dummy3  = on\n
        dummy4  = ON\n
        dummy5  = true\n
        dummy6  = TRUE\n
        dummy7  = yes\n
        dummy8  = YES\n
        dummy9  = 0\n
        dummy10 = '0'\n
        dummy11 = off\n
        dummy12 = OFF\n
        dummy13 = false\n
        dummy14 = FALSE\n
        dummy15 = no\n
        dummy16 = NO\n
        dummy17 = NONE\n
        dummy18 = 'on'\n
        dummy19 = 'ON'\n
        dummy20 = 'true'\n
        dummy21 = 'TRUE'\n
        dummy22 = 'yes'\n
        dummy23 = 'YES'\n
        dummy24 = 'off'\n
        dummy25 = 'OFF'\n
        dummy26 = 'false'\n
        dummy27 = 'FALSE'\n
        dummy28 = 'no'\n
        dummy29 = 'Dummy'\n";

        $config = new PhpConfigReport_Config($string);

        for ($i = 1; $i < 9; $i++) {
            $this->assertTrue($config->isDirectiveEnabled('dummy' . $i), 'dummy' . $i);
        }

        for ($i = 9; $i < 18; $i++) {
            $this->assertFalse($config->isDirectiveEnabled('dummy' . $i));
        }

        for ($i = 18; $i < 30; $i++) {
            try {
                $config->isDirectiveEnabled('dummy' . $i);
                $this->fail("'dummy$i' directive did not raised an exception");
            } catch (UnexpectedValueException $exception) {
                // Do nothing since it is expected behavior
            }
        }
    }

    /**
     * @test
     */
    public function canRetrieveAllTheDirectivesAtOnce()
    {
        $string = "
        display_errors = 1\n
        log_errors = 0\n
        track_errors = on\n
        register_globals = off";

        $config = new PhpConfigReport_Config($string);

        $expectedDirectives = array(
            'display_errors'   => 1,
            'log_errors'       => 0,
            'track_errors'     => 1,
            'register_globals' => '',
        );

        $this->assertEquals($expectedDirectives, $config->getDirectives());
    }

    /*
     * Bugs
     */
}