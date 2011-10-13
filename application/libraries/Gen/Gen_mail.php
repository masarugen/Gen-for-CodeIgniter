<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * メール送受信用クラス
 *
 * メール送受信関連の処理を行う
 *  *
 * @package 
 * @access  public
 * @author Masaru Hashizume <hashizume@sion.ne.jp>
 * @create  2010/06/20
 * @version 1.00
 **/
ci_require('Qdmail', 'Qdmail');
class Gen_mail {

	// --------------------------------------------------------------------

	var $to       = array(); //To
	var $to_name  = array(); //To名前
	var $cc       = array(); //Cc
	var $cc_name  = array(); //Cc名前
	var $bcc      = array(); //Bcc
	var $bcc_name = array(); //Bcc名前
	var $from;         //From
	var $from_name;    //From名前
	var $replyto;      //Reply-To
	var $replyto_name; // Reply-To名前
	var $subject;      //件名
	var $message;      //本文

	// --------------------------------------------------------------------

	/** 
	 * コンストラクタ
	 * 
	 * 初期値を設定する<br>
	 * 
	 * @access public
	 * @return void
	 */
	function __construct()
	{
	}

	// --------------------------------------------------------------------

	/** 
	 * Fromアドレス設定
	 * 
	 * Fromアドレスを設定する<br>
	 * 
	 * @access public
	 * @return void
	 */
	function set_from($mail, $name=null)
	{
		$this->from = $mail;
		$this->from_name = $name;
	}

	// --------------------------------------------------------------------

	/** 
	 * Reply-Toアドレス設定
	 * 
	 * Reply-Toアドレスを設定する<br>
	 * 
	 * @access public
	 * @return void
	 */
	function set_replyto($mail, $name=null)
	{
		$this->replyto = $mail;
		$this->replyto_name = $name;
	}

	// --------------------------------------------------------------------

	/** 
	 * Toアドレス設定
	 * 
	 * Toアドレスを設定する<br>
	 * 
	 * @access public
	 * @return void
	 */
	function add_to($mail, $name=null)
	{
		array_push($this->to, $mail);
		array_push($this->to_name, $name);
	}

	// --------------------------------------------------------------------

	/** 
	 * Ccアドレス設定
	 * 
	 * Ccアドレスを設定する<br>
	 * 
	 * @access public
	 * @return void
	 */
	function add_cc($mail, $name=null)
	{
		array_push($this->cc, $mail);
		array_push($this->cc_name, $name);
	}

	// --------------------------------------------------------------------

	/** 
	 * Bccアドレス設定
	 * 
	 * Bccアドレスを設定する<br>
	 * 
	 * @access public
	 * @return void
	 */
	function add_bcc($mail, $name=null)
	{
		array_push($this->bcc, $mail);
		array_push($this->bcc_name, $name);
	}

	// --------------------------------------------------------------------

	/** 
	 * 件名設定
	 * 
	 * 件名を設定する<br>
	 * 
	 * @access public
	 * @return void
	 */
	function set_subject($subject)
	{
		$this->subject = $subject;
	}

	// --------------------------------------------------------------------

	/** 
	 * 本文設定
	 * 
	 * 本文を設定する<br>
	 * 
	 * @access public
	 * @return void
	 */
	function set_message($message)
	{
		$this->message = $message;
	}

	// --------------------------------------------------------------------
	
	/** 
	 * メール送信
	 * 
	 * メールを送信する<br>
	 * 
	 * @access public
	 * @param string
	 * @return void メールタイプ
	 */
	function send_mail($type="text", $debug=false)
	{
		//メール送信
		$mail = new Qdmail(null, null, null, $debug);
		$mail -> lineFeed("\n");
		$mail->from($this->from, $this->from_name);
		$mail->to($this->to, $this->to_name);
		if (count($this->cc) > 0) $mail->cc($this->cc, $this->cc_name);
		if (count($this->bcc) > 0) $mail->bcc($this->bcc, $this->bcc_name);
		$mail->replyto($this->replyto, $this->replyto_name);
		$mail->subject($this->subject);
		$mail->text($this->message);
		$ret = $mail->send();
		$this->reset();
		return $ret;
	}

	// --------------------------------------------------------------------

	function reset()
	{
		$this->to        = array(); //To
		$this->to_name   = array(); //To
		$this->cc        = array(); //Cc
		$this->cc_name   = array(); //Cc
		$this->bcc       = array(); //Bcc
		$this->bcc_name  = array(); //Bcc
		$this->from      = '';   //From
		$this->from_name = '';   //From
		$this->replyto   = '';   //Reply-To
		$this->replyto_name = '';//Reply-To
		$this->subject   = '';   //件名
		$this->message   = '';   //本文
	}

	// --------------------------------------------------------------------

}

