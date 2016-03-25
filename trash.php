<?php 
// BRINGS IN CLASS TO COMPARE FINAL IMAGE % DIFFERENCES

include('img_class.php');

// SET THE CROP FOR THE CAMERA IMAGE TO INCLUDE ONLY TRASH CAN

// The Width of the crop
$w1 = '76';
// The Height of the crop
$h1 = '61';
// The X coordinate of the cropped region's top left corner
$l1 = '212';
// The Y coordinate of the cropped region's top left corner
$t1 = '225';

// CROPS CAMERA IMAGE FOR TRASH DAY

// Grabs the most recent image (should be image with trash can, unless forgotten)
$filename = shell_exec('ls -t *.jpg | head -1');

$filename = str_replace("\n", '', $filename); // remove new lines
$filename = str_replace("\r", '', $filename); // remove carriage returns

$thumb = new Imagick($filename);
$thumb->cropImage($w1,$h1,$l1,$t1);
$thumb->writeImage('trashday.jpg');

// Grabs yesterday's image (should be image without trash can, unless left out all week)
$filename = shell_exec('ls -t *.jpg | tail -n1');

$filename = str_replace("\n", '', $filename); // remove new lines
$filename = str_replace("\r", '', $filename); // remove carriage returns

$thumb = new Imagick($filename);
$thumb->cropImage($w1,$h1,$l1,$t1);
$thumb->writeImage('nottrashday.jpg');

// CALCULATE DIFFERENCE BETWEEN BOTH CROPPED IMAGES

$diff = new imagediff('trashday.jpg', 'nottrashday.jpg');
$percentdiff = ($diff->diff() * 100 );

// Remove all images so the process can repeat next week
shell_exec('rm *.jpg');

// SEND AN EMAIL IF THE TRASH HAS NOT BEEN TAKEN OUT

// If the percentage difference between the two images is over 70, send an email notification
if($percentdiff < 70){

// Include PHP Mailer class
require_once "Mail-1.3.0/Mail.php";

// Put your email information here
$from = '<SENDER@gmail.com>';
$to = '<RECIPIENT@gmail.com>';
$subject = 'You Forgot to Take Out the Trash!';
$body = "DOH!  Hurry up and take out the trash before it's too late!  (The difference in images is ".$percentdiff."%)";

$headers = array(
    'From' => $from,
    'To' => $to,
    'Subject' => $subject
);

// Gmail smtp settings
$smtp = Mail::factory('smtp', array(
        'host' => 'ssl://smtp.gmail.com',
        'port' => '465',
        'auth' => true,
        'username' => 'USER@gmail.com',
        'password' => 'PASSWORD'
    ));

$mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
    echo('<p>' . $mail->getMessage() . '</p>');
}

}

?> 
