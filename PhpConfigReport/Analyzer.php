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
 * Analyzer for PHP configuration
 */
class PhpConfigReport_Analyzer
{
    const ERROR   = 'error';
    const WARNING = 'warning';

    const PRODUCTION  = 'production';
    const STAGING     = 'staging';
    const TESTING     = 'testing';
    const DEVELOPMENT = 'development';

    /**
     * Config instance
     *
     * @var PhpConfigReport_Config
     */
    protected $_config;

    protected $_environment;

    /**
     * Class constructor
     *
     * @param PhpConfigReport_Config $config Config instance
     * @return void
     */
    public function __construct(PhpConfigReport_Config $config, $environment = 'production')
    {
        $this->_config      = $config;
        $this->_environment = $environment;
    }

    /**
     * Generates and returns report
     *
     * @return PhpConfigReport_Report Report
     */
    public function getReport()
    {
        $report = new PhpConfigReport_Report($this->_environment);

        $extensions = array();
        $path       = dirname(__FILE__) . '/Analyzer/Extension';
        $iterator   = new DirectoryIterator($path);
        foreach ($iterator as $item) {
            if ($item->isFile() && !$item->isDot() && 'Abstract.php' != $item->getFilename()) {
                $extensions[] = substr($item->getFilename(), 0, -4);
            }
        }

        sort($extensions);
        foreach ($extensions as $extension) {
            $class    = "PhpConfigReport_Analyzer_Extension_$extension";
            $instance = new $class($this->_config, $this->_environment);
            $report->addSection($instance->getReportSection());
        }

        return $report;
    }
}