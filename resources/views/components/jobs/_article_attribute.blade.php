<div class="card mt-5 configurator-attribute">
    <div class="card-header pb-0">
        <div class="row">
            <div style="width: fit-content">
                <h4 class="configurator-attribute-name-title">
                    {{ $config['name'] ?? 'Article Attribute Name'}}
                </h4>
                <hr class="horizontal dark my-3">
            </div>
        </div>
    </div>
    <div class="card-body px-3">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a href="#configurator-attribute-main-{{ isset($config) ? $config['name'].md5($config['name']) : '%attribute_ID%' }}" class="nav-link active" data-toggle="tab">Attribute Main Configs</a>
            </li>
            <li class="nav-item">
                <a href="#configurator-attribute-ignoring-{{ isset($config) ? $config['name'].md5($config['name']) : '%attribute_ID%' }}" class="nav-link" data-toggle="tab">Ignoring Elements</a>
            </li>
            <li class="nav-item">
                <a href="#configurator-attribute-replaces-{{ isset($config) ? $config['name'].md5($config['name']) : '%attribute_ID%' }}" class="nav-link" data-toggle="tab">Replacing Elements</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="configurator-attribute-main-{{ isset($config) ? $config['name'].md5($config['name']) : '%attribute_ID%' }}">
                <div class="col-md-12 bg-gray-100 overflow-auto p-3 border-radius-2xl">
                    <div class="col-md-4 float-lg-end">
                        <div class="form-switch ps-0">
                            <label>Is HTML</label>
                            <input class="form-check-input ms-auto configurator-attribute-is-html" type="checkbox" @if($config['is_html'] ?? false) checked @endif>
                        </div>
                    </div>
                    <div class="col-md-4 float-lg-end">
                        <div class="form-switch ps-0">
                            <label>Is File</label>
                            <input class="form-check-input ms-auto configurator-attribute-is-file" type="checkbox" @if($config['is_file'] ?? false) checked @endif>
                        </div>
                    </div>
                    <div class="col-md-4 float-lg-end">
                        <div class="form-switch ps-0">
                            <label>As Thumbnail</label>
                            <input class="form-check-input ms-auto configurator-attribute-as-thumbnail" type="checkbox" @if($config['as_thumb'] ?? false) checked @endif>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="col-md-3">
                        <label>Attribute Name</label>
                        <input type="text" class="configurator-attribute-name form-control" value="{{ $config['name'] ?? ''}}">
                    </div>
                    <div class="col-md-3">
                        <label>Attribute Selector</label>
                        <input type="text" class="configurator-attribute-selector form-control" value="{{ $config['selector'] ?? ''}}">
                    </div>
                    <div class="col-md-3">
                        <label>Attribute Type</label>
                        <select class="form-control configurator-attribute-type">
                            <option value="">default</option>
                            @foreach(\LarraPress\BlogPoster\Crawler\ArticleAttribute::getTypes() as $type)
                                <option @if(isset($config['type']) && $config['type'] === $type) selected @endif value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Custom Tag attribute</label>
                        <input type="text" class="configurator-attribute-custom-tag-attribute form-control" value="{{ $config['custom_tag'] ?? '' }}">
                    </div>
                </div>

                <a class="btn btn-danger configurator-attribute-delete">Remove Attribute</a>
            </div>
            <div class="tab-pane fade" id="configurator-attribute-ignoring-{{ isset($config) ? $config['name'].md5($config['name']) : '%attribute_ID%' }}">
                <div class="col-md-12 configurator-attribute-ignoring-attributes">
                <div class="col-md-12 configurator-attribute-ignoring-attributes-container">
                    @if(isset($config['ignoring_attributes']) && count($config['ignoring_attributes']) > 0)
                        @foreach($config['ignoring_attributes'] as $ignoringAttribute)
                            @include('blog-poster::components.jobs._ignoring-attribute', ['ignoring_attribute' => $ignoringAttribute])
                        @endforeach
                    @endif
                </div>

                    <a class="btn btn-success configurator-attribute-ignoring-attributes-add-new">Add Ignoring Attribute</a>
                </div>
            </div>
            <div class="tab-pane fade" id="configurator-attribute-replaces-{{ isset($config) ? $config['name'].md5($config['name']) : '%attribute_ID%' }}">
                <div class="col-md-12 configurator-attribute-replacing-attributes">
                    <div class="configurator-attribute-replacing-attributes-container">
                    @if(isset($config['replacing_attributes']) && count($config['replacing_attributes']) > 0)
                        @foreach($config['replacing_attributes'] as $replacingAttributes)
                            @include('blog-poster::components.jobs._replace-tag-attribute', [
                                                            'selectorToReplaceAttribute' => $replacingAttributes['selector'],
                                                            'replacingAttribute' => $replacingAttributes['replacing_attribute'],
                                                            'attributeToCopyFrom' => $replacingAttributes['attribute_to_get_value_from'],
                                                        ])
                        @endforeach
                    @endif
                    </div>

                    <a class="btn btn-success configurator-attribute-replacing-attributes-add-new">Add Replacing Attribute</a>
                </div>
            </div>
        </div>
    </div>
</div>
