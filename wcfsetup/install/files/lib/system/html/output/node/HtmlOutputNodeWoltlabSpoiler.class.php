<?php

namespace wcf\system\html\output\node;

use wcf\system\html\node\AbstractHtmlNodeProcessor;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Processes spoilers.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       3.0
 */
class HtmlOutputNodeWoltlabSpoiler extends AbstractHtmlOutputNode
{
    /**
     * @inheritDoc
     */
    protected $tagName = 'woltlab-spoiler';

    /**
     * @inheritDoc
     */
    public function process(array $elements, AbstractHtmlNodeProcessor $htmlNodeProcessor)
    {
        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            if ($this->outputType === 'text/html') {
                [$nodeIdentifier, $tagName] = $htmlNodeProcessor->getWcfNodeIdentifer();
                $htmlNodeProcessor->addNodeData(
                    $this,
                    $nodeIdentifier,
                    ['label' => $element->getAttribute('data-label')]
                );

                $htmlNodeProcessor->renameTag($element, $tagName);
            } elseif ($this->outputType === 'text/simplified-html' || $this->outputType === 'text/plain') {
                $htmlNodeProcessor->replaceElementWithText(
                    $element,
                    WCF::getLanguage()->getDynamicVariable(
                        'wcf.bbcode.spoiler.simplified',
                        ['label' => $element->getAttribute('data-label')]
                    ),
                    true
                );
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function replaceTag(array $data)
    {
        return WCF::getTPL()->render('wcf', 'spoilerMetaCode', [
            'buttonLabel' => $data['label'],
            'spoilerID' => \substr(StringUtil::getRandomID(), 0, 8),
        ]);
    }
}
