<div class="card">
    <div class="card-header pb-0">
        <div class="row">
            <div class="col-lg-6 col-7">
                <h4>{{ $attribute_name ?? '%attribute_name%' }}</h4>
            </div>
        </div>
    </div>
    <div class="card-body px-3">
        <div class="row">
            {{ $attribute_value ?? '%attribute_value%' }}
        </div>
    </div>
</div>
