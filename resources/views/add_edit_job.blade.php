@extends('blog-poster::components.layout')

@php
    $action = isset($job) && !isset($copying) ? route('blog-poster.jobs.update', ['id' => $job->id]) : route('blog-poster.jobs.store');
@endphp

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h4>Job Properties</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body px-3">
                    <div class="row">
                        <form class="form-edit-add" action="{{ $action }}" method="post">
                            @csrf
                            <input name="config" type="hidden" class="configurator-configuration">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="job-name">Name</label>
                                    <input value="@if(isset($job)){{ $job->name }}@endif" name="name" type="text" class="form-control" id="job-name" placeholder="Wikipedia">
                                </div>

                                <div class="form-group">
                                    <label for="job-source">Source</label>
                                    <input value="{{ isset($job) ? $job->source : '' }}" name="source" type="text" class="form-control" id="job-source" placeholder="https://en.wikipedia.org/some/list/to/scrape">
                                </div>

                                <div class="form-group">
                                    <label for="job-source">Icon</label>
                                    <input value="{{ isset($job) ? $job->icon : '' }}" name="icon" type="text" class="form-control" id="job-icon" placeholder="https://www.wikipedia.org/static/favicon/wikipedia.ico">
                                    <a href="#" id="parse-source-icon" class="btn bg-gradient-success">Parse</a>
                                </div>

                                <div class="form-group">
                                    <label for="job-identifier">Identifier In List</label>
                                    <input value="{{ isset($job) ? $job->identifier_in_list : '' }}" name="identifier_in_list" type="text" class="form-control" id="job-identifier" placeholder="#some.css[selector]">
                                </div>
                            </div>

                            <div class="col-md-6">
                                @if(! is_null(config('blog-poster.category')))
                                    <div class="form-group">
                                        <label for="job-category">Category</label>
                                        <select name="category_id" id="job-category" class="form-control">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option @if(isset($job) && $category->id === $job->category_id) selected @endif value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="job-limit">Daily Limit</label>
                                    <input {{ isset($job) ? $job->limit : '' }} name="limit" type="number" class="form-control" id="job-limit" placeholder="4">
                                </div>

                                <div class="form-group">
                                    <div class="form-switch ps-0 mt-5">
                                        <label class="" for="job-draft">Is Draft</label>
                                        <input {{ isset($job) && $job->is_draft ? 'checked' : '' }} name="is_draft" class="form-check-input ms-auto" type="checkbox" id="job-draft" >
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="configurator-attributes-wrapper">
                @if(isset($job))
                    @foreach($job->config as $attribute)
                        @include('blog-poster::components.jobs._article_attribute', ['config' => $attribute])
                    @endforeach
                @endif
            </div>

            <div class="row mt-5 mb-5">
                <div class="col-md-6 text-left">
                    <a class="btn no-animation bg-gradient-success mb-0 submit-form" href="#">
                        <i class="fas fa-check" aria-hidden="true"></i> PUBLISH
                    </a>
                    <a class="btn no-animation bg-gradient-info mb-0" id="testJob" href="#">
                        <i class="fas fa-terminal" aria-hidden="true"></i> TEST
                    </a>
                </div>
                <div class="col-md-6 text-right">
                    <a class="btn no-animation bg-gradient-dark mb-0 add-new-attribute" href="#">
                        <i class="fas fa-plus" aria-hidden="true"></i> ADD NEW ATTRIBUTE
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function (){
            const $configurator = $(".configurator-attributes-wrapper");
            const attr_template = `@include('blog-poster::components.jobs._article_attribute')`;
            const attr_template_ignoring_attrs = `@include('blog-poster::components.jobs._ignoring-attribute')`;
            const attr_template_replacing_attrs = `@include('blog-poster::components.jobs._replace-tag-attribute')`;
            const preview_template = `@include('blog-poster::components.jobs.preview.preview')`;
            const preview_attribute_template = `@include('blog-poster::components.jobs.preview._preview_attribute')`;

            $(".add-new-attribute").on('click', function (e){
                e.preventDefault();

                const random_id = makeId(128);
                $configurator.append(attr_template.replaceAll('%attribute_ID%', random_id))
            })

            $("body").on('keyup', '.configurator-attribute-name', function (){
                const val = $(this).val() !== "" ? $(this).val() : 'Article Attribute Name'
                $(this).closest('.configurator-attribute').find(".configurator-attribute-name-title").text(val)
            }).on('click', '.configurator-attribute-ignoring-attributes-add-new', function (e){
                e.preventDefault();
                $(this).closest('.configurator-attribute-ignoring-attributes').find('.configurator-attribute-ignoring-attributes-container').append(attr_template_ignoring_attrs)
            }).on('click', '.configurator-ignoring-attribute-delete', function (e){
                e.preventDefault();
                $(this).closest('.configurator-ignoring-attribute').remove()
            }).on('click', '.configurator-attribute-replacing-attributes-add-new', function (e){
                e.preventDefault();
                $(this).closest('.configurator-attribute-replacing-attributes').find('.configurator-attribute-replacing-attributes-container').append(attr_template_replacing_attrs)
            }).on('click', '.configurator-replacing-attribute-delete', function (e){
                e.preventDefault();
                $(this).closest('.configurator-replacing-attribute').remove()
            }).on('click', '.configurator-attribute-delete', function (e){
                e.preventDefault();
                $(this).closest('.configurator-attribute').remove()
            })

            $(".submit-form").on('click', function (e){
                e.preventDefault();
                const _json = buildConfigurationJson()
                $(".configurator-configuration").val(_json)

                $("form.form-edit-add").submit();
            })

            function buildConfigurationJson()
            {
                let json = [];

                $(".configurator-attribute").each(function (){
                    let data = {};
                    let $this = $(this);

                    data.is_html = $this.find('.configurator-attribute-is-html').is(':checked')
                    data.is_file = $this.find('.configurator-attribute-is-file').is(':checked')
                    data.as_thumb = $this.find('.configurator-attribute-as-thumbnail').is(':checked')
                    data.name = $this.find('.configurator-attribute-name').val();
                    data.selector = $this.find('.configurator-attribute-selector').val();
                    data.type = $this.find('.configurator-attribute-type').val();
                    data.custom_tag = $this.find('.configurator-attribute-custom-tag-attribute').val();

                    data.ignoring_attributes = [];
                    data.replacing_attributes = [];

                    $this.find('.configurator-ignoring-attribute').each(function (){
                        data.ignoring_attributes.push($(this).find('.configurator-ignoring-attribute-selector').val())
                    })

                    $this.find('.configurator-replacing-attribute').each(function (){
                        const selector = $(this).find('.configurator-selector-to-replace-attribute').val();
                        const replacingAttribute = $(this).find('.configurator-replacing-attribute-value').val();
                        const attributeToGetValueFrom = $(this).find('.configurator-attribute-to-get-value-from').val();

                        data.replacing_attributes.push({
                            selector: selector,
                            replacing_attribute: replacingAttribute,
                            attribute_to_get_value_from: attributeToGetValueFrom
                        })
                    })

                    json.push(data)
                })

                return JSON.stringify(json)
            }

            $("#testJob").on('click', function (e){
                e.preventDefault()
                const _json = buildConfigurationJson()

                $.ajax({
                    'type': 'post',
                    '_token': '{{ csrf_token() }}',
                    'data': {
                        'payload': _json,
                        'source': $('#job-source').val(),
                        'list_item_identifier': $('#job-identifier').val(),
                        '_token': $("meta[name='csrf']").attr('content')
                    },
                    'url': "{{ route('blog-poster.jobs.test') }}"
                }).done(function (response){
                    let html = '';

                    $(".configurator-attribute-name").each(function(){
                        let attrName = $(this).val()
                        let attrValue = response[attrName];
                        let attrType = $(this).closest('.configurator-attribute').find('.configurator-attribute-type').val()

                        if(attrType === 'array')
                        {
                            attrValue = attrValue.join('<br>')
                        }
                        else if(attrType === 'url')
                        {
                            attrValue = "<img src='"+ attrValue +"' width='200px'>"
                        }

                        html += preview_attribute_template.replace('%attribute_name%', attrName).replace('%attribute_value%', attrValue)
                    });

                    const result = preview_template.replace('%attributes%', html);

                    window.open().document.write(result);
                });
            })

            function makeId(length) {
                let result             = '';
                const characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                const charactersLength = characters.length;
                for ( let i = 0; i < length; i++ ) {
                    result += characters.charAt(Math.floor(Math.random() *
                        charactersLength));
                }
                return result;
            }

            $("#parse-source-icon").on('click', function (e){
                e.preventDefault();

                const source_url = $("#job-source").val();

                $.ajax({
                    url: "{{ route('blog-poster.jobs.source_icon') }}",
                    data: {
                        source_url: source_url
                    }
                }).done(function (response){
                    if(response.url !== false) {
                        $("#job-icon").val(response.url)
                    }
                })
            })
        })
    </script>
@endsection
