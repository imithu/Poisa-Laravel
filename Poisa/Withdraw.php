<?php
namespace Poisa;


use Illuminate\Support\Facades\DB;
use Poisa\Balance;
use Poisa\Setting\Profit\Withdraw as Profit_Withdraw;


class Withdraw
{
  /**
   * request a manual withdraw
   * 
   * @param int   $user_id
   * @param float $amount
   * 
   * @since   1.1.0
   * @version 1.3.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function request_manual( int $user_id, float $amount )
  {
    date_default_timezone_set('UTC');
    $amount = round($amount, 2);
    $amount_current = Balance::amount($user_id);
    
    if($amount<=$amount_current){
      $admin_percent  = Profit_Withdraw::get();
      $admin_will_get = round(($amount*$admin_percent)/100, 2);
      $user_will_get  = $amount-$admin_will_get;

      Balance::change( $user_id, -$amount );
      DB::table('Poisa_Withdraw')->insert(
        [
          'user_id' => $user_id,

          'amount' => $amount,
          'admin_percent' => $admin_percent,
          'user_will_get' => $user_will_get,
          'admin_will_get' => $admin_will_get,

          'status' => 'request',
          'method' => 'manual',
          'datetime' => date('Y-m-d H:i:s')
        ]
      );
    }
  }




  /**
   * reject a withdraw
   * 
   * @param int   $id
   * 
   * @since   1.1.0
   * @version 1.3.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function reject( int $id )
  {
    $reject_user_id = (int) DB::table('Poisa_Withdraw')->where('id', $id)->value('user_id');
    $reject_amount  = (float) DB::table('Poisa_Withdraw')->where('id', $id)->value('amount');
    $reject_status  = DB::table('Poisa_Withdraw')->where('id', $id)->value('status');
    
    if($reject_user_id>0 && $reject_status==='request'){
      DB::table('Poisa_Withdraw')->where('id', $id)->update(
        [
          'status'=>'reject'
        ]
      );
      Balance::change( $reject_user_id, $reject_amount );
    }
  }




  /**
   * accept a withdraw
   * 
   * @param int   $id
   * 
   * @since   1.1.0
   * @version 1.3.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function accept( int $id )
  {
    DB::table('Poisa_Withdraw')->where('id', $id)->update(
      [
        'status'=>'accept'
      ]
    );
  }
}