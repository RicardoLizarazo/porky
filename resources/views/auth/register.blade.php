@extends('adminlte::auth.login')

@section('auth_body')
<div class="container-fluid p-0">
    <div class="row no-gutters min-vh-100">

        {{-- IZQUIERDA --}}
        <div class="col-lg-5 d-flex align-items-center justify-content-center bg-white">

            <div class="w-100 px-4 porky-login-box">

                {{-- LOGO --}}
                <div class="text-center mb-4">

                    <img src="{{ asset('vendor/adminlte/dist/img/logo.png') }}"
                         class="mb-3"
                         style="max-height: 80px;">

                    <h4 class="font-weight-bold mb-1 text-danger">
                        Crear cuenta
                    </h4>

                    <p class="text-muted small">
                        Regístrate para realizar tus pedidos
                    </p>

                </div>

                {{-- ERRORES --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('customer.register.post') }}"
                      id="registerForm">

                    @csrf

                    {{-- NOMBRE --}}
                    <div class="input-group mb-3">
                        <input type="text"
                               name="name"
                               class="form-control"
                               placeholder="Nombre completo"
                               value="{{ old('name') }}"
                               required>
                    </div>

                    {{-- EMAIL --}}
                    <div class="input-group mb-3">
                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="Correo electrónico"
                               value="{{ old('email') }}"
                               required>
                    </div>

                    {{-- TELEFONO --}}
                    <div class="input-group mb-3">
                        <input type="text"
                               name="telephone"
                               class="form-control"
                               placeholder="Teléfono"
                               value="{{ old('telephone') }}"
                               required>
                    </div>

                    {{-- DIRECCION --}}
                    <div class="input-group mb-2">
                        <input type="text"
                               id="address"
                               name="address"
                               class="form-control"
                               placeholder="Dirección"
                               autocomplete="off"
                               value="{{ old('address') }}"
                               required>

                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                        </div>
                    </div>

                    {{-- MAPA --}}
                    <div class="mb-3">
                        <div id="map"></div>

                        <small id="coverageMessage"
                               class="font-weight-bold"></small>
                    </div>

                    {{-- HIDDEN --}}
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="distance" id="distance">

                    {{-- PASSWORD --}}
                    <div class="input-group mb-3">
                        <input type="password"
                               name="password"
                               class="form-control"
                               placeholder="Contraseña"
                               required>
                    </div>

                    {{-- CONFIRMACION --}}
                    <div class="input-group mb-3">
                        <input type="password"
                               name="password_confirmation"
                               class="form-control"
                               placeholder="Confirmar contraseña"
                               required>
                    </div>

                    {{-- TERMINOS --}}
                    <div class="form-group mb-4">

                        <div class="custom-control custom-checkbox">

                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="authorization"
                                   name="authorization"
                                   required>

                            <label class="custom-control-label small"
                                   for="authorization">

                                Acepto términos y condiciones para el manejo de datos personales

                            </label>

                        </div>

                    </div>

                    {{-- BOTON --}}
                    <button type="submit"
                            class="btn btn-danger btn-block"
                            id="submitBtn"
                            disabled>

                        Crear cuenta

                    </button>

                </form>

                {{-- LOGIN --}}
                <div class="text-center mt-4">

                    <a href="{{ route('customer.login') }}"
                       class="porky-link small">

                        Ya tengo una cuenta

                    </a>

                </div>

            </div>

        </div>

        {{-- DERECHA --}}
        <div class="col-lg-7 d-none d-lg-block porky-image-panel">

            <div class="h-100 position-relative">

                <div class="porky-bg"
                     style="background-image:url('{{ asset('vendor/adminlte/dist/img/banner2.jpg') }}')">
                </div>

                <div class="porky-overlay"></div>

                <div class="porky-content d-flex align-items-center justify-content-center">

                    <div class="text-center text-white px-5">

                        <i class="fas fa-user-plus fa-5x mb-4"></i>

                        <h1 class="font-weight-bold mb-3">
                            Únete a Porky
                        </h1>

                        <p class="lead">
                            Regístrate y pide desde casa
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>
@endsection

@push('css')
<style>

.min-vh-100{
    min-height:100vh;
}

.porky-login-box{
    max-width:460px;
}

#map{
    width:100%;
    height:220px;
    border-radius:12px;
    overflow:hidden;
    border:1px solid #eee;
}

.porky-image-panel{
    overflow:hidden;
}

.porky-bg{
    position:absolute;
    inset:0;
    background-size:cover;
    background-position:center;
    filter:contrast(1.1) brightness(.8);
    transition:transform .8s ease;
}

.porky-overlay{
    position:absolute;
    inset:0;
    background:linear-gradient(
        to right,
        rgba(20,10,10,.95),
        rgba(198,40,40,.75)
    );
}

.porky-content{
    position:relative;
    z-index:2;
}

.porky-image-panel:hover .porky-bg{
    transform:scale(1.05);
}

.login-box{
    width:100%!important;
    max-width:none!important;
    box-shadow:none!important;
}

.login-card-body{
    padding:0!important;
}

.card{
    border:none!important;
}

@media (max-width:992px){

    .porky-image-panel{
        display:none!important;
    }

}
</style>
@endpush

@push('js')

<script>

let map;
let marker;
let autocomplete;

/*
|--------------------------------------------------------------------------
| SEDE PORKY
|--------------------------------------------------------------------------
*/

const porkyLocation = {
    lat: 4.741748990293169,
    lng: -74.09740027649579
};

/*
|--------------------------------------------------------------------------
| MAPA
|--------------------------------------------------------------------------
*/

function initMap()
{
    map = new google.maps.Map(document.getElementById("map"), {
        center: porkyLocation,
        zoom: 13,
    });

    /*
    |--------------------------------------------------------------------------
    | RADIO COBERTURA
    |--------------------------------------------------------------------------
    */

    new google.maps.Circle({
        strokeColor: "#dc3545",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#dc3545",
        fillOpacity: 0.15,
        map,
        center: porkyLocation,
        radius: 8000
    });

    /*
    |--------------------------------------------------------------------------
    | AUTOCOMPLETE
    |--------------------------------------------------------------------------
    */

    autocomplete = new google.maps.places.Autocomplete(
        document.getElementById("address"),
        {
            componentRestrictions: {
                country: "co"
            },
            fields: [
                "formatted_address",
                "geometry",
                "name"
            ]
        }
    );

    autocomplete.addListener("place_changed", () => {

        const place = autocomplete.getPlace();

        if (!place.geometry) {
            return;
        }

        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();

        /*
        |--------------------------------------------------------------------------
        | DISTANCIA
        |--------------------------------------------------------------------------
        */

        const distance =
            google.maps.geometry.spherical.computeDistanceBetween(
                new google.maps.LatLng(porkyLocation.lat, porkyLocation.lng),
                new google.maps.LatLng(lat, lng)
            );

        const km = (distance / 1000).toFixed(2);

        /*
        |--------------------------------------------------------------------------
        | SET VALUES
        |--------------------------------------------------------------------------
        */

        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        document.getElementById('distance').value = km;

        /*
        |--------------------------------------------------------------------------
        | MARKER
        |--------------------------------------------------------------------------
        */

        if(marker){
            marker.setMap(null);
        }

        marker = new google.maps.Marker({
            map,
            position: { lat, lng }
        });

        map.setCenter({ lat, lng });
        map.setZoom(16);

        /*
        |--------------------------------------------------------------------------
        | VALIDACION COBERTURA
        |--------------------------------------------------------------------------
        */

        const coverageMessage =
            document.getElementById('coverageMessage');

        const submitBtn =
            document.getElementById('submitBtn');

        if (km <= 8) {

            coverageMessage.innerHTML =
                `Cobertura disponible (${km} KM)`;

            coverageMessage.className =
                'text-success font-weight-bold';

            submitBtn.disabled = false;

        } else {

            coverageMessage.innerHTML =
                `La dirección supera la cobertura de 8 KM (${km} KM)`;

            coverageMessage.className =
                'text-danger font-weight-bold';

            submitBtn.disabled = true;
        }

    });
}

window.initMap = initMap;

</script>

<script async defer
src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&loading=async&libraries=places,geometry&callback=initMap">
</script>

@endpush