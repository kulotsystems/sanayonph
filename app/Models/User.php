<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'snyn_users';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'name_suffix',
        'gender',
        'date_of_birth',
        'email',
        'email_verified_at',
        'mobile',
        'mobile_verified_at',
        'username',
        'password',
        'avatar',
        'gcash_name',
        'gcash_number',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'gcash_name',
        'gcash_number',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'name',
        'store'
    ];


    /****************************************************************************************************
     * User's delivery addresses
     *
     * @return HasMany
     */
    public function delivery_addresses()
    {
        return $this->hasMany(DeliveryAddress::class);
    }


    /****************************************************************************************************
     * User's plain delivery addresses
     *
     * @return array
     */
    public function plain_delivery_addresses()
    {
        $delivery_addresses = [];
        foreach ($this->delivery_addresses as $delivery_address) {
            array_push($delivery_addresses, $delivery_address->only('id', 'address'));
        }
        return $delivery_addresses;
    }


    /****************************************************************************************************
     * User's stores
     *
     * @return HasMany
     */
    public function stores()
    {
        return $this->hasMany(Store::class);
    }


    /****************************************************************************************************
     * User's orders
     *
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    /****************************************************************************************************
     * User's full name
     *
     */
    public function getNameAttribute()
    {
        $name = [
            'full_name_1' => '', // MR. JUAN R. DELA CRUZ JR.
            // 'full_name_2' => '', // MR. JUAN REYES DELA CRUZ JR.
            // 'full_name_3' => '', // DELA CRUZ, JUAN R., JR.
            // 'full_name_4' => '', // DELA CRUZ, JUAN REYES, JR.
        ];
        $name['full_name_1'] .= $this->first_name;
        // $name['full_name_2'] .= $this->first_name;
        // $name['full_name_3'] .= $this->last_name . ', ' . $this->first_name;
        // $name['full_name_4'] .= $name['full_name_3'];

        if($this->middle_name != '') {
            $middle_initial = substr($this->middle_name, 0, 1);
            $name['full_name_1'] .= ' ' . $middle_initial . '.';
            // $name['full_name_2'] .= ' ' . $this->middle_name;
            // $name['full_name_3'] .= ' ' . $middle_initial . '.';
            // $name['full_name_4'] .= ' ' . $this->middle_name;
        }

        $name['full_name_1'] .= ' ' . $this->last_name;
        // $name['full_name_2'] .= ' ' . $this->last_name;
        if($this->name_suffix != '') {
            $name['full_name_1'] .= ' '  . $this->name_suffix;
            // $name['full_name_2'] .= ' '  . $this->name_suffix;
            // $name['full_name_3'] .= ', ' . $this->name_suffix;
            // $name['full_name_4'] .= ', ' . $this->name_suffix;
        }

        return $name;
    }


    /****************************************************************************************************
     * User's default store
     *
     * @return Store
     */
    public function getStoreAttribute()
    {
        return Store::where('user_id', $this->id)->where('slug', 'store')->first();
    }


    /****************************************************************************************************
     * User's seller information
     *
     * @return array
     */
    public function seller_info()
    {
        $info  = $this->only('id', 'username', 'name', 'avatar');
        $store = $this->store;
        if($store->name != null)
            $info['name']['full_name_1'] = $store->name;
        if($store->avatar != null)
            $info['avatar'] = $store->avatar;
        return $info;
    }


    /****************************************************************************************************
     * User's gcash information
     *
     * @return array
     */
    public function gcash_info()
    {
        return $this->only('gcash_name', 'gcash_number');
    }
}
