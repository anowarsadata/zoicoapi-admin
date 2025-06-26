<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Email Templates';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('template_name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->disabledOn('edit'),

                TextInput::make('from_email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                TextInput::make('to_email')
                    ->email()
                    ->maxLength(255),

                TextInput::make('reply_to_email')
                    ->email()
                    ->maxLength(255),

                TextInput::make('subject')
                    ->required()
                    ->maxLength(255),

                RichEditor::make('message')
                    ->required()
                    ->columnSpanFull(),

                Actions::make([
                    FormAction::make('preview')
                        ->label('Preview')
                        ->icon('heroicon-o-eye')
                        ->url(fn ($record) => route('preview.email', $record->id))
                        ->hidden(fn ($record) => $record === null)
                        ->openUrlInNewTab()
                ])->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('template_name')->sortable()->searchable(),
                TextColumn::make('from_email')->sortable()->searchable(),
                TextColumn::make('to_email')->sortable()->searchable(),
                TextColumn::make('subject')->sortable()->searchable(),
                TextColumn::make('created_at')->dateTime('d M Y, h:i A')->sortable(),
            ])
            ->filters([
                Filter::make('created_today')
                    ->label('Created Today')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereDate('created_at', now()->toDateString())
                    ),

                TernaryFilter::make('to_email')
                    ->label('Has To Email')
                    ->placeholder('All')
                    ->trueLabel('Has To Email')
                    ->falseLabel('No To Email')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('to_email')->where('to_email', '!=', ''),
                        false: fn (Builder $query) => $query->whereNull('to_email')->orWhere('to_email', ''),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
