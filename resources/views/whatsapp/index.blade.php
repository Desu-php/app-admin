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
        @include('partials.header', ['title' => 'Аккаунты wazzup'])
        <div class="col">
            <div class="card card-small mb-4">
                <div class="card-header border-bottom d-flex justify-content-between">
                    <h6 class="m-0">Аккаунты клиентов</h6>
                    <a href="{{route('whatsapp.create')}}" class="btn btn-success">Добавить</a>
                </div>
                <div class="card-body p-2 pb-3 text-center table-responsive">
                    <table class="table mb-0" id="dataTable">
                        <thead class="bg-light">
                        <tr>
                            <th scope="col" class="border-0">#</th>
                            @role('SuperAdmin')
                            <th scope="col" class="border-0">Клиент</th>
                            @endrole
                            <th scope="col" class="border-0">Имя пользователя</th>
                            <th scope="col" class="border-0">Id пользователя в crm-системе</th>
                            <th scope="col" class="border-0">Статус</th>
                            <th scope="col" class="border-0">Api key</th>
                            <th scope="col" class="border-0">Чат</th>
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
        @role('SuperAdmin')
            {data: "user.email", name: 'user.email'},
        @endrole
            {data: "username", name: 'username'},
            {data: "wazzup_id", name: 'wazzup_id'},
            {data: "status", name: 'status'},
            {data: "api_key", name: 'api_key'},
            {data: "chat", name: 'chat'},
            {data: 'action', name: 'action', orderable: false, searchable: false, width: 180}
        ]
    </script>
@endpush
