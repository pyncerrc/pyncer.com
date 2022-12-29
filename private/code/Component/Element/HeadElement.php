<?php
namespace Pyncer\Docs\Component\Element;

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\Component\Element\AbstractElement;
use Pyncer\Docs\Identifier as ID;

use const DIRECTORY_SEPARATOR as DS;
use function Pyncer\IO\clean_dir as pyncer_io_clean_dir;
use function Pyncer\IO\filenames as pyncer_io_filenames;

class HeadElement extends AbstractElement
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

        $title = $i18n->get('page.title');

        if (!$this->paths) {
            $description = $i18n->get('page.description');
        } else {
            $paths = $this->paths;
            $key = null;
            while (count($paths) > 0) {
                $key = 'page.' . implode('/', $paths) . '.description';

                if ($i18n->has($key)) {
                    break;
                }

                array_pop($paths);
                $key = null;
            }

            if ($key !== null) {
                $description = $i18n->get($key);
            } else {
                $description = $i18n->get('page.description');
            }

            $i18nKey = '';

            foreach ($this->paths as $path) {
                if ($i18nKey === '') {
                    $i18nKey .= $path;
                } else {
                    $i18nKey .= '/' . $path;
                }

                $title = $i18n->get(
                    key: 'syntax.join_title',
                    args: [
                        $i18n->get('page.' . $i18nKey . '.title'),
                        $title,
                    ]
                );

            }
        }

        return [
            'title' => $title,
            'description' => $description,
        ];
    }
}
