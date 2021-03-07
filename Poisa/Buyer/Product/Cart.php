<?php
namespace Poisa\Buyer\Product;


use Illuminate\Support\Facades\DB;
use Poisa\Balance;

/**
 * work with only pending product
 * 
 */
class Cart
{

    /**
     * get all pending product list
     * 
     * @param int $buyer_id
     * 
     * @return array pending product list
     * 
     * @since   1.0.0
     * @version 1.0.0
     * @author  Mahmudul Hasan Mithu
     */
    public static function all_get( int $buyer_id )
    {
        return DB::table('Poisa_Buyer_Product_Subscriptions')
                    ->where('buyer_id', $buyer_id)
                    ->where('status', 'pending')
                    ->get();
    }
    



    /**
     * delete a pending product
     * 
     * @param int $product_id
     * @param int $buyer_id
     * 
     * 
     * @since   1.0.0
     * @version 1.0.0
     * @author  Mahmudul Hasan Mithu
     */
    public static function delete( int $product_id, int $buyer_id )
    {
        DB::table('Poisa_Buyer_Product_Subscriptions')
                ->where('id', $product_id)
                ->where('buyer_id', $buyer_id)
                ->delete();
    }


    
    /**
     * buy all product
     * 
     * @param int $buyer_id
     * 
     * @since   1.0.0
     * @version 1.0.0
     * @author  Mahmudul Hasan Mithu
     */
    public static function all_buy( int $buyer_id )
    {
        // find out buyer balance    and      total price of all products
            $buyer_balance = Balance::amount($buyer_id);
            $product_total_price = 0;

            $product_all = self::all_get($buyer_id);
            foreach( $product_all as $product ){
                $product_total_price += $product->price;
            }

        // if buyer has enough balance then pay the seller
        if($buyer_balance>=$product_total_price){
            date_default_timezone_set('UTC');
            foreach( $product_all as $product ){
                // mark pending subscription product as active
                    DB::table('Poisa_Buyer_Product_Subscriptions')
                        ->where('id', $product->id)
                        ->update(
                            [
                                'status'=>'active',
                                'datetime'=>date('Y-m-d H:i:s')
                            ]
                        );
                
                // pay the seller         and        reduce the balance from buyer
                    Balance::change( $product->seller_id, $product->price );
                    Balance::change( $buyer_id, -$product->price );
            }
        }
    }
}