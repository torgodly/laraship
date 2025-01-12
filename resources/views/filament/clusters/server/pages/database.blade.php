<x-filament-panels::page>
    <x-filament::section>
        <x-filament::section.heading class="text-lg font-bold ">
            Database Connection URL
        </x-filament::section.heading>
        <x-filament::section.description class="text-sm py-2">
            Use the database connection string below to connect to your database from your database client. We recommend
            using TablePlus. You will need to manually provide the database's password that was emailed to you when you
            provisioned this server.
        </x-filament::section.description>
        <x-code-section>
            mysql+ssh://forge@145.223.118.97/forge@127.0.0.1/forge?name=Sahablibya&usePrivateKey=true
        </x-code-section>
    </x-filament::section>

    <x-filament::section>
        <x-filament::section.heading class="text-lg font-bold ">
            Add Database
        </x-filament::section.heading>
        <x-filament-panels::form wire:submit="createDatabase" class="pt-3">
            {{$this->createDatabaseForm}}
            <div class="flex justify-end">
                <x-filament-panels::form.actions
                    :actions="$this->getFormActions('create')"
                />
            </div>
        </x-filament-panels::form>
    </x-filament::section>

    <x-filament::section>
        <x-filament::section.heading class="text-lg font-bold ">
            Databases
        </x-filament::section.heading>
        <x-custom-fileament.table>
            <x-slot name="header_cells">
                <x-custom-filamnet.table.header-cell>
                    Database
                </x-custom-filamnet.table.header-cell>
                <x-custom-filamnet.table.header-cell>
                </x-custom-filamnet.table.header-cell>
            </x-slot>
            <x-slot name="body_cells">

                @foreach($this->getDatabases() as $database)

                    <tr class="h-12 border-t border-gray-100 dark:border-gray-700">
                        <x-custom-filamnet.table.cell>
                            {{$database}}
                        </x-custom-filamnet.table.cell>
                        <x-custom-filamnet.table.cell class="w-full flex justify-end">
                            {{$this->removeDatabaseAction}}
                        </x-custom-filamnet.table.cell>
                    </tr>
                @endforeach
            </x-slot>

        </x-custom-fileament.table>
    </x-filament::section>
    <x-filament::section>
        <x-filament::section.heading class="text-lg font-bold ">
            Add Database User
        </x-filament::section.heading>
        <x-filament-panels::form wire:submit="addDatabaseUser" class="pt-3">
            {{$this->addDatabaseUserForm}}
            <div class="flex justify-end">
                <x-filament-panels::form.actions
                    :actions="$this->getFormActions('create')"
                />
            </div>
        </x-filament-panels::form>
    </x-filament::section>

    <x-filament::section>
        <x-filament::section.heading class="text-lg font-bold ">
            Database Users
        </x-filament::section.heading>
        <x-custom-fileament.table>
            <x-slot name="header_cells">
                <x-custom-filamnet.table.header-cell>
                    Name
                </x-custom-filamnet.table.header-cell>
                <x-custom-filamnet.table.header-cell>
                </x-custom-filamnet.table.header-cell>
            </x-slot>
            <x-slot name="body_cells">

                @foreach($this->getDatabaseUsers() as $user)
                    <tr class="h-12 border-t border-gray-100 dark:border-gray-700">
                        <x-custom-filamnet.table.cell>
                            {{$user}}
                        </x-custom-filamnet.table.cell>
                        <x-custom-filamnet.table.cell class="w-full flex justify-end">
                            {{$this->editDatabaseUserAction}}
                        </x-custom-filamnet.table.cell>
                    </tr>
                @endforeach
            </x-slot>

        </x-custom-fileament.table>
    </x-filament::section>
</x-filament-panels::page>
