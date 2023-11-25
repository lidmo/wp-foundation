<?php

namespace Lidmo\WP\Foundation;
use Composer\Script\Event;
use Lidmo\WP\Foundation\Support\Str;
class ComposerScripts
{
    protected static array $ignorePaths = [
        'vendor',
        'wordpress',
        'node_modules',
        'tests',
        'bin',
    ];

    protected array $replaces = [];


    private function generateReplaces($replace, $search)
    {
        $this->replaces = [
            $search => $replace,
            str_replace('-', '_', $search) => str_replace('-', '_', $replace),
            strtoupper(str_replace('-', '_', $search)) => strtoupper(str_replace('-', '_', $replace)),
            Str::camel($search) => Str::camel($replace),
            Str::studly($search) => Str::studly($replace),
            Str::title(str_replace('-', ' ', $search)) => Str::title(str_replace('-', ' ', $replace)),
        ];

    }

    public function getReplaces(): array
    {
        return $this->replaces;
    }

    /**
     * @param $path
     */
    public function scanTheDir($path)
    {
        $items = scandir($path);
        foreach ($items as $item) {
            $path_item = $path . '/' . $item;
            if ($item == '.' || $item == '..' || realpath($path_item) == __FILE__
                || (is_dir($path_item) && in_array(basename($path_item), self::$ignorePaths))) {
                continue;
            }
            if (is_dir($path_item)) {
                $path_item = $this->renameFile($path, $item);
                $this->scanTheDir($path_item);
            } else {
                $path_item = $this->renameFile($path, $item);
                $content = file_get_contents($path_item);
                $new_content = $this->replaceValues($content);
                file_put_contents($path_item, $new_content);
            }
        }
    }

    /**
     * @param $path
     * @param $item
     * @return string
     */
    public function renameFile($path, $item): string
    {
        $new_name = $this->replaceValues($item);
        if ($new_name != $item && rename($path . '/' . $item, $path . '/' . $new_name)) {
            return $path . '/' . $new_name;
        } else {
            return $path . '/' . $item;
        }
    }

    /**
     * @param $content string
     * @return string
     */
    public function replaceValues(string $content): string
    {

        foreach ($this->getReplaces() as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }

        return $content;
    }

    public static function pluginInstall(Event $event)
    {
        $self = new self;
        $self->generateReplaces(Str::slug(basename(self::_getPath($event))), 'lidmo-prefix');
        $self->scanTheDir('.');
    }

    private static function _getPath(Event $event)
    {
        $package = $event->getComposer()->getPackage();
        $path = $event->getComposer()->getInstallationManager()->getInstallPath($package);
        $standardPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
        $replacement = self::_createPath('vendor', 'lidmo', 'wp-plugin-start');
        return str_replace($replacement, '', $standardPath);
    }

    private static function _createPath()
    {
        return preg_replace('~[/\\\]+~', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, func_get_args()));
    }
}