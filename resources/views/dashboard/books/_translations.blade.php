{{-- Per-language name, description and PDF for the mobile app. Empty fields fall back to English. --}}
@php
    $locales = array_diff_key(config('app.api_locale_names'), ['en' => '']);
@endphp
<div class="col-md-12 mt-2">
    <h5 class="mb-1">{{ __('Translations') }}</h5>
    <p class="text-muted small mb-3">{{ __('Optional. Any language left empty shows the English name, description and PDF in the app.') }}</p>

    <ul class="nav nav-tabs" role="tablist">
        @foreach ($locales as $locale => $label)
            <li class="nav-item">
                <button type="button" class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#book-tr-{{ $locale }}" role="tab">{{ __($label) }}</button>
            </li>
        @endforeach
    </ul>
    <div class="tab-content border border-top-0 rounded-bottom p-4">
        @foreach ($locales as $locale => $label)
            @php($translation = isset($book) ? $book->translation($locale) : null)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="book-tr-{{ $locale }}" role="tabpanel"
                dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
                <div class="row">
                    <div class="mb-4 col-md-12">
                        <label class="form-label" for="tr-{{ $locale }}-name">{{ __('Book Name') }} ({{ __($label) }})</label>
                        <input class="form-control @error("translations.$locale.name") is-invalid @enderror" type="text"
                            id="tr-{{ $locale }}-name" name="translations[{{ $locale }}][name]"
                            value="{{ old("translations.$locale.name", $translation?->name) }}" />
                        @error("translations.$locale.name")
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="mb-4 col-md-12">
                        <label class="form-label" for="tr-{{ $locale }}-description">{{ __('Description') }} ({{ __($label) }})</label>
                        <textarea class="form-control @error("translations.$locale.description") is-invalid @enderror" rows="3"
                            id="tr-{{ $locale }}-description" name="translations[{{ $locale }}][description]">{{ old("translations.$locale.description", $translation?->description) }}</textarea>
                        @error("translations.$locale.description")
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    <div class="mb-2 col-md-12" dir="ltr">
                        <label class="form-label" for="tr-{{ $locale }}-pdf">{{ __('PDF') }} ({{ __($label) }})</label>
                        <input class="form-control @error("translation_pdfs.$locale") is-invalid @enderror" type="file"
                            id="tr-{{ $locale }}-pdf" name="translation_pdfs[{{ $locale }}]" accept="application/pdf,.pdf" />
                        @error("translation_pdfs.$locale")
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                        @if ($translation?->book_pdf)
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <a class="btn btn-link px-0" href="{{ asset($translation->book_pdf) }}" target="_blank">{{ __('View PDF') }}</a>
                                <label class="form-check-label small">
                                    <input class="form-check-input me-1" type="checkbox" name="remove_translation_pdf[{{ $locale }}]" value="1">
                                    {{ __('Remove this PDF') }}
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
