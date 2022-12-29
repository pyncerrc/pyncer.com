<?php
namespace Pyncer\Docs;

use Pyncer\App\Identifier as PyncerIdentifier;

class Identifier extends PyncerIdentifier
{
    const I18N_SOURCE_MAP = 'i18n_source_map';
    const ROUTER_SOURCE_MAP = 'router_source_map';

    private function __construct()
    {}

    public static function isValid(string $value): bool
    {
        switch ($value) {
            case self::I18N_SOURCE_MAP:
            case self::ROUTER_SOURCE_MAP:
                return true;
        }

        return parent::isValid($value);
    }
}
