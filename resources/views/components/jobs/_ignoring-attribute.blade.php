@php
    $ignoringAttribute = $ignoringAttribute ?? null
@endphp

<div class="configurator-ignoring-attribute col-md-12" style="border: 1px solid #e3e3e3; margin-bottom: 5px; margin-top: 5px; overflow: hidden; padding: 10px">
    <div class="col-md-6">
        <label>Ignoring Attribute Selector</label>
        <input type="text" class="form-control configurator-ignoring-attribute-selector" value="{{ $ignoringAttribute }}">
    </div>
    <div class="col-md-6">
        <a class="btn btn-danger configurator-ignoring-attribute-delete">Remove Ignoring Attribute</a>
    </div>
</div>
