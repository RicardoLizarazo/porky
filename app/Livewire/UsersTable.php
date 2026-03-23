<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

/**
 * Componente Livewire para la tabla de usuarios.
 *
 * Muestra la lista de usuarios del sistema con búsqueda avanzada
 * y filtros por rol. Basado en Rappasoft Livewire Tables.
 */
class UsersTable extends DataTableComponent
{
    /** @var string Modelo base de la tabla */
    protected $model = User::class;

    /** @var array Eventos escuchados por Livewire */
    protected $listeners = ['refresh-table' => '$refresh'];

    /**
     * Configuración general del componente.
     */
    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('name', 'asc')
            ->setPerPage(25)
            ->setSearchEnabled()
            ->setTableWrapperAttributes([
                'class' => 'table-responsive shadow-sm'
            ])
            ->setTdAttributes(fn () => [
                'class' => 'align-middle'
            ])
            ->setThAttributes(fn () => [
                'class' => 'text-uppercase small'
            ]);
    }

    /**
     * Define las columnas visibles en la tabla.
     */
    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable()
                ->searchable(),
         
            Column::make("Nombre", "name")
                ->sortable()
                ->searchable()
                ->format(fn($value, $row) => '
                <div class="d-flex align-items-center">
                    <div class="avatar mr-3">
                        '.($row->profile_photo_url ? '
                            <img src="'.$row->profile_photo_url.'" class="rounded-circle" width="40" height="40">
                        ' : '
                            <span class="avatar-initial rounded-circle d-flex align-items-center justify-content-center"
                                style="width:40px;height:40px;">
                                '.mb_strtoupper(substr($row->name, 0, 1)).'
                            </span>
                        ').'
                    </div>
                    <div>
                        <div class="font-weight-bold">'.$row->name.'</div>
                        <div class="text-muted small">'.$row->email.'</div>
                    </div>
                </div>
                ')->html(),

            Column::make("Email", "email")
                ->sortable()
                ->searchable()
                ->hideIf(true),

            Column::make("Roles", "id")
                ->format(function ($value, $row, Column $column) {
                    $roles = $row->getRoleNames();

                    if ($roles->isEmpty()) {
                        return '<span class="badge bg-secondary">Sin roles</span>';
                    }

                    return $roles->map(fn($role) =>
                        '<span class="badge bg-primary mr-1">'.e($role).'</span>'
                    )->implode('');
                })
                ->html(),

            Column::make("Fecha creación", "created_at")
                ->sortable()
                ->format(fn($value, $row, Column $column) =>
                    $row->created_at->format('d/m/Y H:i')
                ),

            Column::make("Acciones", "id")
                ->format(fn($value, $row) =>
                    '<div class="table-actions">'.
                    view('security.users.actions', ['user' => $row]).
                    '</div>'
                )
                ->html(),
        ];
    }

    /**
     * Filtros disponibles (actualmente solo por rol).
     */
    public function filters(): array
    {
        $roles = Role::orderBy('name')->pluck('name', 'name')->toArray();

        return [
            SelectFilter::make('Rol')
                ->options(['' => 'Todos los roles'] + $roles)
                ->filter(function (Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->whereHas('roles', fn($q) => $q->where('name', $value));
                    }
                }),
        ];
    }

    /**
     * Query base del componente, con relaciones cargadas.
     */
    public function query(): Builder
    {
        return User::query()->with('roles');
    }

    /**
     * Aplica búsqueda avanzada sobre múltiples campos del modelo User.
     *
     * Soporta múltiples palabras separadas por espacio y realiza
     * coincidencia parcial (LIKE) sobre los campos más relevantes.
     */
    public function applySearch(): Builder
    {
        $query = User::query()->with('roles');
        $search = null;

        // Intentamos obtener el término de búsqueda
        if (method_exists($this, 'getFilter')) {
            $search = $this->getFilter('search');
        }

        if (empty($search) && method_exists($this, 'getSearch')) {
            $search = $this->getSearch();
        }

        $search = is_string($search) ? trim($search) : null;

        if (empty($search)) {
            return $query;
        }

        $table = (new User())->getTable();

        // Normalizamos el término de búsqueda
        $normalized = mb_strtolower($search, 'UTF-8');
        $terms = preg_split('/\s+/', $normalized, -1, PREG_SPLIT_NO_EMPTY);

        // Búsqueda avanzada: cada palabra debe aparecer en algún campo
        $query->where(function (Builder $q) use ($terms, $table) {
            foreach ($terms as $term) {
                $pattern = '%' . $term . '%';
                $q->where(function (Builder $sub) use ($pattern, $table) {
                    $sub->whereRaw("LOWER({$table}.name) LIKE ?", [$pattern])
                        ->orWhereRaw("LOWER({$table}.email) LIKE ?", [$pattern]);
                });
            }
        });

        return $query;
    }
}
