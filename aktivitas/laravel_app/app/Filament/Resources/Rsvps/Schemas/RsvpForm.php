<?php

namespace App\Filament\Resources\Rsvps\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RsvpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('acara_id')
                    ->label('Acara')
                    ->relationship('acara', 'mempelai')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('nama')->required()->maxLength(100),
                Select::make('kehadiran')
                    ->options(['hadir' => 'Hadir', 'tidak' => 'Tidak hadir'])
                    ->required(),
                TextInput::make('jumlah')->numeric()->minValue(0)->maxValue(5)->required(),
                Textarea::make('ucapan')->maxLength(500)->columnSpanFull(),
            ]);
    }
}
