@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="card p-4 shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h4 class="fw-bold mb-0">User Management</h4>

        <!-- Filter -->
        <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap"
            style="max-width:650px;">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                placeholder="Search by name or email...">

            <select name="role" class="form-select form-select-sm" style="width:150px;">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
            </select>

            <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-filter-list"></i>
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa-duotone fa-solid fa-rotate-reverse"></i>
            </a>
        </form>

        <!-- Action buttons -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-warning" title="Import Excel" data-bs-toggle="modal"
                data-bs-target="#importModal">
                <i class="fa fa-upload"></i>
            </button>

            <a href="{{ route('admin.users.export') }}" class="btn btn-sm btn-outline-success" title="Export Excel">
                <i class="fa fa-file-excel"></i>
            </a>

            <button type="button" class="btn btn-sm btn-primary px-3" data-bs-toggle="modal"
                data-bs-target="#createModal">+ Add</button>
        </div>
    </div>

    {{ $users->links('pagination::bootstrap-5') }}

    <!-- Table -->
    <table class="table table-bordered table-hover align-middle table-sm">
        <thead class="table-light">
            <tr>
                <th style="width:5%">#</th>
                <th style="width:20%">Name</th>
                <th style="width:25%">Email</th>
                <th style="width:15%">Role</th>
                <th style="width:15%">Created</th>
                <th style="width:10%" class="text-center">Status</th>
                <th style="width:10%" class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
            <tr>
                <td>{{ $users->firstItem() + $loop->index }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>
                    <span
                        class="badge bg-{{ $u->role === 'admin' ? 'danger' : ($u->role === 'staff' ? 'info' : 'secondary') }}">
                        {{ ucfirst($u->role) }}
                    </span>
                </td>
                <td>{{ $u->created_at->format('d/m/Y') }}</td>
                <td class="text-center">
                    <div class="form-check form-switch d-inline-flex justify-content-center">
                        <input class="form-check-input toggle-status" type="checkbox" data-id="{{ $u->id }}"
                            {{ $u->status ? 'checked' : '' }}>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary editBtn" data-bs-toggle="modal"
                        data-bs-target="#editModal" data-id="{{ $u->id }}" data-name="{{ $u->name }}"
                        data-email="{{ $u->email }}" data-role="{{ $u->role }}">
                        <i class="fa fa-edit"></i>
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-danger deleteBtn" data-bs-toggle="modal"
                        data-bs-target="#deleteModal" data-id="{{ $u->id }}" data-name="{{ $u->name }}">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">No data available</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $users->links('pagination::bootstrap-5') }}
</div>

@include('admin.users._modals')
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {

        // === Toggle status ===
        document.querySelectorAll('.toggle-status').forEach(input => {
            input.addEventListener('change', async () => {
                const id = input.dataset.id;
                try {
                    const res = await fetch(`{{ url('admin/users') }}/${id}/toggle-status`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) throw new Error();
                } catch {
                    alert('Error updating status');
                    input.checked = !input.checked;
                }
            });
        });

        // === Delete Modal ===
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', e => {
            const btn = e.relatedTarget;
            deleteModal.querySelector('#deleteName').textContent = btn.dataset.name;
            deleteModal.querySelector('#deleteForm').action =
                `{{ url('admin/users') }}/${btn.dataset.id}`;
        });

        // === Edit Modal ===
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', e => {
            const btn = e.relatedTarget;
            const form = editModal.querySelector('#editForm');
            form.action = `{{ url('admin/users') }}/${btn.dataset.id}`;
            form.querySelector('[name="name"]').value = btn.dataset.name;
            form.querySelector('[name="email"]').value = btn.dataset.email;
            form.querySelector('[name="role"]').value = btn.dataset.role;
        });
    });
</script>

@error('file')
<script>
    new bootstrap.Modal(document.getElementById('importModal')).show();
</script>
@enderror
@endpush