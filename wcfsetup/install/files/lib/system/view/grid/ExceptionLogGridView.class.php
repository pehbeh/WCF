<?php

namespace wcf\system\view\grid;

use wcf\system\view\grid\renderer\TimeColumnRenderer;
use wcf\system\view\grid\renderer\TitleColumnRenderer;
use wcf\system\WCF;
use wcf\util\ExceptionLogUtil;

final class ExceptionLogGridView extends ArrayGridView
{
    public function __construct(
        private readonly string $logFile,
        private readonly string $exceptionID = ''
    ) {
        parent::__construct();

        if ($this->exceptionID) {
            $this->sortRows();
            $this->jumpToException();
        }
    }

    #[\Override]
    protected function init(): void
    {
        $this->addColumns([
            GridViewColumn::for('message')
                ->label('wcf.acp.exceptionLog.exception.message')
                ->sortable()
                ->renderer(new TitleColumnRenderer()),
            GridViewColumn::for('exceptionID')
                ->label('wcf.global.objectID')
                ->sortable(),
            GridViewColumn::for('date')
                ->label('wcf.acp.exceptionLog.exception.date')
                ->sortable()
                ->renderer(new TimeColumnRenderer()),
        ]);

        $this->addRowLink(new GridViewRowLink(cssClass: 'jsExceptionLogEntry'));
        $this->setSortField('date');
        $this->setSortOrder('DESC');
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.management.canViewLog');
    }

    private function jumpToException(): void
    {
        $i = 1;
        foreach ($this->dataArray as $key => $val) {
            if ($key == $this->exceptionID) {
                break;
            }
            $i++;
        }

        $this->setPageNo(\ceil($i / $this->getRowsPerPage()));
    }

    #[\Override]
    public function getParameters(): array
    {
        return ['logFile' => $this->logFile];
    }

    #[\Override]
    public function getObjectID(mixed $row): mixed
    {
        return $row['exceptionID'];
    }

    protected function getDataArray(): array
    {
        if (!$this->logFile) {
            return [];
        }

        $contents = \file_get_contents(WCF_DIR . 'log/' . $this->logFile);
        $exceptions = ExceptionLogUtil::splitLog($contents);
        $parsedExceptions = [];

        foreach ($exceptions as $key => $val) {
            $parsed = ExceptionLogUtil::parseException($val);

            $parsedExceptions[$key] = [
                'exceptionID' => $key,
                'message' => $parsed['message'],
                'date' => $parsed['date'],
            ];
        }

        return $parsedExceptions;
    }
}
