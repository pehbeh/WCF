<?php

namespace wcf\system\gridView;

use wcf\event\gridView\ExceptionLogGridViewInitialized;
use wcf\event\IPsr14Event;
use wcf\system\Regex;
use wcf\system\gridView\filter\SelectFilter;
use wcf\system\gridView\filter\TextFilter;
use wcf\system\gridView\renderer\TimeColumnRenderer;
use wcf\system\gridView\renderer\TitleColumnRenderer;
use wcf\system\WCF;
use wcf\util\DirectoryUtil;
use wcf\util\ExceptionLogUtil;

final class ExceptionLogGridView extends DataSourceGridView
{
    private array $availableLogFiles;

    public function __construct(bool $applyDefaultFilter = false)
    {
        $this->addColumns([
            GridViewColumn::for('message')
                ->label('wcf.acp.exceptionLog.exception.message')
                ->sortable()
                ->renderer(new TitleColumnRenderer()),
            GridViewColumn::for('exceptionID')
                ->label('wcf.acp.exceptionLog.search.exceptionID')
                ->filter(new TextFilter())
                ->sortable(),
            GridViewColumn::for('date')
                ->label('wcf.acp.exceptionLog.exception.date')
                ->sortable()
                ->renderer(new TimeColumnRenderer()),
            GridViewColumn::for('logFile')
                ->label('wcf.acp.exceptionLog.search.logFile')
                ->filter(new SelectFilter($this->getAvailableLogFiles()))
                ->hidden(true),
        ]);

        $this->addRowLink(new GridViewRowLink(cssClass: 'jsExceptionLogEntry'));
        $this->setSortField('date');
        $this->setSortOrder('DESC');

        if ($applyDefaultFilter && $this->getDefaultLogFile() !== null) {
            $this->setActiveFilters([
                'logFile' => $this->getDefaultLogFile(),
            ]);
        }
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.management.canViewLog');
    }

    #[\Override]
    public function getObjectID(mixed $row): mixed
    {
        return $row['exceptionID'];
    }

    #[\Override]
    protected function loadDataSource(): array
    {
        if (!empty($this->getActiveFilters()['exceptionID'])) {
            $exceptionID = $this->getActiveFilters()['exceptionID'];
            $contents = $logFile = '';
            foreach ($this->getAvailableLogFiles() as $logFile) {
                $contents = \file_get_contents(WCF_DIR . $logFile);

                if (\str_contains($contents, '<<<<<<<<' . $exceptionID . '<<<<')) {
                    break;
                }

                unset($contents);
            }

            if ($contents === '') {
                return [];
            }

            $exceptions = ExceptionLogUtil::splitLog($contents);
            $parsedExceptions = [];

            foreach ($exceptions as $key => $val) {
                if ($key !== $exceptionID) {
                    continue;
                }

                $parsed = ExceptionLogUtil::parseException($val);

                $parsedExceptions[$key] = [
                    'exceptionID' => $key,
                    'message' => $parsed['message'],
                    'date' => $parsed['date'],
                    'logFile' => $logFile,
                ];
            }

            return $parsedExceptions;
        } elseif (!empty($this->getActiveFilters()['logFile'])) {
            $contents = \file_get_contents(WCF_DIR . $this->getActiveFilters()['logFile']);
            $exceptions = ExceptionLogUtil::splitLog($contents);
            $parsedExceptions = [];

            foreach ($exceptions as $key => $val) {
                $parsed = ExceptionLogUtil::parseException($val);

                $parsedExceptions[$key] = [
                    'exceptionID' => $key,
                    'message' => $parsed['message'],
                    'date' => $parsed['date'],
                    'logFile' => $this->getActiveFilters()['logFile'],
                ];
            }

            return $parsedExceptions;
        }

        return [];
    }

    #[\Override]
    protected function applyFilters(): void
    {
        // Overwrite the default filtering, as this is already applied when the data is loaded.
    }

    private function getAvailableLogFiles(): array
    {
        if (!isset($this->availableLogFiles)) {
            $this->availableLogFiles = [];
            $fileNameRegex = new Regex('(?:^|/)\d{4}-\d{2}-\d{2}\.txt$');
            $logFiles = DirectoryUtil::getInstance(WCF_DIR . 'log/', false)->getFiles(\SORT_DESC, $fileNameRegex);
            foreach ($logFiles as $logFile) {
                $this->availableLogFiles['log/' . $logFile] = 'log/' . $logFile;
            }
        }

        return $this->availableLogFiles;
    }

    private function getDefaultLogFile(): ?string
    {
        return \array_key_first($this->getAvailableLogFiles());
    }

    #[\Override]
    protected function getInitializedEvent(): ?IPsr14Event
    {
        return new ExceptionLogGridViewInitialized($this);
    }
}
