<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SisDoc - Fundeeh</title>
<link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet" type="text/css" >
<link href="{{ asset('css/estilos.css') }}" rel="stylesheet" type="text/css" >
<link href="{{ asset('fonts/fontawesome/css/fontawesome.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('fonts/fontawesome/css/brands.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('fonts/fontawesome/css/solid.css') }}" rel="stylesheet" type="text/css">
    
</head>
<body>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
                <div class="container">
                    <a class="navbar-brand logo-font logo-font-dashboard navbar-brand-dashboard" href="#" id="brand">
                            <img class="img-fluid" src="{{ asset('images/sisdoc.png') }}" alt="" width="95px">
                    </a>
                    <!-- links toggle -->
                    <button class="navbar-toggler order-first" type="button" data-toggle="collapse" data-target="#links" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                        
                    </button>
                    <!-- account toggle -->
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#account" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fa fa-user-circle" aria-hidden="true"></i>
                    </button>
                            
                    {{-- botones lado izquierdo --}}
                    <div class="collapse navbar-collapse" id="account">
                        {{-- Menu princial --}}
                        <ul class="navbar-nav mr-auto">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('recibo.form') }}">Recibo</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('arc.form') }}">Arc</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('solicitudform') }}">Constancia</a>
                  </li>
                  @if (auth()->user()->rol_id == 1)
                  <li class="nav-item">
                        <a class="nav-link" href="{{ route('missolicitudes') }}">Estatus Solicitud</a>
                      </li>
                  @endif
                  @if (auth()->user()->rol_id == 2)
                  <li class="nav-item">
                        <a class="nav-link" href="{{ route('listarsolicitudes') }}">Listar Solicitudes</a>
                      </li>
                  @endif
                  @if (auth()->user()->rol_id == 3)
                  <li class="nav-item">
                        <a class="nav-link" href="{{ route('listarsolicitudes') }}">Solicitudes</a>
                      </li>
                      <li class="nav-item">
                            <a class="nav-link" href="{{ route('listar-usuarios') }}">Usuarios</a>
                          </li>
                         <li class="nav-item">
                                        <a class="nav-link" href="{{ route('arc.form') }}">Periodos de Nomina</a>
                                      </li>
                  @endif
            
                  
                </ul>
                {{-- fin menu principal --}}
                        <ul class="navbar-nav ml-auto navbar-nav-dashboard">
                            {{-- <li class="nav-item"><a class="nav-link-dashboard" href="#">Shopping Cart
                            <i class="fa fa-shopping-cart fa-1x" aria-hidden="true"></i>
                            </a></li> --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link-dashboard dropdown-toggle nav-link-dashboard nav-link-estilo" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user-circle  fa-user-circle-estilo" aria-hidden="true"></i>
                                {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                    <form class="" method="POST" action="{{ route('auth.logout') }}">
                                    @csrf {{-- Para mostrar los errores si enviamos en formulario vasio --}}
                                    <button class="dropdown-item" type="submit">Cerra Sesion</button>
                                    </form>
                                    
                                 </div>
                            </li>
                        </ul>
                    </div>
             </div>
            </nav>


<div class="container">

    <div class="row justify-content-center mt-5">
        <div class="header-texto text-center mb-5">
            <h5>Por favor completa la siguiente informacion para generar la planilla ARC</h5>
            <hr>
    </div>
            
        </header><!-- /header -->
        <div class="col-md-8">
            <form method="POST" action="{{ route('planillaARC') }}"> 
                
                @csrf{{-- errores del formularios --}}
              
                <div class="form-group"> 
                    <label for="codemp">Empresa</label>
                    <select name="codemp" class="form-control estilosinputregistro mb-2 bordes">
                        <option value="">-- Selecciona la empresa --</option>
                            
                                @foreach($codemp as $codemp)
                                <option value="{{ $codemp->cod_emp_sigesp }}">{{ $codemp->descripcion_sigesp }}</option>
                                @endforeach
                    </select>
                </div>
                <div class="form-group"> 
                    <label for="date">Año en Curso</label>
                    <select name="date" class="form-control estilosinputregistro mb-2 bordes">
                        <option value="">-- Selecciona el año en curso --</option>
                            
                                
                               {{-- <option value="{{ $date }}">{{ $date }}</option> --}}
				<option value="2018">2018</option>
                                <option value="2019">2019</option>
                                <option value="2020">2020</option>
                                <option value="2021">2021</option>
                                <option value="2022">2022</option>
                                <option value="2023">2023</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                                <option value="2028">2028</option>
                                <option value="2029">2029</option>
                                <option value="2030">2030</option>
                            
                            
                            {{-- @foreach($codperi as $codperi)
                                <option value="{{ $codperi->codperi }}">{{ $codperi->codperi }}</option>
                            @endforeach --}}
                    </select>
                </div>
                <button class="btn btn-info btn-block my-2 mt-5" type="submit">Generar Planilla ARC</button>

                    
                    </form>
                
            </form>
        </div>
    </div>
    

</div>


<script src="{{ asset('js/jquery-3.4.1.js') }}"></script>
<script src="{{ asset('js/jquery.slim.js') }}"></script>
<script src="{{ asset('js/bootstrap.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
