<?php

namespace wcf\data;

/**
 * Default interface for message action classes supporting quotes.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @deprecated 6.2
 */
interface IMessageQuoteAction
{
    /**
     * Validates parameters to return a parsed template of all associated quotes.
     *
     * @return void
     */
    public function validateGetRenderedQuotes();

    /**
     * Returns the parsed template for all associated quotes.
     *
     * @return mixed[]
     */
    public function getRenderedQuotes();

    /**
     * Validates parameters to quote an entire message.
     *
     * @return void
     */
    public function validateSaveFullQuote();

    /**
     * Quotes an entire message.
     *
     * @return mixed[]
     */
    public function saveFullQuote();

    /**
     * Validates parameters to save a quote.
     *
     * @return void
     */
    public function validateSaveQuote();

    /**
     * Saves the quote message and returns the number of stored quotes.
     *
     * @return mixed[]
     */
    public function saveQuote();
}
