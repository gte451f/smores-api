<?php
namespace PhalconRest\Libraries\File;

use PhalconRest\Util\HTTPException;

/**
 * helper functions needed when dealing with a file
 * this class assumes a linux like environment
 *
 * @author jjenkins
 *
 */
class Util
{

    /**
     * make DI available to class
     */
    function __construct()
    {
        // inject the service locator?
        $this->di = \Phalcon\DI::getDefault();
    }

    /**
     * check that a path is valid and writeable
     *
     * @param string $path
     * @return boolean
     */
    function checkPath($path)
    {
        if (file_exists($path)) {
            return true;
        } else {
            // file path does not exist, attempt to create it
            if (!$this->recursive_mkdir($path)) {
                throw new HTTPException("Error saving file.", @500, array(
                    'dev' => "checkPath Failed for $path",
                    'code' => '734348'
                ));
            }

            // check the final version of the path that we can write to it
            if (!is_writable($path)) {
                // echo "could not write to the path in question" . PHP_EOL;
                throw new HTTPException("Error saving file.", @500, array(
                    'dev' => "checkPath failed: the final path is not writeable",
                    'code' => '48916851'
                ));
                return false;
            }
            // echo "$newPath is writeable" . PHP_EOL;
            return true;
        }
    }

    /**
     * disect a file path and check that each part of the path exists
     * checks that the last part of the path is writeable
     *
     * @param string $path
     * @return boolean
     */
    function createDirectoryTree($path)
    {
        // echo $path . PHP_EOL;
        $dirs = explode(DIRECTORY_SEPARATOR, $path);

        // check for a full linux path otherwise it must be relative
        if (substr($path, 0, 1) == DIRECTORY_SEPARATOR) {
            $newPath = DIRECTORY_SEPARATOR;
        } else {
            $newPath = '';
        }

        foreach ($dirs as $dir) {
            $newPath .= $dir . DIRECTORY_SEPARATOR;
            $newPath = str_ireplace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $newPath);
            // echo $newPath . PHP_EOL;

            if ($newPath == DIRECTORY_SEPARATOR) {
                // echo "false start encountered" . PHP_EOL;
                continue;
            }

            if (!file_exists($newPath)) {
                if (!mkdir($newPath, $this->permissions)) {
                    throw new HTTPException("Error saving file.", @500, array(
                        'dev' => "error creating a subdirectory within the larger path. Failed for $newPath",
                        'code' => '54984'
                    ));

                    // echo "could not make the directory in question $newPath" . PHP_EOL;
                    return false;
                }
            }
            // echo "$newPath exists" . PHP_EOL;
        }

        // full path exists and final path is writeable
        return true;
    }

    /**
     * clear system cache files
     */
    public function clearCache()
    {
        $config = $this->di->get('config');
        $this->clearDirectory($config['application']['cacheDir']);
        $this->clearDirectory($config['application']['tempDir']);
        $this->clearDirectory($config['application']['loggingDir']);
    }

    /**
     * for a given directory, remove all NON HIDDEN files
     * is not recursive, will not delete files in sub folders or the sub folders themselves
     *
     * @param string $path
     */
    public function clearDirectory($path)
    {
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if (!$fileInfo->isDot()) {
                $path = $fileInfo->getPathname();
                if (strpos($path, '/.') == false) {
                    unlink($path);
                }
            }
        }
    }
}