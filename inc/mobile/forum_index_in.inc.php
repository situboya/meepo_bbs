<?php /*折翼天使资源社区 www.zheyitianshi.com*/
global $_W,$_GPC; load()->model('mc'); checkBbsUser(); 
//判断是否微信用户
// if(empty($_W['openid'])){ message('',$this->createMobileUrl('pc'),error); } 
$table = 'meepo_bbs_set';
$tempalte = $this->module['config']['name']?$this->module['config']['name']:'default'; 
$forum = getSet(); 
$sql = "SELECT * FROM ".tablename('modules')." WHERE name = :name "; 
$params = array(':name'=>'meepo_nsign');
$nsign = pdo_fetch($sql,$params); 
$title = $forum['title']; 
$advs = getAdv();
 $cats = getCat();
 $navs = getnavs();
  $tops = getTop();
   $id = $_GPC['id'];
    $jings = getJing($id);
     $start = $_GPC['start']?intval($_GPC['start']):0;
      $params = array(':uniacid'=>$_W['uniacid']);
       $where = " AND 1 ";
        if($id){ $return = check_look($id);
         if(is_error($return)){ message($return['message'],$this->createMobileUrl('forum'),error); }
          $where .= " AND fid = :fid "; $params[':fid'] = $id; } 
          if(!empty($_GPC['srchtxt'])){ $srchtxt = trim($_GPC['srchtxt']); 
          $where .= " AND title like '%{$srchtxt}%' "; 
          $sql = "SELECT * FROM ".tablename('meepo_bbs_topics')." WHERE uniacid = :uniacid $where ORDER BY createtime DESC limit $start,8"; 
          $topics = pdo_fetchall($sql,$params); 
        }else{ 
          $sql = "SELECT * FROM ".tablename('meepo_bbs_topics')." WHERE uniacid = :uniacid $where ORDER BY last_reply_at DESC limit $start,10"; 
          $topics = pdo_fetchall($sql,$params);
       } 
          foreach ($topics as $data) { 
          	$user = mc_fetch($data['uid'],array('avatar','groupid','nickname'));
          	$data['author']['avatar'] = tomedia($user['avatar']); 
          	$data['author']['group'] = getGroupTitle($user['groupid']); 
          	$data['author']['nickname'] = $user['nickname']; 
          	$data['content'] = replace_em($data['content']); 
          	$data['thumb'] = iunserializer($data['thumb']); 
          	$data['thumb_num'] = count($data['thumb']); 
          	$id = $data['id']; 
          	$sql = "SELECT COUNT(*) FROM ".tablename('meepo_bbs_topic_share')." WHERE tid = :tid"; 
          	$params = array(':tid'=>$id); 
          	$sharenum = pdo_fetchcolumn($sql,$params); 
          	$sql = "SELECT COUNT(*) FROM ".tablename('meepo_bbs_topic_replie')." WHERE tid = :tid"; 
          	$params = array(':tid'=>$id); $replynum = pdo_fetchcolumn($sql,$params); 
          	$sql = "SELECT COUNT(*) FROM ".tablename('meepo_bbs_topic_like')." WHERE tid = :tid"; 
          	$params = array(':tid'=>$id); 
          	$likenum = pdo_fetchcolumn($sql,$params); 
          	$sql = "SELECT SUM(fee) FROM ".tablename('meepo_bbs_begging')." WHERE ttid = :ttid AND status = 1"; 
          	$params = array(':ttid'=>$id); 
          	$begging_money = pdo_fetchcolumn($sql,$params); 
          	$sql = "SELECT name FROM ".tablename('meepo_bbs_threadclass')." WHERE typeid = :typeid"; 
          	$params = array(':typeid'=>$data['fid']); 
          	$data['ctitle'] = pdo_fetchcolumn($sql,$params); 
          	$data['sharenum'] = $sharenum; 
          	$data['replynum'] = $replynum; 
          	$data['likenum'] = $likenum; 
          	$data['begging_money'] = $begging_money; 
          	$data['last_reply_at'] = date('Y-m-d',$data['last_reply_at']); 
          	$list[] = $data; } unset($topics); 
          	$sql = "SELECT COUNT(*) FROM ".tablename('meepo_bbs_topics')." WHERE uniacid = :uniacid"; 
          	$params = array(':uniacid'=>$_W['uniacid']); 
          	$topic_num = pdo_fetchcolumn($sql,$params); 
          	$sql = "SELECT COUNT(*) FROM ".tablename('mc_members')." WHERE uniacid = :uniacid"; 
          	$params = array(':uniacid'=>$_W['uniacid']); 
          	$member_num = pdo_fetchcolumn($sql,$params); 
          	$_share = array(); 
          	$_share['title'] = $forum['title']; 
          	$_share['desc'] = $forum['desc']; 
          	$_share['imgUrl'] = tomedia($forum['logo']); 
          	$_share['url'] = $this->createMobileUrl('forum',array('uid'=>$_W['member']['uid'])); 
          	if(!empty($_W['isajax'])){ 
          		ob_start(); 
          		include $this->template($tempalte.'/templates/forum/index_ajax'); 
          		$data = ob_get_contents(); ob_clean();
          		die($data);
          	}else{ 
          		include $this->template($tempalte.'/templates/forum/index_in');
          	} 