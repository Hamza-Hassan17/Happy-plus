<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    public function getBooksData(Request $request)
    {
        try {
            $user = $request->user();

            $locale = app()->getLocale();
            $books = Book::with('translations')->where('is_active', 'active')->get();

            // Serve the requested language, falling back to English per field; missing files stay null.
            $books = $books->map(function ($item) use ($locale) {
                $translation = $locale === 'en' ? null : $item->translation($locale);
                $englishPdf = $item->book_pdf;
                $pdf = $translation?->book_pdf ?: $englishPdf;
                $pdfPath = $pdf ? public_path($pdf) : null;

                $item->name = $translation?->name ?: $item->name;
                $item->description = $translation?->description ?: $item->description;
                $item->image = $item->image ? url($item->image) : null;
                $item->pdf_size = $pdfPath && File::exists($pdfPath) ? File::size($pdfPath) : null;
                $item->book_pdf = $pdf ? url($pdf) : null;
                $item->pdf_language = $pdf ? ($translation?->book_pdf ? $locale : 'en') : null;
                $item->pdf_languages = collect($englishPdf ? ['en'] : [])
                    ->merge($item->translations->whereNotNull('book_pdf')->pluck('locale'))
                    ->values();
                $item->language = $locale;
                $item->unsetRelation('translations');
                return $item;
            });

            return response()->json([
                'books' => $books,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Get Books Data failed', ['error' => $th->getMessage()]);
            return response()->json([
                'message' => __('Something went wrong!')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
