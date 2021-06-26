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
        @include('partials.header', ['title' => 'Аккаунты sbis'])
        <div class="col">
            <div class="card card-small mb-4">
                <div class="card-header border-bottom d-flex justify-content-between">
                    <h6 class="m-0">Аккаунты</h6>
                    @role('Client')
                    <a href="{{route('sbisAccounts.create')}}"
                       class="btn btn-success">Добавить</a>
                    @endrole
                </div>
                <div class="card-body p-2 pb-3 text-center table-responsive">
                    <table class="table mb-0" id="dataTable">
                        <thead class="bg-light">
                        <tr>
                            <th scope="col" class="border-0">#</th>
                            @role('SuperAdmin')
                            <th scope="col" class="border-0">Клиент</th>
                            @endrole
                            <th scope="col" class="border-0">Статус</th>
                            @role('Client')
                            <th scope="col" class="border-0">ID приложения</th>
                            <th scope="col" class="border-0">защищенный ключ</th>
                            <th scope="col" class="border-0">сервисный ключ</th>
                            <th scope="col" class="border-0">Действие</th>
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
        @role('SuperAdmin')
            {data: "user.email", name: 'user.email'},
        @endrole
            {data: "status", name: 'status'},
            @role('Client')
            {data: "app_client_id", name: 'app_client_id'},
            {data: "app_secret", name: 'app_secret'},
            {data: "secret_key", name: 'secret_key'},
            {data: 'action', name: 'action', orderable: false, searchable: false, width: 180}
            @endrole
        ]
    </script>
@endpush
