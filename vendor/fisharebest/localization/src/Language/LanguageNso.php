<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryZa;

/**
 * Class LanguageNso - Representation of the Pedi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2018 Greg Roach
 * @license   GPLv3+
 */
class LanguageNso extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'nso';
    }

    public function defaultTerritory()
    {
        return new TerritoryZa();
    }

    public function pluralRule()
    {
        return new PluralRule2();
    }
}
