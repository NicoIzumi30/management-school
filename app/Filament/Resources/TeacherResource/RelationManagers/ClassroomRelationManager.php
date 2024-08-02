<?php

namespace App\Filament\Resources\TeacherResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Periode;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Classroom;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ClassroomRelationManager extends RelationManager
{
    protected static string $relationship = 'classroom';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('classrooms_id')->label('Select Class')->options(Classroom::all()->pluck('name', 'id'))->searchable()
                    ->relationship('classroom', 'name')->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->required()->live()
                                ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            Forms\Components\Hidden::make('slug')
                                ->required(),
                        ])->createOptionAction(function (Action $action) {
                            return $action->modalHeading('Create Classroom')->modalButton('Create')->modalWidth('2xl');
                        }),
                Select::make('periode_id')->label('Select Periode')->options(Periode::all()->pluck('name', 'id'))
                ->searchable()->relationship('periode', 'name')->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->required()->live()
                        ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                    Forms\Components\Hidden::make('slug')
                        ->required(),
                ])->createOptionAction(function (Action $action) {
                    return $action->modalHeading('Create Periode')->modalButton('Create')->modalWidth('2xl');
                }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('classroom.name'),
                Tables\Columns\TextColumn::make('periode.name'),
                ToggleColumn::make('is_open')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
