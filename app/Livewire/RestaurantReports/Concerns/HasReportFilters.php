<?php
// app/Livewire/RestaurantReports/Concerns/HasReportFilters.php

namespace App\Livewire\RestaurantReports\Concerns;

use App\Models\Location;
use App\Models\Floor;
use Carbon\Carbon;

trait HasReportFilters
{
    public $location_id;
    public $floor_id;
    public $date_from;
    public $date_to;

    public $locations = [];
    public $floors = [];

    public function mountFilters()
    {
        $this->locations = Location::all();

        $this->date_from = now()->startOfMonth()->format('Y-m-d');
        $this->date_to   = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedLocationId()
    {
        $this->floor_id = null;

        $this->floors = Floor::query()
            ->where('location_id', $this->location_id)
            ->orderBy('name')
            ->get();

        $this->loadReport();
    }

    public function updatedFloorId()
    {
        $this->loadReport();
    }

    public function updatedDateFrom()
    {
        $this->loadReport();
    }

    public function updatedDateTo()
    {
        $this->loadReport();
    }

    protected function getDateRange()
    {
        return [
            Carbon::parse($this->date_from)->startOfDay(),
            Carbon::parse($this->date_to)->endOfDay(),
        ];
    }
}