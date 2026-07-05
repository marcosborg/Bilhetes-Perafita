<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bilhetes - Grupo {{ $group->number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-950 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-5xl flex-col gap-5 px-4 py-5 sm:px-6">
        <header class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm font-medium text-amber-700">Grupo de serviço {{ $group->number }}</p>
            <div class="mt-1 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Enviar bilhetes</h1>
                    <p class="mt-1 text-sm text-slate-600">{{ $user->name }} · link válido até {{ $user->magic_login_expires_at?->format('d/m/Y H:i') }}</p>
                </div>
                <div class="rounded-md bg-slate-100 px-3 py-2 text-sm text-slate-700">
                    {{ $tickets->where('status', 'sent')->count() }} enviados de {{ $tickets->count() }}
                </div>
            </div>
        </header>

        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <section class="grid gap-5 lg:grid-cols-[minmax(280px,360px)_1fr]">
            <aside class="rounded-md border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 p-3">
                    <h2 class="text-sm font-semibold text-slate-800">Irmãos</h2>
                </div>
                <div class="max-h-[70vh] divide-y divide-slate-100 overflow-auto">
                    @foreach ($tickets as $ticket)
                        <a href="{{ route('magic-portal', [$user, $token, 'bilhete' => $ticket->id]) }}"
                           class="flex items-center justify-between gap-3 px-3 py-3 text-sm hover:bg-amber-50 {{ $selectedTicket?->id === $ticket->id ? 'bg-amber-50' : '' }}">
                            <span>
                                <span class="block font-medium text-slate-900">{{ $ticket->brother?->name }}</span>
                                <span class="block text-xs text-slate-500">{{ $ticket->family?->name }}</span>
                            </span>
                            <span class="rounded px-2 py-1 text-xs {{ $ticket->status === 'sent' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ $ticket->status === 'sent' ? 'Enviado' : 'Pendente' }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </aside>

            <section class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                @if ($selectedTicket)
                    <div class="flex flex-col gap-4 border-b border-slate-200 pb-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm text-slate-500">{{ $selectedTicket->family?->name }}</p>
                            <h2 class="text-2xl font-semibold">{{ $selectedTicket->brother?->name }}</h2>
                            <p class="mt-1 text-sm text-slate-600">Código interno: {{ $selectedTicket->internal_code ?: 'sem código' }}</p>
                            <p class="text-sm text-slate-600">Estado: {{ $selectedTicket->status === 'sent' ? 'enviado' : 'por enviar' }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
                               target="_blank"
                               href="{{ route('tickets.download', $selectedTicket->public_token) }}">Abrir PDF</a>
                            <a class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500"
                               target="_blank"
                               href="https://wa.me/?text={{ rawurlencode($selectedTicket->whatsappText()) }}">WhatsApp</a>
                            <form method="post" action="{{ route('magic-portal.tickets.sent', [$user, $token, $selectedTicket]) }}">
                                @csrf
                                <button class="rounded-md border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-900 hover:bg-amber-100" type="submit">Marcar enviado</button>
                            </form>
                        </div>
                    </div>

                    <iframe class="mt-4 h-[68vh] w-full rounded-md border border-slate-200" src="{{ route('tickets.download', $selectedTicket->public_token) }}"></iframe>
                @else
                    <div class="rounded-md border border-slate-200 bg-slate-50 p-6 text-sm text-slate-600">
                        Não existem bilhetes atribuídos a este grupo.
                    </div>
                @endif
            </section>
        </section>
    </main>
</body>
</html>
