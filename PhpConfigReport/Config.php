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
 * PHP configuration abstraction layer
 */
class PhpConfigReport_Config
{
    /**
     * Configuration directives
     * @var array
     */
    protected $_directives = array();

    /**
     * Class constructor
     *
     * @param string|null $data Path to the configuration file, the
     * configuration as a string or null which means that configuration will
     * be loaded for the system.
     * @return void
     */
    public function __construct($data = null)
    {
        if (is_file($data)) {
            $this->loadFromFile($data);
        } elseif (is_string($data)) {
            $this->loadFromString($data);
        } else {
            $this->loadFromSystem();
        }
    }

    /**
     * Indicates whether a directive is disabled or not
     *
     * @param string $directiveName
     * @return boolean
     */
    public function isDirectiveDisabled($directiveName)
    {
        return !$this->isDirectiveEnabled($directiveName);
    }

    /**
     * Indicates whether a directive is enabled or not
     *
     * @param string $directiveName
     * @return boolean
     */
    public function isDirectiveEnabled($directiveName)
    {
        $directiveValue = $this->getDirective($directiveName);

        // A "0" value is interpreted as "0" and all other ways to disable a
        // flag directive ("off", "false" and "no") are interpreted as an
        // empty string.
        if ('0' === $directiveValue || '' === $directiveValue) {
            return false;
        } elseif ('1' === $directiveValue) {
            return true;
        } else {
            $message = 'Value "' . $directiveValue . '" is not a valid flag ' .
                       'value for directive "' . $directiveName . '"';
            throw new UnexpectedValueException($message);
        }
    }

    /**
     * Indicates whether a directive is set or not
     *
     * @param string $directiveName
     * @return boolean
     */
    public function isDirectiveSet($directiveName)
    {
        return null !== $this->getDirective($directiveName);
    }

    /**
     * Returns a directive value if it is set, null otherwise
     *
     * @param string $directiveName
     * @return string|null
     */
    public function getDirective($directiveName)
    {
        if (array_key_exists($directiveName, $this->_directives)) {
            return $this->_directives[$directiveName];
        }

        return null;
    }

    /**
     * Returns all the directives values as an array
     *
     * @return array
     */
    public function getDirectives()
    {
        return $this->_directives;
    }

    /**
     * Loads configuration from a php.ini file
     *
     * @param string $path Path to the php.ini file
     * @throws InvalidArgumentException If php.ini can not be found or be read
     * @throws UnexpectedValueException If php.ini format is invalid
     * @return PhpConfigReport_Config Returns self to allow methods chaining
     */
    public function loadFromFile($path)
    {
        if (!is_readable($path)) {
            throw new InvalidArgumentException('Could not read file');
        }

        $directives = @parse_ini_file($path);
        if (false === $directives) {
            throw new UnexpectedValueException('Invalid file format');
        }
        $this->_directives = $directives;

        return $this;
    }

    /**
     * Loads configuration from a string
     *
     * @param string $string Configuration as a string
     * @throws UnexpectedValueException If string format is invalid
     * @return PhpConfigReport_Config Returns self to allow methods chaining
     */
    public function loadFromString($string)
    {
        $directives = @parse_ini_string($string);
        if (false === $directives) {
            throw new UnexpectedValueException('Invalid string format');
        }
        $this->_directives = $directives;

        return $this;
    }

    /**
     * Loads configuration from the system PHP Config Report is run on
     *
     * @return PhpConfigReport_Config Returns self to allow methods chaining
     */
    public function loadFromSystem()
    {
        $directives = array();
        foreach (ini_get_all() as $name => $data) {
            $directives[$name] = $data['local_value'];
        }
        $this->_directives = $directives;

        return $this;
    }
}