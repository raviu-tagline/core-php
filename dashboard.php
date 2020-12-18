<?php
    session_start();
    $email = $_COOKIE['email'];
    $pass = $_COOKIE['pass'];
    if(isset($_SESSION['uid']))
    {
        if(!isset($_POST['btnLogout']))
        {

            echo "Welcome! ".$_SESSION['uname']."  ".$_SESSION['umail']."<br>";
            echo "Password: ".$_COOKIE['pass']."  Email: ".$_COOKIE['email'];
        }
        else{
            $_COOKIE['email'] = $_SESSION['email'];
            $_COOKIE['pass'] = $_SESSION['pass'];
            session_destroy();
            $url = "login.php";
            echo "<script type='text/javascript'>document.location.href='{$url}';</script>";
        }
    }
    else
    {
        $url = "login.php";
        echo "<script type='text/javascript'>document.location.href='{$url}';</script>";
    }
?>
<style>
    .btn-danger{
        background-color: red;
        color: white;
    }
</style>
<form method="post" action="">
<button class="btn-danger" name="btnLogout">Log out</button>
</form>