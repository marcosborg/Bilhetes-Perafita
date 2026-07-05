<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->maxLength(255)
                    ->required(),
                Select::make('responsibility')
                    ->label('Responsabilidade')
                    ->options([
                        'Superintendente de grupo' => 'Superintendente de grupo',
                        'Ajudante' => 'Ajudante',
                        'Administrador' => 'Administrador',
                    ])
                    ->required(),
                Select::make('service_group_id')
                    ->label('Grupo de serviço')
                    ->relationship('serviceGroup', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Usado para filtrar por defeito irmãos, famílias e bilhetes deste grupo.'),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('phone')
                    ->label('Telefone')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Palavra-passe')
                    ->password()
                    ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
            ]);
    }
}
