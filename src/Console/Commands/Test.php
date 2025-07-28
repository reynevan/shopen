<?php

namespace Shopen\Console\Commands;


use Elastic\Adapter\Indices\Index;
use Elastic\Adapter\Indices\IndexManager;
use Elastic\Adapter\Indices\Mapping;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Console\Command;
use Shopen\Models\Attribute\AttributeOption;
use Shopen\Models\Product\Product;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopen:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Query::match()->field('name')->query('buty');
        dd(Product::searchQuery($query)->execute()->models());
        $indexManager = app(IndexManager::class);
        $indexManager->drop('products');
        $mapping = new Mapping();
        $mapping->text('name', ['analyzer' => 'polish']);
        $mapping->text('sku');
        $mapping->integer('color');
        $mapping->integer('id');
        $indexManager->create(new Index('products', $mapping));
        return;
        OpenPayU_Configuration::setEnvironment('sandbox');

        //set POS ID and Second MD5 Key (from merchant admin panel)
        OpenPayU_Configuration::setMerchantPosId('402890');
        OpenPayU_Configuration::setSignatureKey('850ce308b10e3f68916edfc1869ae3fa');

        //set Oauth Client Id and Oauth Client Secret (from merchant admin panel)
        OpenPayU_Configuration::setOauthClientId('402890');
        OpenPayU_Configuration::setOauthClientSecret('01dd82b5a8c229b1629ed4683132fdbb');

        $order['continueUrl'] = 'http://shop/local/payu/'; //customer will be redirected to this page after successfull payment
        $order['notifyUrl'] = 'http://localhos/local/payu/notify';
        $order['customerIp'] = '127.0.0.1';
        $order['merchantPosId'] = OpenPayU_Configuration::getMerchantPosId();
        $order['description'] = 'New order';
        $order['currencyCode'] = 'PLN';
        $order['totalAmount'] = 3200;
        $order['extOrderId'] = '1342123'; //must be unique!

        $order['products'][0]['name'] = 'Product1';
        $order['products'][0]['unitPrice'] = 1000;
        $order['products'][0]['quantity'] = 1;

        $order['products'][1]['name'] = 'Product2';
        $order['products'][1]['unitPrice'] = 2200;
        $order['products'][1]['quantity'] = 1;

        //optional section buyer
        $order['buyer']['email'] = 'dd@ddd.pl';
        $order['buyer']['phone'] = '123123123';
        $order['buyer']['firstName'] = 'Jan';
        $order['buyer']['lastName'] = 'Kowalski';

        $response = OpenPayU_Order::create($order);

        dd($response->getResponse()->redirectUri);

    }
}
