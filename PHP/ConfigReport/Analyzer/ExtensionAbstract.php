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
 * @package PHP_ConfigReport
 * @subpackage Analyzer
 * @author Jean-Marc Fontaine <jm@jmfontaine.net>
 * @copyright 2010 Jean-Marc Fontaine <jm@jmfontaine.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Abstract analyzer for PHP configuration
 *
 * @package PHP_ConfigReport
 * @subpackage Analyzer
 * @author Jean-Marc Fontaine <jm@jmfontaine.net>
 * @copyright 2010 Jean-Marc Fontaine <jm@jmfontaine.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
abstract class PHP_ConfigReport_Analyzer_ExtensionAbstract
{
    protected $_config;
    protected $_environment;
    protected $_extensionCode = 'This should be set in concrete classes';
    protected $_extensionName = 'This should be set in concrete classes';
    protected $_phpVersion;
    protected $_reportSection;

    protected function _checkRequirements()
    {
        // "core" is not a real extension name so it can not be retrieve using
        // get_loaded_extensions() function
        if ('core' === $this->getExtensionName()) {
            return true;
        }

        return in_array(
            $this->getExtensionCode(),
            get_loaded_extensions(true)
        );
    }

    protected function _populateReportSection()
    {
        $checks   = array();
        $path     = dirname(__FILE__) . '/' . $this->_extensionCode;
        $iterator = new DirectoryIterator($path);
        foreach ($iterator as $item) {
            if ($item->isFile() && !$item->isDot()) {
                $checks[] = substr($item->getFilename(), 0, -4);
            }
        }
        sort($checks);

        $reportSection = $this->getReportSection();
        foreach ($checks as $check) {
            $class = 'PHP_ConfigReport_Analyzer_' . $this->_extensionCode .
                      '_' . $check;
            $instance = new $class(
                $this->_config,
                $this->_environment,
                $this->_phpVersion,
                $this->_extensionCode,
                $this->_extensionName
            );
            $instance->check();
            $reportSection->addIssues($instance->getIssues());
        }
    }

    /**
     * Class constructor
     *
     * @param PHP_ConfigReport_Config $config Config instance
     * @return void
     */
    public function __construct(PHP_ConfigReport_Config $config, $environment,
        $phpVersion = null, $reportSection = null)
    {
        $this->_config      = $config;
        $this->_environment = $environment;
        $this->_phpVersion  = $phpVersion;

        if (null === $reportSection) {
            $reportSection = new PHP_ConfigReport_Report_Section(
                $this->getEnvironment()
            );
        }
        $reportSection->setExtensionName($this->getExtensionName());
        $this->_reportSection = $reportSection;

        if ($this->_checkRequirements()) {
            $this->_populateReportSection();
        }
    }

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

    /**
     * Generates and returns report section for this extension
     *
     * @return PHP_ConfigReport_Section Section
     */
    public function getReportSection()
    {
        return $this->_reportSection;
    }
}
