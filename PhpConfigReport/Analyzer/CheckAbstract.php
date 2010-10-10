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
    protected $_phpVersion;

    protected function _addError($directiveName, $type, $directiveActualValue,
        $directiveSuggestedValue, $comments)
    {
        $issue = new PhpConfigReport_Issue_Error(
            $this->_getExtensionName(),
            $directiveName,
            $type,
            $directiveActualValue,
            $directiveSuggestedValue,
            $comments
        );

        $this->_addIssue($issue);
    }

    protected function _addIssue(PhpConfigReport_Issue_Interface $issue)
    {
        $this->_issues[] = $issue;
    }

    protected function _addWarning($directiveName, $type, $directiveActualValue,
        $directiveSuggestedValue, $comments)
    {
        $issue = new PhpConfigReport_Issue_Warning(
            $this->_getExtensionName(),
            $directiveName,
            $type,
            $directiveActualValue,
            $directiveSuggestedValue,
            $comments
        );

        $this->_addIssue($issue);
    }

    protected function _comparePhpVersion($submittedVersion,
        $operator = '=', $strict = false)
    {
        // Allow check when PHP version is unknown
        if (false === $strict && null === $this->_getPhpVersion()) {
            return true;
        }

        return version_compare(
            $this->_getPhpVersion(),
            $submittedVersion,
            $operator
        );
    }

    abstract protected function _doCheck();

    protected function _getConfig()
    {
        return $this->_config;
    }

    protected function _getDirective($directiveName)
    {
        return $this->_getConfig()->getDirective($directiveName);
    }

    protected function _getEnvironment()
    {
        return $this->_environment;
    }

    protected function _getExtensionCode()
    {
        return $this->_extensionCode;
    }

    protected function _getExtensionName()
    {
        return $this->_extensionName;
    }

    protected function _getPhpVersion()
    {
        return $this->_phpVersion;
    }

    protected function _getSizeDirective($directiveName,
        $targetUnit = PhpConfigReport_Config::MEGA_BYTES)
    {
        return $this->_getConfig()->getSizeDirective(
            $directiveName,
            $targetUnit
        );
    }

    protected function _isSizeDirectiveDifferentFrom($directiveName, $treshold)
    {
        return $this->_getConfig()->isSizeDirectiveDifferentFrom(
            $directiveName,
            $treshold
        );
    }

    protected function _isSizeDirectiveEqualTo($directiveName, $treshold)
    {
        return $this->_getConfig()->isSizeDirectiveEqualTo(
            $directiveName,
            $treshold
        );
    }

    protected function _isSizeDirectiveGreaterThan($directiveName, $treshold)
    {
        return $this->_getConfig()->isSizeDirectiveGreaterThan(
            $directiveName,
            $treshold
        );
    }

    protected function _isSizeDirectiveGreaterThanOrEqualTo($directiveName,
        $treshold)
    {
        return $this->_getConfig()->isSizeDirectiveGreaterThanOrEqual(
            $directiveName,
            $treshold
        );
    }

    protected function _isSizeDirectiveLessThan($directiveName, $treshold)
    {
        return $this->_getConfig()->isSizeDirectiveLessThan(
            $directiveName,
            $treshold
        );
    }

    protected function _isSizeDirectiveLessThanOrEqual($directiveName, $treshold)
    {
        return $this->_getConfig()->isSizeDirectiveLessThanOrEqual(
            $directiveName,
            $treshold
        );
    }

    protected function _isDirectiveDisabled($directiveName)
    {
        return $this->_getConfig()->isDirectiveDisabled($directiveName);
    }

    protected function _isDirectiveEnabled($directiveName)
    {
        return $this->_getConfig()->isDirectiveEnabled($directiveName);
    }

    protected function _isDirectiveNumeric($directiveName)
    {
        return $this->_getConfig()->isDirectiveNumeric($directiveName);
    }

    protected function _isEnvironment($expectedEnvironment)
    {
        return $this->_getEnvironment() == $expectedEnvironment;
    }

    protected function _isEnvironmentNot($expectedEnvironment)
    {
        return $this->_getEnvironment() == $expectedEnvironment;
    }

    protected function _isPhpVersionEqualTo($version, $strict = false)
    {
        return $this->_comparePhpVersion($version, '=', $strict);
    }

    protected function _isPhpVersionGreaterThan($version, $strict = false)
    {
        return $this->_comparePhpVersion($version, '>', $strict);
    }

    protected function _isPhpVersionGreaterThanOrEqualTo($version,
        $strict = false)
    {
        return $this->_comparePhpVersion($version, '>=', $strict);
    }

    protected function _isPhpVersionLessThan($version, $strict = false)
    {
        return $this->_comparePhpVersion($version, '<', $strict);
    }

    protected function _isPhpVersionLessThanOrEqualTo($version,
        $strict = false)
    {
        return $this->_comparePhpVersion($version, '<=', $strict);
    }

    public function __construct(PhpConfigReport_Config $config, $environment,
        $phpVersion, $extensionCode, $extensionName)
    {
        $this->_config        = $config;
        $this->_environment   = $environment;
        $this->_extensionCode = $extensionCode;
        $this->_extensionName = $extensionName;
        $this->_phpVersion    = $phpVersion;
    }

    public function check()
    {
        if ($this->isTestable()) {
            $this->_doCheck();
        }
    }

    public function getIssues()
    {
        return $this->_issues;
    }

    public function isTestable()
    {
        return true;
    }
}
