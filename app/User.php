<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'surname', 'password', 'provider', 'provider_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * OneToOne relation with AuthorInfo
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function info()
    {
        return $this->hasOne('App\AuthorInfo', 'user_id', 'id');
    }

    /**
     * OneToOne relation with Kyc data
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function kyc()
    {
        return $this->hasOne('App\Kyc', 'user_id', 'id');
    }

    /**
     * OneToMany relation with Books
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function books()
    {
        return $this->hasMany('App\Book', 'user_id', 'id');
    }

    /**
     * Many to many relations with purchased books
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function purchasedBooks($bookId = false)
    {
        $relationship = $this->belongsToMany('App\Book', 'purchased_book_user', 'user_id', 'book_id')->withPivot('transaction_hash', 'amount');

        if ($bookId) {
            $relationship->where('book_id', $bookId);
        }

        return $relationship;
    }

    /**
     * Method generates random avatar URL
     *
     * @return string
     */
    public function getAvatarAttribute()
    {
        $rand = md5($this->id);
        return "http://api.adorable.io/avatars/285/$rand.png";
    }
}
