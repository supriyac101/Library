<?php
session_start();
include("securimage.php");
$img = new Securimage();
$valid = $img->check($_POST['code']);
$submit_form=true;

if($valid != true) 
{
$submit_form=false;
$security_msg="Enter valid security code!";
} 

$facilityname=trim(stripslashes($_POST['facilityname']));
$contactname=trim(stripslashes($_POST['contactname']));
$title=trim(stripslashes($_POST['title']));
$phone=trim(stripslashes($_POST['phone']));
$fax=trim(stripslashes($_POST['fax']));
$add1=trim(stripslashes($_POST['add1']));
$add2=trim(stripslashes($_POST['add2']));
$city=trim(stripslashes($_POST['city']));
$state=trim(stripslashes($_POST['state']));
$zip=trim(stripslashes($_POST['zip']));
$beds=trim(stripslashes($_POST['beds']));
$email=trim(stripslashes($_POST['email']));

$sample1=trim(stripslashes($_POST['sample1']));
$sample2=trim(stripslashes($_POST['sample2']));
$sample3=trim(stripslashes($_POST['sample3']));
$sample4=trim(stripslashes($_POST['sample4']));
$sample5=trim(stripslashes($_POST['sample5']));


if($submit_form==false)
{
?>
<form action="/free-samples?error=yes" method="post" name="form2">
<?php
foreach($_POST as $key=>$value)
{
?>
<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
<?php
}
?>
</form>
<script>
document.form2.submit();
</script>
<?php
//header("location:".$_SERVER['HTTP_REFERER']."?error=yes");
//exit();
}
else
{
include 'library.php'; // include the library file
include "classes/class.phpmailer.php"; // include the class name

$message="Hi Admin, <br />Below is the Free Samples information:- <br /><br />";
$message.="Facility Name: $facilityname <br />";
$message.="Contact Name: $contactname<br />";
$message.="Title: $title<br />";
$message.="Phone Number: $phone<br />";
$message.="Fax Number: $fax<br />";
$message.="Mailing Address: $add1, $add2<br />";
$message.="City: $city<br />";
$message.="State: $state<br />";
$message.="Zipcode: $zip<br />";
$message.="Number of Beds: $beds<br />";
$message.="E-Mail: $email<br />";
$message.="Sample 1: $sample1<br />";
$message.="Sample 2: $sample2<br />";
$message.="Sample 3: $sample3<br />";
$message.="Sample 4: $sample4<br />";
$message.="Sample 5: $sample5<br />";

$message.="<br />Thank you";
$subject="Free samples - $title - Charm-tex.com";
$to='shelly@charm-tex.com';

/*// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'To: '.$to.' <'.$to.'>' . "\r\n";
$headers .= 'From: '.$contactname.' <'.$email.'>' . "\r\n";
// Mail it

mail($to, $subject, $message, $headers);*/

	
	$mail	= new PHPMailer; // call the class 
	$mail->IsSMTP(); 
	$mail->Host = SMTP_HOST; //Hostname of the mail server
	$mail->Port = SMTP_PORT; //Port of the SMTP like to be 25, 80, 465 or 587
	$mail->SMTPAuth = true; //Whether to use SMTP authentication
	$mail->SMTPSecure = 'tls';
	$mail->Username = SMTP_UNAME; //Username for SMTP authentication any valid email created in your domain
	$mail->Password = SMTP_PWORD; //Password for SMTP authentication
	$mail->AddReplyTo($email, $contactname); //reply-to address
	$mail->SetFrom("info@charm-tex.com", "Charm Tex"); //From address of the mail
	// put your while loop here like below,
	$mail->Subject = $subject; //Subject od your mail
	$mail->AddAddress($to, "Charm Tex"); //To address who will receive this email
	$mail->MsgHTML($message); //Put your body of the message you can place html code here
	 
	$send = $mail->Send(); //Send the mails

header("location:/thank-you");
exit();
}


?>