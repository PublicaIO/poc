<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Book;
use App\User;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    /**
     * User dashboard
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        if (Auth::user()->hasRole('author')) {
            $user = Auth::user();
            $books = Book::where('user_id', $user->id)->whereNotIn('status', [Book::STATUS_CANCELED])->orderBy('status', 'ASC')->get();

            return view('authors.dashboard', compact('user', 'books'));
        }

        if (Auth::user()->hasRole('reader')) {
            $bookId = $_COOKIE['last_book_viewed'] ?? null;

            $lastViewedBook = Book::find($bookId);
            $purchasedBooks = Book::select(DB::raw('books.*, SUM(purchased_book_user.amount) as copies_purchased'))
                ->join('purchased_book_user', 'books.id', '=', 'purchased_book_user.book_id')
                ->where('purchased_book_user.user_id', Auth::user()->id)
                ->orderBy('purchased_book_user.id', 'desc')
                ->groupBy('purchased_book_user.id', 'purchased_book_user.book_id', 'books.id')
                ->get();

            return view('authors.dashboard', compact('lastViewedBook', 'purchasedBooks'));
        }
    }
}
