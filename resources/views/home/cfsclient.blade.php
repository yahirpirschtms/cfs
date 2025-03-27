@auth
    @extends('layouts.app-master')

    @section('title', 'CFS')

    @section('content')
        <div class="container  my-4">
            <div class="my-4 d-flex justify-content-center align-items-center">
                <h2 class="gradient-text text-capitalize fw-bolder" >CFS Clients</h2>
            </div> 
            @if(in_array('view', Auth::user()->permissions))
                <div>Products Section</div>
            @endif  
            
            @if(in_array('edit', Auth::user()->permissions))
                <div>Products Section</div>
            @endif 
        </div>
    @endsection

    @section('scripts')
        <!-- Referencia al archivo JS de manera directa -->
        <script src="{{ asset('js/cfsclient.js') }}"></script> <!-- Asegúrate que el archivo esté en public/js -->
    @endsection
@endauth

@guest
    <p>Access denied, go to the <a href="/login">Login</a></p>
@endguest
