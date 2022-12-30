<?php
namespace Pyncer\Docs\Component\Element;

use League\CommonMark\GithubFlavoredMarkdownConverter;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\Component\Element\AbstractElement;
use Pyncer\Docs\Identifier as ID;
use Pyncer\Http\Message\HtmlResponse;
use Pyncer\Http\Message\Status;

use const DIRECTORY_SEPARATOR as DS;
use function Pyncer\http\clean_path as pyncer_http_clean_path;
use function Pyncer\IO\clean_dir as pyncer_io_clean_dir;
use function Pyncer\IO\filenames as pyncer_io_filenames;

class MainElement extends AbstractElement
{
    protected ?string $dir;
    protected array $paths;

    public function __construct(
        PsrServerRequestInterface $request,
        ?string $dir,
        array $paths,
    ) {
        parent::__construct($request);

        $this->dir = ($dir !== null ? pyncer_io_clean_dir($dir) : $dir);
        $this->paths = $paths;
    }

    protected function getResponseData(): mixed
    {
        $i18n = $this->get(ID::I18N);

        $files = [];

        if ($this->paths) {
            $paths = $this->paths;
            $path = array_pop($paths);

            foreach ($i18n->getRankedLocaleCodes() as $localeCode) {
                if ($paths) {
                    $files[] = $this->dir . DS . implode(DS, $paths) . DS .
                        $path . DS . 'index.' . $localeCode . '.md';

                    $files[] = $this->dir . DS . implode(DS, $paths) . DS .
                         $path . '.' . $localeCode . '.md';
                } else {
                    $files[] = $this->dir . DS .
                        $path . DS . 'index.' . $localeCode . '.md';

                    $files[] = $this->dir . DS .
                         $path . '.' . $localeCode . '.md';
                }
            }

            $files[] = $this->dir . DS . implode(DS, $this->paths) . DS .'index.md';
            $files[] = $this->dir . DS . implode(DS, $this->paths) . '.md';
        } else {
            foreach ($i18n->getRankedLocaleCodes() as $localeCode) {
                $files[] = $this->dir .  DS .'index.' . $localeCode . '.md';
            }

            $files[] = $this->dir .  DS .'index.md';
        }

        $markdown = null;

        foreach ($files as $file) {
            if (file_exists($file)) {
                $markdown = file_get_contents($file);
                $githubFile = substr($file, strlen($this->dir));
                break;
            }
        }

        $githubFile = pyncer_http_clean_path($githubFile);

        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $html = $converter->convert($markdown);

        return [
            'github_file' => $githubFile,
            'content' => $html,
        ];
    }
}
