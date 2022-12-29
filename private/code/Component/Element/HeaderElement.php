<?php
namespace Pyncer\Docs\Component\Element;

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\Component\Element\AbstractElement;
use Pyncer\Docs\Identifier as ID;

use const DIRECTORY_SEPARATOR as DS;
use function Pyncer\IO\clean_dir as pyncer_io_clean_dir;
use function Pyncer\IO\filenames as pyncer_io_filenames;

class HeaderElement extends AbstractElement
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

        if (!$this->paths) {
            $title = $i18n->get('page.title');
        } else {
            $title = $i18n->get('page.' . implode('/', $this->paths) . '.title');
        }

        return [
            'title' => $title,
        ];
    }
}
