<?php
namespace Three\GoldPalm;
/**
 * Created by PhpStorm.
 * User: yan
 * Date: 2017/3/1
 * Time: 下午5:27
 */
class GoldPalm
{

    private $api_url = [
        'test'=>"https://test-api.lyht.cn/ntsms-contract-api/service/ContractWebService?wsdl",
        'production'=>"https://api.lyht.cn/ntsms-contract-api/service/ContractWebService?wsdl"
    ];
    private $api;
    private $token;
    public function __construct($config)
    {

        try {
            //判断什么环境下的接口
            $this->api = $this->api_url[$config['env'] ? $config['env'] : 'test'];
            $client = new \SoapClient($this->api);
            //默认先取authcode为登录token
            if(isset($config['authcode']) && empty($config['authcode'])){
                $param = ['code' => $config['username'], 'authcode' => $config['authcode']];
                $res = $client->__call('authenticationOTA', ['param' => $param]);
            }else{
                $param = ['code' => $config['username'], 'password' => md5($config['password'])];
                $res = $client->__call('authentication', ['param' => $param]);
            }
            $result = json_decode($res->return);

            if ($result->result == 'success') {
                $this->token = $result->token;
            }else{
                throw  new \Exception(json_encode($result->errors));
            }

        } catch (\SoapFault $soapFault) {
            throw  new \Exception($soapFault);
        }
    }

    /**
     * 提交合同
     * @param $contract
     * @return bool
     * @throws \Exception
     */
    public function submit($contract)
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
            foreach ($contract['routes'] as $key=>$route){
                $contractTeam['routes'][$key]['day'] = $route['day'];//第几天行程
                $contractTeam['routes'][$key]['stop'] = $route['stop'];//当天行程第几站
                $contractTeam['routes'][$key]['departcity'] = $route['starting_address'];//出发地
                $contractTeam['routes'][$key]['arrivecity'] = $route['destination_city'];//目的地城市
                $contractTeam['routes'][$key]['arrivestate'] = $route['destination_state'];//前往省
                $contractTeam['routes'][$key]['arrivenation'] = $route['destination_country'];//前往国家
                $contractTeam['routes'][$key]['trip'] =  $route['trip'];//游览行程
            }

            //游客名单
            foreach ($contract['guests'] as $key=>$guest){
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
            $res = $client->__call('submitContract', ['param' => $param]);
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
     * 根据合同ID获取是否签名
     * @param $contractid
     * @return bool|\Exception|\SoapFault
     * @throws \Exception
     */
    public function getSignCreate($contractid)
    {

        try {
            $client = new \SoapClient($this->api);
            $param = array('token' => $this->token, 'contractid' => $contractid);
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
}