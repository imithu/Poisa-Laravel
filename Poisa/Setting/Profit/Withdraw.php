<?php
namespace Poisa\Setting\Profit;

use Illuminate\Support\Facades\DB;



class Withdraw
{
  /**
   * set Profit of each withdraw in percent
   * 
   * @param float   $percent
   * 
   * @since   1.2.0
   * @version 1.2.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function set( float $percent )
  {
    date_default_timezone_set('UTC');


    DB::table('Poisa_Setting')->updateOrInsert(
      [
        'meta_key'=>'Profit__Withdraw__admin_percent'
      ],
      [
        'meta_value'=>$percent,
        'datetime'=>date('Y-m-d H:i:s')
      ]
    );
  }




  /**
   * get Profit of each withdraw in percent
   * 
   * @return float   $percent
   *                 return 0 if fail
   * 
   * @since   1.2.0
   * @version 1.2.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function get()
  {
    return (float) DB::table('Poisa_Setting')->where('meta_key','Profit__Withdraw__admin_percent')->value('meta_value');
  }
}