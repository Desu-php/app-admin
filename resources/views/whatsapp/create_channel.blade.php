@extends('partials.layout')
@section('content')
    <div class="main-content-container container-fluid px-4">
        <!-- Page Header -->
        @include('partials.header', ['title' => 'Аккаунт wazzup привязка канала'])
        <div class="col-md-6 col-sm-12 mb-4">
            <div class="stats-small stats-small--1 card card-small">
                <div class="card-body">
                    <form action="{{route('whatsapp.channel.store', $data->id)}}" method="post" id="added_form">
                        @method('put')
                        <div class="form-group">
                            <label for="service" id="channelId">Канал</label>
                            <select class="form-control" id="channelId" name="channelId" required>
                                @foreach($channels as $channel)
                                    <option
                                        @if($channel->channelId == $data->channelId)
                                        selected
                                        @endif
                                        value="{{$channel->channelId}}">
                                        {{$channel->transport.' '.$channel->plainId .' '. $channel->state}}
                                    </option>
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
