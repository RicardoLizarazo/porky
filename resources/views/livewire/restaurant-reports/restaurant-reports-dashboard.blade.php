<div class="container-fluid">


    {{-- FILTROS --}}
    <div class="card card-primary">

        <div class="card-header">

            <h3 class="card-title">

                <i class="fas fa-filter mr-2"></i>

                Filtros de Consulta

            </h3>

        </div>


        <div class="card-body">
            <div class="row">
                {{-- SEDE --}}
                <div class="col-md-3">
                    <label>Sede</label>
                    <select
                        class="form-control"
                        wire:model.live="location_id"
                    >
                        <option value="">Todas las sedes</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- PISO --}}
                <div class="col-md-3">
                    <label>Piso</label>
                    <select
                        class="form-control"
                        wire:model.live="floor_id"
                    >
                        <option value="">Todos los pisos</option>
                        @foreach($floors as $floor)
                            <option value="{{ $floor->id }}">
                                {{ $floor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- DESDE --}}
                <div class="col-md-3">
                    <label>Desde</label>
                    <input
                        type="date"
                        class="form-control"
                        wire:model.live="date_from"
                    >
                </div>

                {{-- HASTA --}}
                <div class="col-md-3">
                    <label>Hasta</label>
                    <input
                        type="date"
                        class="form-control"
                        wire:model.live="date_to"
                    >
                </div>
            </div>
        </div>
    </div>

    {{-- INDICADORES --}}
    <div class="row">


        {{-- VENTAS --}}
        <div class="col-md-4">


            <div class="info-box">


                <span class="info-box-icon bg-success">

                    <i class="fas fa-dollar-sign"></i>

                </span>


                <div class="info-box-content">


                    <span class="info-box-text">

                        Ventas

                    </span>


                    <span class="info-box-number">

                        $
                        {{ number_format(
                            $this->totalSales,
                            0,
                            ',',
                            '.'
                        ) }}


                    </span>


                </div>


            </div>


        </div>




        {{-- PEDIDOS --}}
        <div class="col-md-4">


            <div class="info-box">


                <span class="info-box-icon bg-info">

                    <i class="fas fa-receipt"></i>

                </span>


                <div class="info-box-content">


                    <span class="info-box-text">

                        Pedidos

                    </span>


                    <span class="info-box-number">

                        {{ $this->ordersCount }}

                    </span>


                </div>


            </div>


        </div>





        {{-- TICKET PROMEDIO --}}
        <div class="col-md-4">


            <div class="info-box">


                <span class="info-box-icon bg-warning">

                    <i class="fas fa-chart-line"></i>

                </span>


                <div class="info-box-content">


                    <span class="info-box-text">

                        Ticket Promedio

                    </span>


                    <span class="info-box-number">


                        $
                        {{ number_format(
                            $this->averageTicket,
                            0,
                            ',',
                            '.'
                        ) }}


                    </span>


                </div>


            </div>


        </div>



    </div>

        {{-- VENTAS POR PISO --}}

    <div class="row">


        <div class="col-md-6">


            <div class="card card-success">


                <div class="card-header">


                    <h3 class="card-title">

                        <i class="fas fa-building mr-2"></i>

                        Ventas por Piso

                    </h3>


                </div>


                <div class="card-body">


                    <table class="table table-sm table-striped">


                        <thead>

                        <tr>

                            <th>
                                Piso
                            </th>


                            <th class="text-right">
                                Total
                            </th>

                        </tr>

                        </thead>



                        <tbody>


                        {{-- VENTAS POR PISO --}}
                        @forelse($this->salesByFloor as $row)
                            <tr>
                                <td>{{ $row->floor_name ?? 'Sin piso' }}</td>
                                <td class="text-right">
                                    $ {{ number_format($row->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">Sin información</td>
                            </tr>
                        @endforelse



                        </tbody>


                    </table>


                </div>


            </div>


        </div>





        {{-- VENTAS POR MESERO --}}

        <div class="col-md-6">


            <div class="card card-info">


                <div class="card-header">


                    <h3 class="card-title">

                        <i class="fas fa-user mr-2"></i>

                        Ventas por Mesero

                    </h3>


                </div>


                <div class="card-body">


                    <table class="table table-sm table-striped">


                        <thead>


                        <tr>


                            <th>
                                Mesero
                            </th>


                            <th class="text-right">
                                Total
                            </th>


                        </tr>


                        </thead>


                        <tbody>


                        {{-- VENTAS POR MESERO --}}
                        @forelse($this->salesByWaiter as $row)
                            <tr>
                                <td>{{ $row->user_name ?? 'Sin usuario' }}</td>
                                <td class="text-right">
                                    $ {{ number_format($row->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">Sin información</td>
                            </tr>
                        @endforelse


                        </tbody>


                    </table>


                </div>


            </div>


        </div>



    </div>


</div>