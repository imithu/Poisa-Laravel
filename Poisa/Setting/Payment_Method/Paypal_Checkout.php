<?php
namespace Poisa\Setting\Payment_Method;

use Illuminate\Support\Facades\DB;



class Paypal_Checkout
{
  /**
   * set Paypal Checkout App Credential
   * 
   * 
   * @param string   $clientId
   * @param string   $clientSecret
   * @param string   $Environment    // sandbox || production
   * 
   * 
   * @since   1.2.0
   * @version 1.2.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function set( string $clientId, string $clientSecret, string $Environment )
  {
    date_default_timezone_set('UTC');
    $App_Credential = [
      'clientId'=> $clientId,
      'clientSecret'=> $clientSecret,
      'Environment'=> $Environment
    ];
    $App_Credential = json_encode($App_Credential);


    DB::table('Poisa_Setting')->updateOrInsert(
      [
        'meta_key'=>'Payment_Method__Paypal_Checkout__App_Credential'
      ],
      [
        'meta_value'=>$App_Credential,
        'datetime'=>date('Y-m-d H:i:s')
      ]
    );
  }




  /**
   * get Paypal Checkout App Credential with last updated info
   * 
   * 
   * @return string (json) - success - see  (i)
   *                       - fail    - see  (ii)
   * 
   * (i)
   * {
   *   "clientId": "value_est",
   *   "clientSecret": "value_est",
   *   "Environment": "value_est",
   *   "last_updated": {
   *     "datetime": "2397-09-13 08:48:12"
   *   }
   * }
   * 
   * 
   * (ii)
   * {
   *   "clientId": "",
   *   "clientSecret": "",
   *   "Environment": "",
   *   "last_updated": {
   *     "datetime": ""
   *   }
   * }
   * 
   * 
   * 
   * 
   * 
   * @since   1.2.0
   * @version 1.2.0
   * @author  Mahmudul Hasan Mithu
   */
  public static function get()
  {
    $App_Credential  = DB::table('Poisa_Setting')->where('meta_key','Payment_Method__Paypal_Checkout__App_Credential')->value('meta_value');
    $App_Credential = json_decode($App_Credential);
    $datetime = DB::table('Poisa_Setting')->where('meta_key','Payment_Method__Paypal_Checkout__App_Credential')->value('datetime');



    $SR = [
      'clientId'=>$App_Credential->clientId ?? "",
      'clientSecret'=>$App_Credential->clientSecret ?? "",
      'Environment'=>$App_Credential->Environment ?? "",
      'last_updated'=> 
        [
          'datetime'=>$datetime ?? ""
        ]
    ];

    echo json_encode($SR);
  }
}