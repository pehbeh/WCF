import * as fs from "fs";
import { promisify } from "util";
import * as path from "path";

const copyFile = promisify(fs.copyFile);
const writeFile = promisify(fs.writeFile);
const rm = promisify(fs.rm);
const readdir = promisify(fs.readdir);

if (process.argv.length !== 4) {
  throw new Error(
    "Expects the path to the directory in which the emoji data is saved as the #1 argument and the path to the Localisation.ts as the #2 argument.",
  );
}

const repository = process.argv[2];
if (!fs.existsSync(repository)) {
  throw new Error(`The path '${repository}' does not exist.`);
}

const localisation = process.argv[3];
if (!fs.existsSync(localisation)) {
  throw new Error(`The path '${localisation}' does not exist.`);
}

const languages: string[] = [
  "da",
  "en",
  "nl",
  "en-gb",
  "et",
  "fi",
  "fr",
  "de",
  "hu",
  "it",
  "lt",
  "nb",
  "pl",
  "pt",
  "ru",
  "es",
  "sv",
  "uk",
];

(async () => {
  let localisationContent = `/**
 * This file is auto-generated, DO NOT MODIFY IT MANUALLY!
 *
 * To update the file, run in the extra directory:
 * > \`npx tsx ./update-emoji-picker-element.ts ../wcfsetup/install/files/emoji ../ts/WoltLabSuite/Core/Component/EmojiPicker/Localization.ts\`
 *
 * @woltlabExcludeBundle all
 */

// prettier-ignore
const locales = [
  ${languages.map((language) => {
    return `"${language}"`;
  })}
];

export function getDataSource(locale: string): string {
  if (!locales.includes(locale)) {
    return \`\${window.WCF_PATH}emoji/en.json\`;
  }

  return \`\${window.WCF_PATH}emoji/\${locale}.json\`;
}
`;

  for (const file in await readdir(repository)) {
    if (!file.endsWith(".json")) {
      continue;
    }

    await rm(path.join(repository, file));
  }

  for (const language of languages) {
    await copyFile(
      path.join(__dirname, `node_modules/emoji-picker-element-data/${language}/cldr-native/data.json`),
      path.join(repository, `${language}.json`),
    );
  }

  await writeFile(localisation, localisationContent);
})();
