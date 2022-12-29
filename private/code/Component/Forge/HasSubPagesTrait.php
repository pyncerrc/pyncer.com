<?php
namespace Pyncer\Docs\Component\Forge;

use function count;
use function array_key_exists;
use function file_exists;
use function Pyncer\IO\dirnames as pyncer_io_dirnames;
use function Pyncer\IO\filenames as pyncer_io_filenames;

use const DIRECTORY_SEPARATOR as DS;

trait HasSubPagesTrait
{
    private function hasSubPages(string $dir)
    {
        static $results = [];

        if (array_key_exists($dir, $results)) {
            return $results[$dir];
        }

        if (file_exists($dir . '.md')) {
            $results[$dir] = true;
            return false;
        }

        $filenames = pyncer_io_filenames(
            dir: $dir,
            extensions: 'md',
            removeExtension: true,
        );

        foreach ($filenames as $key => $filename) {
            if ($filename === 'index' ||
                str_starts_with($filename, '_') ||
                substr_count($filename, '.') > 0
            ) {
                unset($filenames[$key]);
            }
        }

        // index.md is fine
        if (count($filenames) > 1) {
            $results[$dir] = true;
            return true;
        }

        $dirnames = pyncer_io_dirnames(
            dir: $dir,
        );

        foreach ($dirnames as $dirname) {
            if (file_exists($dir . DS . $dirname . DS . 'index.md')) {
                $results[$dir] = true;
                return true;
            }
        }

        $results[$dir] = false;
        return false;
    }

    private function getPages(string $dir): array
    {
        $filenames = pyncer_io_filenames(
            dir: $dir,
            extensions: 'md',
            removeExtension: true,
        );

        $dirnames = pyncer_io_dirnames(
            dir: $dir,
        );

        foreach ($dirnames as $dirname) {
            if (file_exists($dir . DS . $dirname . DS . 'index.md')) {
                $filenames[] = $dirname;
            }
        }

        natsort($filenames);

        if (file_exists($dir . DS . 'order.php')) {
            $order = include $dir . DS . 'order.php';
        } else {
            $order = [];
        }

        $orderedFilenames = [];
        foreach ($order as $value) {
            $search = array_search($value, $filenames);
            if ($search !== false) {
                $orderedFilenames[] = $filenames[$search];
                unset($filenames[$search]);
            }
        }

        $filenames = [...$orderedFilenames, ...$filenames];

        foreach ($filenames as $key => $filename) {
            if ($filename === 'index' ||
                str_starts_with($filename, '_') ||
                substr_count($filename, '.') > 0
            ) {
                unset($filenames[$key]);
            }
        }

        return array_values($filenames);
    }
}
