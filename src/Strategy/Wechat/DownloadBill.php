<?php

namespace EasyPay\Strategy\Wechat;

use EasyPay\TradeData\Ali\TradeData;
use EasyPay\Exception\PayFailException;

/**
 * 下载账单
 *
 * Class DownloadBill
 * @package EasyPay\Strategy\Wechat\Transaction
 */
class DownloadBill extends BaseWechatStrategy
{
    protected $savePath;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (array_key_exists('save_path', $options)) {
            $this->savePath = $options['save_path'];
        }

        parent::__construct($options);
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return ['appid','mch_id','bill_date','bill_type'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return ['appid','mch_id','bill_date','bill_type'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequestMethod()
    {
        return 'POST';
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequestTarget()
    {
        return BaseWechatStrategy::DOWN_LOAD_BILL_URL;
    }

    /**
     * {@inheritDoc}
     */
    protected function handleData($data)
    {
        if (!is_null($this->savePath)) {
            $this->saveToFile($data);
            return true;
        }

        return $data;
    }

    /**
     * 保存到指定文件中
     *
     * @param $data
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    protected function saveToFile($data)
    {
        if (!class_exists(\PHPExcel::class)) {
            throw new \RuntimeException("保存为文件需要PHPExcel库支持");
        }

        $data = $this->parseData($data);

        // Todo 储存统计信息
        list($order, $statistics) = array_chunk($data, count($data) - 2);

        $phpExcel = new \PHPExcel();

        $excel = $phpExcel->setActiveSheetIndex(0);

        $rowNum = 0;
        while ($row = array_shift($order)) {
            $row = array_values($row);
            $rowNum++;

            $column = 0;
            $countRow = count($row);
            $letter = numToLetter($countRow);
            for ($columnNum = 'A' ; $columnNum <= $letter ; $columnNum++,$column++) {
                $excel = $excel->setCellValue($columnNum.$rowNum, $row[$column]);
            }
        }

        $phpExcel->setActiveSheetIndex(0);
        $extensionType = $this->createSaveType();
        $writer = \PHPExcel_IOFactory::createWriter($phpExcel, $extensionType);

        $writer->save($this->savePath);
    }

    /**
     * 解析数据
     *
     * @param $data
     * @return array
     */
    protected function parseData($data)
    {
        $xmlParser = xml_parser_create();

        if (xml_parse($xmlParser, $data)) {
            $data = TradeData::createFromXML($data);

            throw new PayFailException($data, $data['return_msg']);
        }

        $rows = explode("\n", $data);
        // 最后一行为空
        array_pop($rows);

        return array_map(function ($row) {
            return explode(',', str_replace('`','',$row));
        }, $rows);
    }

    /**
     * 获取文件存储格式
     *
     * @return string
     */
    protected function createSaveType()
    {
        $type = pathinfo($this->savePath, PATHINFO_EXTENSION);

        switch ($type) {
            case 'xlsx':			//	Excel (OfficeOpenXML) Spreadsheet
            case 'xlsm':			//	Excel (OfficeOpenXML) Macro Spreadsheet (macros will be discarded)
            case 'xltx':			//	Excel (OfficeOpenXML) Template
            case 'xltm':			//	Excel (OfficeOpenXML) Macro Template (macros will be discarded)
                $extensionType = 'Excel2007';
                break;
            case 'xls':				//	Excel (BIFF) Spreadsheet
            case 'xlt':				//	Excel (BIFF) Template
                $extensionType = 'Excel5';
                break;
            case 'htm':
            case 'html':
                $extensionType = 'HTML';
                break;
            case 'csv':
                $extensionType = 'CSV';
                break;
            case '':
                throw new \RuntimeException("文件格式不能为空");
                break;
            default:
                throw new \RuntimeException("暂不支持[{$type}]格式");
                break;
        }

        return $extensionType;
    }
}