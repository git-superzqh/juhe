<?php

namespace z0y;

class Juhe {

    public function test() {

        echo 'juhe';
        echo config('juhe_key');
    }

    /**
     * 
     * sendSMS  聚合发送短信
     * @param type $sendMobile   手机号
     * @param type $tpl_id       短信模板id
     * @param type $tpl_value   短信内容  ['#param1#=value1','#param2#=value2']
     * return true 发送成功， false 失败
     */
    public function sendSMS($sendMobile = '', $tpl_id = '', $tpl_value = []) {

        header('content-type:text/html;charset=utf-8');

        $str_value = implode('&', $tpl_value);
        $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL

        $smsConf = array(
            'key' => config('juhe_key'), //您申请的APPKEY
            'mobile' => $sendMobile, //接受短信的用户手机号码
            'tpl_id' => $tpl_id, //您申请的短信模板ID，根据实际情况修改
            'tpl_value' => $str_value //您设置的模板变量，根据实际情况修改
        );

        $content = $this->juhecurl($sendUrl, $smsConf, 1); //请求发送短信

        if ($content) {
            $result = json_decode($content, true);
            $error_code = $result['error_code'];
            if ($error_code == 0) {
                //状态为0，说明短信发送成功
                //echo "短信发送成功,短信ID：" . $result['result']['sid'];
                return TRUE;
            } else {
                //状态非0，说明失败
                //$msg = $result['reason'];
                //echo "短信发送失败(" . $error_code . ")：" . $msg;
                return FALSE;
            }
        } else {
            //返回内容异常，以下可根据业务逻辑自行修改
            //echo "请求发送短信失败";
            return FALSE;
        }
    }

    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int $ispost [是否采用POST形式]
     * @return  string
     */
    private function juhecurl($url, $params = false, $ispost = 0) {
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }

}
