<?php
/**
 * Copyright (c) 2010-2011, Jean-Marc Fontaine
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name PHP_ConfigReport nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL Jean-Marc Fontaine BE LIABLE FOR ANY
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
 * @copyright 2010-2011 Jean-Marc Fontaine <jm@jmfontaine.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Analyzer for PHP configuration
 *
 * @package PHP_ConfigReport
 * @subpackage Analyzer
 * @author Jean-Marc Fontaine <jm@jmfontaine.net>
 * @copyright 2010-2011 Jean-Marc Fontaine <jm@jmfontaine.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class PHP_ConfigReport_Analyzer
{
    const DEVELOPMENT = 'development';
    const TESTING     = 'testing';
    const STAGING     = 'staging';
    const PRODUCTION  = 'production';

    /**
     * Config instance
     *
     * @var PHP_ConfigReport_Config
     */
    protected $_config;

    protected $_environment;

    protected $_loadedExtensions;

    protected $_phpVersion;

    /**
     * Class constructor
     *
     * @param PHP_ConfigReport_Config $config Config instance
     * @return void
     */
    public function __construct(PHP_ConfigReport_Config $config,
        $environment = self::PRODUCTION, $phpVersion = null,
        $loadedExtensions = array())
    {
        $this->_config           = $config;
        $this->_environment      = $environment;
        $this->_loadedExtensions = $loadedExtensions;
        $this->_phpVersion       = $phpVersion;
    }

    /**
     * Returns configuration
     *
     * @return PHP_ConfigReport_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Returns environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }

    /**
     * Returns loaded extensions
     *
     * @return array
     */
    public function getLoadedExtensions()
    {
        return $this->_loadedExtensions;
    }

    /**
     * Returns PHP version
     *
     * @return string
     */
    public function getPhpVersion()
    {
        return $this->_phpVersion;
    }

    /**
     * Generates and returns report
     *
     * @return PHP_ConfigReport_Report Report
     */
    public function getReport()
    {
        $report = new PHP_ConfigReport_Report(
            $this->_environment,
            $this->_phpVersion
        );

        $extensions = array();
        $path       = dirname(__FILE__) . '/Analyzer';
        $iterator   = new DirectoryIterator($path);
        foreach ($iterator as $item) {
            if ($item->isFile() && !$item->isDot() &&
                'Abstract.php' != substr($item->getFilename(), -12)) {
                $extensions[] = substr($item->getFilename(), 0, -4);
            }
        }
        sort($extensions);

        foreach ($extensions as $extension) {
            $class    = "PHP_ConfigReport_Analyzer_$extension";
            $instance = new $class(
                $this->_config,
                $this->_environment,
                $this->_phpVersion,
                $this->_loadedExtensions
            );
            $report->addSection($instance->getReportSection());
        }

        return $report;
    }
}
