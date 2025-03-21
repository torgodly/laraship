<td
    {{ $attributes->class(['fi-ta-cell p-0 first-of-type:ps-1 last-of-type:pe-1 sm:first-of-type:ps-3 sm:last-of-type:pe-3 fi-table-cell-student.name']) }}>
    <div class="fi-ta-col-wrp">
        <div class="flex w-full disabled:pointer-events-none justify-start text-start">
            <div class="fi-ta-text grid w-full gap-y-1 px-3 py-4">
                <div class="flex">
                    <div class="flex max-w-max">
                        <div class="fi-ta-text-item inline-flex items-center gap-1.5">
                                                        <span
                                                            class="fi-ta-text-item-label text-sm leading-6 text-gray-950 dark:text-white ">{{$slot}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</td>
