<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Auth;
use App\User;

class BooksController extends Controller
{

    /**
     * Create new crowdfunding campaign
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $author = User::find(Auth::id());

        return view('book.create-book', compact('author'));
    }

    /**
     * save book crowdfunding campaign
     *
     * @param Request $request
     * @return Book
     */
    public function save(Request $request)
    {
        $request->validate([
            'title'                 => 'required',
            'url'                   => 'nullable|string|unique:books,url',
            'short_description'     => 'nullable',
            'promotion_text'        => 'nullable',
            'goal'                  => 'required|numeric',
            'price_for_crowdsale'   => 'required|numeric',
            'soft_cap'              => 'required|numeric',
            'duration'              => 'required|numeric',
            'price_after_crowdsale' => 'required|numeric',
            'aftersale_keys_amount' => 'required|numeric',
            'crowdsale_start_date'  => 'required|date',
            'cover_art'             => 'nullable|image'
        ]);

        if ($request->id) {
            $book = Book::findOrFail($request->id);
        } else {
            $book = new Book();
            $book->status = Book::STATUS_PENDING;
        }

        $book->title = $request->title;
        $book->url = ltrim(parse_url($request->url, PHP_URL_PATH), '/');
        $book->short_description = $request->short_description;
        $book->promotion_text = $request->promotion_text;
        $book->goal = $request->goal;
        $book->price_for_crowdsale = $request->price_for_crowdsale;
        $book->soft_cap = $request->soft_cap;
        $book->soft_cap_description = $request->soft_cap_description;
        $book->duration = $request->duration;
        $book->price_after_crowdsale = $request->price_after_crowdsale;
        $book->aftersale_keys_amount = $request->aftersale_keys_amount;
        $book->crowdsale_start_date = $request->crowdsale_start_date;
        $book->user_id = Auth::id();

        if ($file = $request->file('cover_art')) {
            $book->cover_art = $file->storeAs(
                'images/' . $book->id, str_slug($book->title) . '.' . $file->getClientOriginalExtension(),
                'public'
            );
        }

        $book->save();

        return $book;
    }

    /**
     * Method for updating book status
     *
     * @param Request $request
     * @return bool
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'status' => 'required'
        ]);

        $book = Book::findOrFail($request->id);

        if ($book->author->id == Auth::id()) {
            $book->status = $request->status;
            $book->save();
            return $book;
        }

        return false;
    }

    /**
     * Save book crowdfunding smart contract transaction id and address
     * @param Request $request
     * @return Book
     */
    public function saveContract(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);

        $book = Book::findOrFail($request->id);
        $book->contract_transaction = $request->contract_transaction ?: $book->contract_transaction;
        $book->contract_address = $request->contract_address ?: $book->contract_address;
        $book->status = Book::STATUS_CROWDSALE_STARTED;
        $book->crowdsale_start_date = Carbon::now('UTC');
        $book->save();

        $response['redirect'] = url('/' . $book->url);
        $response['book'] = $book;

        return $response;
    }

    /**
     * crowdfunding campaign view
     *
     * @param $url
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($url)
    {
        $book = Book::where('url', $url)->firstOrFail();
        $author = User::find($book->user_id);
        $purchases = Auth::user() ? Auth::user()->purchasedBooks($book->id)->get() : [];

        //Don't display pending or canceled book to reader/guest
        if (($book->status == Book::STATUS_PENDING || $book->status == Book::STATUS_CANCELED) && Auth::id() !== $book->author->id) {
            abort(404);
        }

        return view('book.view-book', compact('book', 'author', 'purchases'));
    }

    /**
     * Crowdfunding edit view
     *
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function showEdit($id)
    {
        $book = Book::findOrFail($id);
        $author = $book->author;

        if ($author->id != Auth::id()) {
            return response('Book Not Found', 404);
        }

        return view('book.create-book', compact('book', 'author'));

    }

    /**
     * active campaign list view (used for development to quickly locate catalog)
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function all()
    {
        $books = Book::where('status', Book::STATUS_CROWDSALE_STARTED)->get();

        return view('book.list', compact('books'));
    }

    /**
     * Save book purchase
     *
     * @param Request $request
     * @return mixed
     */
    public function buy(Request $request)
    {
        $book = Book::findOrFail($request->book_id);

        Auth::user()->purchasedBooks()->save($book, [
            'transaction_hash' => $request->transaction_hash,
            'amount' => $request->amount,
            'pbl_amount' => $request->pbl_amount
        ]);

        $book->copies_purchased = $request->amount;

        return $book;
    }

    /**
     * Handle book publishing- upload file to aws as non public, generate checksum, put checksum in contract
     *
     * @param Request $request
     * @return mixed
     */
    public function upload(Request $request)
    {
        $book = Book::findOrFail($request->id);

        if (Auth::id() == $book->author->id && $book->status == Book::STATUS_CROWDSALE_ENDED) {
            $file = $request->file('book_file');
            $filename = time() . '_' . $file->getClientOriginalName();

            Storage::disk('s3')->putFileAs('', $file, $filename);

            $book->aws_path = $filename;
            $book->md5 = md5_file($file); //@TODO this later should be added to smart contract instead of saving in database
            $book->status = Book::STATUS_PUBLISHED;
            $book->save();

            return ['success' => true];
        }
    }

    /**
     * Serve file if user have permissions
     *
     * @param $id
     * @return array
     */
    public function download($id)
    {
        $book = Book::findOrFail($id);
        $bookModel = new Book();
        $response = [];

        if ($book->owners->contains(Auth::id()) && $bookModel->userHaveReadToken(Auth::user(), $book)) {
            $response['download_link']['url'] = Storage::disk('s3')->temporaryUrl($book->aws_path, Carbon::now()->addMinutes(5));
            $response['download_link']['expiry'] = 300;

        }

        return $response;
    }

    /**
     * book reader view
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function read($id)
    {
        $book = Book::findOrFail($id);
        return view('book.reader', compact('book'));
    }

    /**
     * Handle book download request
     *
     * @param $id
     * @return mixed
     */
    public function getDownloadUrl($id)
    {
        $book = Book::findOrFail($id);

        if ($book->owners->contains(Auth::id())) {
            return Storage::disk('s3')->temporaryUrl($book->aws_path, Carbon::now()->addMinutes(5));
        }

        abort(404);
    }
}
