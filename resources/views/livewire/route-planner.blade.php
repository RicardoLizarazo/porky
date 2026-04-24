<div class="row">

    {{-- LISTA PEDIDOS --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <strong>Pedidos</strong>
            </div>

            <div class="card-body p-0">
                <ul class="list-group">

                    @foreach($orders as $order)
                    <li class="list-group-item d-flex justify-content-between align-items-center">

                        <div>
                            <strong>#{{ $order->id }}</strong>
                            <br>
                            <small>
                                {{ $order->customer->defaultAddress->address ?? 'Sin dirección' }}
                            </small>
                        </div>

                        <input type="checkbox"
                               wire:click="toggleOrder({{ $order->id }})"
                               @checked(in_array($order->id, $selected))>

                    </li>
                    @endforeach

                </ul>
            </div>

            <div class="card-footer">
                <button class="btn btn-primary btn-block"
                        wire:click="generateRoute">
                    Generar ruta óptima
                </button>
            </div>
        </div>
    </div>

    {{-- MAPA --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <strong>Mapa</strong>
            </div>

            <div class="card-body">
                <div id="map" style="height:500px;"></div>
                <div id="route-info" class="mt-2 text-muted"></div>
            </div>
        </div>
    </div>

</div>

@push('script')

<script src="https://maps.googleapis.com/maps/api/js?key=TU_API_KEY"></script>

<script>
let map;
let directionsService;
let directionsRenderer;

const ORIGIN = { lat: 3.4516, lng: -76.5320 }; // Cali

document.addEventListener('livewire:init', () => {

    initMap();

    Livewire.on('update-route', ({ orders }) => {
        drawRoute(orders);
    });

});

function initMap() {

    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 13,
        center: ORIGIN,
    });

    directionsService = new google.maps.DirectionsService();

    directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer.setMap(map);
}

function drawRoute(orders) {

    if (!orders.length) return;

    const waypoints = orders.map(o => ({
        location: { lat: o.lat, lng: o.lng },
        stopover: true
    }));

    const request = {
        origin: ORIGIN,
        destination: ORIGIN,
        waypoints: waypoints,
        optimizeWaypoints: true,
        travelMode: google.maps.TravelMode.DRIVING,
    };

    directionsService.route(request)
        .then(result => {

            directionsRenderer.setDirections(result);

            let distance = 0;
            let duration = 0;

            result.routes[0].legs.forEach(leg => {
                distance += leg.distance.value;
                duration += leg.duration.value;
            });

            document.getElementById('route-info').innerHTML =
                `Distancia: ${(distance/1000).toFixed(2)} km |
                 Tiempo: ${(duration/60).toFixed(0)} min`;

        })
        .catch(err => {
            console.error(err);
        });
}
</script>

@endpush
