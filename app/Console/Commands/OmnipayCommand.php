<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Omnipay\Omnipay;
use Omnipay\RoboKassa\Gateway;




class OmnipayCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "Omnipay:go";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Go Pay";

    public function handle()
    {
        try {


            $gateway = Omnipay::create('\Omnipay\RoboKassa\Gateway');
            $gateway->setSecretKey('abc123');


            $gateway = Omnipay::create('Best2Pay');
            $gateway->setTestMode(true);

            $formData = array('number'      => '2200200111114591',
                              'expiryMonth' => '5',
                              'expiryYear'  => '2022',
                              'cvv'         => '426'
            );
            $best2payParams = [
                'id'          => time(),
                'amount'      => '101',
                'currency'    => 'RUB',
                'description' => 'test',
                'orderNumber' => '1234',
                //'sector'      => '2139',
                'sector'      => '1',
                'returnUrl'   => 'ya.ru',
                //'password'=>'88W5pKHrp317u',
                'password'    => 'test',
                'card'        => $formData
            ];

            //sector, amount, currency, password
            $signatureString = $best2payParams['sector'] .
                $best2payParams['amount'] .
                $best2payParams['currency'] .
                $best2payParams['password'];

            $sign = base64_encode(md5($signatureString));
            $this->info($sign);
        //    die();


            //$response = $gateway->authorize($best2payParams)->send();
            $response = $gateway->purchase($best2payParams)->send();

            if ($response->isRedirect()) {
                // redirect to offsite payment gateway
                var_dump(
                    ['url'=> $response->getRedirectUrl(),
                     'data' => $response->getRedirectData(),
                     'id' => $response->getOrderId(),
                     'method' => $response->getRedirectMethod()
                    ]
                );

            //    $response->redirect();
            }
            elseif ($response->isSuccessful()) {
                // payment was successful: update database
                $this->info('OK');
                print_r($response);
                //$gateway->completePurchase();
            } else {
                $this->info('FAIL');
                // payment failed: display message to customer
                echo $response->getMessage();
            }

            $message = 'State one';

            $this->info($message);

        } catch (Exception $e) {

            $this->error("An error occurred");
        }
    }


    private function best2pay()
    {
        $gateway = Omnipay::create('Best2Pay');
        $gateway->setTestMode(true);

        $formData = array('number'      => '2200200111114591',
                          'expiryMonth' => '5',
                          'expiryYear'  => '2022',
                          'cvv'         => '426'
        );
        $best2payParams = [
            'id'          => time(),
            'amount'      => '101',
            'currency'    => 'RUB',
            'description' => 'test',
            'orderNumber' => '1234',
            //'sector'      => '2139',
            'sector'      => '1',
            'returnUrl'   => 'ya.ru',
            //'password'=>'88W5pKHrp317u',
            'password'    => 'test',
            'card'        => $formData
        ];

        //sector, amount, currency, password
        $signatureString = $best2payParams['sector'] .
            $best2payParams['amount'] .
            $best2payParams['currency'] .
            $best2payParams['password'];

        $sign = base64_encode(md5($signatureString));
        $this->info($sign);
        //    die();


        //$response = $gateway->authorize($best2payParams)->send();
        $response = $gateway->purchase($best2payParams)->send();

        if ($response->isRedirect()) {
            // redirect to offsite payment gateway
            var_dump(
                ['url'=> $response->getRedirectUrl(),
                 'data' => $response->getRedirectData(),
                 'id' => $response->getOrderId(),
                 'method' => $response->getRedirectMethod()
                ]
            );

            //    $response->redirect();
        }
        elseif ($response->isSuccessful()) {
            // payment was successful: update database
            $this->info('OK');
            print_r($response);
            //$gateway->completePurchase();
        } else {
            $this->info('FAIL');
            // payment failed: display message to customer
            echo $response->getMessage();
        }
    }


}
