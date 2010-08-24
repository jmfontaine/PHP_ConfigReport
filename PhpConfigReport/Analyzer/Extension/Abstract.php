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
 * @package PHP Config Report
 * @author Jean-Marc Fontaine <jm@jmfontaine.net>
 * @copyright 2010 Jean-Marc Fontaine <jm@jmfontaine.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Abstract analyzer for PHP configuration
 */
abstract class PhpConfigReport_Analyzer_Extension_Abstract
{
    protected $_config;
    protected $_environment;
    protected $_extensionName = 'This should be set in concrete classes';
    protected $_reportSection;

    protected function _populateReportSection()
    {
        $classReflection = new ReflectionClass(get_class($this));
        foreach ($classReflection->getMethods() as $methodReflection) {
            if ('check' == substr($methodReflection->name, 0, 5)) {
                $this->{$methodReflection->name}();
            }
        }
    }

    /**
     * Class constructor
     *
     * @param PhpConfigReport_Config $config Config instance
     * @return void
     */
    public function __construct(PhpConfigReport_Config $config, $environment,
        $reportSection = null)
    {
        $this->_config      = $config;
        $this->_environment = $environment;

        if (null === $reportSection) {
            $reportSection = new PhpConfigReport_Report_Section(
                $this->getEnvironment()
            );
        }
        $reportSection->setExtensionName($this->getExtensionName());
        $this->_reportSection = $reportSection;

        $this->_populateReportSection();
    }

    public function addError($directiveName, $actualValue, $expectedValue,
        $comments)
    {
        $this->getReportSection()->addError(
            $directiveName,
            $actualValue,
            $expectedValue,
            $comments
        );
    }

    public function addWarning($directiveName, $actualValue, $expectedValue,
        $comments)
    {
        $this->getReportSection()->addWarning(
            $directiveName,
            $actualValue,
            $expectedValue,
            $comments
        );
    }

    public function getConfig()
    {
        return $this->_config;
    }

    public function getEnvironment()
    {
        return $this->_environment;
    }

    public function getExtensionName()
    {
        return $this->_extensionName;
    }

    /**
     * Generates and returns report section for this extension
     *
     * @return PhpConfigReport_Section Section
     */
    public function getReportSection()
    {
        return $this->_reportSection;
    }

    public function isDirectiveDisabled($directiveName)
    {
        return $this->getConfig()->isDirectiveDisabled($directiveName);
    }

    public function isDirectiveEnabled($directiveName)
    {
        return $this->getConfig()->isDirectiveEnabled($directiveName);
    }

    public function isEnvironment()
    {
        $environment = $this->getEnvironment();
        foreach (func_get_args() as $expectedEnvironment) {
            if ($environment == $expectedEnvironment) {
                return true;
            }
        }

        return false;
    }

    public function isEnvironmentNot()
    {
        $environment = $this->getEnvironment();
        foreach (func_get_args() as $expectedEnvironment) {
            if ($environment == $expectedEnvironment) {
                return false;
            }
        }

        return true;
    }
}