<?php

namespace App\Livewire\Restaurante\Concerns;

use App\Models\KitchenStation;

trait ResolvesKitchenStations
{
    /**
     * Convierte "1" o "1,2" en [1] o [1, 2].
     */
    protected function resolveStationIds(string $param): array
    {
        return collect(explode(',', $param))
            ->map(fn ($id) => (int) trim($id))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Nombre a mostrar: si el set de IDs coincide con un combo
     * configurado usa su label ("Parrilla y Sopas"); si no, junta
     * los nombres reales de las estaciones.
     */
    protected function resolveStationLabel(array $stationIds, $stationModels): string
    {
        foreach (config('kitchen.combos', []) as $combo) {
            if ($this->sameIds($combo['ids'], $stationIds)) {
                return $combo['label'];
            }
        }

        return $stationModels->pluck('name')->implode(' Y ');
    }

    /**
     * Opciones para el selector rápido de arriba: cada estación real
     * + cada combo configurado, marcando cuál está activa.
     */
    protected function buildStationSwitcher(string $routeName, array $currentStationIds): array
    {
        $options = [];

        foreach (KitchenStation::active()->orderBy('name')->get() as $s) {
            $options[] = [
                'label'  => $s->name,
                'url'    => route($routeName, (string) $s->id),
                'active' => $this->sameIds([$s->id], $currentStationIds),
            ];
        }

        foreach (config('kitchen.combos', []) as $combo) {
            $options[] = [
                'label'  => $combo['label'],
                'url'    => route($routeName, implode(',', $combo['ids'])),
                'active' => $this->sameIds($combo['ids'], $currentStationIds),
            ];
        }

        return $options;
    }

    private function sameIds(array $a, array $b): bool
    {
        return collect($a)->sort()->values()->all()
            === collect($b)->sort()->values()->all();
    }
}
