<?php

namespace wcf\system\template\plugin;

use wcf\system\template\TemplateScriptingCompiler;

/**
 * Compiler functions are called during the compilation of a template.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface ICompilerTemplatePlugin
{
    /**
     * Executes the start tag of this compiler function.
     *
     * @param array<int|string, mixed> $tagArgs
     * @param TemplateScriptingCompiler $compiler
     * @return  string
     */
    public function executeStart($tagArgs, TemplateScriptingCompiler $compiler);

    /**
     * Executes the end tag of this compiler function.
     *
     * @param TemplateScriptingCompiler $compiler
     * @return  string
     */
    public function executeEnd(TemplateScriptingCompiler $compiler);
}
