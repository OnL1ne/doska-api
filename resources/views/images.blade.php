@extends('layouts.app')

@section('content')
<div class="container-fluid view-content">
    <div class="row justify-content-center">
        <div class="col-12 filters-main-div">
            <form class="filters-from" method="get" action="{{ route('filters', $url_key) }}">
                <div class="filters-block">
                    <div class="filter-block">
                        <span>{{ __('Name') }}</span>
                        <input type="text" class="name" name="filter" @if($saved_filter) value="{{ $saved_filter }}" @endif   placeholder=".*"/>
                    </div>
                    <div class="filter-block">
                        <span>{{ __('Status') }}</span>
                        <select name="status" class="status">
                            <option value=""></option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->status_id }}" @if($saved_status == $status->status_id) selected="selected" @endif>
                                    {{ $status->status_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-block">
                        <span>{{ __('Rate') }}</span>
                        <select name="rate" class="rate">
                            <option value=""></option>
                            @foreach ($rates as $rate)
                                <option value="{{ $rate->rate_id }}" @if($saved_rate == $rate->rate_id) selected="selected" @endif>
                                    {{ $rate->rate_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-block">
                        <span>{{ __('Tags') }}</span>
                        <select name="tag" class="tag">
                            <option value=""></option>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->tag_id }}" @if($saved_tag == $tag->tag_id) selected="selected" @endif>
                                    {{ $tag->tag_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-block hidden">
                        <input class="btn btn-dark" type="submit" value="{{ __('Filter') }}" />
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row justify-content-center images-row">
        <div class="col-12 content-block">
            @if ($images->items())
                <div class="images-block">
                    @foreach ($images->items() as $image)
                        <div class="image-block">
                            <img class="image"
                                 src="{{ $image->image_src }}"
                                 data-image="{{ $image->image_src }}"
                                 data-uuid="{{ $image->image_uuid }}"
                                 data-rate="{{ $image->rate_name }}"
                                 data-name="{{ $image->image_name }}"
                                 data-description="{{ $image->image_description }}"
                                 data-status="{{ $image->status_name }}"
                                 data-tags="{{ $image->tags }}"
                            />
                            <div class="image-name">
                                {{ $image->image_name }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="data-not-found">
                    <h1>{{ __('Data Not Found') }}</h1>
                </div>
            @endif
        </div>
        <div class="col-4 preview-block hidden">
            <table class="image-preview-table">
                <tr>
                    <td colspan="2" class="text-right">
                        <span class="close-preview">
                            <i class="fas fa-times" title="Close preview"></i>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <img src="https://kjnquiero.s3.amazonaws.com/image-folder/imageurl-1.png" id="image-preview">
                    </td>
                </tr>
                <tr>
                    <td class="w-25">
                        <span class="title-preview">{{ __('Uuid') }}</span>
                    </td>
                    <td>
                        <div class="value-preview" id="uuid-preview"></div>
                    </td>
                </tr>
                <tr>
                    <td class="w-25">
                        <span class="title-preview">{{ __('Name') }}</span>
                    </td>
                    <td>
                        <div class="value-preview" id="name-preview"></div>
                    </td>
                </tr>
                <tr>
                    <td class="w-25">
                        <span class="title-preview">{{ __('Description') }}</span>
                    </td>
                    <td>
                        <div class="value-preview" id="description-preview"></div>
                    </td>
                </tr>
                <tr>
                    <td class="w-25">
                        <span class="title-preview">{{ __('Rate') }}</span>
                    </td>
                    <td>
                        <div class="value-preview" id="rate-preview"></div>
                    </td>
                </tr>
                <tr>
                    <td class="w-25">
                        <span class="title-preview">{{ __('Status') }}</span>
                    </td>
                    <td>
                        <div class="value-preview" id="status-preview"></div>
                    </td>
                </tr>
                <tr>
                    <td class="w-25">
                        <span class="title-preview">{{ __('Tags') }}</span>
                    </td>
                    <td>
                        <div class="value-preview" id="tags-preview"></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

@section('footer')
    <div class="paginate-div" style="margin-left: 5%;">{!! $images->render() !!}</div>
    <div class="paginate-count-div">
        <form class="paginate_count_form" method="get" action="{{ route('set_paginate_count', $url_key) }}">
            <span>{{ __('Count') }}</span>
            <select name="paginate_count" class="paginate_count">
                <option value="10" @if($saved_paginate_count == '10') selected="selected" @endif>10</option>
                <option value="15" @if($saved_paginate_count == '15') selected="selected" @endif>15</option>
                <option value="20" @if($saved_paginate_count == '20') selected="selected" @endif>20</option>
                <option value="25" @if($saved_paginate_count == '25') selected="selected" @endif>25</option>
            </select>
        </form>
    </div>
@endsection
