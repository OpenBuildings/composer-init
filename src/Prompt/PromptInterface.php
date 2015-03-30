<?php

namespace CL\ComposerInit\Prompt;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

/**
 * @author    Ivan Kerin <ikerin@gmail.com>
 * @copyright (c) 2014 Clippings Ltd.
 * @license   http://spdx.org/licenses/BSD-3-Clause
 */
interface PromptInterface
{
    /**
     * @return array
     */
    public function getValues(OutputInterface $output, DialogHelper $helper);
}
