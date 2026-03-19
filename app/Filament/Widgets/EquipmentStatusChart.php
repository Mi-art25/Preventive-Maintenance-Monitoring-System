<?php

namespace App\Filament\Widgets;

use App\Models\Equipment;
use Filament\Widgets\ChartWidget;

class EquipmentStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Equipment Status';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $active           = Equipment::where('status', 'active')->count();
        $inactive         = Equipment::where('status', 'inactive')->count();
        $underMaintenance = Equipment::where('status', 'under_maintenance')->count();
        $retired          = Equipment::where('status', 'retired')->count();

        return [
            'datasets' => [
                [
                    'label'           => 'Equipment',
                    'data'            => [$active, $underMaintenance, $inactive, $retired],
                    'backgroundColor' => [
                        '#4caf82',  // active    — green
                        '#f0a500',  // under maintenance — amber
                        '#6b6f7a',  // inactive  — gray
                        '#e05c5c',  // retired   — red
                    ],
                    'borderWidth' => 0,
                    'hoverOffset' => 6,
                ],
            ],
            'labels' => ['Active', 'Under Maintenance', 'Inactive', 'Retired'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels'   => [
                        'padding'   => 16,
                        'boxWidth'  => 12,
                        'boxHeight' => 12,
                    ],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}