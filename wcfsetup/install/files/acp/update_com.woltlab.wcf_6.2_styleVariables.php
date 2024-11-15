<?php

use wcf\system\WCF;

$styleVariables = [
    ['wcfToggleButtonBackground', 'rgba(105, 109, 114, 1)', 'rgba(89, 89, 89, 1)'],
    ['wcfToggleButtonBackgroundActive', 'rgba(60, 118, 61, 1)', 'rgba(0, 113, 84, 1)'],
    ['wcfToggleButtonSliderBackground', 'rgba(250, 250, 250, 1)', 'rgba(203, 203, 203, 1)'],
    ['wcfToggleButtonSliderBackgroundActive', 'rgba(250, 250, 250, 1)', 'rgba(203, 203, 203, 1)'],
];

$sql = "INSERT INTO             wcf1_style_variable
                                (variableName, defaultValue, defaultValueDarkMode)
        VALUES                  (?, ?, ?)
        ON DUPLICATE KEY UPDATE defaultValue = VALUES(defaultValue),
                                defaultValueDarkMode = VALUES(defaultValueDarkMode)";
$statement = WCF::getDB()->prepare($sql);

foreach ($styleVariables as $data) {
    [$variableName, $defaultValue, $defaultValueDarkMode] = $data;

    $statement->execute([
        $variableName,
        $defaultValue,
        $defaultValueDarkMode,
    ]);
}
