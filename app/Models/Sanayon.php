<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sanayon extends Model
{

    /****************************************************************************************************
     * Sanayon service fees
     * Note: max_order must be sorted in ascending order
     *
     * @return array
     */
    public static function service_fees()
    {
        return [
            [
                'max_order' => 299,
                'percent'   => 2
            ],
            [
                'max_order' => 599,
                'percent'   => 3
            ],
            [
                'max_order' => 899,
                'percent'   => 4
            ],
            [
                'max_order' => PHP_INT_MAX,
                'percent'   => 5
            ]
        ];
    }


    /****************************************************************************************************
     * Sanayon shipping fees
     * Note: max_quantity must be sorted in ascending order
     * Supported operations: =, *
     *
     * @return array
     */
    public static function shipping_fees()
    {
        return [
            [
                'max_quantity' => 1,
                'operation'    => '=',
                'amount'       => 50
            ],
            [
                'max_quantity' => 2,
                'operation'    => '=',
                'amount'       => 48
            ],
            [
                'max_quantity' => 3,
                'operation'    => '=',
                'amount'       => 46
            ],
            [
                'max_quantity' => 4,
                'operation'    => '=',
                'amount'       => 44
            ],
            [
                'max_quantity' => 5,
                'operation'    => '=',
                'amount'       => 42
            ],
            [
                'max_quantity' => 6,
                'operation'    => '=',
                'amount'       => 40
            ],
            [
                'max_quantity' => 7,
                'operation'    => '=',
                'amount'       => 38
            ],
            [
                'max_quantity' => 8,
                'operation'    => '=',
                'amount'       => 36
            ],
            [
                'max_quantity' => 9,
                'operation'    => '=',
                'amount'       => 34
            ],
            [
                'max_quantity' => 10,
                'operation'    => '=',
                'amount'       => 32
            ],
            [
                'max_quantity' => 10,
                'operation'    => '=',
                'amount'       => 30
            ]/*,
            [
                'max_quantity' => PHP_INT_MAX,
                'operation'    => '*',
                'amount'       => 2
            ]*/
        ];
    }
}
