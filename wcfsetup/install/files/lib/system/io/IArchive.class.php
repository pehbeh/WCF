<?php

namespace wcf\system\io;

/**
 * Represents an archive of files.
 *
 * @author  Tim Duesterhus
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IArchive
{
    /**
     * Returns the table of contents (TOC) list for this archive.
     *
     * @return array list of contents
     */
    public function getContentList();

    /**
     * Returns an associative array with information about a specific file
     * in the archive.
     *
     * @param mixed $index index or name of the requested file
     * @return array
     */
    public function getFileInfo($index);

    /**
     * Extracts a specific file and returns the content as string. Returns
     * false if extraction failed.
     *
     * @param mixed $index index or name of the requested file
     * @return string|false content of the requested file
     */
    public function extractToString($index);

    /**
     * Extracts a specific file and writes its content to the file specified
     * with $destination.
     *
     * @param mixed $index index or name of the requested file
     * @param string $destination
     * @return bool
     */
    public function extract($index, $destination);

    /**
     * Searchs a file in the archive and returns the numeric file index.
     * Returns false if not found.
     *
     * @param string $filename
     * @return int|false index of the requested file
     */
    public function getIndexByFilename($filename);
}
