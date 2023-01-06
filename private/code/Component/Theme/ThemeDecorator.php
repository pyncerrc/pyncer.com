<?php
namespace Pyncer\Docs\Component\Theme;

use Pyncer\Component\ComponentDecoratorInterface;
use Pyncer\Component\ComponentInterface;
use Pyncer\Docs\Component\Theme\ThemeComponentInterface;
use Pyncer\Docs\Component\Page\SitemapPage;

class ThemeDecorator implements ComponentDecoratorInterface
{
    public function __construct(
        private ThemeComponentInterface $themeComponent
    ) {}

    public function apply(
        ComponentInterface $component
    ): ComponentInterface
    {
        if ($component instanceof SitemapPage) {
            return $component;
        }

        $this->themeComponent->setPageComponent($component);
        return $this->themeComponent;
    }
}
