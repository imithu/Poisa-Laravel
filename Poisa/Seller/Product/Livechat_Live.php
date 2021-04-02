<?php
namespace Poisa\Seller\Product;


use Illuminate\Support\Facades\DB;



class Livechat_Live
{
  /**
   * add unique livechat product
   * 
   * @param int    $seller_id
   * @param string $name_livechat ( must be unique )
   * @param string $name_product
   * @param float  $price
   * @param int    $duration (in seconds)
   * 
   * @since   1.6.0
   * @version 1.6.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function add( int $seller_id, string $name_livechat, string $name_product, float $price, int $duration )
  {
    if(self::product_id($seller_id, $name_livechat)===0){
      date_default_timezone_set('UTC');
      $price = round($price, 2);
      DB::table('Poisa_Seller_Product_Livechat_Live')->insert(
        [
          'seller_id'=>$seller_id,
          'name_livechat'=>htmlspecialchars(trim($name_livechat)),
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
   * get id of a livechat product
   * 
   * @param int    $seller_id
   * @param string $name_livechat
   * 
   * @return int    0   no livechat product
   *              value livechat product id
   * 
   * @since   1.6.0
   * @version 1.6.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function product_id( int $seller_id, string $name_livechat )
  {
    $product_id = DB::table('Poisa_Seller_Product_Livechat_Live')
            ->where('seller_id', $seller_id)
            ->where('name_livechat', htmlspecialchars(trim($name_livechat)))
            ->value('id');

    return ($product_id===NULL) ? (int) 0 : $product_id;
  }




  /**
   * set new price of a livechat product
   * 
   * @param int   $product_id
   * @param float $price
   * 
   * 
   * @since   1.6.0
   * @version 1.6.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function price_new( int $product_id, float $price )
  {
    $price = round($price, 2);
    $product_id = DB::table('Poisa_Seller_Product_Livechat_Live')
            ->where('id', $product_id)
            ->update(['price'=>$price]);
  }
}