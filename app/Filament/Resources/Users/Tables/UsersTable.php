<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('responsibility')
                    ->label('Responsabilidade')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serviceGroup.name')
                    ->label('Grupo de serviço')
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable(),
                TextColumn::make('magic_login_expires_at')
                    ->label('Link válido até')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Sem link')
                    ->toggleable(),
                TextColumn::make('magic_login_sent_at')
                    ->label('Último envio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Nunca')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('responsibility')
                    ->label('Responsabilidade')
                    ->options([
                        'Superintendente de grupo' => 'Superintendente de grupo',
                        'Ajudante' => 'Ajudante',
                        'Administrador' => 'Administrador',
                    ]),
                SelectFilter::make('service_group_id')
                    ->label('Grupo de serviço')
                    ->relationship('serviceGroup', 'name'),
            ])
            ->recordActions([
                Action::make('magicLogin')
                    ->label('Enviar WhatsApp')
                    ->action(function (User $record) {
                        $phone = $record->whatsappPhone();

                        if ($phone === null) {
                            Notification::make()
                                ->title('Telefone em falta')
                                ->body('Preencha o telefone do utilizador antes de enviar o link por WhatsApp.')
                                ->warning()
                                ->send();

                            return null;
                        }

                        $token = $record->generateMagicLoginToken();
                        $url = route('magic-portal', [$record, $token]);
                        $whatsappUrl = 'https://wa.me/'.$phone.'?text='.rawurlencode($record->magicLoginWhatsappText($url));

                        Notification::make()
                            ->title('Link gerado por 7 dias')
                            ->body('A abrir o WhatsApp para '.$record->phone)
                            ->success()
                            ->send();

                        return redirect()->away($whatsappUrl);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
