<div wire:poll.2s class="kitchen-tv">

    {{--
    Tema compartido de las pantallas de cocina (Board y Despacho).
    Un solo lugar para los estilos: si cambia la marca, se actualiza
    en ambas pantallas a la vez y no se pueden desincronizar.

    Usa las variables ya definidas en theme.css (:root), así que si
    cambian los tonos de marca, este tema se actualiza automáticamente.

    Nota de diseño: fondo oscuro para lectura a distancia (TV) y buen
    contraste en tablet bajo luces de cocina, pero header y tarjetas
    usan los mismos degradados de marca que navbar/card-header.

    Estados de tiempo reutilizan la semántica de tus badges existentes:
    verde = "ready", naranja = "pending", y "vencido" usa el rojo MÁS
    OSCURO de marca (--brand-primary-dark) para no confundirse con el
    rojo de marca que ya está en todos lados (header, botones, etc).
    --}}
    <style>
        .kitchen-tv {
            background: linear-gradient(160deg, var(--brand-dark), #1a0b0b 60%, var(--brand-navy));
            min-height: 100vh;
            padding: 24px 32px;
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
        }
    
        .kitchen-tv .board-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(90deg, var(--brand-dark), var(--brand-primary));
            border-radius: 14px;
            padding: 18px 28px;
            margin-bottom: 22px;
            box-shadow: 0 6px 18px rgba(0,0,0,.35);
            flex-wrap: wrap;
            gap: 12px;
        }
    
        .kitchen-tv .board-header .station-name {
            font-size: 2.6rem;
            font-weight: 800;
            letter-spacing: 1px;
            color: #fff;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 14px;
        }
    
        .kitchen-tv .board-header .station-name i {
            color: #ffb199;
        }
    
        .kitchen-tv .board-header .pending-count {
            background: var(--brand-light);
            color: var(--brand-primary-dark);
            font-size: 1.6rem;
            font-weight: 800;
            padding: 10px 22px;
            border-radius: 40px;
            white-space: nowrap;
        }
    
        .kitchen-tv .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 20px;
        }
    
        .kitchen-tv .ticket {
            background: #211012;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0,0,0,.4);
            border: 2px solid transparent;
            transition: transform .15s ease;
            display: flex;
            flex-direction: column;
        }
    
        .kitchen-tv .ticket-head {
            background: linear-gradient(90deg, var(--brand-secondary), var(--brand-primary));
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
    
        .kitchen-tv .table-name {
            font-size: 1.7rem;
            font-weight: 800;
            color: #fff;
            margin: 0;
        }
    
        .kitchen-tv .floor-name {
            font-size: .9rem;
            color: #ffe0d6;
        }
    
        .kitchen-tv .time-badge {
            text-align: center;
            border-radius: 12px;
            padding: 8px 14px;
            min-width: 92px;
        }
    
        .kitchen-tv .time-badge.ok      { background: #43a047; color: #fff; }
        .kitchen-tv .time-badge.warn    { background: #ff9800; color: #3a2400; }
        .kitchen-tv .time-badge.overdue { background: var(--brand-primary-dark); color: #fff; }
    
        .kitchen-tv .time-badge .mins {
            font-size: 1.35rem;
            font-weight: 900;
            line-height: 1.1;
            white-space: nowrap;
        }
    
        .kitchen-tv .time-badge .lbl {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            opacity: .9;
        }
    
        /* Único uso "extra" de rojo: pedido vencido resalta la tarjeta completa */
        .kitchen-tv .ticket.overdue {
            border-color: var(--brand-primary-dark);
            animation: pulse-red 1.4s infinite;
        }
    
        @keyframes pulse-red {
            0%   { box-shadow: 0 0 0 3px rgba(142,0,0,.55); }
            50%  { box-shadow: 0 0 0 10px rgba(142,0,0,.15); }
            100% { box-shadow: 0 0 0 3px rgba(142,0,0,.55); }
        }
    
        .kitchen-tv .ticket-body {
            padding: 14px 18px 18px;
            flex: 1;
        }
    
        .kitchen-tv .meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
        }
    
        .kitchen-tv .chip {
            font-size: .85rem;
            padding: 5px 12px;
            border-radius: 20px;
            color: #fff;
        }
    
        /* Tono tierra para info de piso/mesero, igual que en el mapa de mesas */
        .kitchen-tv .chip.floor-chip {
            background: var(--brand-success);
        }
    
        .kitchen-tv .chip.time-chip {
            background: #3a2226;
            color: #f1c1b5;
        }
    
        .kitchen-tv .product-line {
            font-size: 2rem;
            font-weight: 900;
            color: #fff;
            line-height: 1.15;
            margin: 8px 0 12px;
        }
    
        .kitchen-tv .product-line .qty {
            color: #ff8a65;
        }
    
        .kitchen-tv .price-row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 6px;
            font-size: .85rem;
            color: #cfa8a0;
            border-top: 1px solid #3a2226;
            padding-top: 10px;
        }
    
        .kitchen-tv .order-total {
            font-size: .95rem;
            color: #fff;
            font-weight: 700;
        }
    
        .kitchen-tv .comment-box {
            margin-top: 12px;
            background: #3a2400;
            border: 1px solid #ff980055;
            color: #ffcc80;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: .95rem;
        }
    
        .kitchen-tv .empty-state {
            text-align: center;
            padding: 90px 20px;
            color: #43a047;
        }
    
        .kitchen-tv .empty-state h2 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-top: 14px;
            color: #fff;
        }
    
        /* Solo usado en Despacho: botón de acción "listo" al pie de la tarjeta */
        .kitchen-tv .ticket-footer {
            padding: 14px 18px 18px;
        }
    
        .kitchen-tv .btn-ready {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 16px;
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: .5px;
            color: #fff;
            background: linear-gradient(90deg, #2e7d32, #43a047);
            box-shadow: 0 4px 12px rgba(0,0,0,.35);
            cursor: pointer;
            transition: transform .1s ease, filter .15s ease;
        }
    
        .kitchen-tv .btn-ready:hover {
            filter: brightness(1.08);
        }
    
        .kitchen-tv .btn-ready:active {
            transform: scale(.98);
        }
    
        /* Puntos indicadores cuando el Board rota entre páginas de pedidos */
        .kitchen-tv .page-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 16px;
        }
    
        .kitchen-tv .page-dots .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #4a3436;
            transition: background .3s ease, transform .3s ease;
        }
    
        .kitchen-tv .page-dots .dot.active {
            background: var(--brand-secondary);
            transform: scale(1.3);
        }
    
        /* Selector rápido de estación (cambiar sin ir al menú lateral) */
        .kitchen-tv .station-switcher {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
    
        .kitchen-tv .station-switcher .station-pill {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: .95rem;
            font-weight: 700;
            color: #f1c1b5;
            background: #2a1416;
            border: 1px solid #4a2c2f;
            text-decoration: none;
            transition: background .15s ease, color .15s ease;
        }
    
        .kitchen-tv .station-switcher .station-pill:hover {
            background: #3a2226;
            color: #fff;
            text-decoration: none;
        }
    
        .kitchen-tv .station-switcher .station-pill.active {
            background: linear-gradient(90deg, var(--brand-secondary), var(--brand-primary));
            color: #fff;
            border-color: transparent;
        }
    </style>

    <div class="board-header">
        <h1 class="station-name">
            <i class="fas fa-check-circle"></i>
            Despacho — {{ strtoupper($stationLabel) }}
        </h1>
 
        <div class="pending-count">
            {{ $tickets->count() }} {{ $tickets->count() === 1 ? 'PEDIDO PENDIENTE' : 'PEDIDOS PENDIENTES' }}
        </div>
    </div>
 
    {{-- Cambiar de estación sin pasar por el menú lateral --}}
    <div class="station-switcher">
        @foreach($switcher as $opt)
            <a href="{{ $opt['url'] }}" class="station-pill {{ $opt['active'] ? 'active' : '' }}">
                {{ $opt['label'] }}
            </a>
        @endforeach
    </div>
 
    @if($tickets->isEmpty())
 
        <div class="empty-state">
            <i class="fas fa-check-circle fa-4x"></i>
            <h2>No hay pedidos pendientes en esta estación</h2>
        </div>
 
    @else
 
        <div class="grid">
 
            @foreach($tickets as $orderId => $items)
 
                @php
                    $ticket = $items->first()->kitchenOrder;
 
                    $minutes = $ticket->sent_at
                        ? (int) floor($ticket->sent_at->diffInMinutes(now()))
                        : 0;
 
                    $state = $minutes <= 10 ? 'ok' : ($minutes <= 20 ? 'warn' : 'overdue');
 
                    $timeLabel = $minutes < 60
                        ? $minutes . ' min'
                        : intdiv($minutes, 60) . 'h ' . str_pad($minutes % 60, 2, '0', STR_PAD_LEFT) . 'min';
                @endphp
 
                <div class="ticket {{ $state }}" wire:key="order-{{ $orderId }}">
 
                    <div class="ticket-head">
                        <div>
                            <p class="table-name">Mesa {{ $ticket->diningTable?->name }}</p>
                            <span class="floor-name">Piso: {{ $ticket->floor?->name }}</span>
                        </div>
 
                        <div class="time-badge {{ $state }}">
                            <div class="mins">{{ $timeLabel }}</div>
                            <div class="lbl">
                                @if($state === 'ok') A tiempo
                                @elseif($state === 'warn') Atención
                                @else Vencido
                                @endif
                            </div>
                        </div>
                    </div>
 
                    <div class="ticket-body">
 
                        <div class="meta-row">
                            <span class="chip floor-chip"><i class="fas fa-user"></i> {{ $ticket->order?->user?->name ?? 'N/A' }}</span>
                            <span class="chip time-chip"><i class="fas fa-clock"></i> {{ $ticket->sent_at?->format('h:i A') }}</span>
                        </div>
 
                        {{-- Solo los productos que corresponden a estas estaciones --}}
                        @foreach($items as $item)
 
                            <div class="product-line" style="font-size: 1.4rem; margin-bottom: 4px;">
                                <span class="qty">{{ $item->quantity }}x</span>
                                {{ strtoupper($item->product_name) }}
                                @if($isCombined)
                                    <span class="chip floor-chip" style="font-size:.7rem; vertical-align: middle;">
                                        {{ $item->station?->name }}
                                    </span>
                                @endif
                            </div>
 
                            @if($item->comment)
                                <div class="comment-box" style="margin-bottom: 10px;">
                                    <strong><i class="fas fa-exclamation-triangle"></i> Observación:</strong>
                                    {{ $item->comment }}
                                </div>
                            @endif
 
                        @endforeach
 
                        <div class="price-row">
                            <span class="order-total">Total pedido ${{ number_format($ticket->order?->total ?? 0,0,',','.') }}</span>
                        </div>
 
                    </div>
 
                    <div class="ticket-footer">
                        <button
                            wire:click="ready({{ $orderId }})"
                            class="btn-ready">
                            <i class="fas fa-check-circle mr-2"></i>
                            LISTO EN {{ strtoupper($stationLabel) }}
                        </button>
                    </div>
 
                </div>
 
            @endforeach
 
        </div>
 
    @endif
 
</div>