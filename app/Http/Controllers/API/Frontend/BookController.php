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

            $books = Book::where('is_active', 'active')->get();

            // Convert stored paths to full URLs; missing files stay null instead of the bare site URL.
            $books = $books->map(function ($item) {
                $pdfPath = $item->book_pdf ? public_path($item->book_pdf) : null;

                $item->image = $item->image ? url($item->image) : null;
                $item->pdf_size = $pdfPath && File::exists($pdfPath) ? File::size($pdfPath) : null;
                $item->book_pdf = $item->book_pdf ? url($item->book_pdf) : null;
                return $item;
            });

            return response()->json([
                'books' => $books,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Get Books Data failed', ['error' => $th->getMessage()]);
            return response()->json([
                'message' => 'Something went wrong!'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
