<?php
namespace Poisa;


use Illuminate\Support\Facades\DB;
use Poisa\Balance;


class Withdraw
{
  /**
   * request a manual withdraw
   * 
   * @param int   $user_id
   * @param float $amount
   * 
   * @since   1.1.0
   * @version 1.1.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function request_manual( int $user_id, float $amount )
  {
      date_default_timezone_set('UTC');
      $amount_current = Balance::amount($user_id);

      if($amount<=$amount_current){
        Balance::change( $user_id, -$amount );
        DB::table('Poisa_Withdraw')->insert(
          [
            'user_id' => $user_id,
            'amount' => $amount,
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
   * @version 1.1.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function reject( int $id )
  {
    $reject_amount = (float) DB::table('Poisa_Withdraw')->where('id', $id)->value('amount');
    $reject_user_id = (int) DB::table('Poisa_Withdraw')->where('id', $id)->value('user_id');
    DB::table('Poisa_Withdraw')->where('id', $id)->update(
      [
        'method'=>'reject'
      ]
    );
    Balance::change( $reject_user_id, $reject_amount );
  }




  /**
   * accept a withdraw
   * 
   * @param int   $id
   * 
   * @since   1.1.0
   * @version 1.1.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function accept( int $id )
  {
    DB::table('Poisa_Withdraw')->where('id', $id)->update(
      [
        'method'=>'accept'
      ]
    );
  }
}