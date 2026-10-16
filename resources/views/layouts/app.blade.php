<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Perk Up Coffee</title>
    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    {{-- Google Font: Inter --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    
    {{-- Custom Styles --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    
</head>
<body>
    <div class="d-flex" id="wrapper">
        {{-- Sidebar (Desktop) --}}
        @include('layouts.sidebar') 

        {{-- Page Content Wrapper --}}
        <div id="page-content-wrapper" class="flex-grow-1">
            {{-- Navbar (Sticky Top) --}}
            @include('layouts.navbar') 

            {{-- Main Content Section --}}
            <main class="container-fluid py-4 px-4 px-lg-5">
                @yield('content') 
            </main> 
        </div>
    </div>

    {{-- Modals/Offcanvas --}}
    @include('layouts.offcanvas') 
    @include('layouts.modals') 

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
</body>
</html>