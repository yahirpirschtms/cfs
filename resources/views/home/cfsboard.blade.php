@auth
    @extends('layouts.app-master')

    @section('title', 'CFS')

    @section('content')
        <div class="container  my-4">
            @if(in_array('add_new', Auth::user()->permissions))
                <div class="my-4 d-flex justify-content-center align-items-center">
                    <h2 class="gradient-text text-capitalize fw-bolder" >CFS Board</h2>
                </div> 
            @endif  
            
            @if(in_array('edit', Auth::user()->permissions))
                <div>Products Section</div>
            @endif 
        </div>
    @endsection

    @section('scripts')
        <!-- Referencia al archivo JS de manera directa -->
        <script src="{{ asset('js/cfsboard.js') }}"></script> <!-- Asegúrate que el archivo esté en public/js -->
    @endsection
@endauth

@guest
    <p>Access denied, go to the <a href="/login">Login</a></p>
@endguest
