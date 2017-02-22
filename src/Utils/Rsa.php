<?php
namespace EasyPay\Utils;

/**
 * 加密时,公钥负责加密，私钥负责解密
 * 签名时,私钥负责签名，公钥负责验证
 *
 * Class RsaEncrypt
 * @package EasyPay\Utils
 */
class RSA
{
    protected $publicKey;

    protected $privateKey;

    protected $password;

    protected $encryptCallback;

    protected $decryptCallback;

    /**
     * 构造Rsa加密
     *
     * @param null|string $publicKey   公钥证书(可为路径)
     * @param null|string $privateKey  私钥证书(可为路径)
     */
    public function __construct($publicKey = null, $privateKey = null)
    {
        if (!extension_loaded('openssl')) {
            throw new \RuntimeException("请安装openssl扩展");
        }

        if (!is_null($publicKey)) {
            $this->setPublicKey($publicKey);
        }

        if (!is_null($privateKey)) {
            $this->setPrivateKey($privateKey);
        }
    }

    /**
     * 设置公钥证书
     *
     * @param $publicKey
     * @return $this
     */
    public function setPublicKey($publicKey)
    {
        if (is_file($publicKey)) {
            $publicKey = @file_get_contents($publicKey);
        }

        if (!is_string($publicKey) && method_exists($publicKey, '__toString')) {
            throw new \RuntimeException("公钥格式错误");
        }

        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * 设置私钥证书
     *
     * @param $privateKey
     * @return $this
     */
    public function setPrivateKey($privateKey)
    {
        if (is_file($privateKey)) {
            $privateKey = @file_get_contents($privateKey);
        }

        if (!is_string($privateKey) && method_exists($privateKey, '__toString')) {
            throw new \RuntimeException("私钥格式错误");
        }

        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * 设置加密后进行回调的函数
     *
     * @param null $callback
     * @return $this
     */
    public function setEncryptCallback($callback = null)
    {
        $this->encryptCallback = $callback;

        return $this;
    }

    /**
     * 设置解密前进行回调的函数
     *
     * @param null $callback
     * @return $this
     */
    public function setDecryptCallback($callback = null)
    {
        $this->decryptCallback = $callback;

        return $this;
    }

    /**
     * 设置密码
     *
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * 通过私钥进行签名
     *
     * @param $data
     * @param int $signType
     * @return mixed
     * @throw \RuntimeException
     */
    public function sign($data, $signType = OPENSSL_ALGO_SHA1)
    {
        // 私钥负责签名,不允许为空
        if (empty($this->privateKey)) {
            throw new \RuntimeException("缺少生成签名所需的私钥证书");
        }

        // 从证书导出私钥
        $priKey = openssl_get_privatekey($this->privateKey);
        if ($priKey === false) {
            throw new \RuntimeException('私钥格式错误,请检查RSA私钥');
        }

        // 进行签名,签名后释放秘钥
        if (!openssl_sign($data, $sign, $priKey, $signType)) {
            throw new \RuntimeException("生成签名出错,错误信息:".openssl_error_string());
        }
        openssl_free_key($priKey);

        return $this->processor($sign);
    }

    /**
     * 通过公钥证书验证签名是否正确
     *
     * @param $data
     * @param $sign
     * @param int $signType
     * @return bool
     * @throw \RuntimeException
     */
    public function validate($data, $sign, $signType = OPENSSL_ALGO_SHA1)
    {
        // 私钥负责签名,不允许为空
        if (empty($this->publicKey)) {
            throw new \RuntimeException("缺少验证签名所需要的公钥证书");
        }

        $pubKey = openssl_get_publickey($this->publicKey);
        if ($pubKey === false) {
            throw new \RuntimeException('RSA公钥错误。请检查公钥文件格式是否正确');
        }

        $sign = $this->processor($sign, false);
        $result = (bool)openssl_verify($data, $sign, $pubKey, $signType);
        openssl_free_key($pubKey);
        return $result;
    }

    /**
     * 通过公钥加密数据
     *
     * @param $data
     * @return string
     */
    public function encrypt($data)
    {
        // 通过公钥加密
        if (empty($this->publicKey)) {
            throw new \RuntimeException("缺少加密所需要的公钥证书");
        }

        $publicKey = openssl_pkey_get_public($this->publicKey);
        if (!$publicKey) {
            throw new \RuntimeException("无法获取公钥,请检查公钥格式是否正确");
        }

        if (!openssl_public_encrypt($data, $encryptedData, $publicKey)) {
            throw new \RuntimeException("加密失败,错误信息".openssl_error_string());
        }
        openssl_free_key($publicKey);

        return $this->processor($encryptedData);
    }

    /**
     * 将通过公钥加密的数据解密
     *
     * @param $data
     * @return string
     */
    public function decrypt($data)
    {
        // 通过私钥解密
        if (empty($this->privateKey)) {
            throw new \RuntimeException("缺少解密所需的私钥证书");
        }

        $privateKey = openssl_pkey_get_private($this->privateKey, $this->password);
        if (!$privateKey) {
            throw new \RuntimeException("无法获取私钥,请检查私钥格式是否正确");
        }

        $data = $this->processor($data, false);
        if (!openssl_private_decrypt($data, $decryptedData, $privateKey)) {
            throw new \RuntimeException("解密失败,错误信息".openssl_error_string());
        }
        openssl_free_key($privateKey);

        return $decryptedData;
    }

    /**
     * 处理加密后与解密前的数据
     *
     * @param $data
     * @param bool|true $isEncrypt
     * @return mixed
     */
    protected function processor($data, $isEncrypt = true)
    {
        $callback = $isEncrypt ? $this->encryptCallback : $this->decryptCallback;

        return is_callable($callback)
            ? call_user_func($callback,$data)
            : $data;
    }
}