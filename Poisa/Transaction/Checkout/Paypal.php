<?php
namespace Poisa\Transaction\Checkout;




use Illuminate\Support\Facades\DB;
use Poisa\Balance;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;




class Paypal
{

    /**
     * connect to paypal application
     * 
     * @return object PayPalHttpClient
     * 
     * @since   1.0.0
     * @version 1.2.0
     * @author  Mahmudul Hasan Mithu
     */
    private static function paypal_client()
    {
        $Paypal_App_Credential = json_decode(DB::table('Poisa_Setting')->where('meta_key', 'Payment_Method__Paypal_Checkout__App_Credential')->value('meta_value'));

        $clientId     = getenv("CLIENT_ID")     ?: $Paypal_App_Credential->clientId;
        $clientSecret = getenv("CLIENT_SECRET") ?: $Paypal_App_Credential->clientSecret;

        if( $Paypal_App_Credential->Environment==='production' )
            $environment = new ProductionEnvironment($clientId, $clientSecret);
        elseif($Paypal_App_Credential->Environment==='sandbox')
            $environment = new SandboxEnvironment($clientId, $clientSecret);

        return new PayPalHttpClient($environment);
    }




    /**
     * add unique transaction based on order_id
     * 
     * @param int    $user_id
     * @param object $transaction_details
     * 
     * @since   1.0.0
     * @version 1.0.0
     * @author  Mahmudul Hasan Mithu
     */
    public static function transaction_add( int $user_id, object $transaction_details )
    {
        date_default_timezone_set('UTC');

        $PayPal_order_id        = $transaction_details->id;
        $PayPal_transaction_id  = $transaction_details->purchase_units[0]->payments->captures[0]->id;

        $PayPal_intent = $transaction_details->intent;
        $PayPal_status = $transaction_details->status;

        $PayPal_payer_payer_id = $transaction_details->payer->payer_id;
        $PayPal_payer_email    = $transaction_details->payer->email_address;

        $PayPal_payee_merchant_id = $transaction_details->purchase_units[0]->payee->merchant_id;
        $PayPal_payee_email       = $transaction_details->purchase_units[0]->payee->email_address;

        $PayPal_purchase_amount   = (float) $transaction_details->purchase_units[0]->amount->value;
        $PayPal_purchase_currency = $transaction_details->purchase_units[0]->amount->currency_code;

        $DB_PayPal_order_id = DB::table('Poisa_Transaction_Checkout_Paypal')->where('PayPal_order_id', $PayPal_order_id)->value('PayPal_order_id');
        if($DB_PayPal_order_id===NULL){
            DB::table('Poisa_Transaction_Checkout_Paypal')->insert(
                [
                    'user_id' =>$user_id,
    
                    'PayPal_order_id'       => $PayPal_order_id,
                    'PayPal_transaction_id' => $PayPal_transaction_id,
    
                    'PayPal_intent' => $PayPal_intent,
                    'PayPal_status' => $PayPal_status,
    
                    'PayPal_payer_payer_id' => $PayPal_payer_payer_id,
                    'PayPal_payer_email'    => $PayPal_payer_email,
    
                    'PayPal_payee_merchant_id' => $PayPal_payee_merchant_id,
                    'PayPal_payee_email'       => $PayPal_payee_email,
    
                    'PayPal_purchase_amount'   => $PayPal_purchase_amount,
                    'PayPal_purchase_currency' => $PayPal_purchase_currency,
    
                    'PayPal_transaction_details_raw' => json_encode($transaction_details),
    
                    'verified_manual' => 'not_verified',
                    'verified_auto'   => 'not_verified',
    
                    'datetime' => date('Y-m-d H:i:s')
                ]
            );
        }
    }



    /**
     * verify all transaction
     * 
     * @param int    $user_id
     * 
     * @since   1.0.0
     * @version 1.0.0
     * @author  Mahmudul Hasan Mithu
     */
    public static function transaction_verify( int $user_id )
    {
        $transaction_all = DB::table('Poisa_Transaction_Checkout_Paypal')
                                ->where('user_id', $user_id)
                                ->where('verified_auto', 'not_verified')
                                ->get();

        
                                
        /**
         * verify each transaction
         * 
         * @param object $transaction
         * 
         * @return bool true  - if transaction is valid
         *              false - if transaction is invalid
         * 
         * @since   1.0.0
         * @version 1.0.0
         * @author  Mahmudul Hasan Mithu
         */
        $verify = function( $transaction_db )
        {
            try{
                $client = self::paypal_client();
                $response = $client->execute(new OrdersGetRequest( $transaction_db->PayPal_order_id ));
            }
            catch( \EXCEPTION $e ){
                $response = 'error';
            }


            if( $response!=='error' ){
                $transaction_paypal_app = (object)
                    [
                        'PayPal_order_id'       => $response->result->id,
                        'PayPal_transaction_id' => $response->result->purchase_units[0]->payments->captures[0]->id,

                        'PayPal_intent' => $response->result->intent,
                        'PayPal_status' => $response->result->status,

                        'PayPal_payer_payer_id' => $response->result->payer->payer_id,
                        'PayPal_payer_email'    => $response->result->payer->email_address,

                        'PayPal_payee_merchant_id' => $response->result->purchase_units[0]->payee->merchant_id ,
                        'PayPal_payee_email'       => $response->result->purchase_units[0]->payee->email_address ,

                        'PayPal_purchase_amount'   => (float) $response->result->purchase_units[0]->amount->value ,
                        'PayPal_purchase_currency' => $response->result->purchase_units[0]->amount->currency_code
                    ];

                if(
                    $transaction_db->PayPal_order_id          === $transaction_paypal_app->PayPal_order_id             &&
                    $transaction_db->PayPal_transaction_id    === $transaction_paypal_app->PayPal_transaction_id       &&

                    $transaction_db->PayPal_intent            === 'CAPTURE'                                            &&
                    $transaction_db->PayPal_status            === 'COMPLETED'                                          &&
                    $transaction_db->PayPal_intent            === $transaction_paypal_app->PayPal_intent               &&
                    $transaction_db->PayPal_status            === $transaction_paypal_app->PayPal_status               &&

                    $transaction_db->PayPal_payer_payer_id    === $transaction_paypal_app->PayPal_payer_payer_id       &&
                    $transaction_db->PayPal_payer_email       === $transaction_paypal_app->PayPal_payer_email          &&

                    $transaction_db->PayPal_payee_merchant_id === $transaction_paypal_app->PayPal_payee_merchant_id    &&
                    $transaction_db->PayPal_payee_email       === $transaction_paypal_app->PayPal_payee_email          &&

                    $transaction_db->PayPal_purchase_amount   === $transaction_paypal_app->PayPal_purchase_amount      &&
                    $transaction_db->PayPal_purchase_currency === $transaction_paypal_app->PayPal_purchase_currency    &&
                    $transaction_db->PayPal_purchase_currency === 'USD'
                  )
                    return true;
            }

            return false;
        };

        foreach( $transaction_all as $transaction ){

            $transaction_is_valid = $verify($transaction);

            if($transaction_is_valid===true){
                DB::table('Poisa_Transaction_Checkout_Paypal')
                    ->where('id', $transaction->id )
                    ->update(
                        [
                            'verified_auto'=>'verified'
                        ]
                    );
                Balance::change( $user_id, $transaction->PayPal_purchase_amount );
            }else{
                DB::table('Poisa_Transaction_Checkout_Paypal')
                    ->where('id', $transaction->id )
                    ->update(
                        [
                            'verified_auto'=>'error'
                        ]
                    );
            }
        }
    }
}