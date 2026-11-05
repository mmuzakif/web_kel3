<?php

namespace App\Filament\Resources\Acaras\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class AcaraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('mempelai')->label('Mempelai')->required()->maxLength(100),
                DatePicker::make('tanggal')->required(),
                TimePicker::make('waktu')->required()->seconds(false),
                TextInput::make('lokasi')->required()->maxLength(150),
                Textarea::make('alamat')->required()->columnSpanFull(),
                TimePicker::make('waktu_akad_mulai')->label('Akad mulai')->required()->seconds(false),
                TimePicker::make('waktu_akad_selesai')->label('Akad selesai')->required()->seconds(false),
                TimePicker::make('waktu_resepsi_mulai')->label('Resepsi mulai')->required()->seconds(false),
                TimePicker::make('waktu_resepsi_selesai')->label('Resepsi selesai')->required()->seconds(false),
                TextInput::make('map_url')->label('Link peta')->url()->nullable()->columnSpanFull(),
            ]);
    }
}
