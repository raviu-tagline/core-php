<?php
    include 'include/header.php';
?>

<?php 
    session_start();
    $fname = $lname = $email = $gen = $mo_no = $pass = $cpass = $bdate = $addr = $rid = $tet = "";
    require 'App_Data/dbClass.php';
    $conn = new dbClass();
    $connect = $conn->connect();
?>
<style>
    .hidden{
       display: none;
    }
    .show{
        display: block;
    }
</style>
<!-- 

    /**************************************************************    
    
    Try to use array for storing and implementing values in database

    ***************************************************************/

-->

<?php

    function validate_name($data){
        if(preg_match("/^[a-zA-z]{0,9}$/i",$data))
            return $data;
        else
            return false;
    }

    function validate_email($data){
        
        if(preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix",$data))
        {
            
            return $data;
        }
        else{
            return false;
        }
    }

    function validate_mobile($data){
        if(preg_match("/^[6-9][0-9]{9}$/i",$data))
            return $data;
        else
            return false;
    }

    function verify_password($cdata, $data){
        if($cdata == $data)
            return $cdata;
        else
            return false;
    }

    function test_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function image_upload(){
        
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        $status = 1;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $fileName = htmlspecialchars(basename($_FILES["profile_pic"]["name"]));

        $chk = getimagesize($_FILES["profile_pic"]["tmp_name"]);
        if($chk !== false)
        {
            $status = 1;
        }
        else{
            echo "<script>alert('Not image')</script>";
            $status = 0;
        }

        //Check already exist or not
        // if (file_exists($target_file)) {
        //     echo "<script>alert('Sorry, file already exists Check Your file name.')</script>";
        //     $status = 0;
        // }

        // Check file size
        if ($_FILES["profile_pic"]["size"] > 2097152) {
            echo "<script>alert('Sorry, make sure your file size is < 2Mb ')</script>";
            $status = 0;
        }

        if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg"
            && $fileType != "gif" ) 
        {
            echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.')</script>";
            $status = 0;
        }

        if ($status == 0) 
        {
            echo "<script>alert('Sorry, your image was not uploaded.')</script>";
            return false;
        } 
        else 
        {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) 
            {
                return $fileName;
            } 
            else 
            {
                echo "<script>alert('Sorry, there was an error uploading your file.')</script>";
            }
        }
    }

    function test_input_date($data){
        $year = DateTime::createFromFormat("Y-m-d",$data);
        $y2 = $year->format("Y");
        $cy = date("Y");
        $age = $cy - $y2;
        if($age >= 18){
            return $data;
        }
        else{
            echo "<script>alert('Under 18')</script>";
            return false;
        }
    }

    function get_hob($hoby){
        $str = "";
        foreach($hoby as $k => $v){
            $str .= $v." ";
        }
        return rtrim($str, " ");
    }

    function get_values()
    {
        $fname = validate_name(test_input($_POST['first_name']));
        $lname = validate_name(test_input($_POST['last_name']));
        $email = validate_email(test_input($_POST['user_mail']));
        $pass = test_input($_POST['user_password']);
        $cpass = verify_password(test_input($_POST['confirm_password']),$pass);
        $gen = test_input($_POST['gender']);
        $bdate = test_input_date($_POST['birth_date']);
        $mo_no = validate_mobile(test_input($_POST['user_mobile']));
        $addr = test_input($_POST['user_address']);
        $hob = get_hob($_POST['hob']);
        $state = $_POST['ddlState'];
        $city = $_POST['ddlCity'];

        $date = date('Y-m-d');
        date_default_timezone_set("Asia/Kolkata");
        $time = date("H:i:s");

        $img_name = image_upload();

       

        $tmp_arr = array("first_name"=>$fname,"last_name"=>$lname,"email_id"=>$email,"password"=>$pass,"cpass"=>$cpass,"gender_id"=>$gen,
        "birth_date"=>$bdate,"mobile_no"=>$mo_no,"address"=>$addr,"h_id"=>$hob,"user_image"=>$img_name, "state_id"=>$state,"city_id"=>$city,"reg_date"=>$date,"reg_time"=>$time);
 
        return $tmp_arr;
    }

    function resetForm(){
        $fname = $lname = $email = $gen = $mo_no = $pass = $cpass = $bdate = $addr = "";
    }

    function fetch_val($data){
        $chk_arr = explode(" ",$data);
        $f_arr = implode(', ', $chk_arr);
        return $f_arr;
    }

    function get_hobby($data_arr,$conn){
        $str = "";
        
        

        $data_arr = explode(",", $data_arr);
        
        foreach($data_arr as $ky => $vl)
        {
            // $qryStr = "select hobby_name from tbl_hobbies where h_id = $vl";
            $res = $conn->select_op("select hobby_name from tbl_hobbies where h_id = $vl");
            if($res->num_rows > 0)
            {
                while($rw = $res->fetch_assoc())
                {
                    $str .= $rw['hobby_name'].", ";
                }
            }
        }
        return rtrim($str, ", ");
    }

    if(isset($_GET['id']))
    {
        $rid =  intval($_GET['id']);
        $Sclass = 'hidden';
        $Uclass = 'show';
        $_SESSION['rid'] = $rid;

        // $qry = "select * from tbl_register_data where reg_id = $rid";
        
        
        
        $result = $conn->select_op("select * from tbl_register_data where reg_id = $rid");
        // $result = $conn->query($qry);
        
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $fname = $row["first_name"];
                $lname = $row["last_name"];
                $email = $row["email_id"];
                $gen = $row["gender_id"];
                $bdate = $row["birth_date"];
                $mo_no = $row["mobile_no"];
                $addr = $row["address"];
                $img_name = $row["user_image"];
                $hob = explode(" ",$row['h_id']);
            }
        }
    }
    else if(isset($_GET['delid']))
    {
        $rid = intval($_GET['delid']);
        // $sql = "select user_image from tbl_register_data where reg_id = $rid";
        // $sql = ;
        // $result = $conn->query($sql);
        $result = $conn->select_op("select user_image from tbl_register_data where reg_id = $rid");
        // $qry = "delete from tbl_register_data where reg_id = $rid";
        $path = 'images/';
        if($result->num_rows > 0)
        {
            while($row = $result->fetch_assoc())
            {
                unlink($path.$row['user_image']);
            }
        }
        if($conn->insert_update_delete_op("delete from tbl_register_data where reg_id = $rid"))
        {
            echo "<script>alert('Record deleted')</script>";
        }
        $Sclass = 'show';
        $Uclass = 'hidden';
    }
    else{
        
        $Sclass = 'show';
        $Uclass = 'hidden';
        if(isset($_POST['submit']))
        {
            if($_SERVER["REQUEST_METHOD"] == "POST")
            {
                $user_arr = get_values();
                
              
                if(!$conn)
                {
                    print "<script>alert('Err: $conn->connect_error')</script>";
                }

                else
                {
                    if($user_arr['first_name'] && $user_arr['last_name'] && $user_arr['email_id'] && $user_arr['cpass'] && 
                    $user_arr['mobile_no'] && $user_arr['birth_date'] && $user_arr['user_image'] != false)
                    {
                        
                        $date = date('y-m-d');
                        date_default_timezone_set("Asia/Kolkata");
                        $time = date('H:i:s');
                        $tbl_name = "tbl_register_data";

                        if($conn->insert($tbl_name, $user_arr))
                        {
                            echo "<script>alert('Data inserted')</script>";
                            resetForm();                        
                        }
                        else{
                            echo "<script>alert('Err : Not inserted')</script>";
                        }
                                
                    }
                    else{
                        echo "<script>alert('Something wrong please check your inputs !')</script>";
                    }
                }
            } 
        }
        else if(isset($_POST['update']))
        {
            $rid = $_SESSION['rid'];
           if($_SERVER["REQUEST_METHOD"] == "POST"){

                $update_arr = get_values();

                // $date = date('y-m-d');
                // date_default_timezone_set("Asia/Kolkata");
                // $time = date("H:i:s");

                

                $sql = "update tbl_register_data set first_name = '".$update_arr['first_name']."', last_name = '".$update_arr['last_name']."', email_id = '".$update_arr['email_id']."',password = '".$update_arr['password']."',".
                "gender_id = '".$update_arr['gender_id']."', birth_date = '".$update_arr['birth_date']."', mobile_no = '".$update_arr['mobile_no']."', address = '".$update_arr['address']."', reg_date = '".$update_arr['reg_date']."', reg_time = '".$update_arr['reg_time']."',".
                "user_image = '".$update_arr['user_image']."',h_id = '".$update_arr['h_id']."',state_id = ".$update_arr['state_id'].",city_id = ".$update_arr['city_id']." where reg_id = $rid";

                if($conn->insert_update_delete_op($sql)){
                    resetForm();
                }
           }
        } 
    }
    

?>

<!-- 

    /**************************************************************    
    
                --------- BODY PART OF FILE ---------

    ***************************************************************/

-->


<div class="container">
    <div class="container-fluid">
        <div class="row py-5">
            <div class="card col-8 mx-auto shadow-lg">
                <div class="py-3 text-center">
                    <h6 class="h2 text-info font-shadow"><span><i class="fa fa-user-plus"></i></span>&nbsp;&nbsp;&nbsp;User Registration Form</h6>
                </div>
                <hr>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" enctype="multipart/form-data">
                    <div class="form-group">
                            <label for="input-name">
                                First Name : 
                            </label>
                            <input type="text" id="fname" name="first_name" placeholder="Enter Your Name" class="form-control" value="<?php echo $fname;?>" required/>
                    </div>
                    <div class="form-group">
                            <label for="input-name">
                                Last Name : 
                            </label>
                            <input type="text" id="lname" name="last_name" placeholder="Enter Your Surname" class="form-control" value="<?php echo $lname;?>" required/>
                    </div>
                    <div class="form-group">
                        <label for="input-email">
                            Email ID :
                        </label>
                        <input type="email" id="uMail" name="user_mail" placeholder="Enter Your Email" class="form-control" value="<?php echo $email;?>" required />
                    </div>
                    <div class="form-group">
                        <label for="input-email">
                            Password :
                        </label>
                        <input type="password" id="uPass" name="user_password" placeholder="Enter Password" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="input-email">
                            Confirm Password :
                        </label>
                        <input type="password" id="uCPass" name="confirm_password" placeholder="Confirm Your Pasword" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label for="input-gender">
                            Gender :
                        </label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="gMale" name="gender" <?php if(isset($gen) && $gen == 1) echo "checked";?> value="1"/>
                            <label class="form-check-label" for="gMale">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="gFemale" name="gender" <?php if(isset($gen) && $gen == 2) echo "checked";?> value="2">
                            <label class="form-check-label" for="gFemale">Female</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input-hobbies">
                            Hobbies :
                        </label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="hMusic" name="hob[]" 
                            <?php 
                                if(isset($hob) && in_array(1, $hob)) {
                                    echo "checked";
                                }
                                else{
                                    echo "";
                                }
                            ?> value="1"/>
                            <label class="form-check-label" for="Music">Music</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="hDancing" name="hob[]" 
                            <?php 
                                if(isset($hob) && in_array(2, $hob)) {
                                    echo "checked";   
                                }
                                else{
                                    echo "";
                                }
                            ?> value="2"/>
                            <label class="form-check-label" for="Dancing">Dancing</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="hSinging" name="hob[]" 
                            <?php 
                                if(isset($hob) && in_array(3, $hob))
                                { 
                                    echo "checked";
                                }
                                else{
                                    echo "";
                                }
                                ?> value="3"/>
                            <label class="form-check-label" for="Singing">Singing</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="hPlaying" name="hob[]" 
                            <?php 
                                if(isset($hob) && in_array(4, $hob))
                                {
                                    echo "checked";
                                }
                                else{
                                    echo "";
                                }
                            ?> value="4"/>
                            <label class="form-check-label" for="Playing">Playing</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="hEating" name="hob[]" 
                            <?php 
                                if(isset($hob) && in_array(5, $hob)) 
                                {
                                    echo "checked";
                                }
                                else{
                                    echo "";
                                }
                            ?> value="5"/>
                            <label class="form-check-label" for="Eating">Eating</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input-email">
                            Birth Date :
                        </label>
                        <input type="date" id="uBDate" name="birth_date" placeholder="Select Your Birth Date" class="form-control" value="<?php echo $bdate;?>" required />
                    </div>
                    <div class="form-group">
                        <label for="input-email">
                            Mobile Number :
                        </label>
                        <input type="number" id="uNumber" name="user_mobile" placeholder="Enter Your Number" class="form-control" value="<?php echo $mo_no;?>" required />
                    </div>
                    <div class="form-group">
                        <label for="input-state">
                            State :
                        </label>
                        <select id="ddlState" class="form-control" name='ddlState'>
                            <option value="0" id="sFValue" disabled selected>Select State</option>
                            <?php 
                                $sql = "select * from tbl_state";
                                $result = $conn->select_op($sql);

                                if($result->num_rows > 0){
                                    while($row = $result->fetch_assoc())
                                    {
                                        echo "<option value='".$row['state_id']."'>".$row['state_name']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="input-city">City : </label>
                        <select id="ddlCity" class="form-control" name='ddlCity' disabled>
                            <option value="0" id="cFValue" disabled selected>Select City</option>
                            <?php 
                                
                                $sql = "select * from tbl_city";
                                $result = $conn->select_op  ($sql);

                                if($result->num_rows > 0){
                                    while($row = $result->fetch_assoc())
                                    {
                                        echo "<option value='".$row['city_id']."'>".$row['city_name']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="input-email">
                            Address :
                        </label>
                        <textarea id="uAddr" name="user_address" placeholder="Enter Your Address" rows="5" class="form-control" required><?php echo $addr;?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image-upload">
                            Profile Picture :
                        </label>
                        <input type="file" name="profile_pic" class="form-control" value="<?php echo $img_name?>">
                    </div>
                    <hr>
                    <input type="submit" id="btnSubmit" value="Submit" name="submit" class="btn btn-primary btn-block my-3 font-weight-bold <?php echo $Sclass;?>">
                    <input type="submit" id="btnUpdate" value="Update" name="update" class="btn btn-success btn-block my-3 font-weight-bold <?php echo $Uclass;?>">
                </form>
            </div>
        </div>
    </div>
</div>


<div class="container">
    <div class="container-fluid">
        <div class="row py-2">
            <div class="card col-12 mx-auto shadow-lg py-3">
                
                <div class="">
                    <!-- <div class="clearfix py-3">
                        <input type="search" id="fSearch" placeholder="Search By Name" class="float-left" />
                        <div class="float-right">
                            <label for="select-sorting" id="lblSort">Select sorting by name : </label>
                            <select id="ddlASort">
                                <option value="0" id="fValue"></option>
                            </select>
                        </div>
                    </div> -->
                    <table class="table table-responsive table-striped table-bordered table-hover text-nowrap">
                        <thead>
                            <th colspan="2">Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Hobbies</th>
                            <th>Birth Date</th>
                            <th>Mobile Number</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Address</th>
                            <th>Register Date</th>
                            <th>Register Time</th>
                            <th colspan="2">Actions</th>
                        </thead>
                        <tbody id="tblData">
                            <?php
                                $sql = "select rd.*,g.gender_name,h.hobby_name,st.state_name,ct.city_name from tbl_register_data rd".
                                " join tbl_gender g on rd.gender_id = g.gender_id".
                                " join tbl_hobbies h on rd.h_id = h.h_id ".
                                " join tbl_state st on rd.state_id = st.state_id".
                                " join tbl_city ct on rd.city_id = ct.city_id";
                                $result = $conn->select_op($sql);

                                if($result->num_rows > 0)
                                {
                                    while($row = $result->fetch_assoc())
                                    {
                                        // echo "Array of row :::: ";
                                        // print_r($row);
                                        // echo "<br><br>";
                                        $date = DateTime::createFromFormat("Y-m-d",$row['birth_date']);
                                        $regDate = DateTime::createFromFormat("Y-m-d",$row['reg_date']);
                                        ?>
                                        <tr>
                                            <td>
                                                <img src='images/<?php echo $row['user_image'];?>' width='50px' height='50px' class='rounded'/>
                                            </td>
                                            <td style='border-left: none;!important'>
                                                <?php echo "{$row["first_name"]} {$row["last_name"]}";?>
                                            </td>
                                            <td>
                                                <?php echo $row["email_id"];?>
                                            </td>
                                            <td>
                                                <?php echo $row["gender_name"];?>
                                            </td>
                                            <td>
                                                <?php echo get_hobby(fetch_val($row["h_id"]),$conn);?>
                                            </td>
                                            <td>
                                                <?php echo $date->format('d/m/Y');?>
                                            </td>
                                            <td>
                                                <?php echo $row["mobile_no"];?>
                                            </td>
                                            <td>
                                                <?php echo $row["state_name"];?>
                                            </td>
                                            <td>
                                                <?php echo $row["city_name"];?>
                                            </td>
                                            <td>
                                                <?php echo $row["address"];?>
                                            </td>
                                            <td> 
                                                <?php echo $regDate->format('d/m/Y');?>
                                            </td>
                                            <td>
                                                <?php echo $row["reg_time"];?>
                                            </td>
                                            <td>
                                                <a name='edit' id='btnEdit' class='btn btn-success btn-block font-weight-bold' href='?id=<?php echo $row["reg_id"];?>'>
                                                    Edit
                                                </a>
                                            </td>
                                            <td>
                                                <a name='delete' id='btnDelete' class='btn btn-danger btn-block font-weight-bold' onClick="javascript: return confirm('Are you sure to delete this record ?');" href='?delid=<?php echo $row["reg_id"];?>'>
                                                    Delete
                                                </a>
                                            </td>
                                    </tr>
                                <?php
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    $connect->close();
?>




<script>
    $(document).ready(function(){
        var chkname = /^[a-zA-z]{0,10}$/i;
        $('input[type=text]').keyup(function(){
            var nm = $(this).val();
            if(chkname.test(nm))
            {
                $(this).css('border-color','green');
            }
            else{
                $(this).css('border-color','red');
            }
        });
        var chknum = /^[6-9][0-9]{9}$/i;
        $('input[type=number]').keyup(function(){
            if(chknum.test($(this).val())){
                $(this).css('border-color','green');
            }
            else{
                $(this).css('border-color','red');
            }
        });
        
        $('#ddlState').on('change',function(){
            $('#ddlCity').prop('disabled',false);
        });
    })

    // function get_id(param){
    //    var sPageURL = window.location.search.substring(1),
    //     sURLVariables = sPageURL.split('&'),
    //     sParameterName,
    //     i;

    //     for (i = 0; i < sURLVariables.length; i++) {
    //         sParameterName = sURLVariables[i].split('=');

    //         if (sParameterName[0] === sParam) {
    //             return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
    //         }
    //     }
    // }
</script>


<?php
    include 'include/footer.php';
?>