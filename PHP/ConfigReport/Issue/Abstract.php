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
 * @subpackage Issue
 * @author Jean-Marc Fontaine <jm@jmfontaine.net>
 * @copyright 2010-2011 Jean-Marc Fontaine <jm@jmfontaine.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Abstract class for issues
 *
 * @package PHP_ConfigReport
 * @subpackage Issue
 * @author Jean-Marc Fontaine <jm@jmfontaine.net>
 * @copyright 2010-2011 Jean-Marc Fontaine <jm@jmfontaine.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
abstract class PHP_ConfigReport_Issue_Abstract
    implements PHP_ConfigReport_Issue_Interface
{
    protected $_comments;
    protected $_directiveActualValue;
    protected $_directiveSuggestedValue;
    protected $_directiveName;
    protected $_extensionName;
    protected $_level;
    protected $_type;

    public function __construct($extensionName, $directiveName, $type,
        $directiveActualValue, $directiveSuggestedValue, $comments)
    {
        $this->setComments($comments);
        $this->setDirectiveActualValue($directiveActualValue);
        $this->setDirectiveSuggestedValue($directiveSuggestedValue);
        $this->setDirectiveName($directiveName);
        $this->setExtensionName($extensionName);
        $this->setType($type);
    }

    public function getComments()
    {
        return $this->_comments;
    }

    public function getDirectiveActualValue()
    {
        return $this->_directiveActualValue;
    }

    public function getDirectiveSuggestedValue()
    {
        return $this->_directiveSuggestedValue;
    }

    public function getDirectiveName()
    {
        return $this->_directiveName;
    }

    public function getExtensionName()
    {
        return $this->_extensionName;
    }

    public function getLevel()
    {
        return $this->_level;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setComments($comments)
    {
        $this->_comments = $comments;
        return $this;
    }

    public function setDirectiveActualValue($directiveActualValue)
    {
        $this->_directiveActualValue = $directiveActualValue;
        return $this;
    }

    public function setDirectiveSuggestedValue($directiveSuggestedValue)
    {
        $this->_directiveSuggestedValue = $directiveSuggestedValue;
        return $this;
    }

    public function setDirectiveName($directiveName)
    {
        $this->_directiveName = $directiveName;
        return $this;
    }

    public function setExtensionName($extensionName)
    {
        $this->_extensionName = $extensionName;
        return $this;
    }

    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }
}
