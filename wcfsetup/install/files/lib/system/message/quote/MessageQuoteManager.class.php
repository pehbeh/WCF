<?php

namespace wcf\system\message\quote;

use wcf\data\IMessage;
use wcf\system\event\EventHandler;
use wcf\system\exception\SystemException;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Manages message quotes.
 *
 * @author      Olaf Braun, Alexander Ebert
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class MessageQuoteManager extends SingletonFactory
{
    /**
     * message id for quoting
     * @var int
     */
    protected $quoteMessageID = 0;

    /**
     * list of quote ids to be removed
     * @var string[]
     */
    protected $removeQuoteIDs = [];

    /**
     * @inheritDoc
     */
    protected function init()
    {
        // load stored quotes from session
        $messageQuotes = WCF::getSession()->getVar('__messageQuotes');
        if (\is_array($messageQuotes)) {
            $this->removeQuoteIDs = $messageQuotes['removeQuoteIDs'] ?? [];
        }
    }

    /**
     * Adds a quote unless it is already stored. If you want to quote a whole
     * message while maintaining the original markup, pass $obj->getExcerpt() for
     * $message and $obj->getMessage() for $fullQuote.
     *
     * @param string $objectType
     * @param int $parentObjectID
     * @param int $objectID
     * @param string $message
     * @param string $fullQuote
     * @param bool $returnFalseIfExists
     *
     * @return  mixed
     * @throws  SystemException
     * @deprecated 6.2
     */
    public function addQuote(
        $objectType,
        $parentObjectID,
        $objectID,
        $message,
        $fullQuote = '',
        $returnFalseIfExists = true
    ) {
        return false;
    }

    /**
     * Returns the quote id for given quote.
     *
     * @param string $objectType
     * @param int $objectID
     * @param string $message
     * @param string $fullQuote
     *
     * @return  string
     * @deprecated 6.2
     */
    public function getQuoteID($objectType, $objectID, $message, $fullQuote = '')
    {
        return \substr(\sha1($objectType . '|' . $objectID . '|' . $message . '|' . $fullQuote), 0, 8);
    }

    /**
     * Removes a quote from storage and returns true if the quote has successfully been removed.
     *
     * @param string $quoteID
     *
     * @return  bool
     * @deprecated 6.2
     */
    public function removeQuote($quoteID)
    {
        return false;
    }

    /**
     * Returns an array containing the quote author, link and text.
     *
     * @param string $quoteID
     *
     * @return  string[]|false
     * @deprecated 6.2
     */
    public function getQuoteComponents($quoteID)
    {
        return false;
    }

    /**
     * Returns a list of quotes.
     *
     * @param bool $supportPaste
     *
     * @return  string
     *
     * @deprecated 6.2
     */
    public function getQuotes($supportPaste = false)
    {
        return '';
    }

    /**
     * Returns a list of quotes by object type and id.
     *
     * @param string $objectType
     * @param int[] $objectIDs
     * @param bool $markForRemoval
     *
     * @return  string[]
     *
     * @deprecated 6.2
     */
    public function getQuotesByObjectIDs($objectType, array $objectIDs, $markForRemoval = true)
    {
        return [];
    }

    /**
     * Returns a list of quotes by object type and parent object id.
     *
     * @param string $objectType
     * @param int $parentObjectID
     * @param bool $markForRemoval
     *
     * @return  string[]
     *
     * @deprecated 6.2
     */
    public function getQuotesByParentObjectID($objectType, $parentObjectID, $markForRemoval = true)
    {
        return [];
    }

    /**
     * Returns a quote by id.
     *
     * @param string $quoteID
     * @param bool $useFullQuote
     *
     * @return  string|null
     *
     * @deprecated 6.2
     */
    public function getQuote($quoteID, $useFullQuote = true)
    {
        return null;
    }

    /**
     * Returns the object id by quote id.
     *
     * @param string $quoteID
     *
     * @return  int|null
     *
     * @deprecated 6.2
     */
    public function getObjectID($quoteID)
    {
        return null;
    }

    /**
     * Marks quote ids for removal.
     *
     * @param string[] $quoteIDs
     */
    public function markQuotesForRemoval(array $quoteIDs): void
    {
        foreach ($quoteIDs as $index => $quoteID) {
            if (\in_array($quoteID, $this->removeQuoteIDs)) {
                unset($quoteIDs[$index]);
            }
        }

        if (!empty($quoteIDs)) {
            $this->removeQuoteIDs = \array_merge($this->removeQuoteIDs, $quoteIDs);
            $this->updateSession();
        }
    }

    /**
     * Renders a quote for given message.
     *
     * @param IMessage $message
     * @param string $text
     * @param bool $renderAsString
     *
     * @return  array|string
     */
    public function renderQuote(IMessage $message, $text, $renderAsString = true)
    {
        $parameters = [
            'message' => $message,
            'text' => $text,
        ];
        EventHandler::getInstance()->fireAction($this, 'beforeRenderQuote', $parameters);
        $text = $parameters['text'];

        $escapedLink = \str_replace(["\\", "'"], ["\\\\", "\\'"], $message->getLink());

        if ($renderAsString) {
            return "[quote='" . $message->getUsername() . "','" . $escapedLink . "']" . $text . "[/quote]";
        } else {
            return [
                'username' => $message->getUsername(),
                'link' => $escapedLink,
                'text' => $text,
            ];
        }
    }

    /**
     * Removes quotes marked for removal.
     *
     * @deprecated 6.2
     */
    public function removeMarkedQuotes()
    {
    }

    /**
     * Returns the number of stored quotes.
     *
     * @return  int
     * @deprecated 6.2
     */
    public function countQuotes()
    {
        return 0;
    }

    /**
     * Returns a list of full quotes by object id for given object types.
     *
     * @param string[] $objectTypes
     *
     * @return  mixed[][]
     * @throws  SystemException
     * @deprecated 6.2
     */
    public function getFullQuoteObjectIDs(array $objectTypes)
    {
        return [];
    }

    /**
     * Sets object type and object ids.
     *
     * @param string $objectType
     * @param int[] $objectIDs
     *
     * @throws  SystemException
     *
     * @deprecated 6.2
     */
    public function initObjects($objectType, array $objectIDs)
    {
    }

    /**
     * Reads the quote message id.
     */
    public function readParameters()
    {
        if (isset($_REQUEST['quoteMessageID'])) {
            $this->quoteMessageID = \intval($_REQUEST['quoteMessageID']);
        }
    }

    /**
     * Reads a list of quote ids to remove.
     */
    public function readFormParameters()
    {
        if (isset($_REQUEST['__removeQuoteIDs']) && \is_array($_REQUEST['__removeQuoteIDs'])) {
            $quoteIDs = ArrayUtil::trim($_REQUEST['__removeQuoteIDs']);

            if (!empty($quoteIDs)) {
                $this->removeQuoteIDs = \array_merge($this->removeQuoteIDs, $quoteIDs);
            }
        }
    }

    /**
     * Removes quotes after saving current message.
     */
    public function saved()
    {
        $this->updateSession();
    }

    /**
     * Assigns variables on page load.
     *
     * @deprecated 6.2
     */
    public function assignVariables()
    {
    }

    /**
     * Returns quote message id.
     *
     * @return  int
     */
    public function getQuoteMessageID()
    {
        return $this->quoteMessageID;
    }

    /**
     * Removes orphaned quote ids
     *
     * @param int[] $quoteIDs
     *
     * @deprecated 6.2
     */
    public function removeOrphanedQuotes(array $quoteIDs)
    {
    }

    /**
     * Returns true if a quote id represents a full quote.
     *
     * @param string $quoteID
     *
     * @return      bool
     * @deprecated 6.2
     */
    public function isFullQuote($quoteID)
    {
        return false;
    }

    /**
     * Returns the list of quote uuids to be removed.
     *
     * @return  string[]
     * @since 6.2
     */
    public function getRemoveQuoteIDs(): array
    {
        return $this->removeQuoteIDs;
    }

    /**
     * Resets the list of quote uuids to be removed.
     *
     * @since 6.2
     */
    public function reset(): void
    {
        $this->removeQuoteIDs = [];

        $this->updateSession();
    }

    /**
     * Updates data stored in session,
     */
    protected function updateSession(): void
    {
        WCF::getSession()->register('__messageQuotes', [
            'removeQuoteIDs' => $this->removeQuoteIDs,
        ]);
    }
}
