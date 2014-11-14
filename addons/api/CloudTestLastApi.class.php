<?php
/**
 *
 * @author  ŷ����
 * @name �ƶ˲����ֻ���API���
 * @abstract �̳�Action.class.php
 * @version1.0
 *
 *������룺
 *
 *		1������,����ϵͳ�������ж�,�����������
 *			{
 * 				"message":"����",
 * 				"code":"00000"
 * 			}
 *
 *
 *		2����������ֵΪ��
 *			{
 * 				"message":"��������Ϊ�գ�",
 * 				"code":"00001"
 * 			}
 *
 *		3������������ϢΪ��
 *
 * 			{
 * 				"message":"û���µ�������Ϣ��",
 * 				"code":"00002"
 * 			}
 *
 * 		4�����²���Ϊ��
 *
 * 			{
 * 				"message":"û���µĲ�����Ϣ��",
 * 				"code":"00003"
 * 			}
 * 		5��û����ʷ������Ϣ��
 *
 * 			{
 * 				"message":"û����ʷ������Ϣ�ˣ�",
 * 				"code":"00004"
 * 			}
 * 		6��û�и�����ʷ����������
 *
 * 			{
 * 				"message":"û�и�����ʷ���������ˣ�",
 * 				"code":"00005"
 * 			}
 *		7���ظ�����ʧ��
 *			{
 * 				"message":"�ظ�ʧ�ܣ������ԣ�",
 * 				"code":"00006"
 * 			}
 * 		8����ʱ���û�п���
 *
 * 			{
 * 				"message":"��ʱ���û�п��ԣ�",
 * 				"code":"00007"
 * 			}
 *
 *
 */

class CloudTestLastApi extends Api{

	/*
	 *
	 * ��ȡ��ǰ�ҳ���ID
	 *
	 */
	private function getParent_id() {
		$this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
		$user  = model('User')->getUserInfo($this->user_id);
		$login = $user['login'];
		//$login = '13451672388';


		return $login;
	}


	/*
	 *
	 * ��ȡ��ǰ�ҳ��ĺ��ӵ�ID
	 *
	 */

	private function getStudent_id() {
		$this->user_id = empty($this->user_id) ? $this->mid : $this->user_id;
		$user  = model('User')->getUserInfo($this->user_id);
		$login = $user['login'];
		//$login = '13451672388';

		$student_idarr = M()->table("ts_students_join_users sju")->where("sju.staff_id='".$login."'")->field("student_id")->select();
		$student_id = $student_idarr[0]["student_id"];

		return $student_id;
	}

	private function getStudentClass_id() {
		$indexSubject = $this->getSubject();
		return $indexSubject[0]['class_id'];
	}

	/*
	 * �˽ӿ�������¼������Ŀ�б�ĳ�ʼ������
	 * ��������
	 * ����ʽ��GET
	 * ���أ�
	 * 1���ɹ����������б��json��ʽ��
	 * 			[
	 * 				{
	 * 					"subject_type":"��Ŀid",
	 * 					"subject_type_desc":"��Ŀ����",
	 * 					"class_id":"�༶id",
	 * 					"grade_id":"�꼶id",
	 * 					"school_id":"ѧУid",
	 * 					"uid":"���ſγ���ʦ���û�id",
	 * 					"login":"���ſγ���ʦ��ID",
	 * 					"uname":"�̴��ſγ̵���ʦ����"
	 * 				},
	 * 				{},
	 * 				{}
	 * 			]
	 * 2��ʧ�ܣ�
	 * 			{
	 * 				"message":"����",
	 * 				"code":"00000"
	 * 			}
	 *
	 *
	 */

	public function getIndex() {

		//����˽�з�������ȡ��
		$indexSubject = $this->getSubject();

		//test
		//dump($indexSubject);

		exit(json_encode($indexSubject));

	}


	/*
	 *
	 * �˷���Ϊ�ڲ�����
	 * ���ڻ�ȡ�ҳ���¼���Լ����ӵ���Ϣ
	 * ���ڰ༶���꼶��ѧУ����ѧ�Ŀ�Ŀ����Ŀ����Ӧ����ʦ
	 *
	 */

	private function getSubject() {

		$student_id = $this->getStudent_id();

		//���ݰ༶�õ��˰༶��ص���ʦ�Լ���Ŀ
		$teach_su_cllist = M()->table("ts_teacher_subject_classes tsc")
		->join("ts_school_class_students scs on scs.class_id=tsc.class_id")
		->join("ts_user u on u.login=tsc.login")
		->join("ts_subject_master sm on sm.subject_type=tsc.subject_type")
		->where("scs.login='".$student_id."' and sm.subject_type != '00'")
		->field('sm.subject_type,sm.subject_type_desc,scs.class_id,scs.grade_id,scs.school_id,u.uid,u.login,u.uname')
		->select();


		if ($teach_su_cllist) {
			//�ҵ����еĿ�Ŀ�Լ���Ӧ��ʦ��Ϣ����json��ʽ���ظ��ͻ���
			//exit(json_encode($indexSubject));
			return $teach_su_cllist;

		}else {		//�������������Ϊ�գ�

			$message['message'] = "the subject is empty��";
			$message['code']    = "00000";
			//exit( json_encode( $message ) );

			return $message;
		}


	}

	/*
	 * �˽ӿ�������¼����ҳ�桢������ҳ��ĳ�ʼ��
	 * ÿ�ſ�Ŀ���������Ե����ݡ���Ŀ��ʱ��
	 * ÿ�ſ�Ŀ�����²���������
	 *
	 * ��������
	 *
	 * ����ʽ��GET
	 *
	 * ���أ�
	 * 1���ɹ����������б��json��ʽ��
	 * 			[
	 * 				{
	 * 					"subject_type":"��Ŀid",
	 * 					"subject_type_desc":"��Ŀ����",
	 * 					"class_id":"�༶id",
	 * 					"grade_id":"�꼶id",
	 * 					"school_id":"ѧУid",
	 * 					"uid":"���ſγ���ʦ���û�id",
	 * 					"login":"���ſγ���ʦ��ID",
	 * 					"uname":"�̴��ſγ̵���ʦ����",
	 * 					"messagecount":"���ſγ�����������Ŀ",
	 * 					"messagetime":"����ʱ��",
	 * 					"testcount":"���ſγ����²�����Ŀ"
	 * 				},
	 * 				{},
	 * 				{}
	 * 			]
	 * 2��ʧ�ܣ�
	 * 			{
	 * 				"message":"����",
	 * 				"code":"00001"
	 * 			}
	 *
	 *
	 */


	public function getinitIndex() {
		//�õ�����ҳ���Ŀ
		$indexSubject = $this->getSubject();

		for ($i = 0; $i < count($indexSubject); $i++) {

			//��ÿ�ſ�Ŀ��������Ϣд�뵽indexSubject������,type:false,ֻ����ͳ��
			$subjectMessages = $this->getOneSubjectMessages($indexSubject[$i]['subject_type'],$indexSubject[$i]['login'],false);

			//test
			//dump($subjectMessages);

			$indexSubject[$i]['newMessagesLastTime'] = $subjectMessages[0]['sendmessage_date'];
			$indexSubject[$i]['newMessagesCount'] = count($subjectMessages);


			//��ÿ�ſ�Ŀ��������Ϣд�뵽indexSbject������
			$indexSubject[$i]['testcount'] = count($this->getOneSubjectTest($indexSubject[$i]['subject_type']));

		}

		//test
		//dump($indexSubject);

		exit(json_encode($indexSubject));


	}



	/*
	 * ˽�з���
	 * ������
	 * 		$subject_type:�γ̵�ID
	 * 		$message_receiver�����ſγ̶�Ӧ����ʦ
	 * 		$type:true,����ͳ��;false,���ڲ鿴����Ϣ
	 *
	 * ˵����
	 * 		��ts_evaluation_paper_result_messages���и�����ʦ��id���ҳ�id�ҵ���Ϣ��¼����
	 * �ٸ���paper���ж��Ƿ�����ĳһ��Ŀ���͵ģ�������ڣ�����ӽ�ȥ
	 */
	private function getOneSubjectMessages($subject_type,$message_receiver,$type) {
		//��ȡ��ĸid
		$login = $this->getParent_id();



		//���ĳһ����ʦ�����ҳ���δ������Ϣ
		//��ʦ���ҳ��������ԣ�δ���ģ���������ʦ���ҳ����͵�
		$newMessages = M()->table("ts_evaluation_paper_result_messages eprm")
		->where("eprm.message_receiver='".$login."' AND eprm.isread = '0' AND comefrom='0' AND staff_id='".$message_receiver."'")
		->field("eprm.message_id,eprm.paper_id,eprm.staff_id,eprm.message_content,eprm.message_receiver,eprm.sendmessage_date,eprm.message_id,eprm.school_period_id,eprm.isread,eprm.comefrom,eprm.lastmessage_id")
		->order('eprm.sendmessage_date desc')
		->select();
		$messageCount = count($newMessages);
		for($i=0; $i<$messageCount; $i++) {
			if($this->isSubjectPaper($newMessages[$i]['paper_id'], $subject_type)) {
				$newMessageList[] = $newMessages[$i];

				if ($type) {

					//����ǲ鿴����Ϣ����ô�޸��ֶ�Ϊ�Ѷ�eprm.isread=1
					//ÿ�β������Ҫ�޸��ֶ�->field('message_id,staff_id,message_content,sendmessage_date,lastmessage_id')
					M()->table("ts_evaluation_paper_result_messages eprm")
					->where("eprm.message_id='".$newMessages[$i]['message_id']."'")
					->setField('isread','1');
				}

			}
		}

		return $newMessageList;
	}

	/*
	 *
	 * ���ã�
	 * 		�ж��Ƿ�����ĳһ��Ŀ
	 *
	 */

	private  function isSubjectPaper($paper_id,$subject_type) {

		$subjectPaper = M()->table("ts_evaluation_paper ep")
		->join("ts_evaluation_section es on es.section_id=ep.section_id")
		->join("ts_evaluation_section_book esb on es.section_book_id=esb.section_book_id")
		->where("ep.paper_id='".$paper_id."' AND esb.subject_type='".$subject_type."'")
		->select();

		//test
		//dump(M()->getLastsql());
		if($subjectPaper) {
			return true;
		}else {
			return false;
		}
	}

/*
	 * ˽�з���
	 * �ҵ�ĳһ�ſγ̵�ǰѧ�����²����Ծ�
	 * �����ҵ���ֻ��paper�������������Ծ����Ŀ
	 * ������
	 * 		$subject_type:�γ̵�ID
	 *
	 * ˵����
	 * 		��ts_evaluation_paper_result_messages���и�����ʦ��id���ҳ�id�ҵ���Ϣ��¼����
	 * �ٸ���paper���ж��Ƿ�����ĳһ��Ŀ���͵ģ�������ڣ�����ӽ�ȥ
	 */
	
	private function getOneSubjectTest($subject_type) {

		$student_id = $this->getStudent_id();

		//���Ƿ���Ҫ���͸��ҳ��Ĳ����ҳ���,�����Ǽҳ�δ����
		$subjectPaper = M()->table("ts_evaluation_paper_result epr")
		->join("ts_evaluation_paper ep on ep.paper_id=epr.paper_id")
		->join("ts_evaluation_section es on es.section_id=ep.section_id")
		->join("ts_evaluation_section esparent on esparent.section_id=es.parent_section_id AND esparent.section_book_id=es.section_book_id")
		->join("ts_evaluation_section_book esb on esb.section_book_id=es.section_book_id")
		->where("esb.subject_type='".$subject_type."' AND epr.user_id='".$student_id."' AND epr.send_status='1' AND epr.is_read='0'")
		->field("epr.paper_id,epr.user_total_score,es.section_title,esparent.section_title parent_section_title,ep.examdate")
		->order('ep.examdate')
		->select();
		//dump(M()->getLastSql());
		//dump($paperSublist);
		return $subjectPaper;

	}


	/*
	 *
	 * �˽ӿ����ڵ����ĳһ�ſγ̵�ʱ�����˿γ̵���ʦ���ҳ�����������
	 *
	 * ������
	 * 		subject_type �� ����Ŀγ�
	 * 		login ������Ŀγ̵���ʦ
	 *
	 * ����ʽ��POST
	 *
	 * ���أ�
	 * 1���ɹ����������б��json��ʽ��
	 * 			[
	 * 				{
	 * 					"message_id":"����ID",
	 * 					"paper_id":"�Ծ�ID",
	 * 					"staff_id":"��ʦID",
	 * 					"message_content":"��������",
	 * 					"message_receiver":"�ҳ���ID",
	 * 					"sendmessage_date":"��������1899-12-30 01:00:00",
	 * 					"school_period_id":"ѧ��ID,�Ժ�����õ�",
	 * 					"isread":"�Ƿ��Ѷ���0����û�ж���1�����Ѷ�",
	 * 					"comefrom":"1����ҳ�������ʦ��0������ʦ�����ҳ�",
	 * 					"lastmessage_id":"��һ��message_id"
	 * 				},
	 * 				{},
	 * 				{}
	 * 			]
	 * 2��ʧ�ܣ�
	 *		1����������ֵΪ��
	 *			{
	 * 				"message":"��������Ϊ�գ�",
	 * 				"code":"00001"
	 * 			}
	 *
	 *		2������������ϢΪ��
	 *
	 * 			{
	 * 				"message":"û���µ�������Ϣ��",
	 * 				"code":"00002"
	 * 			}
	 *
	 *
	 */
	public function getNewMessages() {
		$_REQUEST = array_merge($_GET,$_POST);

		if (!empty($_REQUEST['subject_type']) && !empty($_REQUEST['login'])) {

			//��ȡ��ʦ��ID
			$subject_type = $_REQUEST['subject_type'];

			//��ȡ��ʦ�̵�ѧ��ID
			$login = $_REQUEST['login'];

			//�õ����ſ�Ŀ��������Ϣ,type:true,��ȡ����������Ϣ����,����Ϊ�˸������ݿ���ֶ�
			$subjectMessages = $this->getOneSubjectMessages($subject_type,$login,true);
			if($subjectMessages) {
				exit( json_encode( $subjectMessages ) );
			}else {
				$message['message'] = 'û���µ�������Ϣ��';
				$message['code']    = '00002';
				$message['$subject_type'] = $subject_type;
				$message['$login'] = $login;
				exit( json_encode( $message ) );
			}
		}else {
			$message['message'] = '������������Ϊ�գ�';
			$message['code']    = '00001';
			exit( json_encode( $message ) );
		}

	}



	/*
	 *
	 * �˽ӿ����ڵ��ҳ�������ȡ��ʷ����ʱ����
	 *
	 * ������
	 * 		lastmessage_id �� ��ǰ���Ե���һ������message_id
	 *
	 * ����ʽ��POST
	 *
	 * ���أ�
	 * 1���ɹ����������б��json��ʽ��
	 * 			[
	 * 				{
	 * 					"message_id":"����ID",
	 * 					"paper_id":"�Ծ�ID",
	 * 					"staff_id":"��ʦID",
	 * 					"message_content":"��������",
	 * 					"message_receiver":"�ҳ���ID",
	 * 					"sendmessage_date":"��������1899-12-30 01:00:00",
	 * 					"school_period_id":"ѧ��ID,�Ժ�����õ�",
	 * 					"isread":"�Ƿ��Ѷ���0����û�ж���1�����Ѷ�",
	 * 					"comefrom":"1����ҳ�������ʦ��0������ʦ�����ҳ�",
	 * 					"lastmessage_id":"��һ��message_id"
	 * 				},
	 * 				{},
	 * 				{}
	 * 			]
	 * 2��ʧ��
	 *		1.lastmessage_idȡ������
	 *			{
	 * 				"message":"û����ʷ������Ϣ�ˣ�",
	 * 				"code":"00004"
	 * 			}
	 *
	 * 		2.������������Ϊ��
	 *			{
	 * 				"message":"��������Ϊ�գ�",
	 * 				"code":"00001"
	 * 			}
	 *
	 *
	 *
	 */

	public function getHistoryMessages() {

		//		//test
		//		$lastmessage_id = "1";
		//			for ($i=0;$i<10;$i++) {
		//			if(empty($lastmessage_id)){//����������Ե�û����������
		//					break;
		//				}
		//			$messages = M()->table("ts_evaluation_paper_result_messages eprm")
		//			->where("eprm.message_id='".$lastmessage_id."'")
		//			->field("eprm.message_id,eprm.paper_id,eprm.staff_id,eprm.message_content,eprm.message_receiver,eprm.sendmessage_date,eprm.message_id,eprm.school_period_id,eprm.isread,eprm.comefrom,eprm.lastmessage_id")
		//			->select();
		//			$lastmessage_id = $messages[0]['lastmessage_id'];
		//			$messagesList[] = $messages[0];
		//
		//			}
		//
		//			dump($messagesList);
		//			dump(json_encode($messagesList));
		//			die("");


		$_REQUEST = array_merge($_GET,$_POST);
		if (!empty($_REQUEST['lastmessage_id'])) {
			$lastmessage_id = $_REQUEST['lastmessage_id'];

			//�ҳ�������Ϊ����ʮ��
			for ($i=0;$i<10;$i++) {

				if(empty($lastmessage_id)){//����������Ե�û����������
					break;
				}

				$messages = M()->table("ts_evaluation_paper_result_messages eprm")
				->where("eprm.message_id='".$lastmessage_id."'")
				->field("eprm.message_id,eprm.paper_id,eprm.staff_id,eprm.message_content,eprm.message_receiver,eprm.sendmessage_date,eprm.message_id,eprm.school_period_id,eprm.isread,eprm.comefrom,eprm.lastmessage_id")
				->select();
				//Ѱ���������Ե���һ������
				$lastmessage_id = $messages[0]['lastmessage_id'];
				$messagesList[] = $messages[0];
			}

			if ($messagesList) {

				exit(json_encode($messagesList));
			}else {
				$message['message'] = 'û����ʷ������Ϣ�ˣ�';
				$message['code']    = '00004';
				exit( json_encode( $message ) );
			}
		}else {
			$message['message'] = '���͹���������Ϊ�գ�';
			$message['code']    = '00001';
			exit( json_encode( $message ) );
		}

			
	}




	/*
	 *
	 * �˽ӿ��������ظ���ʦ�����Ե�
	 * ������messageJSON
	 * 		ֱ�ӽ����ظ�������ת����json
	 * 		[
	 * 			{
	 * 				"message_id":"����",
	 * 				"paper_id":"�Ծ�ID������ԭ���Ĳ���",
	 * 				"staff_id":"�ҳ���ID������ԭ���Ĳ���",
	 * 				"message_content":"����ظ�������",
	 * 				"message_receiver":"�ҳ�ID������ԭ���Ĳ���",
	 * 				"sendmessage_date":"����",
	 * 				"school_period_id":"ѧ��ID������ԭ���Ĳ���",
	 * 				"isread":"��0���Ƿ��Ѷ���0����û�ж���1�����Ѷ�",
	 * 				"comefrom":"��1��1����ҳ�������ʦ��0������ʦ�����ҳ�",
	 * 				"lastmessage_id":"���뱻�ظ������Ե�message_id"
	 * 			}
	 * 		]
	 *
	 * ����ʽ��
	 *		POST
	 *
	 *���أ�
	 *		1���ɹ�
	 *			{
	 *				"message_id":"2841247443524���ظ������Ե�ID"
	 *				"sendmessage_date":"2013-11-14 20:15:33,�ջظ����Ե�",
	 *				"code":"11111"
	 *			}
	 *
	 *		2��ʧ��
	 *
	 * 			{
	 * 				"message":"�ظ�ʧ�ܣ������ԣ�",
	 * 				"code":"00006"
	 * 			}
	 * 		3��
	 * 			{
	 * 				"message":"��������Ϊ�գ�",
	 * 				"code":"00001"
	 * 			}
	 *
	 *
	 *
	 *
	 */

	public function setMessages() {

		$_REQUEST = array_merge($_GET,$_POST);
		$messageJSO = $_REQUEST['messageJSON'];
		
		$messageJSON  = stripslashes($messageJSO);
		
		if (!empty($_REQUEST['messageJSON'])) {
			$messagearr = json_decode($messageJSON);
			$messagearr = $this->object_array($messagearr);
			
			$messageModel =  M('Evaluation_paper_result_messages');

			//$messageModel->message_id = $messagearr[0]['message_id'];//����ID��Ψһ����
			$messageModel->message_id = $this->getRandOnlyId();
			//���ֲ����
			$messageModel->paper_id = $messagearr[0]['paper_id'];
			$messageModel->staff_id = $messagearr[0]['staff_id'];
			$messageModel->school_period_id = $messagearr[0]['school_period_id'];
			$messageModel->message_content = $messagearr[0]['message_content'];

			//$messageModel->sendmessage_date = $messagearr[0]['sendmessage_date'];//����ʱ�䣬��ȡ��ǰʱ��
			$messageModel->sendmessage_date =  date('Y-m-d H:i:s');

			//$messageModel->message_receiver = $messagearr[0]['message_receiver'];//д��login
			$messageModel->message_receiver = $this->getParent_id();

			$messageModel->isread = '0';
			$messageModel->comefrom = '1';
			$messageModel->lastmessage_id = $messagearr[0]['lastmessage_id'];


			if($messageModel->add()){
				$message['message_id'] = $messageModel->message_id;
				$message['sendmessage_date'] = $messageModel->sendmessage_date;
				
				$message['code']    = '11111';
				exit( json_encode( $message ) );
			}else{
				$message['message'] = '����ʧ�ܣ�����Ϊ�գ�';
				$message['code']    = '00006';
				$message['messageJSON']    = $messageJSON; 
				exit( json_encode( $message ) );
			}

		}else {
			$message['message'] = '���͹���������Ϊ�գ�';
			$message['code']    = '00001';
			$message['messageJSON']    = $messageJSON;
			exit( json_encode( $message ) );
		}



		//test

		//		$jsonStr = '{"message_id":null,"paper_id":"11","staff_id":"1","message_content":"�Ұ���","message_receiver":"1","sendmessage_date":"1899-12-30 01:00:00","school_period_id":"1","isread":"1","comefrom":"1","lastmessage_id":null}';
		//
		//		$messagearr = json_decode($jsonStr);
		//		$messagearr = $this->object_array($messagearr);
		//		$messageModel =  M('Evaluation_paper_result_messages');
		//
		//
		//
		//		//$messageModel->message_id = $messagearr['message_id'];//����ID��Ψһ����
		//		$messageModel->message_id = $this->getRandOnlyId();
		//		//���ֲ����
		//		$messageModel->paper_id = $messagearr['paper_id'];
		//		$messageModel->staff_id = $messagearr['staff_id'];
		//		$messageModel->school_period_id = $messagearr['school_period_id'];
		//		$messageModel->message_content = $messagearr['message_content'];
		//
		//		//$messageModel->sendmessage_date = $messagearr['sendmessage_date'];//����ʱ�䣬��ȡ��ǰʱ��
		//		$messageModel->sendmessage_date =  date('Y-m-d H:i:s');
		//
		//		$messageModel->message_receiver = $messagearr['message_receiver'];//д��login
		//		//$messageModel->message_receiver = $login;
		//
		//		$messageModel->isread = '0';
		//		$messageModel->comefrom = '1';
		//		$messageModel->lastmessage_id = $messagearr['lastmessage_id'];
		//
		//
		//		if($messageModel->add()){
		//			$message['message_id'] = $messageModel->sendmessage_date;
		//			$message['sendmessage_date'] = $messageModel->message_id;
		//
		//			$message['code']    = '11111';
		//			exit( json_encode( $message ) );
		//		}else{
		//			$message['message'] = '����ʧ�ܣ�����Ϊ�գ�';
		//			$message['code']    = '00001';
		//			exit( json_encode( $message ) );
		//		}


			
	}

	/*
	 * ��ȡΨһ�������
	 */
	private function getRandOnlyId() {
		//��ʱ��ض���,��������δ��2012-12-21��ʱ�����
		$endtime=1356019200;
		//2012-12-21ʱ���
		$curtime=time();
		//��ǰʱ���
		$newtime=$curtime-$endtime;
		//��ʱ���
		$rand=rand(0,99999);
		//��ȡ��λ���
		$all=$newtime.$rand;

		//$onlyid=base_convert($all,10,36);
		//��10����תΪ36���Ƶ�ΨһID
		return $all;
	}

	/*
	 * ��jsonת���������ת��Ϊ
	 * ��ֵ�Ե�����
	 */
	private function object_array($array){
		if(is_object($array)){
			$array = (array)$array;
		}
		if(is_array($array)){
			foreach($array as $key=>$value){
				$array[$key] = $this->object_array($value);
			}
		}
		return $array;
	}




	/*
	 *
	 * �˽ӿ����ڵ����ĳһ�ſγ̵�ʱ�����˿γ̵����²���
	 *
	 * ������
	 * 		subject_type �� ����Ŀγ�
	 *
	 * ����ʽ��POST
	 *
	 * ���أ�
	 * 1���ɹ����������б��json��ʽ��
	 * 			[
	 * 				{
	 * 					"paper_id":"�Ծ�ID",
	 * 					"user_total_score":"ѧ�����Ծ�ĵ÷�",
	 * 					"section_title":"֪ʶ���ӱ���",
	 * 					"parent_section_title":"֪ʶ�㸸����",
	 * 					"examdate":"����ʱ��",
	 * 					"percent":"�ٷ�λ",
	 * 					"avg":"�˿��԰༶��ƽ���ɼ�",
	 * 					"question":[
	 * 									{
	 * 										"question_id":"���Ծ���Ŀid",
	 * 										"question_index":"��Ŀ��",
	 * 										"question_content":"��Ŀ����",
	 * 										"question_content_text":"��Ŀ����",
	 * 										"question_type":"��Ŀ����",
	 * 										"answer":"��Ŀ��ȷ��",
	 * 										"difficulties":"��Ŀ�Ѷ�",
	 * 										"user_answer":"��Ŀ�����"
	 * 									},
	 * 									{},
	 * 									...
	 * 									{}
	 * 								]
	 * 				},	
	 * 				{},
	 * 				...
	 * 				{}
	 * 			]
	 * 2��ʧ�ܣ�
	 *		1����������ֵΪ��
	 *			{
	 * 				"message":"��������Ϊ�գ�",
	 * 				"code":"00001"
	 * 			}
	 *
	 *		2�����²���Ϊ��
	 *
	 * 			{
	 * 				"message":"���²���Ϊ�գ�",
	 * 				"code":"00003"
	 * 			}
	 *
	 *
	 */
	public function getNewTest() {
		//�õ�һ������ĳһ�ſγ��Ծ�id�����飬���µľͷ��Ѿ����͹�ȥ�˵�
		$_REQUEST = array_merge($_GET,$_POST);
		if(!empty($_REQUEST['subject_type'])) {
			$subject_type = $_REQUEST['subject_type'];
			//�õ�ĳһ�γ̵����������Ծ�ID�����������ݿ�
			$subjectPaper = $this->getOneSubjectTest($subject_type);
			$class_id = $this->getStudentClass_id();
			//�õ�����༶�����е���
			$classStudent = $this->getStudentClass($class_id);
			$classStudentCount = count($classStudent);
			
			$student_id = $this->getStudent_id();
			if($subjectPaper) {
				for($i=0;$i<count($subjectPaper);$i++) {
					$thisScore = $this->getStudentScore($subjectPaper[$i]['paper_id'], $student_id);
					$percentCount = $classStudentCount;
					$sumScore = 0;
					$kaoshiCount = 0;
					//ѭ������༶���ˣ��ó�ÿ���˵�$paper_id����Ӧ�ķ���
					for ($k=0;$k<$classStudentCount;$k++) {
						$score = $this->getStudentScore($subjectPaper[$i]['paper_id'], $classStudent[$k]['login']);
						if($score < $thisScore) {
							$percentCount--;
						}
						

						if(!empty($score) && $score !=0) {
							$kaoshiCount++;
						}
						
						$sumScore = $sumScore + $score;
						$score = 0;
					}
					$subjectPaper[$i]['percent'] = strval($percentCount/$classStudentCount*100);
					$subjectPaper[$i]['avg'] = strval($sumScore/$kaoshiCount);
					//$subjectPaper[$i]['avg'] = strval($sumScore/$classStudentCount);
					$subjectPaper[$i]['question'] = $this->getSomePaperDeatls($subjectPaper[$i]['paper_id']);
					
					//�õ�ĳһ�γ̵����������Ծ�ID�����������ݿ�
					$this->setTestToHis($subjectPaper[$i]['paper_id']);
					
				}
				//dump($subjectPaper);
				exit(json_encode($subjectPaper));
			}else {
				$message['message'] = '���²���Ϊ�գ�';
				$message['code']    = '00003';
				exit( json_encode( $message ) );
			}
		}else {
			$message['message'] = '���͹���������Ϊ�գ�';
			$message['code']    = '00001';
			exit( json_encode( $message ) );
		}

	}

	
	
	/*
	 * 
	 * ����Ѿ���������²����Ծ���ϸ����ô�������ݿ�����Ϊ�Ѷ�
	 * 
	 */
	
private function setTestToHis($paper_id) {
		
		$student_id = $this->getStudent_id();
		
		M()->table("ts_evaluation_paper_result epr")
		->where("epr.paper_id='".$paper_id."' AND epr.user_id='".$student_id."' AND epr.send_status = '1' AND epr.is_read='0'")
		->setField('is_read','1');
	}
	
	private function getStudentClass($class_id) {
		$class_student_cllist = M()->table("ts_school_class_students scs")
		->where("scs.class_id='".$class_id."'")
		->field('scs.class_id,scs.login')
		->select();
		
		return $class_student_cllist;
	}
	
	private function getStudentScore($paper_id,$student_id) {
		$studentScore = M()->table("ts_evaluation_paper_result epr")
		->where("epr.paper_id='".$paper_id."' AND epr.user_id='".$student_id."'")
		->field("epr.user_total_score")
		->select();
		if($studentScore) {
			$score = floatval($studentScore[0]['user_total_score']);
			return $score;
		}else {
			return "0";
		}
	}
	
	/*
	 * ���������ã�
	 */

	
	/*
	public function getNewTestTest() {
//		$j = "20";
//		$k = "100";
//		$p = floatval($j)/floatval($k)*100;
//		dump($p);
//		dump(strval($p));
		
		
		//�õ�һ������ĳһ�ſγ��Ծ�id�����飬���µľͷ��Ѿ����͹�ȥ�˵�
			$subject_type = "01";
			//�õ�ĳһ�γ̵����������Ծ�ID�����������ݿ�
			$subjectPaper = $this->getOneSubjectTest($subject_type);
			$class_id = $this->getStudentClass_id();
			//�õ�����༶�����е���
			$classStudent = $this->getStudentClass($class_id);
			$classStudentCount = count($classStudent);
			
			$student_id = $this->getStudent_id();
			if($subjectPaper) {
				for($i=0;$i<count($subjectPaper);$i++) {
					
					$thisScore = $this->getStudentScore($subjectPaper[$i]['paper_id'], $student_id);
					$percentCount = $classStudentCount;
					$sumScore = 0;
					//ѭ������༶���ˣ��ó�ÿ���˵�$paper_id����Ӧ�ķ���
					for ($k=0;$k<count($classStudentCount);$k++) {
						$score = $this->getStudentScore($subjectPaper[$i]['paper_id'], $classStudent[$i]['login']);
						
						if($score < $thisScore) {
							$percentCount--;
						}
						$sumScore = $sumScore + $score;
						
					}
					echo "============================";
					
					dump($sumScore);
					dump($thisScore);
					dump($percentCount);
					dump($classStudentCount);
					dump($percentCount/$classStudentCount);
					dump($sumScore/$classStudentCount);
					
					echo "********************************";
					$subjectPaper[$i]['percent'] = strval($percentCount/$classStudentCount*100);
					$subjectPaper[$i]['avg'] = strval($sumScore/$classStudentCount);
					$subjectPaper[$i]['question'] = $this->getSomePaperDeatls($subjectPaper[$i]['paper_id']);
					//$this->setTestToHis($subjectPaper[$i]['paper_id']);
					
				}
				dump($subjectPaper);
				exit(json_encode($subjectPaper));
			}else {
				$message['message'] = '���²���Ϊ�գ�';
				$message['code']    = '00003';
				exit( json_encode( $message ) );
			}

	}

	*/
	
	
	

		/*
	 *
	 * �˽ӿ����ڵ����ĳһ�ſγ̵�ʱ�����˿γ̵����²���
	 *
	 * ������
	 * 		subject_type �� ����Ŀγ�
	 * 		daybegin: ���ʱ��֮ǰ�Ĳ���  2013-10-10
	 *
	 * ����ʽ��POST
	 *
	 * ���أ�
	 * 1���ɹ����������б��json��ʽ��
	 * 			[
	 * 				{
	 * 					"paper_id":"�Ծ�ID",
	 * 					"user_total_score":"ѧ�����Ծ�ĵ÷�",
	 * 					"section_title":"֪ʶ���ӱ���",
	 * 					"parent_section_title":"֪ʶ�㸸����",
	 * 					"examdate":"����ʱ��",
	 * 					"percent":"�ٷ�λ",
	 * 					"avg":"�˿��԰༶��ƽ���ɼ�",
	 * 					"question":[
	 * 									{
	 * 										"question_id":"���Ծ���Ŀid",
	 * 										"question_index":"��Ŀ��",
	 * 										"question_content":"��Ŀ����",
	 * 										"question_content_text":"��Ŀ����",
	 * 										"question_type":"��Ŀ����",
	 * 										"answer":"��Ŀ��ȷ��",
	 * 										"difficulties":"��Ŀ�Ѷ�",
	 * 										"user_answer":"��Ŀ�����"
	 * 									},
	 * 									{},
	 * 									...
	 * 									{}
	 * 								]
	 * 				},	
	 * 				{},
	 * 				...
	 * 				{}
	 * 			]
	 * 2��ʧ�ܣ�
	 *		1����������ֵΪ��
	 *			{
	 * 				"message":"��������Ϊ�գ�",
	 * 				"code":"00001"
	 * 			}
	 *
	 *		2��û�и�����ʷ���������ˣ�
	 *
	 * 			{
	 * 				"message":"û�и�����ʷ���������ˣ���",
	 * 				"code":"00004"
	 * 			}
	 *
	 *
	 */
	public function getHistoryTest() {
		$_REQUEST = array_merge($_GET,$_POST);

		
//		$subjectPaper = $this->getTimeBeforTest("01","2013-12-30");
//		dump($subjectPaper);
//		dump(json_encode($subjectPaper));
		
		if (!empty($_REQUEST['daybegin']) && !empty($_REQUEST['subject_type'])) {
				$daybegin = $_REQUEST['daybegin'];
				$subject_type = $_REQUEST['subject_type'];
			
				//�õ�ĳһ�γ̵���ʷ�Ծ�ID
				$subjectPaper = $this->getTimeBeforTest($subject_type,$daybegin);
			//�õ���һ��paperid
		
			
			$class_id = $this->getStudentClass_id();
			//�õ�����༶�����е���
			$classStudent = $this->getStudentClass($class_id);
			$classStudentCount = count($classStudent);
			
			$student_id = $this->getStudent_id();
			if($subjectPaper) {
				for($i=0;$i<count($subjectPaper);$i++) {
					$thisScore = $this->getStudentScore($subjectPaper[$i]['paper_id'], $student_id);
					$percentCount = $classStudentCount;
					$sumScore = 0;
					$kaoshiCount = 0;
					//ѭ������༶���ˣ��ó�ÿ���˵�$paper_id����Ӧ�ķ���
					for ($k=0;$k<$classStudentCount;$k++) {
						$score = $this->getStudentScore($subjectPaper[$i]['paper_id'], $classStudent[$k]['login']);
						if($score < $thisScore) {
							$percentCount--;
						}
						if(!empty($score) && $score !=0) {
							$kaoshiCount++;
						}
						$sumScore = $sumScore + $score;
						$score = 0;
					}
					$subjectPaper[$i]['percent'] = strval($percentCount/$classStudentCount*100);
					$subjectPaper[$i]['avg'] = strval($sumScore/$kaoshiCount);
				//	$subjectPaper[$i]['avg'] = strval($sumScore/$classStudentCount);
					$subjectPaper[$i]['question'] = $this->getSomePaperDeatls($subjectPaper[$i]['paper_id']);
					
				}
				exit(json_encode($subjectPaper));
			}else {
				$message['message'] = '��ʷ����Ϊ�գ�';
				$message['code']    = '00003';
				exit( json_encode( $message ) );
			}
		}else {
			$message['message'] = '���͹���������Ϊ�գ�';
			$message['code']    = '00001';
			exit( json_encode( $message ) );
		}
	}

	
/*
	 *
	 * �˷������ڵõ�ĳʱ����ǰʮ�����ݵ�paper
	 */
	private function getTimeBeforTest($subject_type,$daybegin) {
		$student_id = $this->getStudent_id();

		//���Ƿ���Ҫ���͸��ҳ��Ĳ����ҳ���,�����Ǽҳ��Ѿ�����
		$subjectPaper = M()->table("ts_evaluation_paper_result epr")
		->join("ts_evaluation_paper ep on ep.paper_id=epr.paper_id")
		->join("ts_evaluation_section es on es.section_id=ep.section_id")
		->join("ts_evaluation_section esparent on esparent.section_id=es.parent_section_id AND esparent.section_book_id=es.section_book_id")
		->join("ts_evaluation_section_book esb on esb.section_book_id=es.section_book_id")
		->where("esb.subject_type='".$subject_type."' AND epr.user_id='".$student_id."' AND epr.send_status='1' AND epr.is_read='1' AND ep.examdate < '".$daybegin."'")
		->field("epr.paper_id,epr.user_total_score,es.section_title,esparent.section_title parent_section_title,ep.examdate")
		->order('ep.examdate')
		->limit(10)
		->select();
		//dump(M()->getLastSql());
		//dump($paperSublist);
		return $subjectPaper;

		

	}
	
	
	/*
	 * �õ�ĳ����ĳһ���Ծ����������
	 * �û���
	 * �Ծ���Ϣ
	 */
	private function getSomePaperDeatls($paper_id) {
		$student_id = $this->getStudent_id();
		
		//����paper_id�õ����Ծ����Ŀ����ɡ���ȷ�𰸵ȣ���paper����restut����detal�������û��Ĵ�ҲҪ�õ�
		$paper_detals=M()->table("ts_evaluation_paper_questions epq ")
		->join("ts_evaluation_question eq on epq.question_id=eq.question_id")
		->join("ts_evaluation_paper_result_detail eprd on eprd.question_id=eq.question_id")
		->where("epq.paper_id='".$paper_id."' AND eprd.user_id='".$student_id."' AND epq.paper_id=eprd.paper_id")
		->field("eq.question_id,epq.question_index,eq.question_content,eq.question_content_text,eq.question_type,eq.answer,eq.difficulties,eprd.user_answer")
		->select();
		return $paper_detals;
		
	}
	
	
		/*
	 *
	 * �˽ӿ����ڵ����ĳһ�ſγ̵�ʱ�����˿γ̵����²���
	 *
	 * ������
	 * 		subject_type �� ����Ŀγ�
	 * 		daybegin: ���ʱ��֮ǰ�Ĳ���  2013-10-09
	 * 		dayend: ���ʱ��֮��Ĳ���  2013-10-10
	 *
	 * ����ʽ��POST
	 *
	 * ���أ�
	 * 1���ɹ����������б��json��ʽ��
	 * 			[
	 * 				{
	 * 					"score":"���쿼�Ե�ƽ���ɼ�",
	 * 					"exam_date":"2013-11-14 ĳһ��"
	 * 				},	
	 * 				{},
	 * 				...
	 * 				{}
	 * 			]
	 * 2��ʧ�ܣ�
	 *		1����������ֵΪ��
	 *			{
	 * 				"message":"��������Ϊ�գ�",
	 * 				"code":"00001"
	 * 			}
	 *
	 *		2����ʱ���û�п��ԣ���
	 *
	 * 			{
	 * 				"message":"û�и�����ʷ���������ˣ���",
	 * 				"code":"00007"
	 * 			}
	 *
	 *
	 */
	public function getTestquXian() {
		$_REQUEST = array_merge($_GET,$_POST);

		$daybegin = $_REQUEST['daybegin'];
		$dayend = $_REQUEST['dayend'];
		$subject_type = $_REQUEST['subject_type'];
		
//		//test
//		
//		$daybegin = "2012-10-09";
//		$dayend = "2014-12-11";
//		$subject_type = "01";
//		$paperSublist = $this->getTimeTest($subject_type, $daybegin, $dayend);
//		dump(M()->getLastSql());
//		dump($paperSublist);
//		exit(json_encode($paperSublist));
		
		
		if (!empty($_REQUEST['daybegin']) && !empty($_REQUEST['dayend']) && !empty($_REQUEST['subject_type'])) {
			
			
			
			$paperSublist = $this->getTimeTest($subject_type, $daybegin, $dayend);
				
			//�õ���һ��paperid
			//ѭ������paperid���õ��������
			if($paperSublist) {
				exit(json_encode($paperSublist));
					
			}else {
				$message['message'] = '��ʱ���û�п��ԣ�';
				$message['code']    = '00007';
				exit( json_encode( $message ) );
			}

		}else {
			$message['message'] = '���͹���������Ϊ�գ�';
			$message['code']    = '00001';
			exit( json_encode( $message ) );
		}


	}
	
	
	
	/*
	 *
	 * �˷������ڵõ�ĳʱ����ڵ��Ծ�
	 */
	private function getTimeTest($subject_type,$daybegin,$dayend) {
		$student_id = $this->getStudent_id();

		
		//���Ƿ���Ҫ���͸��ҳ��Ĳ����ҳ���,�����Ǽҳ��Ѿ�����
		$subjectPaper = M()->table("ts_evaluation_paper_result epr")
		->join("ts_evaluation_paper ep on ep.paper_id=epr.paper_id")
		->join("ts_evaluation_section es on es.section_id=ep.section_id")
		->join("ts_evaluation_section esparent on esparent.section_id=es.parent_section_id AND esparent.section_book_id=es.section_book_id")
		->join("ts_evaluation_section_book esb on esb.section_book_id=es.section_book_id")
		->where("esb.subject_type='".$subject_type."' AND epr.user_id='".$student_id."' AND epr.send_status='1' AND epr.is_read='1' AND examdate between '".$daybegin."' and '".$dayend."'")
		->field("avg(epr.user_total_score) score,convert(examdate,date) exam_date")
		->group('exam_date')
		->order('exam_date desc')
		->select();
		

		return $subjectPaper;

		

	}
	





}

?>
