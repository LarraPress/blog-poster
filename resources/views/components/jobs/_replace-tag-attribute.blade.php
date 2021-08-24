@php
    $selectorToReplaceAttribute = $selectorToReplaceAttribute ?? null;
    $replacingAttribute = $replacingAttribute ?? null;
    $attributeToCopyFrom = $attributeToCopyFrom ?? null;
@endphp

<div class="configurator-replacing-attribute col-md-12" style="border: 1px solid #e3e3e3; margin-bottom: 5px; margin-top: 5px; overflow: hidden; padding: 10px">
    <div class="col-md-3">
        <label>Selector to replace attribute</label>
        <input type="text" class="form-control configurator-selector-to-replace-attribute" value="{{ $selectorToReplaceAttribute }}">
    </div>
    <div class="col-md-3">
        <label>Replacing attribute</label>
        <input type="text" class="form-control configurator-replacing-attribute-value" value="{{ $replacingAttribute }}">
    </div>
    <div class="col-md-3">
        <label>Attribute to get value from</label>
        <input type="text" class="form-control configurator-attribute-to-get-value-from" value="{{ $attributeToCopyFrom }}">
    </div>
    <div class="col-md-6">
        <a class="btn btn-danger configurator-replacing-attribute-delete">Remove Replacing Attribute</a>
    </div>
</div>
