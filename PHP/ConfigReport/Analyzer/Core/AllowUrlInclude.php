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
 * allow_url_include check
 *
 * @package PHP_ConfigReport
 * @subpackage Analyzer
 * @author Jean-Marc Fontaine <jm@jmfontaine.net>
 * @copyright 2010-2011 Jean-Marc Fontaine <jm@jmfontaine.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class PHP_ConfigReport_Analyzer_Core_AllowUrlInclude
    extends PHP_ConfigReport_Analyzer_CheckAbstract
{
    protected function _doCheck()
    {
        if ($this->_isDirectiveEnabled('allow_url_include')) {
            $comments = 'If not really needed this directive should be set ' .
                        'to off for security reasons';

            $this->_addError(
                'allow_url_include',
                PHP_ConfigReport_Issue_Interface::SECURITY,
                'on',
                'off',
                $comments
            );
        }
    }

    public function isTestable()
    {
        // This directive is available since PHP 5.2.0.
        return $this->_isPhpVersionGreaterThanOrEqualTo('5.2.0');
    }
}
