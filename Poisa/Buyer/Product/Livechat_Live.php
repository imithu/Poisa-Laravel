<?php
namespace Poisa\Buyer\Product;


use Illuminate\Support\Facades\DB;
use Poisa\Balance;


class Livechat_Live
{
  /**
   * add livechat history      and      change the balance of buyer and seller
   * 
   * |__________________________
   * @param int    $buyer_id
   * @param int    $seller_id
   * @param string $name_livechat
   * 
   * @param string $name_product
   * @param float  $price
   * @param int    $duration
   * __________________________/
   * 
   * @return bool  true  - success
   *               false - fail
   * ________________________________/
   * 
   * 
   * 
   * @since   1.6.0
   * @version 1.6.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function add( int $buyer_id, int $seller_id, string $name_livechat, string $name_product, float $price, int $duration )
  {
    if( Balance::amount($buyer_id)>=$price ){
      date_default_timezone_set('UTC');
  
      DB::table('Poisa_Buyer_Product_Livechat_Live')->insert(
        [
          'buyer_id'=>$buyer_id,
          'seller_id'=>$seller_id,
          'name_livechat'=>htmlspecialchars(trim($name_livechat)),
  
          'name_product'=>htmlspecialchars(trim($name_product)),
          'price'=>$price,
          'duration'=>$duration,
  
          'status'=>'done',
          'datetime'=>date('Y-m-d H:i:s')
        ]
      );
  
  
      // take money from buyer and give it to seller
        Balance::change( $buyer_id, -$price );
        Balance::change( $seller_id, $price );
      return true;
    }

    return false;
  }
}