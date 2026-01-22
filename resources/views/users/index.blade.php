@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        @session('success')
            <x-ui.alert variant="success">
                {{ $value }}
            </x-ui.alert>
        @endsession

        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base-content mb-1.5 text-2xl font-semibold">Пользователи</h3>
                <p class="text-base-content/80">Управление пользователями системы</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-gradient">
                <span class="icon-[tabler--plus] size-5"></span>
                Создать пользователя
            </a>
        </div>

        <div class="rounded-box shadow-base-300/10 bg-base-100 w-full pb-2 shadow-md">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Телефон</th>
                            <th>Роль</th>
                            <th>Создан</th>
                            <th>Обновлен</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $user->isAdmin() ? 'badge-primary' : 'badge-ghost' }}">
                                        {{ $user->isAdmin() ? 'Администратор' : 'Пользователь' }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                <td>{{ $user->updated_at->format('d.m.Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-circle btn-text btn-sm" aria-label="Редактировать пользователя">
                                        <span class="icon-[tabler--pencil] size-5"></span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8">
                                    <p class="text-base-content/70">Пользователи не найдены.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($users->hasPages())
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection

