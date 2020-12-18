<?php
    include 'include/header.php';
    require 'App_Data/dbClass.php';
    $pass = "";
    $email = "";
    if(isset($_COOKIE['pass']) && isset($_COOKIE['email']))
    {
        $email = $_COOKIE['email'];
        $pass = $_COOKIE['pass'];
    }
    else
    {
        echo "eslelseslelesels :::::  ";
        $pass = "";
        $email = "";
    }
?>

<!-- 
    /**************************************************************    
    
                --------- BODY PART OF FILE ---------

    ***************************************************************/
-->

<style>
    .bg-light-info{
        background-color: #AAA;
    }
</style>

<div class="container">
        <div class="container-fluid">
            <div class="row py-5">
                <div class="card col-6 mx-auto shadow-lg">
                    <div class="py-3 text-center">
                        <h6 class="h2 text-info font-shadow"><span><i class="fa fa-user"></i></span>&nbsp;&nbsp;&nbsp;User Login Form</h6>
                    </div>
                    <hr>
                    <img src="images/no-image.png" width="150px" height="150px" class="rounded-circle bg-light-info mx-auto d-block"/>
                    <br>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>">
                        <div class="form-group">
                                <label for="input-name">
                                    User Name : 
                                </label>
                                <input type="text" id="uMail" name="email_id" placeholder="Enter Email / Mobile number" class="form-control" value="<?php echo $email;?>" required/>
                        </div>
                        <div class="form-group">
                            <label for="input-email">
                                Password :
                            </label>
                            <input type="password" id="uPass" name="user_password" placeholder="Enter Password" class="form-control" value="<?php echo $pass;?>" required />
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="remember" name="remember_pass" class="form-input-check" value="1"/>
                            <label for="input-email" class="form-check-label">
                                Remember Me
                            </label>
                        </div>
                        <hr>
                        <input type="submit" id="btnSubmit" value="Login" name="submit" class="btn btn-primary btn-block my-3 font-weight-bold">
                        <input type="hidden" id="hdnImg" name="hdnImg" value='female_avtar.png'/>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    


    session_start();
    
    if(isset($_POST['submit']))
    {
        $conn = new dbClass();
        $id = $_POST["email_id"];
        $pass = $_POST["user_password"];
        $sql = "select * from tbl_register_data where (email_id = '$id' or mobile_no = '$id') and password like '$pass'";
        $connect = $conn->connect();
        $res = $conn->select_op($sql);

        $tmp = $res->fetch_assoc();
        if($res->num_rows > 0){
            $_SESSION['uid'] = $tmp['reg_id'];
            $name = $tmp['first_name']." ".$tmp['last_name'];
            $_SESSION['uname'] = $name;
            $_SESSION['umail'] = $tmp['email_id'];

            echo "asdas : {$_POST['remember_pass']}";

            if($_POST['remember_pass'] == "1")
            {
                setcookie('pass',$_POST['user_password'],time() + (20 * 365 * 24 * 60 * 60));
                setcookie('email', $_POST['email_id'], time() + (20 * 365 * 24 * 60 * 60));
            }
            $URL = "dashboard.php";
            echo "<script type='text/javascript'>document.location.href='{$URL}';</script>";
        }
        else{
            echo "<script>alert('Incorrect id/ Pass')</script>";
        }
    }
?>

<!-- <script>

    $(document).ready(function (){
        $('#uPass').on('focus',function(){
            
            $img_name = $('#hdnImg').val();
            $path = 'images/'+$img_name;
            $('img').attr('src',$path).addClass('rounded-circle')
            console.log("Image naem: ",$img_name,"  Image path: ",$path);
        })
    })

</script> -->

<?php
    include 'include/footer.php';
?>