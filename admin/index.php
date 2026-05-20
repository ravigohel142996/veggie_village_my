<?php
session_start();
$msg_error='';
if(isset($_SESSION['msg']))
{
    $msg_error=$_SESSION['msg'];
    unset($_SESSION['msg']);
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../css/form-style.css">
    <link rel="stylesheet" href="../css/materialize.css">
    <title>Admin Login</title>

</head>
<body>


    <div class="login-page section" style="padding:20px;">
        <div class="center-align">
            <div class="row">

                <div class="col s12">

                    <div class="card horizontal hoverable" style="background:white; border-radius: 20px;min-width: 450px;width:100%;margin:auto;box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);">

                        <div class="card-stacked">
                            <form class="card-content" action="login-admin.php" method="post" style="border-radius:20px;background-color:#A5D6A7;padding:auto;">
                                <h4 style="font-family: 'Pacifico', cursive;color:#2d572c;">Admin Login</h4>

                                <?php

                                    if(!empty($msg_error)){
                                        echo '<div class="row error-msg" style="border-radius:10px;">
                                                    <div class="col">
                                                        <b>'.$msg_error.'</b>
                                                    </div>
                                                </div>';

                                    }
                                ?>

                                <div class="row">
                                    <div class="input-field col s12">
                                        <span for="email" style="font-family: 'Pacifico', cursive;"><b>Email</b></span>
                                        <input name="email" id="email" type="email" class="validate" autocomplete="off" style="font-family: 'Pacifico', cursive;">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="input-field col s12">
                                    <span for="email" style="font-family: 'Pacifico', cursive;"><b>Password</b></span>
                                        <input id="password" name="password" type="password" class="validate">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col s12">
                                        <button type="submit"  class="waves-effect waves-light btn"><b>Log In</b></button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>


                </div>





            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.js"></script>
</body>
</html>
