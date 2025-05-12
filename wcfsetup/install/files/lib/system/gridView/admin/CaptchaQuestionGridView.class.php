<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\CaptchaQuestionEditForm;
use wcf\data\captcha\question\CaptchaQuestion;
use wcf\data\captcha\question\CaptchaQuestionList;
use wcf\data\DatabaseObject;
use wcf\event\gridView\admin\CaptchaQuestionGridViewInitialized;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\filter\TextFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\interaction\admin\CaptchaQuestionInteractions;
use wcf\system\interaction\bulk\admin\CaptchaQuestionBulkInteractions;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\interaction\ToggleInteraction;
use wcf\system\language\MultilingualHelper;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Grid view for the list of user ranks.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @extends AbstractGridView<CaptchaQuestion, CaptchaQuestionList>
 */
final class CaptchaQuestionGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('questionID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for('question')
                ->label('wcf.acp.captcha.question.question')
                ->titleColumn()
                ->renderer(
                    new class extends DefaultColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof CaptchaQuestion);

                            return StringUtil::encodeHTML($row->getQuestion());
                        }
                    }
                )
                ->filter(new TextFilter($this->subqueryQuestion()))
                ->sortable(sortByDatabaseColumn: $this->subqueryQuestion()),
            GridViewColumn::for('views')
                ->label('wcf.acp.captcha.question.views')
                ->sortable()
                ->filter(new NumericFilter()),
            GridViewColumn::for('correctSubmissions')
                ->label('wcf.acp.captcha.question.correctSubmissions')
                ->sortable()
                ->filter(new NumericFilter()),
            GridViewColumn::for('incorrectSubmissions')
                ->label('wcf.acp.captcha.question.incorrectSubmissions')
                ->sortable()
                ->filter(new NumericFilter()),
        ]);

        $provider = new CaptchaQuestionInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(CaptchaQuestionEditForm::class),
        ]);
        $this->setInteractionProvider($provider);
        $this->setBulkInteractionProvider(new CaptchaQuestionBulkInteractions());

        $this->addQuickInteraction(
            new ToggleInteraction('enable', 'core/captchas/questions/%s/enable', 'core/captchas/questions/%s/disable')
        );

        $this->setSortField('questionID');
        $this->addRowLink(new GridViewRowLink(CaptchaQuestionEditForm::class));
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.captcha.canManageCaptchaQuestion');
    }

    #[\Override]
    protected function createObjectList(): CaptchaQuestionList
    {
        return new CaptchaQuestionList();
    }

    #[\Override]
    protected function getInitializedEvent(): CaptchaQuestionGridViewInitialized
    {
        return new CaptchaQuestionGridViewInitialized($this);
    }

    private function subqueryQuestion(): string
    {
        return MultilingualHelper::subqueryForContentTable(
            "question",
            "wcf1_captcha_question_content",
            "questionID",
            "captcha_question",
        );
    }
}
