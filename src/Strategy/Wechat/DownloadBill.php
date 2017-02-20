<?php
namespace EasyPay\Strategy\Wechat;

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
        // 发起Http请求
        $response = $this->sendHttpRequest(
            $this->getRequestMethod(),
            $this->getRequestTarget(),
            $this->buildData()
        );

        // 处理账单信息
        return $this->handleResult($response->getBody()->getContents());
    }

    /**
     * @return string
     */
    protected function getRequestMethod()
    {
        return 'POST';
    }

    /**
     * @return string
     */
    protected function getRequestTarget()
    {
        return BaseWechatStrategy::DOWN_LOAD_BILL_URL;
    }

    /**
     * 生成Http请求Body内容
     *
     * @return \EasyPay\DataManager\Wechat\Data
     */
    protected function buildData()
    {
        // 检查必要参数是否存在
        $this->payData->checkParamsExits(['appid','mch_id','bill_date','bill_type']);

        // 将多余参数剔除
        $this->payData->selectedParams(['appid','mch_id','bill_date','bill_type']);

        return $this->payData;
    }

    /**
     * @param $result
     * @return mixed
     */
    protected function handleResult($result)
    {
        // todo 保存为文件
        return $result;
    }
}