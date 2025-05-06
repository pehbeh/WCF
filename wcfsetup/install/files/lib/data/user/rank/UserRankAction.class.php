<?php

namespace wcf\data\user\rank;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\InvalidObjectArgument;
use wcf\system\file\upload\UploadFile;
use wcf\system\user\rank\command\SaveContent;

/**
 * Executes user rank-related actions.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractDatabaseObjectAction<UserRank, UserRankEditor>
 */
class UserRankAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.user.rank.canManageRank'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['delete'];

    /**
     * @inheritDoc
     */
    public function create()
    {
        /** @var UserRank $rank */
        $rank = parent::create();

        if (isset($this->parameters['rankTitle'])) {
            (new SaveContent($rank->rankID, $this->parameters['rankTitle']))();
        }

        if (isset($this->parameters['rankImageFile']) && !empty($this->parameters['rankImageFile'])) {
            $rankImageFile = \reset($this->parameters['rankImageFile']);

            if (!($rankImageFile instanceof UploadFile)) {
                throw new InvalidObjectArgument(
                    $rankImageFile,
                    UploadFile::class,
                    "The parameter 'rankImageFile'"
                );
            }

            if (!$rankImageFile->isProcessed()) {
                $fileName = $rank->rankID . '-' . $rankImageFile->getFilename();

                \rename(
                    $rankImageFile->getLocation(),
                    WCF_DIR . UserRank::RANK_IMAGE_DIR . $fileName
                );
                $rankImageFile->setProcessed(WCF_DIR . UserRank::RANK_IMAGE_DIR . $fileName);

                $updateData['rankImage'] = $fileName;

                $rankEditor = new UserRankEditor($rank);
                $rankEditor->update($updateData);
            }
        }

        return $rank;
    }

    /**
     * @inheritDoc
     */
    public function update()
    {
        $removedFiles = $this->parameters['rankImageFile__removedFiles'] ?? [];
        if (\is_array($removedFiles)) {
            foreach ($removedFiles as $file) {
                if (!($file instanceof UploadFile)) {
                    throw new InvalidObjectArgument(
                        $file,
                        UploadFile::class,
                        "An array values of 'rankImageFile__removedFiles'"
                    );
                }

                @\unlink($file->getLocation());
            }
        }

        if (isset($this->parameters['rankImageFile'])) {
            if (\count($this->objects) > 1) {
                throw new \BadMethodCallException("The parameter 'rankImageFile' can only be processed, if there is only one object to update.");
            }

            $object = \reset($this->objects);
            $rankImageFile = \reset($this->parameters['rankImageFile']);

            if (!$rankImageFile) {
                $this->parameters['data']['rankImage'] = "";
            } else {
                if (!($rankImageFile instanceof UploadFile)) {
                    throw new InvalidObjectArgument(
                        $rankImageFile,
                        UploadFile::class,
                        "The parameter 'rankImageFile'"
                    );
                }

                if (!$rankImageFile->isProcessed()) {
                    $fileName = $object->rankID . '-' . $rankImageFile->getFilename();

                    \rename(
                        $rankImageFile->getLocation(),
                        WCF_DIR . UserRank::RANK_IMAGE_DIR . $fileName
                    );
                    $rankImageFile->setProcessed(WCF_DIR . UserRank::RANK_IMAGE_DIR . $fileName);

                    $this->parameters['data']['rankImage'] = $fileName;
                }
            }
        }

        parent::update();

        if (isset($this->parameters['rankTitle'])) {
            foreach ($this->objects as $editor) {
                (new SaveContent($editor->rankID, $this->parameters['rankTitle']))();
            }
        }
    }
}
