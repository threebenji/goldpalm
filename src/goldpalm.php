<?php
namespace ThreeBenji\GoldPalm;
/**
 * Created by PhpStorm.
 * User: yan
 * Date: 2017/3/1
 * Time: 下午5:27
 */
class GoldPalm
{

    private $api_url = [
        'test' => "https://test-api.lyht.cn/ntsms-contract-api/service/ContractWebService?wsdl",
        'production' => "https://api.lyht.cn/ntsms-contract-api/service/ContractWebService?wsdl"
    ];
    private $api;
    private $token;

    /**
     * 实例化基础配置
     * GoldPalm constructor.
     * @param $config
     * @throws \Exception
     */
    public function __construct($config)
    {

        try {
            //判断什么环境下的接口
            $this->api = $this->api_url[$config['env'] ? $config['env'] : 'test'];
            $client = new \SoapClient($this->api);
            //默认先取authcode为登录token
            if (isset($config['authcode']) && empty($config['authcode'])) {
                $param = ['code' => $config['username'], 'authcode' => $config['authcode']];
                $res = $client->__call('authenticationOTA', ['param' => $param]);
            } else {
                $param = ['code' => $config['username'], 'password' => md5($config['password'])];
                $res = $client->__call('authentication', ['param' => $param]);
            }
            $result = json_decode($res->return);

            if ($result->result == 'success') {
                $this->token = $result->token;
            } else {
                throw  new \Exception(json_encode($result->errors));
            }

        } catch (\SoapFault $soapFault) {
            throw  new \Exception($soapFault);
        }
    }

    /**
     * 上传电子合同，上传后进入已提交状态 or 上传后不提交
     * @param $contract
     * @param $is_upload bool true 上传后不提交 false 上传后提交
     * @return mixed
     * @throws \Exception
     */
    public function submit($contract, $is_upload = false)
    {
        try {
            $submitRequest = array();
            $submitRequest['version'] = $contract['version'];//合同版本名称
            $submitRequest['travelname'] = $contract['travel_name'];//旅游者代表
            $submitRequest['travelmobile'] = $contract['travel_tel'];//旅游者代表手机号
            $submitRequest['transactor'] = $contract['transactor'];//经办人
            $submitRequest['price'] = $contract['price'];//费用合计
            $submitRequest['no'] = $contract['no'];//合同号
            //合同详情
            $contractJSON = [];
            //旅游者代表相关信息
            $contractJSON['traveler'] = $contract['travelerJson'];
            //地接社信息
            $contractJSON['supplier'] = $contract['supplierJson'];
            //旅行社信息(组团社信息)
            $contractJSON['groupcorp'] = $contract['groupcorpJson'];
            //旅游线路相关信息
            $contractJSON['line'] = $contract['lineJson'];
            //旅游费用支付方式及时间
            $contractJSON['pay'] = $contract['payJson'];
            //保险事项
            $contractJSON['insurance'] = $contract['insuranceJson'];
            //成团约定
            $contractJSON['group'] = $contract['groupJson'];
            //黄金周特别约定
            $contractJSON['goldenweek'] = $contract['goldenweekJson'];
            //争议处理
            $contractJSON['controversy'] = $contract['controversyJson'];
            //其他事项
            $contractJSON['other'] = $contract['otherJson'];
            $submitRequest['contractJSON'] = $contractJSON;
            //电子合同团队信息
            $contractTeam = [];
            $contractTeam['linename'] = $contract['line_name'];//线路名称
            $contractTeam['teamcode'] = $contract['team_code'];//团号
            $contractTeam['days'] = $contract['days'];//行程天数
            $contractTeam['nights'] = $contract['night_days'];//行程夜晚天数
            $contractTeam['bgndate'] = $contract['start_on'];//出团日期
            $contractTeam['enddate'] = $contract['end_on'];//返回日期
            $contractTeam['qty'] = $contract['traveler_num'];//旅游人数
            //行程安排
            foreach ($contract['routes'] as $key => $route) {
                $contractTeam['routes'][$key]['day'] = $route['day'];//第几天行程
                $contractTeam['routes'][$key]['stop'] = $route['stop'];//当天行程第几站
                $contractTeam['routes'][$key]['departcity'] = $route['starting_address'];//出发地
                $contractTeam['routes'][$key]['arrivecity'] = $route['destination_city'];//目的地城市
                $contractTeam['routes'][$key]['arrivestate'] = $route['destination_state'];//前往省
                $contractTeam['routes'][$key]['arrivenation'] = $route['destination_country'];//前往国家
                $contractTeam['routes'][$key]['trip'] = $route['trip'];//游览行程
            }

            //游客名单
            foreach ($contract['guests'] as $key => $guest) {
                $contractTeam['guests'][$key]['idtype'] = '1';//证件类型身份证号
                $contractTeam['guests'][$key]['idcode'] = $guest['id_card'];//证件号
                $contractTeam['guests'][$key]['name'] = $guest['name'];//姓名
                $contractTeam['guests'][$key]['sex'] = Utils::gender($guest['id_card']);//性别
                $contractTeam['guests'][$key]['birthday'] = Utils::birthday($guest['id_card']);//生日
                $contractTeam['guests'][$key]['mobile'] = $guest['tel'];//合同人手机号
                $contractTeam['guests'][$key]['no'] = $key + 1;//名单序号
            }
            $submitRequest['contractTeam'] = $contractTeam;//合同团队
            $client = new \SoapClient($this->api);
            $param = array('token' => $this->token, 'submitContractRequest' => $submitRequest);
            if ($is_upload) {
                $res = $client->__call('uploadContract', ['param' => $param]);
            } else {
                $res = $client->__call('submitContract', ['param' => $param]);
            }
            $result = json_decode($res->return);
            if ($result->result == 'success') {
                return $result->contract;
            } else {
                throw  new \Exception(json_encode($result->errors));
            }
        } catch (\SoapFault $soapFault) {
            throw  new \Exception($soapFault);
        }
    }

    /**
     * 提交已经上传的合同
     * @param $id
     * @param $no
     * @return bool
     * @throws \Exception
     */
    public function submitStatus($id, $no)
    {

        try {
            $client = new \SoapClient($this->api);
            $param = array('token' => $this->token, 'submitStatusRequest' => ['id' => $id, 'no' => $no]);
            $res = $client->__call('submitStatus', array('param' => $param));
            $result = json_decode($res->return);

            if ($result->result == 'success') {
                return true;
            } else {
                throw  new \Exception(json_encode($result->errors));
            }
        } catch (\SoapFault $soapFault) {
            throw  new \Exception($soapFault);
        }
    }

    /**
     * 取消合同
     * @param $id
     * @param $no
     * @return bool
     * @throws \Exception
     */
    public function cancelContract($id, $no)
    {

        try {
            $client = new \SoapClient($this->api);
            $param = array('token' => $this->token, 'cancelContractRequest' => ['id' => $id, 'no' => $no]);
            $res = $client->__call('cancelContract', array('param' => $param));
            $result = json_decode($res->return);

            if ($result->result == 'success') {
                return true;
            } else {
                throw  new \Exception(json_encode($result->errors));
            }
        } catch (\SoapFault $soapFault) {
            throw  new \Exception($soapFault);
        }
    }

    /**
     * 补充完善合同保险信息接口
     * @param $no
     * @param $insurance string json
     * @return bool
     * @throws \Exception
     */
    public function complementInsurance($no, $insurance)
    {

        try {
            $client = new \SoapClient($this->api);
            $param = array('token' => $this->token, 'complementInsuranceRequest' => ['no' => $no, 'insurance' => $insurance]);
            $res = $client->__call('complementInsurance', array('param' => $param));
            $result = json_decode($res->return);

            if ($result->result == 'success') {
                return true;
            } else {
                throw  new \Exception(json_encode($result->errors));
            }
        } catch (\SoapFault $soapFault) {
            throw  new \Exception($soapFault);
        }
    }

    /**
     * 补充游客列表只有为提交的合同可以使用
     * @param $guests
     * @return bool
     * @throws \Exception
     */
    public function complementGuest($guests)
    {
        try {
            $client = new \SoapClient($this->api);
            $param = array('token' => $this->token, 'complementGuestRequest' =>$guests);
            $res = $client->__call('complementGuest', array('param' => $param));
            $result = json_decode($res->return);

            if ($result->result == 'success') {
                return true;
            } else {
                throw  new \Exception(json_encode($result->errors));
            }
        } catch (\SoapFault $soapFault) {
            throw  new \Exception($soapFault);
        }
    }

    /**
     * 补充签名功能
     * @param $id
     * @param $base64image
     * @return bool
     * @throws \Exception
     */
    public function submitSign($id,$base64image)
    {
        try {
            $client = new \SoapClient($this->api);
            $param = array('token' => $this->token, 'complementSignRequest' =>['id'=>$id,'base64image'=>$base64image]);
            $res = $client->__call('submitSign', array('param' => $param));
            $result = json_decode($res->return);

            if ($result->result == 'success') {
                return true;
            } else {
                throw  new \Exception(json_encode($result->errors));
            }
        } catch (\SoapFault $soapFault) {
            throw  new \Exception($soapFault);
        }
    }
    /**
     * 根据合同ID获取是否签名
     * @param $id
     * @return bool|\Exception|\SoapFault
     * @throws \Exception
     */
    public function getSignCreate($id)
    {

        try {
            $client = new \SoapClient($this->api);
            $param = array('token' => $this->token, 'contractid' => $id);
            $res = $client->__call('getSignCreate', array('param' => $param));
            $result = json_decode($res->return);

            if ($result->result == 'success') {
                return true;
            } else {
                throw  new \Exception(json_encode($result->errors));
            }
        } catch (\SoapFault $soapFault) {
            throw  new \Exception($soapFault);
        }
    }

    /**
     * 根据合同号获取合同基本信息
     * @param $no
     * @return bool
     * @throws \Exception
     */
    public function getContractBaseInfo($no)
    {

        try {
            $client = new \SoapClient($this->api);
            $param = array('token' => $this->token, 'contractno' => $no);
            $res = $client->__call('getContractBaseInfo', array('param' => $param));
            $result = json_decode($res->return);

            if ($result->result == 'success') {
                return true;
            } else {
                throw  new \Exception(json_encode($result->errors));
            }
        } catch (\SoapFault $soapFault) {
            throw  new \Exception($soapFault);
        }
    }

    /**
     * 重发短信
     * @param $id
     * @param $guestname
     * @param $guestmobile
     * @return bool
     * @throws \Exception
     */
    public function resendMsg($id,$guestname,$guestmobile)
    {

        try {
            $client = new \SoapClient($this->api);
            $param = array('token' => $this->token, 'contractid' => $id,'guestname'=>$guestname,'guestmobile'=>$guestmobile);
            $res = $client->__call('resendMsg', array('param' => $param));
            $result = json_decode($res->return);

            if ($result->result == 'success') {
                return true;
            } else {
                throw  new \Exception(json_encode($result->errors));
            }
        } catch (\SoapFault $soapFault) {
            throw  new \Exception($soapFault);
        }
    }
}