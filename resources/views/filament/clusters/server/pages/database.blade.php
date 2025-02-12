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
        <x-custom-filament.table>
            <x-slot name="header_cells">
                <x-custom-filament.table.header-cell>
                    Database
                </x-custom-filament.table.header-cell>
                <x-custom-filament.table.header-cell>
                    Users
                </x-custom-filament.table.header-cell>
                <x-custom-filament.table.header-cell>
                </x-custom-filament.table.header-cell>
            </x-slot>
            <x-slot name="body_cells">

                @foreach($this->getDatabases() as $database)

                    <tr class="h-12 border-t border-gray-100 dark:border-gray-700">
                        <x-custom-filament.table.cell>
                            {{$database->name}}
                        </x-custom-filament.table.cell>
                        <x-custom-filament.table.cell>
                            <div class="flex flex-wrap gap-2">
                                @foreach($database->users as $user)
                                    <x-filament::badge >{{$user->username}}</x-filament::badge>
                                @endforeach
                            </div>
                        </x-custom-filament.table.cell>
                        <x-custom-filament.table.cell class="w-full flex justify-end min-w-fit">
                            {{($this->removeDatabaseAction)(['database' => $database->id])}}
                        </x-custom-filament.table.cell>
                    </tr>
                @endforeach
            </x-slot>

        </x-custom-filament.table>
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
        <x-custom-filament.table>
            <x-slot name="header_cells">
                <x-custom-filament.table.header-cell>
                    Name
                </x-custom-filament.table.header-cell>
                <x-custom-filament.table.header-cell>
                    Databases
                </x-custom-filament.table.header-cell>
                <x-custom-filament.table.header-cell>
                </x-custom-filament.table.header-cell>
            </x-slot>
            <x-slot name="body_cells">

                @foreach($this->getDatabaseUsers() as $user)
                    <tr class="h-12 border-t border-gray-100 dark:border-gray-700">
                        <x-custom-filament.table.cell>
                            {{$user->username}}
                        </x-custom-filament.table.cell>
                        <x-custom-filament.table.cell>
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->databases as $database)
                                    <x-filament::badge >{{$database->name}}</x-filament::badge>
                                @endforeach
                            </div>
                        </x-custom-filament.table.cell>
                        <x-custom-filament.table.cell class="w-full flex justify-end">
                            {{($this->editDatabaseUserAction)(['user' => $user])}}
                        </x-custom-filament.table.cell>
                    </tr>
                @endforeach
            </x-slot>

        </x-custom-filament.table>
    </x-filament::section>
</x-filament-panels::page>
