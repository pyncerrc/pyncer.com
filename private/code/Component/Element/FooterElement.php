<?php
namespace Pyncer\Docs\Component\Element;

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\Component\Element\AbstractElement;
use Pyncer\Docs\Identifier as ID;

use const DIRECTORY_SEPARATOR as DS;
use function Pyncer\IO\clean_dir as pyncer_io_clean_dir;
use function Pyncer\IO\filenames as pyncer_io_filenames;

class FooterElement extends AbstractElement
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
        return null;
    }
}
