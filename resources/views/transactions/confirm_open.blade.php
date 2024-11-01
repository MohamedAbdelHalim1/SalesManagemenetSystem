<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Open New Transaction') }}
        </h2>
    </x-slot>

    <!-- Full-screen overlay with blurred background -->
    <div class="overlay">
        <!-- Modal content in the center -->
        <div class="modal-content">
            <h3 class="text-lg font-semibold mb-4">Do you want to open a new transaction?</h3>
            
            <div class="action-btn flex justify-center space-x-4">
                <a href="{{ route('open_transaction') }}" class="open text-white font-bold py-2 px-4 rounded">OK</a>
                <a href="{{ route('cancel_transaction') }}" class="close  text-white font-bold py-2 px-4 rounded">No</a>
            </div>
        </div>
    </div>

    <!-- Inline CSS -->
    <style>
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 50;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .action-btn{
            display:flex;
            flex-direction:row;
            gap:20px;
        }
        .action-btn .open {
            background-color: green; 
        }
        .action-btn .close{
            background-color:red;
        }
    </style>
</x-app-layout>
