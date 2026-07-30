@if(auth()->user()->role === 'admin')
    <x-layouts.admin>
        <x-slot:breadcrumb>
            <li class="flex items-center text-[#0F034D] font-semibold gap-1.5">
                <svg class="w-4 h-4 shrink-0 text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Profile Settings
            </li>
        </x-slot:breadcrumb>

        <x-slot:header>
            Profile Settings
        </x-slot:header>

        @include('profile.partials.form')
    </x-layouts.admin>
@elseif(auth()->user()->role === 'owner')
    <x-layouts.owner>
        <x-slot:breadcrumb>
            <li class="flex items-center text-[#0F034D] font-semibold gap-1.5">
                <svg class="w-4 h-4 shrink-0 text-[#0F034D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Profile Settings
            </li>
        </x-slot:breadcrumb>

        <x-slot:header>
            Profile Settings
        </x-slot:header>

        @include('profile.partials.form')
    </x-layouts.owner>
@else
    <x-layouts.produksi>
        <x-slot:header>
            Profile Settings
        </x-slot:header>

        @include('profile.partials.form')
    </x-layouts.produksi>
@endif
