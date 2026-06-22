<div
    class="container-fluid"
    x-data="{ showLoading: false }"
    @toggle-loading.window="showLoading = $event.detail"
>
    {{-- Spinner --}}
    <template x-if="showLoading">
        <div
            class="position-fixed w-100 h-100 d-flex justify-content-center align-items-center"
            style="top: 0; left: 0; background: rgba(255,255,255,0.6); z-index: 9999;"
        >
            <div class="text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-dark font-weight-bold">Cargando información...</p>
            </div>
        </div>
    </template>

    <div class="row">
        <div class="col-12">
        	@include('customers.create')
            @include('customers.edit')
            <div class="card">
                <div class="card-header">
                    <h3>Clientes
                    @can('users.create')
                        <button @click="window.dispatchEvent(new CustomEvent('toggle-loading', { detail: true }));
                            $dispatch('create')"
                            class="btn btn-cef btn-cef-create"
                            title="Crear"
                            data-toggle="tooltip"
                        >
                            <i class="fas fa-plus"></i>
                        </button>
                    @endcan
                    </h3>
                </div>

                <div class="card-body">
                    <livewire:customers-table theme="bootstrap-4" />
                </div>
            </div>        
        </div>
    </div>
</div>
@push('script')

<script>

document.addEventListener('livewire:init', () => {

    /* =========================================================
    MODAL CREATE
    ========================================================= */

    Livewire.on('open-create-modal', () => {

        window.dispatchEvent(
            new CustomEvent('toggle-loading', {
                detail: false
            })
        );

        $('#modal-create').modal('show');

        setTimeout(() => {
            initAutocomplete();
        }, 500);

    });


    /* =========================================================
    MODAL EDIT
    ========================================================= */

    Livewire.on('open-edit-modal', () => {

        window.dispatchEvent(
            new CustomEvent('toggle-loading', {
                detail: false
            })
        );

        $('#modal-edit').modal('show');

        setTimeout(() => {
            initAutocomplete();
        }, 500);

    });


    /* =========================================================
    STORE
    ========================================================= */

    Livewire.on('store', () => {

        $('#modal-create').modal('hide');

        Swal.fire({
            icon: 'success',
            title: 'Registro creado correctamente',
            showConfirmButton: false,
            timer: 1500
        });

    });


    /* =========================================================
    UPDATE
    ========================================================= */

    Livewire.on('update', () => {

        $('#modal-edit').modal('hide');

        Swal.fire({
            icon: 'success',
            title: 'Registro actualizado correctamente',
            showConfirmButton: false,
            timer: 1500
        });

    });


    /* =========================================================
    DELETE
    ========================================================= */

    Livewire.on('delete', id => {

        Swal.fire({
            title: '¿Está seguro de eliminar el registro?',
            text: 'No podrás revertir esto',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí',
            cancelButtonText: 'No'
        }).then((result) => {

            if (result.isConfirmed) {

                @this.call('delete', id);

                Swal.fire({
                    title: 'Eliminado',
                    text: 'El registro ha sido eliminado',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                });

            }

        });

    });

});


/* =========================================================
GOOGLE AUTOCOMPLETE
========================================================= */

window.initAutocomplete = function () {

    document.querySelectorAll('.address-input').forEach((input) => {

        // evitar doble inicialización
        if (input.dataset.initialized === 'true') {
            return;
        }

        const index = input.dataset.index;

        const autocomplete = new google.maps.places.Autocomplete(input, {

            componentRestrictions: {
                country: 'CO'
            },

            fields: [
                'formatted_address',
                'geometry',
                'name'
            ],

            types: ['address']

        });


        autocomplete.addListener('place_changed', () => {

            const place = autocomplete.getPlace();

            // validar dirección
            if (!place.geometry || !place.geometry.location) {

                Swal.fire({
                    icon: 'error',
                    title: 'Dirección inválida',
                    text: 'Selecciona una dirección sugerida por Google'
                });

                return;
            }

            // obtener componente livewire
            const componentElement = input.closest('[wire\\:id]');

            if (!componentElement) {
                return;
            }

            const component = Livewire.find(
                componentElement.getAttribute('wire:id')
            );

            if (!component) {
                return;
            }

            // actualizar dirección
            component.set(
                `addresses.${index}.address`,
                place.formatted_address
            );

            // actualizar latitud
            component.set(
                `addresses.${index}.latitude`,
                place.geometry.location.lat()
            );

            // actualizar longitud
            component.set(
                `addresses.${index}.longitude`,
                place.geometry.location.lng()
            );

        });

        // marcar como inicializado
        input.dataset.initialized = 'true';

    });

};


/* =========================================================
REINICIALIZAR AUTOCOMPLETE
========================================================= */

document.addEventListener('livewire:navigated', () => {

    setTimeout(() => {
        initAutocomplete();
    }, 500);

});

document.addEventListener('DOMContentLoaded', () => {

    setTimeout(() => {
        initAutocomplete();
    }, 500);

});

</script>


{{-- =========================================================
GOOGLE MAPS API
========================================================= --}}

<script async defer
src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&loading=async&libraries=places&callback=initAutocomplete">
</script>

@endpush