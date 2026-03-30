{{-- Shared India location (API: countriesnow + postalpincode.in). IDs: #state, #city, #pincode, #country --}}
@php
    $locStateVal = old('state', $user->state ?? '');
    $locCityVal = old('city', $user->city ?? '');
    $locZipVal = old('zip', $user->zip ?? '');
@endphp
<div class="form-group col-12 col-sm-6 col-md-6">
    <label class="form-label">@lang('State')</label>
    <div class="position-relative">
        <select name="state" id="state" class="form-select form--control">
            <option value=""></option>
        </select>
    </div>
</div>
<div class="form-group col-12 col-sm-6 col-md-6">
    <label class="form-label">@lang('City')</label>
    <div class="position-relative">
        <select name="city" id="city" class="form-select form--control">
            <option value=""></option>
        </select>
    </div>
</div>
<div class="form-group col-12 col-sm-6 col-md-6">
    <label class="form-label">@lang('Zip / Pincode')</label>
    <input type="text" class="form-control form--control" name="zip" id="pincode" value="{{ $locZipVal }}"
        maxlength="12" inputmode="numeric" autocomplete="postal-code"
        placeholder="@lang('Enter 6-digit pincode to auto-fill')">
</div>
<div class="form-group col-12 col-sm-6 col-md-6">
    <label class="form-label">@lang('Country')</label>
    <input type="hidden" name="country" value="India">
    <input type="hidden" name="country_code" id="country_code" value="IN">
    <input type="text" id="country" class="form-control form--control" value="India" readonly>
</div>
