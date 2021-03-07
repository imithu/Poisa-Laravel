<?php
namespace Poisa;


use Illuminate\Support\Facades\DB;


class Balance
{
    /**
     * increment or decrement the current amount of an user
     * 
     * @param int   $user_id
     * @param float $amount
     * 
     * @since   1.0.0
     * @version 1.0.0
     * @author  Mahmudul Hasan Mithu
     */
    public static function change( int $user_id, float $amount )
    {
        date_default_timezone_set('UTC');
        $amount_current = DB::table('Poisa_Balance')->where('user_id', $user_id)->value('amount');

        if($amount_current===NULL){
            DB::table('Poisa_Balance')->insert(
                [
                    'user_id'=>$user_id,
                    'amount'=>$amount,
                    'datetime'=>date('Y-m-d H:i:s')
                ]
            );
        }else{
            $amount_new = $amount_current+$amount;
            DB::table('Poisa_Balance')->where('user_id',$user_id)->update(
                [
                    'amount'=>$amount_new,
                    'datetime'=>date('Y-m-d H:i:s')
                ]
            );
        }
    }


    /**
     * return current amount of an user
     * 
     * @param int    $user_id
     * 
     * @return float current amount
     * 
     * @since   1.0.0
     * @version 1.0.0
     * @author  Mahmudul Hasan Mithu
     */
    public static function amount( int $user_id )
    {
        $amount_current = DB::table('Poisa_Balance')->where('user_id', $user_id)->value('amount');

        if($amount_current===NULL){
            self::change( $user_id, 0.00 );
            return (float) 0.00;
        }

        return (float) $amount_current;
    }
}