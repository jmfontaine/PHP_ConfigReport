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
 * Analyzer for PHP core configuration
 */
class PhpConfigReport_Analyzer_Extension_Core
    extends PhpConfigReport_Analyzer_Extension_Abstract
{
    protected $_extensionName = 'Core';

    public function checkDisplayErrors()
    {
        if ($this->isEnvironment(PhpConfigReport_Analyzer::PRODUCTION) &&
            $this->isDirectiveEnabled('display_errors')) {
            $comments = 'Errors can display useful informations to attackers ' .
                        'and can only confuse legitimate users. In production' .
                        ', errors should be logged but never displayed.';

            $this->addError(
                'display_errors',
                'on',
                'off',
                $comments
            );
        }
    }

    public function checkDisplayOrLogErrors()
    {
        if ($this->isDirectiveDisabled('display_errors') &&
            $this->isDirectiveDisabled('log_errors')) {
            $comments = 'Errors errors should be either displayed or logged. ' .
                        'Otherwise they will get unnoticed.';

            $this->addError(
                'display_errors / log_errors',
                'off',
                'on',
                $comments
            );
        }
    }

    public function checkLogErrors()
    {
        if ($this->isEnvironment(PhpConfigReport_Analyzer::PRODUCTION) &&
            $this->isDirectiveDisabled('log_errors')) {
            $comments = 'Errors should be logged in production so they can ' .
                        'be analyzed later.';

            $this->addError(
                'log_errors',
                'off',
                'on',
                $comments
            );
        }

        if ($this->isEnvironment(PhpConfigReport_Analyzer::TESTING,
                PhpConfigReport_Analyzer::DEVELOPMENT) &&
            $this->isDirectiveEnabled('log_errors')) {
            $comments = 'Errors should not be logged in ' .
                        $this->getEnvironment() . ' because it may generate ' .
                        'huge log files and errors can get unnoticed.';

            $this->addWarning(
                'log_errors',
                'on',
                'off',
                $comments
            );
        }
    }
}