<?php
namespace EasyPay\Strategy\Wechat\Transaction;

use EasyPay\Strategy\Wechat\BaseWechatStrategy;

/**
 * 下载账单
 *
 * Class DownloadBill
 * @package EasyPay\Strategy\Wechat\Transaction
 */
class DownloadBill extends BaseWechatStrategy
{
    // Todo 以流的形式进去读取
    public function execute()
    {
        // Todo 保存文件
        $this->payData->checkParamsExits(['appid','mch_id','bill_date','bill_type']);

        $context = stream_context_create([
            'http'  =>  [
                'method'    =>  'POST',
                'header'    =>  "Content-Type: text/xml\r\n",
                'content'   =>  (string)$this->payData
            ]
        ]);

        $fd = fopen(static::DOWN_LOAD_BILL_URL, 'r', false, $context);
        $log = fopen('test.log','w+');
        stream_copy_to_stream($fd, $log);
//        $client = new Client('POST', static::DOWN_LOAD_BILL_URL);
//        $response = $client->send((string)$this->payData);
//        file_put_contents('test.log', $response->getBody()->getContents());
    }
}