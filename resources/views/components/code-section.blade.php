<div class="cursor-pointer rounded-sm focus:outline-none" tabindex="0"
     x-on:click="window.navigator.clipboard.writeText('{{$slot}}'); $tooltip('Copied to Clipboard', { theme: 'dark', timeout: 800 });"
     x-tooltip="{ content: 'Click to copy', theme: 'dark' }"
>
    <div
        class="bg-slate-900 text-white p-4 rounded-lg shadow-lg hover:bg-slate-800 transition duration-300 focus:outline-none">
        <code class="block text-sm font-mono overflow-x-auto whitespace-normal break-words focus:outline-none">
            {{$slot}}
        </code>
    </div>
</div>
