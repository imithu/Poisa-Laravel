<?php
namespace Poisa\Buyer\Product;


use Illuminate\Support\Facades\DB;



class Subscription
{
    /**
     * add unique subscription
     * 
     * |__________________________
     * @param int    $buyer_id
     * @param int    $seller_id
     * @param string $name_subscription ( must be unique )
     * 
     * @param string $name_product
     * @param float  $price
     * @param int    $duration
     * __________________________/
     * 
     * @since   1.0.0
     * @version 1.0.0
     * @author  Mahmudul Hasan Mithu
     */
    public static function add( int $buyer_id, int $seller_id, string $name_subscription, string $name_product, float $price, int $duration )
    {
        if(self::product_id($buyer_id, $seller_id, $name_subscription, 'active')===0 && self::product_id($buyer_id, $seller_id, $name_subscription, 'pending')===0){
            date_default_timezone_set('UTC');
            DB::table('Poisa_Buyer_Product_Subscriptions')->insert(
                [
                    'buyer_id'=>$buyer_id,
                    'seller_id'=>$seller_id,
                    'name_subscription'=>htmlspecialchars(trim($name_subscription)),

                    'name_product'=>htmlspecialchars(trim($name_product)),
                    'price'=>$price,
                    'duration'=>$duration,

                    'status'=>'pending',
                    'datetime'=>date('Y-m-d H:i:s')
                ]
            );
        }
    }




    /**
     * get id of a subscription product
     * 
     * @param int    $buyer_id
     * @param int    $seller_id
     * @param string $name_subscription
     * 
     * @return int    0   no subscription product
     *              value subscription product id
     * 
     * @since   1.0.0
     * @version 1.0.0
     * @author  Mahmudul Hasan Mithu
     */
    public static function product_id( int $buyer_id, int $seller_id, string $name_subscription, string $status='pending' )
    {
        $product_id = DB::table('Poisa_Buyer_Product_Subscriptions')
                ->where('buyer_id', $buyer_id)
                ->where('seller_id', $seller_id)
                ->where('name_subscription', htmlspecialchars(trim($name_subscription)))
                ->where('status', $status)
                ->orderBy('id', 'desc')
                ->value('id');
        if($product_id===NULL)
            return (int) 0;
        return $product_id;
    }




    /**
     * check a buyer has subscribed in a subscription or not
     * 
     * @param int    $seller_id
     * @param string $name_subscription
     * 
     * @return int    0   no subscription product
     *              value subscription product id
     * 
     * @since   1.0.0
     * @version 1.0.0
     * @author  Mahmudul Hasan Mithu
     */
    public static function subscribed( int $buyer_id, int $seller_id, string $name_subscription )
    {
        $product_id = self::product_id( $buyer_id, $seller_id, $name_subscription, 'active' );
        if($product_id!==0){
            $product_duration = DB::table('Poisa_Buyer_Product_Subscriptions')->where('id', $product_id)->value('duration');
            $product_datetime = DB::table('Poisa_Buyer_Product_Subscriptions')->where('id', $product_id)->value('datetime');
            $product_status   = DB::table('Poisa_Buyer_Product_Subscriptions')->where('id', $product_id)->value('status');
            if( $product_status==='active' ){
                date_default_timezone_set('UTC');
                
                $unix_seconds_product_timeout = strtotime($product_datetime)+$product_duration;
                $unix_seconds_now = time();

                if( $unix_seconds_now<$unix_seconds_product_timeout ){
                    return true;
                }else{
                    DB::table('Poisa_Buyer_Product_Subscriptions')->where('id', $product_id)->update(['status'=>'done']);
                }
            }
        }

        return false;
    }
}