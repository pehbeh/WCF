<?php

namespace wcf\form;

use Psr\Http\Message\ResponseInterface;
use wcf\page\IPage;

/**
 * All form classes should implement this interface.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IForm extends IPage
{
    /**
     * Is called when the form was submitted.
     *
     * @return void
     */
    public function submit();

    /**
     * Validates form inputs.
     *
     * @return void|ResponseInterface
     */
    public function validate();

    /**
     * Saves the data of the form.
     *
     * @return void|ResponseInterface
     */
    public function save();

    /**
     * Reads the given form parameters.
     *
     * @return void|ResponseInterface
     */
    public function readFormParameters();
}
