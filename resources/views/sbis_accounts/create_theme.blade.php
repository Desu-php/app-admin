@extends('partials.layout')
@section('content')
    <div class="main-content-container container-fluid px-4">
        <!-- Page Header -->
        @include('partials.header', ['title' => 'Темы'])
        <div class="col-md-6 col-sm-12 mb-4">
            <div class="stats-small stats-small--1 card card-small">
                <div class="card-body">
                    <form action="{{route('sbisAccounts.store_theme')}}" method="post" id="added_form">
                            <div class="form-group">
                                <label for="service" id="users">Темы</label>
                                <select class="form-control" id="service" name="theme" required>
                                    @foreach($themes as $theme)
                                        <option value="{{$theme[2]}}">{{$theme[2]}}</option>
                                    @endforeach
                                </select>
                            </div>
                        <button type="submit" class="btn btn-success">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
