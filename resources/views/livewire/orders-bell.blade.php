<div wire:poll.10s="checkOrders">

    <li class="nav-item dropdown">

        <!-- 🔔 ICONO -->
        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="far fa-bell"></i>

            @if($count > 0)
                <span class="badge badge-danger navbar-badge">
                    {{ $count }}
                </span>
            @endif
        </a>

        <!-- 📋 DROPDOWN -->
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

            <span class="dropdown-header">
                {{ $count }} pedidos nuevos
            </span>

            <div class="dropdown-divider"></div>

            @forelse($orders as $order)
                <a href="{{ url('orders') }}" 
                   class="dropdown-item"
                   onclick="setOrderFilter({{ $order->id }})">

                    <i class="fas fa-receipt mr-2 text-danger"></i>

                    Pedido #{{ $order->id }}

                    <span class="float-right text-muted text-sm">
                        {{ $order->created_at->diffForHumans() }}
                    </span>
                </a>

                <div class="dropdown-divider"></div>
            @empty
                <span class="dropdown-item text-muted">
                    Sin pedidos nuevos
                </span>
            @endforelse

            <a href="{{ url('orders') }}" class="dropdown-item dropdown-footer">
                Ver todos
            </a>
        </div>

    </li>
</div>
