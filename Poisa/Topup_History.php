<?php
namespace Poisa;


use Illuminate\Support\Facades\DB;


class Topup_History
{
  /**
   * insert topup history
   * 
   * @param int    $user_id
   * @param float  $amount
   * @param string $method
   * 
   * @since   1.5.0
   * @version 1.5.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function insert( int $user_id, float $amount, string $method )
  {
    date_default_timezone_set('UTC');
    $amount = round($amount, 2);
    
    DB::table('Poisa_Topup')->insert(
      [
        'user_id' => $user_id,

        'amount' => $amount,

        'status' => 'done',
        'method' => $method,
        'datetime' => date('Y-m-d H:i:s')
      ]
    );
  }
}
