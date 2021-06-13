@extends('partials.layout')
@section('content')
    <div class="main-content-container container-fluid px-4">
        <!-- Page Header -->
        @include('partials.header', ['title' => 'Аккаунт wazzup'])
        <div class="col-md-6 col-sm-12 mb-4">
            <div class="stats-small stats-small--1 card card-small">
                <div class="card-body">
                    <form action="{{route('whatsapp.store')}}" method="post" id="added_form">
                        @if(!empty($users))
                            <div class="form-group">
                                <label for="service" id="users">Клиенты</label>
                                <select class="form-control" id="users" name="user_id" required>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->email}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="username">username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username"
                                   value="" required>
                        </div>
                        <div class="form-group">
                            <label for="api_key">Api key</label>
                            <input type="text" class="form-control" id="api_key" name="api_key" placeholder="Api key" value="">
                        </div>
{{--                        <div class="form-group">--}}
{{--                            <label for="wazzup">Id пользователя в crm-системе</label>--}}
{{--                            <input type="text" class="form-control" id="wazzup" name="wazzup_id"--}}
{{--                                   placeholder="Id пользователя в crm-системе" value="" required>--}}
{{--                        </div>--}}
                            <div class="form-check mb-5">
                                <input type="checkbox" class="form-check-input" id="exampleCheck1" value="1" name="status">
                                <label class="form-check-label" for="exampleCheck1">Включить</label>
                            </div>
                        <button type="submit" class="btn btn-success">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
