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
abstract class PhpConfigReport_Analyzer_CheckAbstract
{
    protected $_config;
    protected $_environment;
    protected $_extensionCode;
    protected $_extensionName;
    protected $_issues = array();

    public function __construct(PhpConfigReport_Config $config, $environment,
        $extensionCode, $extensionName)
    {
        $this->_config        = $config;
        $this->_environment   = $environment;
        $this->_extensionCode = $extensionCode;
        $this->_extensionName = $extensionName;
    }

    public function addError($directiveName, $type, $directiveActualValue,
        $directiveSuggestedValue, $comments)
    {
        $issue = new PhpConfigReport_Issue_Error(
            $this->getExtensionName(),
            $directiveName,
            $type,
            $directiveActualValue,
            $directiveSuggestedValue,
            $comments
        );

        $this->addIssue($issue);
    }

    public function addIssue(PhpConfigReport_Issue_Interface $issue)
    {
        $this->_issues[] = $issue;
    }

    public function addWarning($directiveName, $type, $directiveActualValue,
        $directiveSuggestedValue, $comments)
    {
        $issue = new PhpConfigReport_Issue_Warning(
            $this->getExtensionName(),
            $directiveName,
            $type,
            $directiveActualValue,
            $directiveSuggestedValue,
            $comments
        );

        $this->addIssue($issue);
    }

    abstract public function check();

    public function getConfig()
    {
        return $this->_config;
    }

    public function getEnvironment()
    {
        return $this->_environment;
    }

    public function getExtensionCode()
    {
        return $this->_extensionCode;
    }

    public function getExtensionName()
    {
        return $this->_extensionName;
    }

    public function getIssues()
    {
        return $this->_issues;
    }

    public function isDirectiveDisabled($directiveName)
    {
        return $this->getConfig()->isDirectiveDisabled($directiveName);
    }

    public function isDirectiveEnabled($directiveName)
    {
        return $this->getConfig()->isDirectiveEnabled($directiveName);
    }

    public function isEnvironment($expectedEnvironment)
    {
        return $this->getEnvironment() == $expectedEnvironment;
    }

    public function isEnvironmentNot($expectedEnvironment)
    {
        return $this->getEnvironment() == $expectedEnvironment;
    }
}