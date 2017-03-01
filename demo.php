<?php
include "./src/goldpalm.php";
include "./src/Utils.php";
/**
 * Created by PhpStorm.
 * User: yan
 * Date: 2017/3/1
 * Time: 下午5:37
 */
try {
    $test = new \Three\GoldPalm\GoldPalm(['env' => 'test', 'username' => 'goldpalm-a1', 'password' => 'goldpalm789']);
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
    $re2 = $test->getSignCreate('b1e668ad-b6a7-4593-a5f0-477fa561f1b3');
    var_dump($re2);die;
} catch (Exception $exception) {
    var_dump($exception->getMessage());
}