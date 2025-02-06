<?php

namespace App\Infolists\Components;

use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Entry;

class MaskedTextEntry extends Entry
{
    protected string $view = 'infolists.components.masked-text-entry';


    public function MaskAction(): array
    {
        return
            Action::make('mask');
    }
}
