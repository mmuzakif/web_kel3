<?php

namespace App\Filament\Resources\Rsvps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RsvpsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->searchable()->sortable(),
                TextColumn::make('acara.mempelai')->label('Acara')->searchable(),
                TextColumn::make('kehadiran')->badge()->color(fn (string $state): string => $state === 'hadir' ? 'success' : 'gray'),
                TextColumn::make('jumlah')->label('Jumlah tamu')->sortable(),
                TextColumn::make('ucapan')->limit(60)->toggleable(),
                TextColumn::make('dibuat_pada')->label('Dikirim')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('kehadiran')->options(['hadir' => 'Hadir', 'tidak' => 'Tidak hadir']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
