@extends('partials.layout')
@section('content')
    @if(!empty($errors->any()))
        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert" id="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <i class="fa fa-exclamation mx-2"></i>
            {{$errors->first()}}
        </div>
    @endif
    <div class="main-content-container container-fluid px-4">
        <!-- Page Header -->
        @include('partials.header', ['title' => 'Сотрудники'])
        <div class="col">
            <div class="card card-small mb-4">
                <div class="card-header border-bottom d-flex justify-content-between">
                    <h6 class="m-0">Аккаунты сотрудников</h6>
                    @role('Client')
                    <a href="{{route('employees.create')}}"
                       class="btn btn-success">Добавить</a>
                    @endrole
                </div>
                <div class="card-body p-2 pb-3 text-center table-responsive">
                    <table class="table mb-0" id="dataTable">
                        <thead class="bg-light">
                        <tr>
                            <th scope="col" class="border-0">#</th>
                            <th scope="col" class="border-0">Email</th>
                            <th scope="col" class="border-0">Имя</th>
                            <th scope="col" class="border-0">Фамилия</th>
                            <th scope="col" class="border-0">Должность</th>
                            <th scope="col" class="border-0">Пользователь Wazzup</th>
                            @role('Client')
                            <th scope="col" class="border-0">Действие</th>
                            @endrole
                            @role('SuperAdmin')
                            <th scope="col" class="border-0">Клиент</th>
                            @endrole
                        </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        var columns = [
            {data: "id", name: 'id'},
            {data: "email", name: 'email'},
            {data: "employee.first_name", name: 'employee.first_name'},
            {data: "employee.last_name", name: 'employee.last_name'},
            {data: "employee.position", name: 'employee.position'},
            {data: "employee.user_wazzup", name: 'employee.user_wazzup'},
            @role('Client')
            {data: 'action', name: 'action', orderable: false, searchable: false, width: 180}
            @endrole
            @role('SuperAdmin')
            {data: 'user.email', name: 'user.email'}
            @endrole
        ]
    </script>
@endpush
