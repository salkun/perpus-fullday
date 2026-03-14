<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%")
                  ->orWhere('author', 'like', "%{$request->search}%");
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        $books = $query->latest()->paginate($request->per_page ?? 12);

        return response()->json($books);
    }

    public function show(Book $book)
    {
        return response()->json($book);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'publisher'   => 'nullable|string|max:255',
            'year'        => 'nullable|integer|min:1900|max:' . date('Y'),
            'isbn'        => 'nullable|string|max:20|unique:books,isbn',
            'category'    => 'required|string|max:100',
            'description' => 'nullable|string',
            'stock'       => 'nullable|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'pdf_file'    => 'nullable|mimes:pdf|max:51200', // max 50MB
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('books/covers', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            $validated['pdf_file'] = $request->file('pdf_file')
                ->store('books/pdfs', 'public');
        }

        $book = Book::create($validated);

        return response()->json($book, 201);
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'publisher'   => 'nullable|string|max:255',
            'year'        => 'nullable|integer|min:1900|max:' . date('Y'),
            'isbn'        => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
            'category'    => 'required|string|max:100',
            'description' => 'nullable|string',
            'stock'       => 'nullable|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'pdf_file'    => 'nullable|mimes:pdf|max:51200',
        ]);

        if ($request->hasFile('cover_image')) {
            // Hapus cover lama
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')
                ->store('books/covers', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            // Hapus PDF lama
            if ($book->pdf_file) {
                Storage::disk('public')->delete($book->pdf_file);
            }
            $validated['pdf_file'] = $request->file('pdf_file')
                ->store('books/pdfs', 'public');
        }

        $book->update($validated);

        return response()->json($book);
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        if ($book->pdf_file) {
            Storage::disk('public')->delete($book->pdf_file);
        }

        $book->delete();

        return response()->json(['message' => 'Buku berhasil dihapus.']);
    }

    public function categories()
    {
        $categories = Book::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return response()->json($categories);
    }
}
