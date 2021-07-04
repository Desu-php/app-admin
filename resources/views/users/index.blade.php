@extends('partials.layout')
@section('content')
    <div class="main-content-container container-fluid px-4">
        <!-- Page Header -->
        @include('partials.header', ['title' => 'Пользователи админки'])
        <div class="col">
            <div class="card card-small mb-4">
                <div class="card-header border-bottom d-flex justify-content-between">
                    <h6 class="m-0">Пользователи админки</h6>
                    <a href="{{route('users.create')}}" class="btn btn-success">Добавить</a>
                </div>
                <div class="card-body p-2 pb-3 text-center table-responsive">
                    <table class="table mb-0" id="dataTable">
                        <thead class="bg-light">
                        <tr>
                            <th scope="col" class="border-0">#</th>
                            <th scope="col" class="border-0">Email</th>
                            <th scope="col" class="border-0">Имя</th>
                            <th scope="col" class="border-0">Дата</th>
                            <th scope="col" class="border-0">Аккаунты</th>
                            <th scope="col" class="border-0">Действие</th>
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
            {data: "name", name: 'name'},
            {data: "created_at", name: 'created_at'},
            {data: "accounts", name: 'accounts', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false, width: 180}
        ]
    </script>
@endpush
