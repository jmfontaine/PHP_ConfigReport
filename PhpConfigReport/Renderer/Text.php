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
class PhpConfigReport_Renderer_Text
    implements PhpConfigReport_Renderer_Interface
{
    /**
     * Displays report or generates its files
     *
     * @param PhpConfigReport_Report $report Report
     * @return void
     */
    public function render(PhpConfigReport_Report $report)
    {
        $consoleOutput = new ezcConsoleOutput();
        $consoleOutput->formats->extensionName->style = array('bold');
        $consoleOutput->formats->columnTitle->style   = array('bold');
        $consoleOutput->formats->error->bgcolor       = 'red';
        $consoleOutput->formats->warning->bgcolor     = 'yellow';

        $consoleOutput->outputLine('Environment: ' . $report->getEnvironment());

        $noIssue = true;
        foreach ($report->getSections() as $section) {
            if ($section->hasIssues()) {
                $noIssue = false;

                $consoleOutput->outputLine();
                $consoleOutput->outputLine($section->getExtensionName(), 'extensionName');

                $table = new ezcConsoleTable($consoleOutput, 80);

                $table[0]->format     = 'columnTitle';
                $table[0][0]->content = 'Directive';
                $table[0][1]->content = 'Level';
                $table[0][2]->content = 'Value';
                $table[0][3]->content = 'Suggested value';
                $table[0][4]->content = 'Comments';

                foreach ($section->getIssues() as $index => $item) {
                    $table[$index + 1][0]->content = $item['directiveName'];
                    $table[$index + 1]->format     = $item['level'];
                    $table[$index + 1][1]->content = $item['level'];
                    $table[$index + 1][2]->content = $item['actualValue'];
                    $table[$index + 1][3]->content = $item['suggestedValue'];
                    $table[$index + 1][4]->content = $item['comments'];
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
}