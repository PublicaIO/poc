<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    const STATUS_PENDING = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_CROWDSALE_STARTED = 3;
    const STATUS_CROWDSALE_ENDED = 4;
    const STATUS_PUBLISHED = 5;
    const STATUS_CANCELED = 6;

    static $statuses = [
        1 => 'Campaign: Not Active', //pending
        2 => 'Campaign: Active',
        3 => 'Crowdfunding: In Progress',
        4 => 'Crowdfunding: Ended',
        5 => 'Published: Yes',
        6 => 'Canceled',
    ];

    protected $dates = [
        'crowdsale_start_date',
    ];

    protected $hidden = [
        'aws_path',
    ];

    protected $appends = [
        'download_url',
        'cover_art_url',
        'status_label',
        'sold_keys',
        'received_pbl',
    ];

    /**
     * relation to User (author)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    /**
     * Relation to purchases
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function owners()
    {
        return $this->belongsToMany('App\User', 'purchased_book_user', 'book_id', 'user_id')->withPivot('pbl_amount');
    }

    /**
     * append download url
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getDownloadUrlAttribute()
    {
        if (isset($this->aws_path) && $this->owners->contains(Auth::id())) {
            return url('/book/download', $this->id);
        }
    }

    /**
     * return file url
     *
     * @return null
     */
    public function getCoverArtUrlAttribute()
    {
        if (isset($this->cover_art)) {
            return Storage::url($this->cover_art);
        }

        return '/img/book-cover-placeholder.png';
    }

    /**
     * status to label conversion
     *
     * @return mixed
     */
    public function getStatusLabelAttribute()
    {
        if (isset($this->status)) {
            return self::$statuses[$this->status];
        }
    }

    /**
     * Sold keys amount
     *
     * @return int
     */
    public function getSoldKeysAttribute()
    {
        return count($this->owners);
    }

    /**
     * total amount of received PBLs for current book
     * @return int
     */
    public function getReceivedPblAttribute()
    {
        $sum = 0;
        foreach ($this->owners as $owner) {
            $sum += $owner->pivot->pbl_amount;
        }

        return $sum;
    }

    /**
     * method for checking smart contract if user have read token of boook and is allowed to download it
     *
     * @param $user
     * @param $book
     * @return bool
     */
    public function userHaveReadToken($user, $book)
    {
        return true;
    }
}
