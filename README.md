# 金棕榈合同 
##安装
    
    composer require threebenji/glodpalm

##实例化
    
    $test = new \Three\GoldPalm\GoldPalm(['env' => 'test', 'username' => 'xxx', 'password' => 'xxx']);
    
##提交合同并且上线
    
    $test->submit([
            'version' => 'dljn2014',
            'travel_name' => '张三',
            'travel_tel' => '13800138000',
            'transactor' => '李四',
            'price' => 100,
            'no'=>'',
            'travelerJson'=>json_encode([
                'traveler'=>'张三',
                'addr'=>'北京'
            ]),
            'supplierJson'=>json_encode([
                'corp'=>'北京中国国际旅行社有限公司',
                'corpCode'=>'L-BJ-CJ00056',
                'scope'=>'经营范围'
            ]),
            'groupcorpJson'=>json_encode([
                'corp'=>'北京中国国际旅行社有限公司',
                'corpCode'=>'L-BJ-CJ00056',
                'scope'=>'经营范围'
            ]),
            'lineJson'=>json_encode([
                'linename'=>"旅游线路",
                'teamcode'=>'1'
            ]),
            'payJson'=>json_encode([
                'payEachAdult'=>100,
                'payEachChild'=>200,
                'payTravel'=>300,
                'payGuide'=>100,
                'payDeadline'=>'2017-03-01',
                'payType'=>1,
                'payOther'=>''
            ]),
            'insuranceJson'=>json_encode([
                'agree'=>'2',
                'product'=>'太平洋出境意外险'
            ]),
            'groupJson'=>json_encode([
                'personLimit'=>10,
                'transAgree'=>1,
                'delayAgree'=>1,
                'changeLineAgree'=>1,
                'terminateAgree'=>1,
                'mergeAgree'=>1,
                'teminateDealType'=>1
            ]),
            'goldenweekJson'=>json_encode([
                'personLimit'=>10,
                'transAgree'=>1,
                'delayAgree'=>1,
                'changeLineAgree'=>1,
                'terminateAgree'=>1,
                'mergeAgree'=>1,
                'teminateDealType'=>1
            ]),
            'controversyJson'=>json_encode([
                'personLimit'=>10,
                'transAgree'=>1,
                'delayAgree'=>1,
                'changeLineAgree'=>1,
                'terminateAgree'=>1,
                'mergeAgree'=>1,
                'teminateDealType'=>1
            ]),
            'otherJson'=>json_encode([
                'supplementaryClause'=>'',
                'copys1'=>'贰',
                'copys2'=>'壹',
                'agencyComplaintsMobile'=>'010-xxxxxx',
                'lawState'=>'北京市',
                'lawCity'=>'北京市',
                'lawComplaintsMobile'=>'12301',
                'lawEmail'=>'',
                'lawAddress'=>'',
                'lawZip'=>''
            ]),
            "line_name"=>'线路名称',
            "team_code"=>"123",
            "days"=>2,
            "night_days"=>1,
            "start_on"=>"2017-03-01",
            "end_on"=>"2017-03-02",
            "traveler_num"=>1,
            "routes"=>[
                [
                    'day'=>1,
                    'stop'=>1,
                    'starting_address'=>"北京",
                    'destination_city'=>"北京",
                    'destination_state'=>"北京",
                    'destination_country'=>"中国",
                    'trip'=>"行程"
                ],
                [
                    'day'=>2,
                    'stop'=>1,
                    'starting_address'=>"北京",
                    'destination_city'=>"北京",
                    'destination_state'=>"北京",
                    'destination_country'=>"中国",
                    'trip'=>"行程2"
                ]
            ],
            'guests'=>[
                [
                    'id_card'=>'370523199001221750',
                    'name'=>'张三',
                    'tel'=>'13800138000'
                ],
                [
                    'id_card'=>'370523199001221750',
                    'name'=>'张三1',
                    'tel'=>'13800138001'
                ]
            ]
    
        ]);
        
 ##方法
 
    /**
     * 上传电子合同，上传后进入已提交状态 or 上传后不提交
     * @param $contract
     * @param $is_upload bool true 上传后不提交 false 上传后提交
     * @return mixed
     * @throws \Exception
     */
    public function submit($contract, $is_upload = false)
        
    /**
     * 提交已经上传的合同
     * @param $id
     * @param $no
     * @return bool
     * @throws \Exception
     */
    public function submitStatus($id, $no)
    
    /**
     * 取消合同
     * @param $id
     * @param $no
     * @return bool
     * @throws \Exception
     */
    public function cancelContract($id, $no)
    
    /**
     * 补充完善合同保险信息接口
     * @param $no
     * @param $insurance string json
     * @return bool
     * @throws \Exception
     */
    public function complementInsurance($no, $insurance)
    
    /**
     * 补充游客列表只有为提交的合同可以使用
     * @param $guests
     * @return bool
     * @throws \Exception
     */
    public function complementGuest($guests)
    
    /**
     * 补充签名功能
     * @param $id
     * @param $base64image
     * @return bool
     * @throws \Exception
     */
    public function submitSign($id,$base64image)
    
    /**
     * 根据合同ID获取是否签名
     * @param $id
     * @return bool|\Exception|\SoapFault
     * @throws \Exception
     */
    public function getSignCreate($id)
    
    /**
     * 重发短信
     * @param $id
     * @param $guestname
     * @param $guestmobile
     * @return bool
     * @throws \Exception
     */
    public function resendMsg($id,$guestname,$guestmobile)