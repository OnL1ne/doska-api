@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card pipeline-card">
                    <div class="card-header">{{ __('Urls list') }}</div>

                    <div class="card-body">
                        <ul>
                            @foreach ($urls as $url)
                                <li><a href="{{ url('/').'/view/'.$url->url_key }}" target="_blank">Url key {{ $url->url_key }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
