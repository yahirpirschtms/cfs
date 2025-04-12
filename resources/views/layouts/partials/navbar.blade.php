@auth
<div class="fixed-padding" style="padding-bottom: 110px;">
        <nav class="navbar navbar-expand-lg fixed-top">
          <div class="container">
            <button class="navbar-toggler ps-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
              <i class="fa-solid fa-bars text-light"></i>
            </button>
            <a class="navbar-brand fw-bolder d-none d-lg-block non" href="#"><img src="{!! asset('/icons/tms_logo1.png')!!}" alt="Logo" height="35" class="d-inline-block align-middle ">
              CFS</a>
            <a class="d-md-block d-lg-none navbar-brand fw-bolder align-middle non" href="#">TMS CFS</a>
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
              <div class="offcanvas-header position-relative" style="background-color: #1e4877;">
                <h5 class="offcanvas-title align-middle non text-light fw-bolder" id="offcanvasNavbarLabel">CFS</h5>
                <div data-bs-theme="dark">
                <button type="button" class="btn-close position-absolute top-0 end-0 " data-bs-dismiss="offcanvas" aria-label="Close" style="padding-top: 3.5rem; padding-right: 3.1rem"></button>
                </div>
              </div>
              <div class="offcanvas-body offcanvas-bodyy">
                <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
                  <!--Trailer Status-->
                  <li class="nav-item ">
                    <a class="nav-link mx-lg-2 text-light" aria-current="page" href=""></a>
                  </li>

                </ul>
              </div>
            </div><p class="navbar-brand fw-bolder d-none d-lg-block non" style="color:transparent">TMS CFS
                  </p>
            <a href="" class="login-button"  data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fa-solid fa-user"></i></a>
          </div>
        </nav>
      </div>
      <!--End Navbar-->

      <!-- Modal -->
      <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialogg modal-sm">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">CFS</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <p>Bienvenido {{auth()->user()->username ?? auth()->user()->username}}</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn text-white btn-danger" style="" onclick="document.getElementById('logout-form').submit();">Logout</button>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
            </div>
        </div>
      </div>
@endauth
