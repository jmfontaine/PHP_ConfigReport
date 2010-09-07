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
 * This is necessary since the autoloader is not configured yet when this class
 * is used.
 */
require_once 'PhpConfigReport/Runner/Abstract.php';

/**
 * Command line runner
 */
class PhpConfigReport_Runner_Cli extends PhpConfigReport_Runner_Abstract
{
    /**
     * Command line argument parser
     *
     * @var ezcConsoleInput
     * @see http://ezcomponents.org/docs/api/trunk/ConsoleTools/ezcConsoleInput.html
     */
    protected static $_consoleInput;

    /**
     * Object used for display in command line
     *
     * @var ezcConsoleOutput
     * @see http://ezcomponents.org/docs/api/trunk/ConsoleTools/ezcConsoleOutput.html
     */
    protected static $_consoleOutput;

    /**
     * Main function. Sets up the environment and coordinate the work.
     *
     * @return void
     */
    public static function run()
    {
        // Set autoload up
        require_once 'PhpConfigReport/Loader.php';
        spl_autoload_register(array('PhpConfigReport_Loader', 'autoload'));
        require_once 'ezc/Base/base.php';
        spl_autoload_register(array('ezcBase', 'autoload'));

        // Set console output up
        $output = new ezcConsoleOutput();
        $output->formats->version->style = array('bold');
        $output->formats->debug->color   = 'yellow';
        $output->formats->debug->style   = array('italic');
        $output->formats->error->color   = 'red';
        self::$_consoleOutput = $output;

        // Set console input up
        $input = new ezcConsoleInput();
        self::$_consoleInput = $input;

        $debugOption = new ezcConsoleOption('d', 'debug');
        $debugOption->type = ezcConsoleInput::TYPE_NONE;
        $input->registerOption($debugOption);

        $environmentOption          = new ezcConsoleOption('e', 'environment');
        $environmentOption->type    = ezcConsoleInput::TYPE_STRING;
        $environmentOption->default = 'production';
        $input->registerOption($environmentOption);

        $helpOption = new ezcConsoleOption('h', 'help');
        $helpOption->type = ezcConsoleInput::TYPE_NONE;
        $input->registerOption($helpOption);

        $phpDirectiveOption = new ezcConsoleOption('p', 'php');
        $phpDirectiveOption->type = ezcConsoleInput::TYPE_STRING;
        $phpDirectiveOption->multiple = true;
        $input->registerOption($phpDirectiveOption);

        $phpVersionOption = new ezcConsoleOption(null, 'php-version');
        $phpVersionOption->type = ezcConsoleInput::TYPE_STRING;
        $input->registerOption($phpVersionOption);

        $verboseOption = new ezcConsoleOption('v', 'verbose');
        $verboseOption->type = ezcConsoleInput::TYPE_NONE;
        $input->registerOption($verboseOption);

        $versionOption = new ezcConsoleOption(null, 'version');
        $versionOption->type = ezcConsoleInput::TYPE_NONE;
        $input->registerOption($versionOption);

        // Process console input
        try {
            $input->process();
        } catch (ezcConsoleOptionException $exception) {
            echo $exception->getMessage() . "\n";
            exit(1);
        }

        if ($input->getOption('help')->value) {
            self::displayHelp();
            exit(0);
        } else if ($input->getOption('version')->value) {
            self::displayVersion();
            exit(0);
        }

        if (false !== $input->getOption('php')->value) {
            foreach ($input->getOption('php')->value as $directive) {
                $position = strpos($directive, '=');
                if (false === $position) {
                    throw new InvalidArgumentException(
                        "'$directive' is not a valid PHP configuration directive"
                    );
                }

                $name  = substr($directive, 0, $position);
                $value = substr($directive, $position + 1);
                ini_set($name, $value);
            }
            unset($name, $value);
        }

        // Do the actual work
        self::displayVersion();

        try {
            $arguments = $input->getArguments();
            $path      = !empty($arguments[0]) ? $arguments[0] : null;
            $config    = new PhpConfigReport_Config($path);

            $analyzer = new PhpConfigReport_Analyzer(
                $config,
                $input->getOption('environment')->value,
                $input->getOption('php-version')->value
            );
            $report = $analyzer->getReport();

            $renderer = new PhpConfigReport_Renderer_Text();
            $renderer->render($report);
        } catch (Exception $exception) {
            self::displayError($exception->getMessage());
            exit(1);
        }
    }

    /**
     * Displays debug informations
     *
     * @var string $message Message to display
     * @return void
     */
    public static function displayDebug($message)
    {
        self::displayMessage($message, 'debug');
    }

    /**
     * Displays errors
     *
     * @var string $message Message to display
     * @return void
     */
    public static function displayError($message)
    {
        self::displayMessage($message, 'error');
    }

    /**
     * Displays help informations
     *
     * @return void
     */
    public static function displayHelp()
    {
        self::displayVersion();

        echo <<<EOT

Usage:
  phpcr [options] <path>

Options:
  -d    --debug                 Display debug informations
  -e    --environment           Define PHP environment (default: production)
  -h    --help                  Display this message
  -p    --php NAME=VALUE        Set PHP configuration directive
        --php-version VERSION   Set PHP version
  -v    --verbose               Display processing informations
        --version               Display the version of PHP Config Report


EOT;
    }

    /**
     * Displays messages
     *
     * @var string $message Message to display
     * @var string $type    Type of the message
     * @return void
     */
    public static function displayMessage($message, $type = 'info')
    {
        if ('info' == $type &&
            !self::getConsoleInput()->getOption('verbose')->value) {
            return;
        } elseif ('debug' == $type
                  && !self::getConsoleInput()->getOption('debug')->value) {
            return;
        }

        self::getConsoleOutput()->outputText("$message\n", $type);
    }

    /**
     * Displays version informations
     *
     * @return void
     */
    public static function displayVersion()
    {
        self::getConsoleOutput()->outputLine(
            'PHP Config Report 0.1-dev by Jean-Marc Fontaine',
            'version'
        );
        self::getConsoleOutput()->outputLine();
    }

    public static function getConsoleInput()
    {
        return self::$_consoleInput;
    }

    public static function getConsoleOutput()
    {
        return self::$_consoleOutput;
    }
}