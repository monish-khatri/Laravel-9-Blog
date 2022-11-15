<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="jokeHtml p-6 bg-white border-b border-gray-200">
                    <strong>{{$joke}}</strong>
                    <a onclick="refreshJoke()" class="btn btn-xs float-right">
                        <span><i class="fa fa-refresh text-red" aria-hidden="true"></i></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
     <script>
        function refreshJoke() {
            $.ajax({
                type : "GET",
                url : '{{route('randomJoke')}}',
                dataType: 'html',
                success : function(response) {
                    var $htmlResponse = $(response);
                    data = $htmlResponse.find('.jokeHtml').html()
                    $(".jokeHtml").html(data);
                }
            });
        }
    </script>