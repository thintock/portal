{{-- ✅ Success --}}
@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50">
        <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif

{{-- ✅ Error --}}
@if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50">
        <div class="text-red-500 text-sm mb-2">
            <span>{{ session('error') }}</span>
        </div>
    </div>
@endif

{{-- ✅ Warning --}}
@if (session('warning'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50">
        <div class="alert alert-warning shadow-md text-sm px-4 py-2 bg-opacity-80">
            <span>{{ session('warning') }}</span>
        </div>
    </div>
@endif

{{-- ✅ Validation Errors --}}
@if ($errors->any())
    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50">
        <div class="alert alert-error shadow-md text-sm px-4 py-2 bg-opacity-80">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
