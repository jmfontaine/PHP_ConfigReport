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
 * @package Tests
 * @author Jean-Marc Fontaine <jm@jmfontaine.net>
 * @copyright 2010 Jean-Marc Fontaine <jm@jmfontaine.net>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

class PHP_ConfigReport_Analyzer_Apc_StatTest
    extends PHP_ConfigReport_Test_CheckTestCase
{
    /*
     * Methods
     */

   /**
     * @test
     */
    public function checksDisabledDirectiveDoesNotTriggerIssueInProduction()
    {
        $this->assertIssuesEmpty(
            'apc.stat=0',
            'apc.stat',
            PHP_ConfigReport_Analyzer::PRODUCTION,
            null,
            'apc'
        );
    }

   /**
     * @test
     */
    public function checksEnabledDirectiveTriggersWarningInProduction()
    {
        $this->assertIssuesContainWarningOnly(
            'apc.stat=1',
            'apc.stat',
            PHP_ConfigReport_Analyzer::PRODUCTION,
            null,
            'apc'
        );
    }

   /**
     * @test
     */
    public function checksDisabledDirectiveTriggersErrorInEveryEnvironmentExceptProduction()
    {
        $this->assertIssuesContainErrorOnly(
            'apc.stat=0',
            'apc.stat',
            array(
                PHP_ConfigReport_Analyzer::DEVELOPMENT,
                PHP_ConfigReport_Analyzer::STAGING,
                PHP_ConfigReport_Analyzer::TESTING,
            ),
            null,
            'apc'
        );
    }

   /**
     * @test
     */
    public function checksDirectiveIsUntesteableIfTheExtensionIsNotLoaded()
    {
        $this->assertCheckIsTestable(
            'apc.enabled',
            null,
            null,
            'apc'
        );

        $this->assertCheckIsNotTestable(
            'apc.enabled'
        );
    }

    /*
     * Bugs
     */
}