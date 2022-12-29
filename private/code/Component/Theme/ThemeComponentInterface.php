<?php
namespace Pyncer\Docs\Component\Theme;

use Pyncer\Component\Page\PageComponentInterface;

interface ThemeComponentInterface
{
    public function setPageComponent(PageComponentInterface $page);
}
