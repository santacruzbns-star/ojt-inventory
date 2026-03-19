<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <style>
        /* table,
        tr,
        th,
        td {
            border-color: black;
            border-style: solid;
            border: collapse;
            font-display: center;
        } */
        span{
            font-size: 30px;
            font-weight: bold;
            padding: 99px;
            border: 1px solid black;
            margin: 20px;
                
        }

    </style>
    <div class="py-12">
        {{-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div> --}}
    </div>
    <div class="content">
        
       <span> Item Count <br>{{ $ItemCounts }}</span>;
        <span> Outbound Count <br>{{ $OutboundCounts }}</span>
    </div>
</x-app-layout>
