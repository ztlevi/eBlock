<?php
header("content-type:application/json; charset:utf-8");
ini_set('display_errors', false);
// require_once('./PHPMailer-master/class.phpmailer.php');
// require_once("./PHPMailer-master/class.smtp.php");

$rawData = file_get_contents("php://input");
$parameters = json_decode($rawData);
$pdo = new PDO("mysql:host=localhost;dbname=e_guard","root","root");
$whitelistReview = new WhiteListReview;
$blacklistReview = new BlackListReivew;
$localReview = new LocalReview;
$bluecoatReview = new BlueCoatReview;
$control_facade = new Control_Facade($pdo,$whitelistReview,
                                     $blacklistReview,$localReview,$bluecoatReview);
if($parameters){
    switch($parameters->action){
    case "request" :
        $CheckURLresult = $control_facade->CheckURL($parameters->url);
        break;
    case "login" :
        $control_facade->CheckSession($parameters->username, $parameters->password);
        break;
    case "configini" :
        $control_facade->getInitalizationInfo();
        break;
    case "configsave" :
        $control_facade->saveConfiguration($parameters);
        break;
    }
}

class Control_Facade {
    private $db = null;
    private $whitelistReview = null;
    private $blacklistReview = null;
    private $localReview = null;
    private $bluecoatReview = null;

    // initialization for Control_Facade
    public function __construct(PDO $db, WhiteListReview $whitelistReview,
                                BlackListReivew $blacklistReview, LocalReview $localReview, BlueCoatReview $bluecoatReview){
        $this->db = $db;
        $this->whitelistReview = $whitelistReview;
        $this->blacklistReview = $blacklistReview;
        $this->localReview = $localReview;
        $this->bluecoatReview = $bluecoatReview;
    }

    // set the chain of responsibilities by using setSuccessor()
    public function CheckURL($url){
        $this->whitelistReview->setSuccessor($this->blacklistReview);
        $this->blacklistReview->setSuccessor($this->localReview);
        $this->localReview->setSuccessor($this->bluecoatReview);
        $this->bluecoatReview->setSuccessor($this->localReview);
        return $this->whitelistReview->handle($url, $this->db);
    }

    // Valid the user login
    public function CheckSession($username, $password){
        $query = ("SELECT * FROM eguard_user WHERE Username = '{$username}' "
                  . "AND Password = '{$password}'");
        $result = $this->db->query($query);
        if ($result->rowCount() != 0) {
            exit(json_encode("allow"));
        }
        else{
            exit(json_encode("deny"));
        }
    }
    public function getInitalizationInfo(){
        $query_blockedCategory = ("SELECT Category FROM block_category");
        $configuration->block_category = $this->db->query($query_blockedCategory)->fetchAll(PDO::FETCH_COLUMN, 0);
        $query_timer = ("SELECT Limitation FROM timer");
        $configuration->timer = $this->db->query($query_timer)->fetchAll(PDO::FETCH_COLUMN, 0);
        $query_white_list = ("SELECT URL FROM website_white_list");
        $configuration->white_list = $this->db->query($query_white_list)->fetchAll(PDO::FETCH_COLUMN, 0);
        $query_black_list = ("SELECT URL FROM website_black_list");
        $configuration->black_list = $this->db->query($query_black_list)->fetchAll(PDO::FETCH_COLUMN, 0);
        exit(json_encode($configuration));
    }

    public function saveConfiguration($configuration){
        $blockedCategories = $configuration->blockedCategories;
        $timer = $configuration->timer;
        $whitelist = $configuration->whitelist;
        $blacklist = $configuration->blacklist;
        $website = $configuration->website;
        $category = $configuration->category;
        $query_blockedCategory = ("delete FROM block_category");
        $result = $this->db->exec($query_blockedCategory);
        for($numOfBlockedCategories = 0;
            $numOfBlockedCategories < sizeof($blockedCategories);
            $numOfBlockedCategories++)
        {
            $query_blockedCategory = "INSERT INTO block_category (`Category`) VALUES ('{$blockedCategories[$numOfBlockedCategories]}');";
            $result = $this->db->exec($query_blockedCategory);
        };
        $query_timer = "UPDATE timer SET Limitation = $timer LIMIT 1;";
        $result = $this->db->exec($query_timer);
        preg_match_all('/.+?,/', $whitelist , $whitelist);
        $query_white_list = "DELETE FROM website_white_list";
        $result = $this->db->exec($query_white_list);
        for($numOfWhitelist = 0; $numOfWhitelist < sizeof($whitelist[0]); $numOfWhitelist++){
            $whitelist_URL = str_replace(array(","),"",$whitelist[0][$numOfWhitelist]);
            $query_white_list = "INSERT INTO website_white_list (`URL`) VALUES ('{$whitelist_URL}')";
            $result = $this->db->exec($query_white_list);
        }
        preg_match_all('/.+?,/', $blacklist , $blacklist);
        $query_black_list = "DELETE FROM website_black_list";
        $result = $this->db->exec($query_black_list);
        for($numOfBlacklist = 0; $numOfBlacklist < sizeof($blacklist[0]); $numOfBlacklist++){
            $blacklist_URL = str_replace(array(","),"",$blacklist[0][$numOfBlacklist]);
            $query_black_list = "INSERT INTO website_black_list (`URL`) VALUES ('{$blacklist_URL}')";
            $result = $this->db->exec($query_black_list);
        }
        if(!empty($website) && !empty($category)){
            $query = "DELETE FROM website_category WHERE URL = '{$website}'";
            $delete_result = $this->db->exec($query);
            $query_newWebsite = "INSERT INTO website_category (`URL`,`Category`) VALUES ('{$website}','{$category}');";
            $result = $this->db->exec($query_newWebsite);
        }
        exit(json_encode("success!"));
    }
}

class Model_DatabaseOperation extends mysqli {
    public function __construct($host, $user, $pass, $db) {
        parent::init();

        if (!parent::options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 0')) {
            die('Setting MYSQLI_INIT_COMMAND failed');
        }

        if (!parent::options(MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
            die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
        }

        if (!parent::real_connect($host, $user, $pass, $db)) {
            die('Connect Error (' . mysqli_connect_errno() . ') '
                . mysqli_connect_error());
        }
    }
}

// chain of responsibilities
abstract class Handler{

    protected $successor = null;

    public function setSuccessor(Handler $handler){
        $this->successor = $handler;
    }

    abstract public function handle($request, PDO $db);
}

class WhiteListReview extends Handler{

    public function handle($request,PDO $db){
        $query = "SELECT URL FROM website_white_list Where URL = '{$request}' LIMIT 10";
        $result = $db->query($query);
        if ($result->rowCount() != 0) {
            $datarow = $result->fetch();
            /* free result set */
            exit(json_encode("allow"));
        }
        else{

            $this->successor->handle($request,  $db);
        }
    }
}

class BlackListReivew extends Handler{
    public function handle($request, PDO $db){
        $query = "SELECT URL FROM website_black_list Where URL = '{$request}' LIMIT 10";
        $result = $db->query($query);
        if ($result->rowCount() != 0) {
            $datarow = $result->fetch();
            exit(json_encode("deny"));
        }
        else{
            $this->successor->handle($request, $db);
        }
    }
}

class LocalReview extends Handler{
    public function handle($request, PDO $db){
        $query = "SELECT Category FROM website_category WHERE URL = '{$request}' LIMIT 10";
        $result = $db->query($query);
        if ($result->rowCount() != 0){
            $datarow = $result->fetch();
            $query = "SELECT Category FROM block_category WHERE Category = '{$datarow[Category]}'";
            $result = $db->query($query);
            if ($result->rowCount() != 0){
                exit(json_encode("deny"));
            }
            else{
                exit(json_encode("allow"));
            }
        }
        else{
            $this->successor->handle($request, $db);
        }
    }
}

class BlueCoatReview extends Handler{
    public function handle($request, PDO $db){
        $post_data = array(
            'url' => $request
        );
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents('http://sitereview.bluecoat.com/rest/categorization', false, $context);
        $jsonResult = json_decode($result, true);
        if(preg_match_all('/>.+?<\/a>/', $jsonResult['categorization'] , $categorization)){
            for($categorizationIndex=0; $categorizationIndex < sizeof($categorization[0]); $categorizationIndex++){
                // trim the categorization string
                $categorization[0][$categorizationIndex] = str_replace(array("</a>",">"),"",$categorization[0][$categorizationIndex]);
                $query = "INSERT INTO website_category (`URL`, `Category`) VALUES ('{$request}','{$categorization[0][$categorizationIndex]}');";
                $result = $db->exec($query);
            }
            if (strcmp($categorization[0][0],"Uncategorized")==0){
                require './PHPMailer/PHPMailerAutoload.php';
                $query = ("SELECT Username FROM eguard_user");
                $result = $db->query($query);
                $emailAdress = $result->fetch();
                $mail  = new PHPMailer();

                // $mail->SMTPDebug = true;
                // $mail->SMTPAuth = true;
                // $mail->CharSet = 'utf-8';
                // $mail->isSMTP();                                      // Set mailer to use SMTP
                // $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
                // $mail->SMTPAuth = true;                               // Enable SMTP authentication
                // $mail->Username = 'ztlevitest@gmail.com';                 // SMTP username
                // $mail->Password = 'helloTest';                           // SMTP password
                // $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                // $mail->Port = 587;                                    // TCP port to connect to
                //
                // $mail->setFrom('ztlevitest@gmail.com', 'E-Guard');
                // $mail->AddAddress("{$emailAdress['Username']}", "Ting Zhou");
                // $mail->addAddress('ztlevi1993@gmail.com', 'Ting Zhou');     // Add a recipient
                // $mail->addReplyTo('ztlevitest@gmail.com', 'Information');
                //
                // $mail->isHTML(true);                                  // Set email format to HTML
                //
                // $mail->Subject = 'E-Guard Notification';
                // $mail->Body    = 'Hello!\r\n $request is uncategorized, please go to E-Guard option page and assign it to one category! \r\nThanks';
                // $mail->AltBody = 'Hello!\r\n $request is uncategorized, please go to E-Guard option page and assign it to one category! \r\nThanks';
                //
                // if(!$mail->send()) {
                //     echo 'Message could not be sent.';
                //     echo 'Mailer Error: ' . $mail->ErrorInfo;
                // } else {
                //     echo 'Message has been sent';
                // }
                $mail->CharSet    ="UTF-8";
                $mail->IsSMTP();
                $mail->SMTPAuth   = true;
                $mail->SMTPSecure = "tls";
                $mail->Host       = "smtp.gmail.com";
                $mail->Port       = 587;
                $mail->Username   = "ztlevitest@gmail.com";
                $mail->Password   = "helloTest";
                $mail->SetFrom('ztlevitest@gmail.com', 'fdsafds');
                $mail->AddReplyTo("ztlevitest@gmail.com","fdsafds");
                $mail->Subject    = 'E-Guard Notification';
                $mail->AltBody    = "To check the mail，please use HTML client";
                $mail->MsgHTML("Hello!\r\n $request is uncategorized, please go to E-Guard option page and assign it to one category! \r\nThanks");
                $mail->AddAddress("{$emailAdress['Username']}", "Ting Zhou");
                $mail->Send();
                if(!$mail->send()) {
                    echo 'Message could not be sent.';
                    echo 'Mailer Error: ' . $mail->ErrorInfo;
                } else {
                    echo 'Message has been sent';
                }
            }
            $this->successor->handle($request,  $db);
        }
    }
}
?>
