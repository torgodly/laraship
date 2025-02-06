<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="fi-in-text w-full">
        <div class="fi-in-affixes flex">
            <div class="min-w-0 flex-1">
                <div class="flex ">
                    <div class="flex max-w-max" style="">
                        <div class="fi-in-text-item inline-flex items-center gap-1.5  ">
                            <div class="text-sm leading-6 text-gray-950 dark:text-white  " style="">
                                {{$getState()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
