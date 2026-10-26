{{-- Per-language animation name for the mobile app. Empty fields fall back to English. --}}
@php
    $locales = array_diff_key(config('app.api_locale_names'), ['en' => '']);
@endphp
<div class="col-md-12 mt-2">
    <h5 class="mb-1">{{ __('Translations') }}</h5>
    <p class="text-muted small mb-3">{{ __('Optional. Any language left empty shows the English name in the app.') }}</p>
    <div class="row">
        @foreach ($locales as $locale => $label)
            <div class="mb-4 col-md-4">
                <label class="form-label" for="tr-{{ $locale }}-name">{{ __('Animation Name') }} ({{ __($label) }})</label>
                <input class="form-control @error("translations.$locale.name") is-invalid @enderror" type="text"
                    id="tr-{{ $locale }}-name" name="translations[{{ $locale }}][name]" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}"
                    value="{{ old("translations.$locale.name", isset($animation) ? $animation->translation($locale)?->name : null) }}" />
                @error("translations.$locale.name")
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
        @endforeach
    </div>
</div>
