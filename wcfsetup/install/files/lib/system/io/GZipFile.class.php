<?php

namespace wcf\system\io;

use wcf\system\exception\SystemException;

/**
 * The File class handles all file operations on a gzip file.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @method  resource    open($mode, $use_include_path = 0)
 * @method  bool        rewind()
 */
final class GZipFile extends File
{
    /** @noinspection PhpMissingParentConstructorInspection */

    /**
     * Opens a gzip file.
     *
     * @param string $filename
     * @param string $mode
     * @throws  SystemException
     */
    public function __construct($filename, $mode = 'wb')
    {
        $this->filename = $filename;
        $this->resource = \gzopen($filename, $mode);
        if ($this->resource === false) {
            throw new SystemException('Can not open file ' . $filename);
        }
    }

    /**
     * Calls the specified function on the open file.
     *
     * @param string $function
     * @param mixed[] $arguments
     * @return  mixed
     * @throws  SystemException
     */
    public function __call($function, $arguments)
    {
        if (\function_exists('gz' . $function)) {
            \array_unshift($arguments, $this->resource);

            return \call_user_func_array('gz' . $function, $arguments);
        } elseif (\function_exists($function)) {
            \array_unshift($arguments, $this->filename);

            return \call_user_func_array($function, $arguments);
        } else {
            throw new SystemException('Can not call method ' . $function);
        }
    }

    /**
     * @see \gzread()
     */
    public function read(int $length): string|false
    {
        return \gzread($this->resource, $length);
    }

    /**
     * @see \gztell()
     */
    public function tell(): int|false
    {
        return \gztell($this->resource);
    }

    /**
     * @see \gzseek()
     */
    public function seek(int $offset, int $whence = \SEEK_SET): int
    {
        return \gzseek($this->resource, $offset, $whence);
    }

    /**
     * Returns the filesize of the unzipped file.
     *
     * @return  int
     */
    public function getFileSize()
    {
        $byteBlock = 1 << 14;
        $eof = $byteBlock;

        // the correction is for zip files that are too small
        // to get in the first while loop
        $correction = 1;
        while ($this->seek($eof) == 0) {
            $eof += $byteBlock;
            $correction = 0;
        }

        while ($byteBlock > 1) {
            $byteBlock >>= 1;
            $eof += $byteBlock * ($this->seek($eof) ? -1 : 1);
        }

        if ($this->seek($eof) == -1) {
            $eof--;
        }

        $this->rewind();

        return $eof - $correction;
    }
}
