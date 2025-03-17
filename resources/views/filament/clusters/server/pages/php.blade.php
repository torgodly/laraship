<x-filament-panels::page>
    <x-filament::section>
        <x-filament::section.heading class="text-lg font-bold ">
            PHP Versions
        </x-filament::section.heading>
        <x-custom-filament.table>
            <x-slot name="header_cells">
                <x-custom-filament.table.header-cell>
                    Version
                </x-custom-filament.table.header-cell>
                <x-custom-filament.table.header-cell>
                    Status
                </x-custom-filament.table.header-cell>
                <x-custom-filament.table.header-cell>
                    CLI Default
                </x-custom-filament.table.header-cell>
                <x-custom-filament.table.header-cell>
                    Sites Default
                </x-custom-filament.table.header-cell>
                <x-custom-filament.table.header-cell>
                    Patch
                </x-custom-filament.table.header-cell>
                <x-custom-filament.table.header-cell>
                </x-custom-filament.table.header-cell>
            </x-slot>
            <x-slot name="body_cells">
                @foreach(\App\Enums\PhpVersionsEnum::cases() as $php)
                    <tr class="h-12 border-t border-gray-100 dark:border-gray-700">
                        <x-custom-filament.table.cell>
                            <div class="flex-col flex">
                                {{$php->label()}}
                                <span class="text-xs text-red-500">
                                {{$php->value}}
                            </span>
                            </div>
                        </x-custom-filament.table.cell>
                        <x-custom-filament.table.cell>
                            <div class="flex flex-wrap gap-2">
                                @if($php->isInstalled())
                                    <span
                                        class="text-uppercase inline-flex items-center rounded-full px-2.5 py-1 text-sm bg-teal-400 bg-opacity-10 text-gray-900 dark:bg-teal-400/40 dark:text-white/80"><span
                                            class="relative mr-1.5 flex h-2.5 w-2.5"><!----><span
                                                class="relative inline-flex h-2.5 w-2.5 rounded-full bg-teal-400"></span></span>Installed</span>
                                @else
                                    <span>—</span>
                                @endif
                            </div>
                        </x-custom-filament.table.cell>
                        @if($php->isDefault())

                            <x-custom-filament.table.cell>
                                <span
                                    class="text-uppercase inline-flex items-center rounded-full px-2.5 py-1 text-sm bg-teal-400 bg-opacity-10 text-gray-900 dark:bg-teal-400/40 dark:text-white/80"><span
                                        class="relative mr-1.5 flex h-2.5 w-2.5"><!----><span
                                            class="relative inline-flex h-2.5 w-2.5 rounded-full bg-teal-400"></span></span>Default</span>
                            </x-custom-filament.table.cell>
                            <x-custom-filament.table.cell>
                            <span
                                class="text-uppercase inline-flex items-center rounded-full px-2.5 py-1 text-sm bg-teal-400 bg-opacity-10 text-gray-900 dark:bg-teal-400/40 dark:text-white/80"><span
                                    class="relative mr-1.5 flex h-2.5 w-2.5"><!----><span
                                        class="relative inline-flex h-2.5 w-2.5 rounded-full bg-teal-400"></span></span>Default</span>
                            </x-custom-filament.table.cell>
                        @else
                            <x-custom-filament.table.cell>
                                <span>—</span>
                            </x-custom-filament.table.cell>
                            <x-custom-filament.table.cell>
                                <span>—</span>
                            </x-custom-filament.table.cell>
                        @endif

                        <x-custom-filament.table.cell class="">
                            {{($this->patchPhpAction)(['version' => $php->value])}}
                        </x-custom-filament.table.cell>
                        <x-custom-filament.table.cell class="">
                            {{($this->installPhpAction)(['version' => $php->value])}}
                        </x-custom-filament.table.cell>

                    </tr>
                @endforeach
            </x-slot>
        </x-custom-filament.table>
    </x-filament::section>
</x-filament-panels::page>
