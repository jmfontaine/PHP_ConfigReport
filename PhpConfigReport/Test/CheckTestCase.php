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

class PhpConfigReport_Test_CheckTestCase
    extends PHPUnit_Framework_TestCase
{
    protected $_checkCode;
    protected $_extensionCode;
    protected $_extensionName;

    protected function _getIssues($config,
        $environment = PhpConfigReport_Analyzer::PRODUCTION)
    {
        if (is_string($config)) {
            $config = new PhpConfigReport_Config($config);
        }

        $className = sprintf(
            'PhpConfigReport_Analyzer_%s_%s',
            $this->_extensionCode,
            $this->_checkCode
        );

        $check = new $className(
            $config,
            $environment,
            $this->_extensionCode,
            $this->_extensionName
        );
        $check->check();

        return $check->getIssues();
    }

    protected function _initialize()
    {
        $className             = get_class($this);
        $parts                 = explode('_', $className);
        $this->_checkCode     = substr($parts[3], 0, -4);
        $this->_extensionCode = $parts[2];

        $class     = 'PhpConfigReport_Analyzer_' . $this->_extensionCode;
        $extension = new $class(
            new PhpConfigReport_Config(),
            PhpConfigReport_Analyzer::PRODUCTION
        );
        $this->_extensionName = $extension->getExtensionName();
    }

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->_initialize();
    }

    public function assertIssuesContainError($directiveName,
        $directiveValue, $environments = null, $type = null, $message = '')
    {
        $this->assertIssuesContainLevel(
            'PhpConfigReport_Issue_Error',
            $directiveName,
            $directiveValue,
            $environments,
            $type,
            $message
        );
    }

    public function assertIssuesContainLevel($level, $directiveName,
        $directiveValue, $environments = null, $type = null, $message = '')
    {
        if (is_array($directiveName) && is_array($directiveValue)) {
            $config = '';
            $count = count($directiveName);
            for ($i = 0; $i < $count; $i++) {
                $config .= "$directiveName[$i]=$directiveValue[$i]\n";
            }
        } else {
            $config = "$directiveName = $directiveValue";
        }

        if (null === $environments) {
            $environments = array(
                PhpConfigReport_Analyzer::DEVELOPMENT,
                PhpConfigReport_Analyzer::TESTING,
                PhpConfigReport_Analyzer::STAGING,
                PhpConfigReport_Analyzer::PRODUCTION,
            );
        } else {
            $environments = (array) $environments;
        }
        foreach ($environments as $environment) {
            $issues = $this->_getIssues($config, $environment);

            $count = 0;
            foreach ($issues as $issue) {
                $ok = true;

                if ($issue instanceof $level) {
                    if (null !== $directiveName &&
                        $issue->getDirectiveName() != $directiveName) {
                        $ok = false;
                    }

                    if (null !== $type && $type != $issue->getType()) {
                        $ok = false;
                    }
                }

                if (true === $ok) {
                    $count++;
                }
            }

            $this->assertTrue(0 < $count, $message);
        }
    }

    public function assertIssuesContainWarning($directiveName,
        $directiveValue, $environments = null, $type = null, $message = '')
    {
        $this->assertIssuesContainLevel(
            'PhpConfigReport_Issue_Warning',
            $directiveName,
            $directiveValue,
            $environments,
            $type,
            $message
        );
    }

    public function assertIssuesNotContainError($directiveName,
        $directiveValue, $environment = null, $type = null, $message = '')
    {
        $this->assertIssuesNotContainLevel(
            'PhpConfigReport_Issue_Error',
            $directiveName,
            $directiveValue,
            $environment,
            $type,
            $message
        );
    }

    public function assertIssuesNotContainLevel($level, $directiveName,
        $directiveValue, $environments = null, $type = null, $message = '')
    {
        if (is_array($directiveName) && is_array($directiveValue)) {
            $config = '';
            $count = count($directiveName);
            for ($i = 0; $i < $count; $i++) {
                $config .= "$directiveName[$i]=$directiveValue[$i]\n";
            }
        } else {
            $config = "$directiveName = $directiveValue";
        }

        if (null === $environments) {
            $environments = array(
                PhpConfigReport_Analyzer::DEVELOPMENT,
                PhpConfigReport_Analyzer::TESTING,
                PhpConfigReport_Analyzer::STAGING,
                PhpConfigReport_Analyzer::PRODUCTION,
            );
        } else {
            $environments = (array) $environments;
        }
        foreach ($environments as $environment) {
            $issues = $this->_getIssues($config, $environment);

            $count = 0;
            foreach ($issues as $issue) {
                $ok = true;

                if ($issue instanceof $level) {
                    if (null !== $directiveName &&
                        $issue->getDirectiveName() != $directiveName) {
                        $ok = false;
                    }

                    if (null !== $type && $type != $issue->getType()) {
                        $ok = false;
                    }
                }

                if (true === $ok) {
                    $count++;
                }
            }

            $this->assertTrue(0 == $count, $message);
        }
    }

    public function assertIssuesNotContainWarning($directiveName,
        $directiveValue, $environments = null, $type = null, $message = '')
    {
        $this->assertIssuesNotContainLevel(
            'PhpConfigReport_Issue_Warning',
            $directiveName,
            $directiveValue,
            $environments,
            $type,
            $message
        );
    }
}