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

class PHP_ConfigReport_Test_CheckTestCase
    extends PHPUnit_Framework_TestCase
{
    protected $_checkCode;
    protected $_extensionCode;
    protected $_extensionName;

    protected function _getIssues($config,
        $environment = PHP_ConfigReport_Analyzer::PRODUCTION,
        $phpVersion = null)
    {
        if (!$config instanceof PHP_ConfigReport_Config) {
            $config = new PHP_ConfigReport_Config($config);
        }

        $className = sprintf(
            'PHP_ConfigReport_Analyzer_%s_%s',
            $this->_extensionCode,
            $this->_checkCode
        );

        $check = new $className(
            $config,
            $environment,
            $phpVersion,
            $this->_extensionCode,
            $this->_extensionName
        );

        if (false === $check->isTestable()) {
            $this->markTestSkipped();
        }

        $check->check();

        return $check->getIssues();
    }

    protected function _initialize()
    {
        $className             = get_class($this);
        $parts                 = explode('_', $className);
        $this->_checkCode     = substr($parts[4], 0, -4);
        $this->_extensionCode = $parts[3];

        $class     = 'PHP_ConfigReport_Analyzer_' . $this->_extensionCode;
        $extension = new $class(
            new PHP_ConfigReport_Config(),
            PHP_ConfigReport_Analyzer::PRODUCTION
        );
        $this->_extensionName = $extension->getExtensionName();
    }

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->_initialize();
    }

    public function assertIssuesContainError($config, $directiveName = null,
        $environments = null, $type = null, $phpVersion = null, $message = '')
    {
        $this->assertIssuesContainLevel(
            'PHP_ConfigReport_Issue_Error',
            $config,
            $directiveName,
            $environments,
            $type,
            $phpVersion,
            false,
            $message
        );
    }

    public function assertIssuesContainErrorOnly($config, $directiveName = null,
        $environments = null, $type = null, $phpVersion = null, $message = '')
    {
        $this->assertIssuesContainLevel(
            'PHP_ConfigReport_Issue_Error',
            $config,
            $directiveName,
            $environments,
            $type,
            $phpVersion,
            true,
            $message
        );
    }

    public function assertIssuesContainLevel($level, $config,
        $directiveName = null, $environments = null, $type = null,
        $phpVersion = null, $strict = false, $message = '')
    {
        if (null === $environments) {
            $environments = array(
                PHP_ConfigReport_Analyzer::DEVELOPMENT,
                PHP_ConfigReport_Analyzer::TESTING,
                PHP_ConfigReport_Analyzer::STAGING,
                PHP_ConfigReport_Analyzer::PRODUCTION,
            );
        } else {
            $environments = (array) $environments;
        }
        foreach ($environments as $environment) {
            $issues = $this->_getIssues($config, $environment, $phpVersion);

            $count = 0;
            foreach ($issues as $issue) {
                $countIssue = true;

                if ($issue instanceof $level) {
                    if (null !== $directiveName &&
                        $issue->getDirectiveName() != $directiveName) {
                        $countIssue = false;
                    }

                    if (null !== $type && $type != $issue->getType()) {
                        $countIssue = false;
                    }
                }

                if (true === $countIssue) {
                    $count++;
                }
            }

            if (true === $strict) {
                $this->assertTrue(1 === $count, $message);
            } else {
                $this->assertTrue(0 < $count, $message);
            }
        }
    }

    public function assertIssuesEmpty($config, $directiveName = null,
        $environments = null, $phpVersion = null, $message = '')
    {
        if (null === $environments) {
            $environments = array(
                PHP_ConfigReport_Analyzer::DEVELOPMENT,
                PHP_ConfigReport_Analyzer::TESTING,
                PHP_ConfigReport_Analyzer::STAGING,
                PHP_ConfigReport_Analyzer::PRODUCTION,
            );
        } else {
            $environments = (array) $environments;
        }
        foreach ($environments as $environment) {
            $issues = $this->_getIssues($config, $environment, $phpVersion);

            $count = 0;
            foreach ($issues as $issue) {
                if (null !== $directiveName) {
                    if ($issue->getDirectiveName() == $directiveName) {
                        $count++;
                    }
                } else {
                    $count++;
                }
            }

            $this->assertTrue(0 === $count, $message);
        }
    }

    public function assertIssuesContainWarning($config,
        $directiveName = null, $environments = null, $type = null,
        $phpVersion = null, $message = '')
    {
        $this->assertIssuesContainLevel(
            'PHP_ConfigReport_Issue_Warning',
            $config,
            $directiveName,
            $environments,
            $type,
            $phpVersion,
            false,
            $message
        );
    }

    public function assertIssuesContainWarningOnly($config,
        $directiveName = null, $environments = null, $type = null,
        $phpVersion = null, $message = '')
    {
        $this->assertIssuesContainLevel(
            'PHP_ConfigReport_Issue_Warning',
            $config,
            $directiveName,
            $environments,
            $type,
            $phpVersion,
            true,
            $message
        );
    }


    public function assertIssuesNotContainError($config,
        $directiveName = null, $environment = null, $type = null,
        $phpVersion = null, $message = '')
    {
        $this->assertIssuesNotContainLevel(
            'PHP_ConfigReport_Issue_Error',
            $config,
            $directiveName,
            $environment,
            $type,
            $phpVersion,
            $message
        );
    }

    public function assertIssuesNotContainLevel($level, $config,
        $directiveName = null, $environments = null, $type = null,
        $phpVersion = null, $message = '')
    {
        if (null === $environments) {
            $environments = array(
                PHP_ConfigReport_Analyzer::DEVELOPMENT,
                PHP_ConfigReport_Analyzer::TESTING,
                PHP_ConfigReport_Analyzer::STAGING,
                PHP_ConfigReport_Analyzer::PRODUCTION,
            );
        } else {
            $environments = (array) $environments;
        }
        foreach ($environments as $environment) {
            $issues = $this->_getIssues($config, $environment, $phpVersion);

            $count = 0;
            foreach ($issues as $issue) {
                $countIssue = false;

                if ($issue instanceof $level) {
                    $countIssue = true;

                    if (null !== $directiveName &&
                        $issue->getDirectiveName() != $directiveName) {
                        $countIssue = false;
                    }

                    if (null !== $type && $type != $issue->getType()) {
                        $countIssue = false;
                    }
                }

                if (true === $countIssue) {
                    $count++;
                }
            }

            $this->assertTrue(0 == $count, $message);
        }
    }

    public function assertIssuesNotContainWarning($config,
        $directiveName = null, $environments = null, $type = null,
        $phpVersion = null, $message = '')
    {
        $this->assertIssuesNotContainLevel(
            'PHP_ConfigReport_Issue_Warning',
            $config,
            $directiveName,
            $environments,
            $type,
            $phpVersion,
            $message
        );
    }
}
