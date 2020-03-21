<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Weixin extends Weixin_Controller {

    private $tpwd_msg = "%s\n【原价】: %s元\n【内部优惠券】: %s元\n【券后价】: %s元\n【淘口令下单】: 复制这条信息，打开→手机淘宝领取优惠券%s";
    public function __construct() {
        parent::__construct();
     

    }
    public function openid(){
        return $this->wechat->getRevFrom();
    }
    public function key(){
        $msg = $this->wechat->getRevData();
        return trim($msg['Content']);
    }
    public function event(){
        return $this->wechat->getRevEvent ();
    }
    public function index(){

        if(!$this->wechat->valid ()){
            exit;
        }
        $type = $this->wechat->getRev()->getRevType ();
		switch ($type) {
			case Wechat::MSGTYPE_TEXT :
				$this->msg_handler();
				break;
			case Wechat::MSGTYPE_EVENT :
				$this->event_handler();
				break;
			case Wechat::MSGTYPE_IMAGE :
				break;
			default :
				$this->wechat->text ( "help info" )->reply ();
        }
        
    }
    
    private function msg_handler(){
		
        $key = $this->key();
        log_message(D,"key:".$key);

        $userinfo['openid'] = $this->openid() ;
        $userinfo['agency_id']= "";
        $userinfo['nickname']="无";
        log_message(D,"type:".$this->type);
        if($this->type == 2){
            $user_info_key = "user_info_".$this->openid();
            $uf = $this->get_cache($user_info_key);
            if(!$uf){
                $uf = $this->wechat->getUserInfo($this->openid() );
                $this->set_cache($user_info_key,$uf,60 * 60);
            }
            if($uf){
                $userinfo = $uf;
            }
        }
        log_message(D,"userinfo:".json_encode($userinfo,true));
        $wx_user = $this->wx_user->get_wx_user($this->openid());
        log_message(D,"wx user:".json_encode($wx_user));

        if(!$wx_user){
            $wx_user = $this->init_user($userinfo);
        }

        $num_iid = $this->get_numm_iid($key);
        if($num_iid != ""){
            $data = $this->api->get_taoword($num_iid);
            if($data){
                log_message(D,"coupon:".json_encode($data));
                $reply_msg = "【{$data['title']}】\n原价：{$data['org_pirce']}元\n优惠券：{$data['coupon_amout']}元\n券后价：{$data['price']}元\n桔子分红：{$data['commission']}个\n购买方式：复制这条信息，{$data['tpwd']}打开手机淘宝即可下单";
                if($data['introduce']){
                    $reply_msg .= "\n推荐理由:".$data['introduce'];
                }
            }else{
                $reply_msg= "该宝贝没有优惠策略";
            }
            $this->wechat->text ($reply_msg)->reply ();
            exit;
        }

        $bind = preg_match("/(^([0-9]{10,25})$)/",$key,$match);
        if($bind){
            log_message(D,"bind");
            $reply_msg = $this->bind_orders($wx_user['user_id'] ,$key);
            log_message(D,"bind reply:".$reply_msg);
            $this->wechat->text ($reply_msg)->reply ();
            exit;
        }

        if($key == "帮助"){
            $reply_msg = $this->help();
            $this->wechat->text ($reply_msg)->reply ();
            exit;
        }

        if($key == "余额"){
            $reply_msg = $this->balance($wx_user['user_id']);
            $this->wechat->text ($reply_msg)->reply ();
            exit;
        }
        
        if($key == "签到"){
            $reply_msg = $this->attend($wx_user['user_id']);
            $this->wechat->text ($reply_msg)->reply ();
            exit;
        }

        if($key == "用户号"){
            $reply_msg = $this->openid();
            $this->wechat->text ($reply_msg)->reply ();
            exit;
        }
        
        if($key == "指令"){
            $reply_msg = $this->command();
            $this->wechat->text ($reply_msg)->reply ();
            exit;
        }
        $search = preg_match("/^(买|找|查|搜).*/",$key,$match);
        if($search){
            $keyword = mb_substr($key,1);
            log_message(D,"keyword:".$keyword);
            $this->sendCoupon($keyword);
            exit;
        }
        
        

        $msg="该内容尚无自动回复，请确认输入的指令正确，搜索商品关键词或标题前加“买/找/查/搜,例如:买XXXXXX";
        $this->wechat->text ( $msg )->reply ();

    }
    private function get_numm_iid($key){
        $num_iid = "";
        $reg = "/.*(([€￥$]{1}([A-Za-z0-9]{11,})[€￥$]{1})|(http)).*/";
        if(preg_match($reg,$key,$match)){//正则匹配是否是口令或者购买链接
            $tpwd_reg = "/[€￥$]{1}[0-9a-zA-Z]{6,30}[€￥$]{1}/";
            
            if(preg_match($tpwd_reg,$key,$tpwd_match)){
                return $this->api->tpwd2id($key);
            }else if(preg_match("/id=[0-9]+(&)?/",$key,$match)){
                if(preg_match("/[0-9]+/",$match[0],$match)){
                    return $match[0];
                }
            }
        }
        return $num_iid;
    }
    function help(){
        
        //vip  level is 2
        $up_level_vip = 3;//邀请3个成为vip
        $withdrawals_amount_limit = $this->config->item('withdrawals_amount_limit');
        $user_level = $this->user_level->get_user_level(array("id"=>LEVEL_VIP),true);
        $rebate = $user_level['rebate_ratio'] * 100;
        $reply_msg = "【虞城之家使用方法】\n① 在淘宝APP选择需要购买的宝贝，【分享】到微信给我\n② 长按【复制】我生成的淘口令，打开手机淘宝,等待弹出商品,打开弹出商品领券下单（★重点★）\n③ 付款后，未自动跟踪订单需将\"订单号\"发给我绑定\n④ 发送“余额”，查询余额累积\n⑤ 发送“提现”，满{$withdrawals_amount_limit}元微信转账给你\n------------------\n成功邀请{$up_level_vip}位好友，免费升级VIP,享受vip网购分红{$rebate}%\n【更多功能】发送“指令”，获取全部指令";
        return $reply_msg;
    }
    function attend($user_id){
        $today_begin = today_begin();
        $attend = $this->score->get_score(array(
            'user_id'=>$user_id,
            'type'=>SCORE_ATTEND,
            'create_time > '=>$today_begin
        ));
        if($attend){
            return "今日已签到"; //已经签到
        }
        $this->asset->increment_score($user_id);
        $score = $this->asset->get_asset(array("user_id"=>$user_id),true);
        $score_balance = $score['score'];
        $data = $this->score->add(array(
            'user_id' => $user_id,
            'type' => SCORE_ATTEND,
            'create_time'=>time(),
            'score'=> 1,
            'balance'=>$score_balance,
            'status' => STATUS_SUCCESS,
            'change'=>MONEY_CHANGE_INCOME,
            'note'=>"签到: +1 分"
        ));
        if($data){
            return "恭喜您,签到成功！\n积分余额：${score_balance}分\n获得积分：+1 分";;
        }else{
            return "签到失败,请重试";//系统错误
        }
    }
	private function randomCoupon($w=""){
		$words = array(
				"女装",
				"男装",
				"童装",
				"母婴",
				"情人节"
			      );
		if($w != null){
			$word = $w;
		}else{
			$word = $words[mt_rand(0,count($words) - 1)];
		}
		$pageNo = mt_rand(1,20);
		$list =  $this->api->get_coupon($word,20,$pageNo);
		$coupon = $list[mt_rand(0,count($list) - 1)];
		$small_images = json_decode($coupon['small_images'],true);
		$logo = "";
		if(count($small_images) > 0){
			$logo = $small_images[0];
		}
		$title = $coupon['title'];
		$coupon_click_url = $coupon['coupon_click_url'];
		$coupon['tpwd'] = $this->api->get_tpwd($title,$coupon_click_url,$logo);
		return $coupon;
	}
    private function sendCoupon($w = ""){
        $coupon = $this->randomCoupon($w);
        log_message(D,"coupon:".json_encode($coupon));
		$title = $coupon['title'];
		$couponInfo = $coupon['coupon_info'];
		$zk_final_price = $coupon['zk_final_price'];
		$commission_rate = $coupon['commission_rate'];
		$coupon_click_url= $coupon['coupon_click_url'];
		$tpwd = $coupon['tpwd'];
		preg_match_all('/\d+/',$couponInfo,$arr);
		$now_price = ((float)$zk_final_price - (float)$arr[0][1])."";
        $retMsg = sprintf($this->tpwd_msg,$title,$zk_final_price,$arr[0][1],$now_price,$tpwd);
        $introduce = "";
        log_message(D,"introduce:".$introduce);
        if($introduce){
            $retMsg.="\n推荐理由:".$introduce;
        }
		$this->wechat->text ( $retMsg )->reply ();
	}
    

    private function command(){
        $withdrawals_amount_limit = $this->config->item('withdrawals_amount_limit');
        $reply_msg = "【帮助】虞城之家助手使用方法\n" .
        "【邀请查询】查询下线提成信息\n" .
        "【指令】查询所有指令信息\n" .
        "【签到】每天签到领积分，积分可用于兑换物品，参与评级\n" .
        "【余额】查询积分、余额、提现、订单、分红的情况\n" .
        "【提现】分红满{$withdrawals_amount_limit}元客服微信转账\n" .
        "【单号】发送订单号，系统会根据此订单号绑定返利订单确保成功返利\n" .
        "【推广】获取专属虞城之家推广二维码，分享赚钱\n" .
        "【买XX】某类产品优惠推荐\n".
        "【用户号】获取用户在公众号的用户号，用于在微信客服中绑定公众号用户";
        log_message(D,"msg:".$reply_msg);
        return $reply_msg;
    }

    
    private function event_handler() {
        $msg = $this->wechat->getRevData();
        $event = $this->event();
        
		switch ($event['event']) {
            case Wechat::EVENT_SUBSCRIBE :
                $userinfo['openid'] = $this->openid() ;
                $userinfo['agency_id']= "";
                log_message(D,"type:".$this->type);
                if($this->type == 2){
                    $userinfo = $this->wechat->getUserInfo($this->openid() );
                }
                log_message(D,"userinfo:".json_encode($userinfo,true));
                $wx_user = $this->wx_user->get_wx_user($this->openid() );
                if(!$wx_user){
                    $event_key = $msg['EventKey'];
                    if($event_key != ""){
                        if(count($arr) > 0){
                        $arr = explode("_",$event_key);
                            $qrcode_id = $arr[1];
                            $qrcode = $this->qrcode->get_qrcode_by_id($qrcode_id);
                            $agency  = $this->agency->get_agency(array("user_id"=>$qrcode['user_id']),true);
                            $userinfo['agency_id'] = $agency['id'];
                        }
                    }
                    $this->init_user($userinfo);
                   
                }else{
                    $data=array(
                        'subscribe'=>1
                    );
                    $this->wx_user->update_by_openid($this->openid() ,$data);
                }

                

				$reply_msg = $this->help();
				$this->wechat->text ($reply_msg)->reply ();
                break;
            case Wechat::EVENT_UNSUBSCRIBE:
                //取消关注
            
                $data=array(
                    'subscribe'=>0
                );
                $this->wx_user->update_by_openid($this->openid() ,$data);
                break;
            case Wechat:: EVENT_MENU_CLICK:
                if($msg['EventKey'] == '快捷指令'){
                    $withdrawals_amount_limit = $this->config->item('withdrawals_amount_limit');
                    $reply_msg = "【帮助】虞城之家助手使用方法\n" .
                    "【邀请查询】查询下线提成信息\n" .
                    "【指令】查询所有指令信息\n" .
                    "【签到】每天签到领积分，积分可用于兑换物品，参与评级\n" .
                    "【余额】查询积分、余额、提现、订单、分红的情况\n" .
                    "【提现】分红满{$withdrawals_amount_limit}元客服微信转账\n" .
                    "【单号】发送订单号，系统会根据此订单号绑定返利订单确保成功返利\n" .
                    "【推广】获取专属虞城之家推广二维码，分享赚钱\n" .
                    "【买XX】某类产品优惠推荐\n".
                    "【用户号】获取用户在公众号的用户号，用于在微信客服中绑定公众号用户";
                    log_message(D,"msg:".$reply_msg);
                    $this->wechat->text ($reply_msg)->reply ();
                }
                break;
		}
	}

}