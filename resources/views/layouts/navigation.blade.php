<div class="it-header-wrapper">
  <div class="it-header-slim-wrapper">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="it-header-slim-wrapper-content">
            <a class="d-none d-lg-block navbar-brand" href="{{ route('welcome') }}">Calabria Verde</a>
            <div class="nav-mobile">
              <nav aria-label="Navigazione secondaria">
                <a class="it-opener d-lg-none" data-bs-toggle="collapse" href="#menu-principale" role="button" aria-expanded="false" aria-controls="menu-principale">
                  <span>Menu</span>
                  <svg class="icon" aria-hidden="true"><use href="{{ asset('sprites.php') }}#it-expand"></use></svg>
                </a>
              </nav>
            </div>
            
            <div class="it-header-slim-right-zone">
              @auth
              <div class="nav-item dropdown" style="position:relative;">
                <a class="nav-link text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration:none; cursor:pointer; padding: 4px 0;">
                  Ciao, <strong>{{ Auth::user()->name }} {{ Auth::user()->surname }}</strong> ▾
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width:180px; padding:4px 0; border-radius:6px; border:1px solid #e0e0e0; margin-top:2px;">
                  <li>
                    <a class="dropdown-item py-2 px-3" href="{{ route('profile.edit') }}" style="color:#333; font-size:0.9em;"
                       onmouseover="this.style.backgroundColor='#e8e8e8'" onmouseout="this.style.backgroundColor='transparent'">
                      👤 Il mio Profilo
                    </a>
                  </li>
                  <li><hr class="dropdown-divider my-1"></li>
                  <li>
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit" class="dropdown-item py-2 px-3" style="color:#333; font-size:0.9em; background-color:#f5f5f5 !important;"
                              onmouseover="this.style.backgroundColor='#e0e0e0'" onmouseout="this.style.backgroundColor='#f5f5f5'">
                        🚪 Esci
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
              @else
              <div class="nav-item">
                <a class="btn btn-primary btn-sm" href="{{ route('login') }}">Accedi</a>
              </div>
              @endauth
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="it-nav-wrapper">
    <div class="it-header-center-wrapper">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="it-header-center-content-wrapper">
              <div class="it-brand-wrapper">
                <a href="{{ Auth::check() ? route('dashboard') : route('welcome') }}" class="d-flex align-items-center text-decoration-none">
                  <img src="{{ asset('images/logoCalabriaVerde.png') }}" alt="Logo Calabria Verde" style="height: 80px; margin-right: 15px;">
                  <div class="it-brand-text">
                    <div class="it-brand-title">Gestionale CV</div>
                    <div class="it-brand-tagline">Sistema Informativo Aziendale</div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
