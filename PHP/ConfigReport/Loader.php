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
 * @subpackage Loader
 * @author Jean-Marc Fontaine <jm@jmfontaine.net>
 * @copyright 2010 Jean-Marc Fontaine <jm@jmfontaine.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Class loader
 *
 * @package PHP_ConfigReport
 * @subpackage Loader
 * @author Jean-Marc Fontaine <jm@jmfontaine.net>
 * @copyright 2010 Jean-Marc Fontaine <jm@jmfontaine.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class PHP_ConfigReport_Loader
{
    protected static function _getClassPath($class)
    {
        $relativePath = str_replace('_', '/', $class) . '.php';

        $includePathParts = self::_getIncludePathParts();
        foreach ($includePathParts as $part) {
            $classPath = $part . DIRECTORY_SEPARATOR . $relativePath;
            if (is_readable($classPath)) {
                return $classPath;
            }
        }

        return false;
    }

    protected static function _getIncludePathParts()
    {
        return explode(PATH_SEPARATOR, get_include_path());
    }

    /**
     * Tries to load the file containing the specified class.
     * This method is to be used with spl_autoload_register() function.
     *
     * @param string $class name of the class to load
     * @return bool Whether the class was found or not
     */
    public static function autoload($class)
    {
        $classPath = self::_getClassPath($class);
        if (false === $classPath) {
            return false;
        }

        return (bool) include $classPath;
    }
}
