@php
    $gridDirection = $getGridDirection() ?? 'column';
    $hasInlineLabel = $hasInlineLabel();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isInline = $isInline();
    $isMultiple = $isMultiple();
    $statePath = $getStatePath();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    :has-inline-label="$hasInlineLabel"
>
    <x-slot
        name="label"
        @class([
            'sm:pt-1.5' => $hasInlineLabel,
        ])
    >
        {{ $getLabel() }}
    </x-slot>

    {{--    <x-filament::grid--}}
    {{--        :default="$getColumns('default')"--}}
    {{--        :sm="$getColumns('sm')"--}}
    {{--        :md="$getColumns('md')"--}}
    {{--        :lg="$getColumns('lg')"--}}
    {{--        :xl="$getColumns('xl')"--}}
    {{--        :two-xl="$getColumns('2xl')"--}}
    {{--        :is-grid="! $isInline"--}}
    {{--        :direction="$gridDirection"--}}
    {{--        :attributes="--}}
    {{--            \Filament\Support\prepare_inherited_attributes($attributes)--}}
    {{--                ->merge($getExtraAttributes(), escape: false)--}}
    {{--                ->class([--}}
    {{--                    'fi-fo-toggle-buttons gap-3',--}}
    {{--                    '-mt-3' => (! $isInline) && ($gridDirection === 'column'),--}}
    {{--                    'flex flex-wrap w-full' => $isInline,--}}
    {{--                ])--}}
    {{--        "--}}
    {{--    >--}}
    <div class="flex w-full gap-3">
        @foreach ($getOptions() as $value => $label)
            @php
                $inputId = "{$id}-{$value}";
                $shouldOptionBeDisabled = $isDisabled || $isOptionDisabled($value, $label);
            @endphp

            <div class="w-full"
                @class([
                    'break-inside-avoid pt-3 w-full' => (! $isInline) && ($gridDirection === 'column'),
                ])
            >
                <input
                    @disabled($shouldOptionBeDisabled)
                    id="{{ $inputId }}"
                    @if (! $isMultiple)
                        name="{{ $id }}"
                    @endif
                    type="{{ $isMultiple ? 'checkbox' : 'radio' }}"
                    value="{{ $value }}"
                    wire:loading.attr="disabled"
                {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}"
                {{ $getExtraInputAttributeBag()->class(['peer pointer-events-none absolute opacity-0']) }}
                />

                <x-filament::button
                    :color="$getColor($value)"
                    :disabled="$shouldOptionBeDisabled"
                    :for="$inputId"
                    {{--                    :icon=""--}}
                    tag="label"
                    class="text-gray-600 dark:text-gray-400 border-gray-200 hover:cursor-pointer justify-center false bg-gray-50 dark:bg-gray-800 dark:border-gray-600 hover:border-teal-400 mx-1 flex w-full items-center rounded-md border px-4 py-6 text-xl font-medium"
                >
                    <div class="flex justify-center items-center gap-3">
                        <x-dynamic-component
                            :component="$getIcon($value)"
                            class="mr-2 h-10 w-10 flex-shrink-0 stroke-2 text-gray-700"
                        />
                        {{ $label }}
                    </div>
                </x-filament::button>
            </div>
        @endforeach

    </div>
    {{--    </x-filament::grid>--}}
</x-dynamic-component>
