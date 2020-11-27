<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

</head>
<body class="bg-gray-100 font-sans antialiased">
<div id="app">
    <div class="bg-white">
        <div class="container mx-auto px-8 py-4 flex flex-row">
            <a href="#" class="logo text-xl font-semibold flex-initial flex flex-row items-center tracking-wider">
                <img src="/images/logo.svg" class="mr-4"/>
                Your<span class="text-blue-600">Balance</span>
            </a>
            <div class="flex content-center flex-row flex-grow justify-end h-full">
                <a href="#" class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="block mx-auto my-auto" width="16" height="16"
                         viewBox="0 0 16 16">
                        <path class="notificationIcon" fill="#A0A5BA"
                              d="M10 14L6 14C6 15.1 6.9 16 8 16 9.1 16 10 15.1 10 14zM15 11L14.5 11C13.8 10.3 13 9.3 13 8L13 5C13 2.2 10.8 0 8 0 5.2 0 3 2.2 3 5L3 8C3 9.3 2.2 10.3 1.5 11L1 11C.4 11 0 11.4 0 12 0 12.6.4 13 1 13L15 13C15.6 13 16 12.6 16 12 16 11.4 15.6 11 15 11z"/>
                    </svg>
                </a>
                <a href="#" class="flex items-center font-bold text-sm text-gray-500">
                    <img src="/images/avatar.png" class="w-8 mx-4"/>
                    {{ Auth::user()->name }}
                </a>
            </div>
        </div>
    </div>

    <div class="mb-12 py-6 bg-gray-800">
        <div class="container mx-auto flex px-8">
            <div class="my-auto text-white flex flex-grow items-center">
                <h1 class="md:block hidden mr-4 text-2xl font-bold">
                    Your Balance
                </h1>

                <div class="flex flex-row">
                    <button id="insertModalButton"
                        class="flex items-center mr-4 px-3 py-2 bg-blue-700 rounded-md text-white text-xs font-bold uppercase tracking-tight disabled:opacity-50"
                        @click="showInsertModal" {{ Auth::user()->jobRunning == true ? 'disabled' : '' }}>
                        Add Entry
                    </button>
                    <button id="insertCsvModalButton"
                        class="flex items-center mr-4 px-3 py-2 bg-blue-700 rounded-md text-white text-xs font-bold uppercase tracking-tight disabled:opacity-50"
                        @click="showInsertCsvModal" {{ Auth::user()->jobRunning == true ? 'disabled' : '' }}>
                        Import CSV
                    </button>
                </div>
            </div>
            <Balance :balance="this.balance"></Balance>
        </div>
    </div>

    <div class="container mx-auto px-8">
        <div id="jobRunning-message" class="p-6 m-10 full-w mx-auto border-gray-100 bg-orange-400 text-white text-bold rounded text-center {{ Auth::user()->jobRunning ? '' : 'hidden' }}">
            We're importing the balance entries. Sit tight.
        </div>
        @foreach($transactions as $date => $transaction)
            <Group date="{{ $date }}" :transactions="{{ json_encode($transaction) }}"></Group>
        @endforeach
    </div>

    <insert-modal v-show="isInsertModalVisible" @close="closeInsertModal" ref="insertModal"></insert-modal>
    <insert-csv-modal v-show="isInsertCsvModalVisible" @close="closeInsertCsvModal" @import="importCsv"
                      ref="insertCsvModal"></insert-csv-modal>
</div>


<!-- Scripts -->
<script src="{{ mix('js/app.js') }}" defer></script>
</body>
</html>
