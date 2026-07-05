<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grupo {{ $group->number }} - Bilhetes</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-6xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-8">
        <header class="flex flex-col gap-2 border-b border-slate-200 pb-5">
            <p class="text-sm font-medium text-amber-700">Grupo de serviço {{ $group->number }}</p>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight">Entrega de bilhetes</h1>
                    <p class="mt-1 text-sm text-slate-600">Escolha a família, o irmão e partilhe o PDF correto pelo WhatsApp.</p>
                </div>
                <div class="rounded-md border border-slate-200 bg-white px-4 py-3 text-sm">
                    <span class="font-medium">{{ $group->tickets()->where('status', 'sent')->count() }}</span>
                    enviados de
                    <span class="font-medium">{{ $group->tickets()->count() }}</span>
                </div>
            </div>
        </header>

        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <section class="grid gap-6 lg:grid-cols-[340px_1fr]">
            <aside class="space-y-4">
                <form method="get" class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                    <label for="familia" class="text-sm font-medium text-slate-700">Família</label>
                    <select id="familia" name="familia" class="mt-2 w-full rounded-md border-slate-300 text-sm" onchange="this.form.submit()">
                        @foreach ($families as $family)
                            <option value="{{ $family->id }}" @selected($selectedFamily?->id === $family->id)>
                                {{ $family->name }}
                            </option>
                        @endforeach
                    </select>

                    <label for="irmao" class="mt-4 block text-sm font-medium text-slate-700">Irmão</label>
                    <select id="irmao" name="irmao" class="mt-2 w-full rounded-md border-slate-300 text-sm" onchange="this.form.submit()">
                        @foreach (($selectedFamily?->brothers ?? collect())->sortBy('name') as $brother)
                            <option value="{{ $brother->id }}" @selected($selectedBrother?->id === $brother->id)>
                                {{ $brother->name }}{{ $brother->ticket?->status === 'sent' ? ' - enviado' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>

                @if ($selectedFamily)
                    <div class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                        <h2 class="text-sm font-semibold text-slate-800">{{ $selectedFamily->name }}</h2>
                        <div class="mt-3 divide-y divide-slate-100">
                            @foreach ($selectedFamily->brothers->sortBy('name') as $brother)
                                <a class="flex items-center justify-between gap-3 py-2 text-sm hover:text-amber-700" href="{{ route('groups.portal', ['group' => $group, 'familia' => $selectedFamily->id, 'irmao' => $brother->id]) }}">
                                    <span>{{ $brother->name }}</span>
                                    <span class="rounded px-2 py-1 text-xs {{ $brother->ticket?->status === 'sent' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $brother->ticket?->status === 'sent' ? 'Enviado' : 'Pendente' }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>

            <section class="rounded-md border border-slate-200 bg-white p-4 shadow-sm">
                @if ($selectedBrother && $ticket)
                    <div class="flex flex-col gap-4 border-b border-slate-200 pb-4 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="text-sm text-slate-500">{{ $selectedFamily->name }}</p>
                            <h2 class="text-2xl font-semibold">{{ $selectedBrother->name }}</h2>
                            <p class="mt-1 text-sm text-slate-600">Código interno: {{ $ticket->internal_code ?: 'sem código' }}</p>
                            <p class="text-sm text-slate-600">Estado: {{ $ticket->status === 'sent' ? 'enviado' : 'por enviar' }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if ($ticket->pdf_path)
                                <a class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700" target="_blank" href="{{ route('tickets.download', $ticket->public_token) }}">Abrir PDF</a>
                                <a class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500" target="_blank" href="https://wa.me/?text={{ rawurlencode($ticket->whatsappText()) }}">WhatsApp</a>
                            @endif
                            <form method="post" action="{{ route('groups.tickets.sent', [$group, $ticket]) }}">
                                @csrf
                                <button class="rounded-md border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-medium text-amber-900 hover:bg-amber-100" type="submit">Marcar enviado</button>
                            </form>
                        </div>
                    </div>

                    @if ($ticket->pdf_path)
                        <iframe class="mt-4 h-[72vh] w-full rounded-md border border-slate-200" src="{{ route('tickets.download', $ticket->public_token) }}"></iframe>
                    @else
                        <div class="mt-4 rounded-md border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                            Este irmão tem uma correspondência no Excel, mas o PDF não foi encontrado no ZIP.
                        </div>
                    @endif
                @else
                    <div class="rounded-md border border-slate-200 bg-slate-50 p-6 text-sm text-slate-600">
                        Selecione uma família e um irmão para ver o bilhete.
                    </div>
                @endif
            </section>
        </section>
    </main>
</body>
</html>
