@extends('layout.admin.masters')
@section('content')
<div id="main-content">
    <div class="container-fluid">
        <h3 class="page-title">Circular Admin
            <small>Manage office-wise Circular Admins</small>
        </h3>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li>Circular Admin</li>
            <li class="pull-right">
                <a href="{{ route('admin.circular-admin.create') }}" class="btn btn-primary">
                    <i class="icon-plus"></i> Add Circular Admin
                </a>
            </li>
        </ul>

        <div class="widget">
            <div class="widget-title">
                <h4><i class="icon-reorder"></i> Circular Admin List</h4>
            </div>
            <div class="widget-body">
                @include('layout.admin.alert')
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Username / Email</th>
                                <th>Office</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse ($accounts as $index => $account)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $account->username }}</td>
                                <td>
                                    @if ($account->office)
                                        {{ optional($account->office->details->first())->office_name ?? 'Office #' . $account->office_id }}
                                    @else
                                        Office #{{ $account->office_id }}
                                    @endif
                                </td>
                                <td>
                                    @if ($account->is_active)
                                        <span class="label label-success">Active</span>
                                    @else
                                        <span class="label label-default">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-small btn-primary"
                                       href="{{ route('admin.circular-admin.edit', $account->id) }}">
                                        <i class="icon-edit"></i> Edit
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.circular-admin.status', $account->id) }}"
                                          style="display:inline-block"
                                          onsubmit="return confirm('Change this account status?');">
                                        @csrf
                                        <button type="submit" class="btn btn-small btn-warning">
                                            {{ $account->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No Circular Admin found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
