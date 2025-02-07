<?php

namespace wcf\system\interaction\admin;

use wcf\acp\form\TemplateAddForm;
use wcf\acp\page\TemplateDiffPage;
use wcf\data\DatabaseObject;
use wcf\data\template\Template;
use wcf\event\interaction\admin\TemplateInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;
use wcf\system\interaction\LinkInteraction;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Interaction provider for templates.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class TemplateInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new class("copy", TemplateAddForm::class, "wcf.acp.template.copy") extends LinkInteraction {
                #[\Override]
                public function render(DatabaseObject $object): string
                {
                    \assert($object instanceof Template);

                    $href = LinkHandler::getInstance()->getControllerLink(
                        $this->controllerClass,
                        ['copy' => $object->templateID]
                    );

                    return \sprintf(
                        '<a href="%s">%s</a>',
                        StringUtil::encodeHTML($href),
                        WCF::getLanguage()->get($this->languageItem)
                    );
                }
            },
            new LinkInteraction(
                "diff",
                TemplateDiffPage::class,
                "wcf.acp.template.diff",
                static fn(Template $template) => $template->templateGroupID !== null
            ),
            new DeleteInteraction(
                'core/templates/%s',
                static fn(Template $template) => $template->templateGroupID !== null
            ),
        ]);

        EventHandler::getInstance()->fire(
            new TemplateInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return Template::class;
    }
}
