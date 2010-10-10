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
 * Text renderer
 */
class PHP_ConfigReport_Renderer_Text
    implements PHP_ConfigReport_Renderer_Interface
{
    protected $_width = 80;

    public function __construct($width = null)
    {
        if (null !== $width) {
            $this->setWidth($width);
        }
    }

    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * Displays report or generates its files
     *
     * @param PHP_ConfigReport_Report $report Report
     * @return void
     */
    public function render(PHP_ConfigReport_Report $report)
    {
        $consoleOutput = new ezcConsoleOutput();
        $consoleOutput->formats->extensionName->style = array('bold');
        $consoleOutput->formats->columnTitle->style   = array('bold');
        $consoleOutput->formats->error->bgcolor       = 'red';
        $consoleOutput->formats->warning->bgcolor     = 'yellow';

        $consoleOutput->outputLine('PHP version: ' . $report->getPhpVersion());
        $consoleOutput->outputLine('Environment: ' . $report->getEnvironment());

        $noIssue = true;
        foreach ($report->getSections() as $section) {
            if ($section->hasIssues()) {
                $noIssue = false;

                $consoleOutput->outputLine();
                $consoleOutput->outputLine(
                    $section->getExtensionName(),
                    'extensionName'
                );

                $table = new ezcConsoleTable($consoleOutput, $this->_width);

                $table[0]->format     = 'columnTitle';
                $table[0][0]->content = 'Directive';
                $table[0][1]->content = 'Level';
                $table[0][2]->content = 'Type';
                $table[0][3]->content = 'Value';
                $table[0][4]->content = 'Suggested value';
                $table[0][5]->content = 'Comments';

                foreach ($section->getIssues() as $index => $issue) {
                    $table[$index + 1]->format     = $issue->getLevel();

                    $directiveName = $issue->getDirectiveName();
                    if (is_array($directiveName)) {
                        $directiveName = implode(' / ', $directiveName);
                    }
                    $table[$index + 1][0]->content = $directiveName;

                    $table[$index + 1][1]->content = $issue->getLevel();
                    $table[$index + 1][2]->content = $issue->getType();

                    $directiveActualValue = $issue->getDirectiveActualValue();
                    if (is_array($directiveActualValue)) {
                        $directiveActualValue = implode(
                            ' / ',
                            $directiveActualValue
                        );
                    }
                    $table[$index + 1][3]->content = $directiveActualValue;

                    $directiveSuggestedValue = $issue->getDirectiveSuggestedValue();
                    if (is_array($directiveSuggestedValue)) {
                        $directiveSuggestedValue = implode(
                            ' / ',
                            $directiveSuggestedValue
                        );
                    }
                    $table[$index + 1][4]->content = $directiveSuggestedValue;

                    $table[$index + 1][5]->content = $issue->getComments();
                }

                $table->outputTable();
                $consoleOutput->outputLine();
            }
        }

        if ($noIssue) {
            $consoleOutput->outputLine('No issue found.');
            $consoleOutput->outputLine();
        }
    }

    public function setWidth($width)
    {
        $width = (int) $width;
        if (0 >= $width) {
            throw new InvalidArgumentException(
            	'Report width must be a positive integer'
            );
        }

        $this->_width = $width;
    }
}