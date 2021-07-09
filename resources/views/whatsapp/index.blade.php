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
    <div class="main-content-container container-fluid px-4" id="after_alert">
        <!-- Page Header -->
        @include('partials.header', ['title' => 'Аккаунты wazzup'])
        <div class="col">
            <div class="card card-small mb-4">
                <div class="card-header border-bottom d-flex justify-content-between">
                    <h6 class="m-0">Аккаунты клиентов</h6>
                    @role('Client')
                    <div>
                        <button id="setWebHook" data-url="{{route('setWebHook')}}" class="btn btn-primary">
                            Привязать вебхук</button>
                        <a href="{{route('whatsapp.create')}}"
                           @if(!empty($whatsapp))
                           style="display:none"
                           id="btn_add"
                           @endif
                           class="btn btn-success">Добавить</a>
                        @if(!empty($whatsapp))
                            <a href="{{route('whatsapp.channel.create', $whatsapp->id)}}"
                               id="btn_channel"
                               class="btn btn-primary">Привязать канал</a>
                        @endif
                    </div>
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
                            <th scope="col" class="border-0">Имя пользователя</th>
{{--                            <th scope="col" class="border-0">Id пользователя в crm-системе</th>--}}
                            <th scope="col" class="border-0">Статус</th>
                            @role('Client')
                            <th scope="col" class="border-0">Api key</th>
                            @endrole
                            <th scope="col" class="border-0">Канал Id</th>
                            @role('Client')
                            <th scope="col" class="border-0">Чат</th>
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
            {data: "username", name: 'username'},
            // {data: "wazzup_id", name: 'wazzup_id'},
            {data: "status", name: 'status'},
            @role('Client')
            {data: "api_key", name: 'api_key'},
            @endrole
            {data: "channelId", name: 'channelId'},
            @role('Client')
            {data: "chat", name: 'chat'},
            {data: 'action', name: 'action', orderable: false, searchable: false, width: 180}
            @endrole
        ]
    </script>
@endpush
