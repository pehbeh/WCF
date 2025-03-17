<?php

namespace wcf\system\cli\command;

/**
 * @deprecated 6.0 This interface is unused.
 */
interface IArgumentedCLICommand extends ICLICommand
{
    /**
     * @return string
     */
    public function getUsage();
}
