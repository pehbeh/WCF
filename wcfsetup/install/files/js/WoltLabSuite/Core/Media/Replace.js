/**
 * Uploads replacemnts for media files.
 *
 * @author  Matthias Schmidt
 * @copyright 2001-2021 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 5.3
 * @woltlabExcludeBundle tiny
 */
define(["require", "exports", "tslib", "../Core", "./Upload", "../Language", "../Dom/Util", "../Dom/Change/Listener", "../Component/Snackbar"], function (require, exports, tslib_1, Core, Upload_1, Language, Util_1, DomChangeListener, Snackbar_1) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    Upload_1 = tslib_1.__importDefault(Upload_1);
    Language = tslib_1.__importStar(Language);
    Util_1 = tslib_1.__importDefault(Util_1);
    DomChangeListener = tslib_1.__importStar(DomChangeListener);
    class MediaReplace extends Upload_1.default {
        _mediaID;
        constructor(mediaID, buttonContainerId, targetId, options) {
            super(buttonContainerId, targetId, Core.extend(options, {
                action: "replaceFile",
            }));
            this._mediaID = mediaID;
        }
        _createButton() {
            super._createButton();
            this._button.classList.add("small");
            this._button.querySelector("span").textContent = Language.get("wcf.media.button.replaceFile");
        }
        _createFileElement() {
            return this._target;
        }
        _getFormData() {
            return {
                objectIDs: [this._mediaID],
            };
        }
        _success(uploadId, data) {
            this._fileElements[uploadId].forEach((file) => {
                const internalFileId = file.dataset.internalFileId;
                const media = data.returnValues.media[internalFileId];
                if (media) {
                    if (media.isImage) {
                        this._target.innerHTML = media.smallThumbnailTag;
                    }
                    document.getElementById("mediaFilename").textContent = media.filename;
                    document.getElementById("mediaFilesize").textContent = media.formattedFilesize;
                    if (media.isImage) {
                        document.getElementById("mediaImageDimensions").textContent = media.imageDimensions;
                    }
                    document.getElementById("mediaUploader").innerHTML = media.userLinkElement;
                    this._options.mediaEditor.updateData(media);
                    // Remove existing error messages.
                    Util_1.default.innerError(this._buttonContainer, "");
                    (0, Snackbar_1.showDefaultSuccessSnackbar)();
                }
                else {
                    let error = data.returnValues.errors[internalFileId];
                    if (!error) {
                        error = {
                            errorType: "uploadFailed",
                            filename: file.dataset.filename,
                        };
                    }
                    Util_1.default.innerError(this._buttonContainer, Language.get("wcf.media.upload.error." + error.errorType, {
                        filename: error.filename,
                    }));
                }
                DomChangeListener.trigger();
            });
        }
    }
    return MediaReplace;
});
