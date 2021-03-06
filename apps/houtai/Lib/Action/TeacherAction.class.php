<?php
/**
 * Created by PhpStorm.
 * User: Robert
 * Date: 14-9-20
 * Time: 下午4:33
 */

class TeacherAction extends Action {
    public function teacherInfo() {
        $model = model("TeacherInfo");
        import('ORG,Util.Page');//分页
        $Page = new Page ($model->sumTeacherInfo(), 5);//实例化分页类
        $TeacherInfoList = $model -> selectTeacherInfo($Page->firstRow.','.$Page->listRows);
        foreach($TeacherInfoList as $key=>$v){
            $title = $model -> schoolTitle($TeacherInfoList[$key]['school_id']);
            $TeacherInfoList[$key]['school_id'] = $title['title'];
        }
        $show=$Page->show();// 分页显示输出\
        $this->assign("page",$show);
        $this->assign("Teacher",$TeacherInfoList);
        $this->display();
    }

    private function genTree($items)
    {
        $tree = array(); //格式化好的树
        $tmpMap = array(); //临时扁平数据
        foreach ($items as $item) {
            $tmpMap[$item['area_id']] = $item;
        }
        foreach ($items as $item) {
            if (isset($tmpMap[$item['pid']]) != 0) {
                //加入儿子节点统计字段
                //$tmpMap[$item['parent_id']]['son_id'] = $tmpMap[$item['parent_id']]['son_id'] . '/' . $item['id'];
                $tmpMap[$item['pid']]['son'][] = & $tmpMap[$item['area_id']];
            } else {
                $tree[] = & $tmpMap[$item['area_id']];
            }
        }
        unset($tmpMap);
        return $tree;
    }
    //删除单个- -
    public function delTeacherInfo(){
//		if(!isAjax())return;
        $model = model("TeacherInfo");
        $Id = $_REQUEST['id'];
        $result = $model->delTeacherInfo($Id);
        if($result)
        {
            $this->ajaxReturn($result,"删除成功！",1);
        }
        else
        {
            $this->ajaxReturn(0,"删除失败！",0);
        }
    }
    //修改-列表
    public function selectSaveTeacherInfo(){
        $id = $_REQUEST['id'];
        $model = model("TeacherInfo");
        $result = $model -> selectSaveTeacherInfo($id);
        $this -> assign('data',$result);
        $this -> display('saveInfo');
    }
    //修改信息
    public function saveTeacherInfo(){
        $data["uname"]=$_REQUEST['uname'];
        $data["email"]=$_REQUEST['email'];
        $data["sex"]=$_REQUEST['sex'];
        $data["location"]=$_REQUEST['location'];
        $model = model("TeacherInfo");
        $saveInfo = $model -> saveTeacherInfo($_GET['id'],$data);
        if($saveInfo){
            $this -> success("修改成功");
        } else {
            $this -> error("修改失败");
        }
    }
    //复选框多个删除--5
    public function mulDeleteTeacherInfo(){
        $model = model("TeacherInfo");
        $ids = $_REQUEST['ids'];
        $id = explode(',',$ids);
        $count = count($id);
        $realCount = 0;
        for($i=0;$i<$count;$i++)
        {
            $delInfo = $model -> delTeacherInfo($id[$i]);
            if($delInfo)
            {
                $realCount++;
            }
        }

        if($count==$realCount)
        {
            $this->ajaxReturn($delInfo,'删除成功！',1);
        }
        else
        {
            $this -> ajaxReturn(0,"删除失败！",0);
        }
    }
    public function addInfo(){
        $school = model("ClassInfo");
        $area = model("SchoolInfo");
        $this->assign("schoolMsg",$school->getSchoolInfo());
        $address = $area -> selectAreaInfo();
        $tree = $this -> genTree($address);
        $this -> assign("address",$tree);
        $this -> display();
    }
    //添加
    public function addTeacherInfo()
    {
        $smodel = model("TeacherInfo");
        $data["login"]=$_REQUEST['login'];
        $data["password"]="b17de38bbe48bae511d9b71d473bd7d1";
        $data["login_salt"]="70556";
        $data["ctime"]=time();
        $data["uname"]=$_REQUEST['uname'];
        $data["email"]=$_REQUEST['email'];
        $data["sex"]=$_REQUEST['sex'];
        $data["province"]=$_REQUEST['province'];
        $data["city"]=$_REQUEST['city'];
        $data["area"]=$_REQUEST['area'];
        $model = model("SchoolInfo");
        $da1["area_id"]=$data["province"];
        $da2["area_id"]=$data["city"];
        $da3["area_id"]=$data["area"];
        $pro = $model -> selectAddressInfo($da1);
        $city = $model -> selectAddressInfo($da2);
        $area = $model -> selectAddressInfo($da3);
        $data["location"]=$pro["title"].$city["title"].$area["title"];
        $data["school_id"]=$_REQUEST['school_id'];
        $data["profile_id"]=1;
        $userProfile = $smodel -> selectTeacherLastInfo();
        $data['profile_no'] = $userProfile["profile_no"]+1;
        $data["login"]=$data['profile_no'];
        $result = $smodel -> addTeacherInfo($data);
//        dump(M()->getlastsql());
//        dump($data);die;
        if($result)
        {
            $this->success("添加成功！");
        }
        else
        {
            $this->error('添加失败！');
        }
    }
} 