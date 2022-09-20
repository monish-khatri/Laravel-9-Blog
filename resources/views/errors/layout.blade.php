<x-app-layout>
@push('css')
    <title>@yield('title')</title>

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 36px;
            padding: 20px;
        }
    </style>
@endpush
    <div class="py-12">
        <div class="container">
            <div class="col-md-12 content">
                <div class="flex-center position-ref full-height">
                    <div class="content">
                        <div class="title">
                            @yield('message')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>