<?php
namespace Poisa\Seller\Product;


use Illuminate\Support\Facades\DB;



class Subscription
{
  /**
   * add unique subscription
   * 
   * @param int    $seller_id
   * @param string $name_subscription ( must be unique )
   * @param string $name_product
   * @param float  $price
   * @param int    $duration
   * 
   * @since   1.0.0
   * @version 1.6.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function add( int $seller_id, string $name_subscription, string $name_product, float $price, int $duration )
  {
    if(self::product_id($seller_id, $name_subscription)===0){
      date_default_timezone_set('UTC');
      $price = round($price, 2);

      DB::table('Poisa_Seller_Product_Subscriptions')->insert(
        [
          'seller_id'=>$seller_id,
          'name_subscription'=>htmlspecialchars(trim($name_subscription)),
          'name_product'=>htmlspecialchars(trim($name_product)),
          'price'=>$price,
          'duration'=>$duration,
          'status'=>'active',
          'datetime'=>date('Y-m-d H:i:s')
        ]
      );
    }
  }




  /**
   * get id of a subscription product
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
  public static function product_id( int $seller_id, string $name_subscription )
  {
    $product_id = DB::table('Poisa_Seller_Product_Subscriptions')
            ->where('seller_id', $seller_id)
            ->where('name_subscription', htmlspecialchars(trim($name_subscription)))
            ->value('id');
    if($product_id===NULL)
      return (int) 0;
    return $product_id;
  }
}