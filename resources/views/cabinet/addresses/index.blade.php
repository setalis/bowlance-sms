@extends('layouts.cabinet')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ $title }}</h1>
        <a href="{{ route('cabinet.addresses.create') }}" class="btn btn-primary gap-2">
            <span class="icon-[tabler--plus] size-5"></span>
            Добавить адрес
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-6">
            <span class="icon-[tabler--check] size-5"></span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error mb-6">
            <span class="icon-[tabler--alert-triangle] size-5"></span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($addresses->isEmpty())
        <div class="card">
            <div class="card-body text-center">
                <span class="icon-[tabler--map-pin-off] size-16 mb-4 inline-block text-base-content/40"></span>
                <p class="text-base-content/60 mb-4">У вас пока нет сохраненных адресов</p>
                <a href="{{ route('cabinet.addresses.create') }}" class="btn btn-primary gap-2">
                    <span class="icon-[tabler--plus] size-5"></span>
                    Добавить первый адрес
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($addresses as $address)
                <div class="card {{ $address->is_default ? 'ring-2 ring-primary' : '' }}">
                    <div class="card-body">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="icon-[tabler--map-pin] size-5 text-primary"></span>
                                <h3 class="font-bold">{{ $address->label }}</h3>
                            </div>
                            @if($address->is_default)
                                <span class="badge badge-primary badge-sm">По умолчанию</span>
                            @endif
                        </div>

                        <p class="text-sm text-base-content/70 mb-2">{{ $address->address }}</p>

                        @if($address->entrance || $address->floor || $address->apartment || $address->intercom)
                            <div class="text-xs text-base-content/60 mb-2 space-y-1">
                                @if($address->entrance || $address->floor)
                                    <div class="flex gap-3">
                                        @if($address->entrance)
                                            <span>Подъезд: {{ $address->entrance }}</span>
                                        @endif
                                        @if($address->floor)
                                            <span>Этаж: {{ $address->floor }}</span>
                                        @endif
                                    </div>
                                @endif
                                @if($address->apartment || $address->intercom)
                                    <div class="flex gap-3">
                                        @if($address->apartment)
                                            <span>Кв: {{ $address->apartment }}</span>
                                        @endif
                                        @if($address->intercom)
                                            <span>Домофон: {{ $address->intercom }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($address->courier_comment)
                            <div class="text-xs text-base-content/50 italic mb-2">
                                <span class="icon-[tabler--message] size-3 inline"></span>
                                {{ $address->courier_comment }}
                            </div>
                        @endif

                        @if($address->receiver_phone)
                            <div class="text-xs text-base-content/60 mb-2">
                                <span class="icon-[tabler--phone] size-3 inline"></span>
                                {{ $address->receiver_phone }}
                            </div>
                        @endif

                        @if($address->leave_at_door)
                            <div class="badge badge-outline badge-sm mb-3">
                                <span class="icon-[tabler--door] size-3 mr-1"></span>
                                Оставить у двери
                            </div>
                        @endif

                        <div class="flex flex-wrap gap-2 mt-3">
                            @if(!$address->is_default)
                                <form action="{{ route('cabinet.addresses.setDefault', $address) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-ghost gap-2">
                                        <span class="icon-[tabler--star] size-4"></span>
                                        По умолчанию
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('cabinet.addresses.edit', $address) }}" class="btn btn-sm btn-ghost gap-2">
                                <span class="icon-[tabler--pencil] size-4"></span>
                                Редактировать
                            </a>

                            <form action="{{ route('cabinet.addresses.destroy', $address) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Вы уверены, что хотите удалить этот адрес?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-ghost text-error gap-2">
                                    <span class="icon-[tabler--trash] size-4"></span>
                                    Удалить
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
