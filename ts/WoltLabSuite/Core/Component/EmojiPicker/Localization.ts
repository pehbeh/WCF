/**
 * This file is auto-generated, DO NOT MODIFY IT MANUALLY!
 *
 * To update the file, run in the extra directory:
 * > `npx tsx ./update-emoji-picker-element.ts ../wcfsetup/install/files/emoji ../ts/WoltLabSuite/Core/Component/EmojiPicker/Localization.ts`
 *
 * @woltlabExcludeBundle all
 */

// prettier-ignore
const locales = [
  "da","en","nl","en-gb","et","fi","fr","de","hu","it","lt","nb","pl","pt","ru","es","sv","uk"
];

export function getDataSource(locale: string): string {
  if (!locales.includes(locale)) {
    return `${window.WCF_PATH}emoji/en.json`;
  }

  return `${window.WCF_PATH}emoji/${locale}.json`;
}
