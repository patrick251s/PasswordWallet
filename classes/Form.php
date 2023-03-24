<?php

class Form {
    public static function headForm($title) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
            <meta name="description" content="Password Wallet">
            <meta name="keywords" content="password, wallet, hash>
            <meta name="author" content="PK">

            <title><?php echo $title; ?></title>
            <link rel="stylesheet" type="text/css" href="css/bootstrap/bootstrap.min.css">

            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Lato:ital@1&display=swap" rel="stylesheet">
        </head>
    <?php
    }
    
    public static function loginPageForm() {
        Form::headForm("Password Wallet - Login");
    ?>
        <body class="bg-dark text-light text-center">
            <header class="pt-5 py-3">
                <h2 class="text-primary">Your password wallet</h2>
            </header>
            <section>
                <?php
                    session_start();
                    if(isset($_SESSION['registrationSuccess']) && $_SESSION['registrationSuccess'] == true) { 
                        echo '<h5 class="col-sm-8 mx-auto alert alert-success alert-dismissible fade show text-center" role="alert">You have successfully created your account!'.
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                        unset($_SESSION['registrationSuccess']);
                    }
                    else if(isset($_SESSION['loginFail'])) {
                        $text = $_SESSION['loginFail'];
                        /*if(isset($_SESSION['loginFailAttempts'])) {
                            $text .= "<br/> It is your ".$_SESSION['loginFailAttempts']." unseccessful login attempt!";
                        }*/
                        if(isset($_SESSION['blockadeDate'])) {
                            $text .= "<br/> Your account has been locked to ".$_SESSION['blockadeDate'];
                        }
                        echo '<h5 class="col-sm-8 mx-auto alert alert-danger alert-dismissible fade show text-center" role="alert">'.$text.
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                        unset($_SESSION['loginFail']);
                        unset($_SESSION['loginFailAttempts']);
                        unset($_SESSION['blockadeDate']);
                    }
                    else if(isset($_SESSION['accountBlockade'])) {
                        echo '<h5 class="col-sm-8 mx-auto alert alert-warning alert-dismissible fade show text-center" role="alert">Your account has been locked to '.$_SESSION['accountBlockade'].
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                        unset($_SESSION['accountBlockade']);
                    }
                ?>
                <form method="POST" action="controller.php" class="col-sm-8 col-lg-4 mx-auto">
                    <div class="form-group mb-sm-4">
                      <label for="loginInput">Login</label>
                      <input type="text" class="form-control" id="loginInput" name="loginL" required="true">
                    </div>
                    <div class="form-group">
                      <label for="passwordInput">Password</label>
                      <input type="password" class="form-control" id="passwordInput" name="passwordL" required="true">
                    </div>
                    <button type="submit" name="submit" value="login" class="btn btn-success col-sm-4 mt-4">Login</button>
                  </form>
            </section>
            <section class='mt-sm-5 mt-2'>
                <a href="register.php"><p class="h4 text-warning">Don't have an accout? Let's create!</p></a>
            </section> 
            
            <script src="js/jquery/jquery-3.5.1.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/ulg/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
            <script src="js/bootstrap/bootstrap.bundle.min.js"></script>
        </body>
        </html>
    <?php
    }
    
    public static function registerPageForm() {
        Form::headForm("Password Wallet - Register");
    ?>
        <body class="bg-dark text-light text-center">
            <header class="py-4">
                <h2 class="text-primary">Your password wallet</h2>
            </header>
            <?php
            session_start();
            if(isset($_SESSION['registerValidation'])) { 
                echo '<h5 class="col-sm-8 mx-auto alert alert-danger alert-dismissible fade show pb-2 text-center" role="alert">'.$_SESSION['registerValidation'].
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                unset($_SESSION['registerValidation']);
            }
            else if(isset($_SESSION['registrationSuccess']) && $_SESSION['registrationSuccess'] == false) { 
                echo '<h5 class="col-sm-8 mx-auto alert alert-danger alert-dismissible fade show pb-2 text-center" role="alert">An error has occurred while creating the account. Try again.'.
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                unset($_SESSION['registrationSuccess']);
            }
            ?>
            <section>
                <h3 class="text-warning pb-2">Registration</h3>
                <form method="POST" action="controller.php" class="col-sm-8 col-lg-4 mx-auto">
                    <div class="form-group mb-sm-4">
                      <label for="loginInputinRegistration">Please enter your login</label>
                      <input type="text" class="form-control" id="loginInputInRegistration" name="loginR" title="Please enter a login consisting of at least 3 characters and starting with a letter" 
                             <?php
                            if(isset($_SESSION['errorLoginR'])) {
                                echo 'value='.$_SESSION['errorLoginR'];
                                unset($_SESSION['errorLoginR']);
                            }
                            ?>
                             >
                    </div>
                    <div class="form-group mb-sm-4">
                      <label for="passwordInputinRegistration">Please enter your password</label>
                      <input type="password" class="form-control" id="passwordInputInRegistration" name="passwordR" title="Please enter a password of at least 3 characters"
                            <?php
                            if(isset($_SESSION['errorPassR'])) {
                                echo 'value='.$_SESSION['errorPassR'];
                                unset($_SESSION['errorPassR']);
                            }
                            ?>
                            >
                    </div>
                    <div class="form-group mb-sm-4">
                      <label for="passwordInputinRegistration2">Please enter your password again</label>
                      <input type="password" class="form-control" id="passwordInputInRegistration2" name="passwordR2" title="Please enter a password of at least 3 characters"
                            <?php
                            if(isset($_SESSION['errorPassR2'])) {
                                echo 'value='.$_SESSION['errorPassR2'];
                                unset($_SESSION['errorPassR2']);
                            }
                            ?>
                            >
                    </div>
                    <div class="form-group">
                        <p>Please select the type of password protection</p>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="passwordProtection" id="sha" value="sha" 
                            <?php
                            if(isset($_SESSION['errorTypeR']) && $_SESSION['errorTypeR'] == 'sha') {
                                echo "checked";
                                unset($_SESSION['errorTypeR']);
                            }
                            ?>
                            >
                            <label class="form-check-label" for="sha">SHA512 with salt and pepper</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="passwordProtection" id="hmac" value="hmac" 
                            <?php
                            if(isset($_SESSION['errorTypeR']) && $_SESSION['errorTypeR'] == 'hmac') {
                                echo "checked";
                                unset($_SESSION['errorTypeR']);
                            }
                            ?>
                            >
                            <label class="form-check-label" for="hmac">HMAC</label>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <button type="submit" name="submit" value="register" class="btn btn-success col-sm-4 mx-auto">Register</button>
                        <button type="reset" class="btn btn-danger col-sm-4 mx-auto my-4 my-sm-0">Reset</button>
                    </div>
                  </form>
            </section>
            <section class='mt-2 mt-sm-4'>
                <a href="index.php"><p class="h4 text-info">Back to login page</p></a>
            </section>
            
            <script src="js/jquery/jquery-3.5.1.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/ulg/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
            <script src="js/bootstrap/bootstrap.bundle.min.js"></script>
        </body>
        </html>
    <?php
    }
    
    public static function passwordsPageForm() {
        Form::headForm("Password Wallet - My Account");
    ?>
        <body class="bg-dark text-light text-center">
            <header>
                <nav class="navbar navbar-expand-lg navbar-light bg-light py-sm-3">
                    <h3 class="text-primary">Password Wallet</h3>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                      <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                      <ul class="navbar-nav mr-auto">
                        <li class="nav-item active h5 px-sm-3 ml-lg-3">
                          <a class="nav-link" href="passwords.php">Your passwords</a>
                        </li>
                        <li class="nav-item px-sm-3 h5">
                          <a class="nav-link" href="addPassword.php">Add password</a>
                        </li>
                        <li class="nav-item px-sm-3 h5">
                          <a class="nav-link" href="account.php">Account</a>
                        </li>
                        
                      </ul>
                      <ul class="navbar-nav ml-auto mr-lg-3">
                        <li class="nav-item h5 ">
                            <form method="POST" action="controller.php">
                                <button class="btn btn-outline-info" type="submit" name="submit" value="logout" id="logoutBTN">Logout</button>
                            </form>
                        </li>
                      </ul>
                    </div>
                </nav>                      
            </header>
            
            <header class="text-primary py-4">
                <h2>Hello <?php echo $_SESSION['userLogin'];?>! <br/>Here are your saved passwords</h2>
            </header>
            
            <?php
            if(isset($_SESSION['addPasswordSuccess']) && $_SESSION['addPasswordSuccess'] == true) { 
                echo '<h5 class="col-sm-8 mx-auto alert alert-success alert-dismissible fade show pb-2 text-center" role="alert">You have successfully added the password!'.
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                unset($_SESSION['addPasswordSuccess']);
            }
            else if(isset($_SESSION['deleteUserPasswordValidation'])) {
                echo '<h5 class="col-sm-8 mx-auto alert alert-danger alert-dismissible fade show pb-2 text-center" role="alert">'.$_SESSION['deleteUserPasswordValidation'].
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                unset($_SESSION['deleteUserPasswordValidation']);
            }
            else if(isset($_SESSION['deleteUserPasswordSuccess']) && $_SESSION['deleteUserPasswordSuccess'] == true) { 
                echo '<h5 class="col-sm-8 mx-auto alert alert-success alert-dismissible fade show pb-2 text-center" role="alert">You have successfully delete the password!'.
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                unset($_SESSION['deleteUserPasswordSuccess']);
            }
            else if(isset($_SESSION['deleteUserPasswordSuccess']) && $_SESSION['deleteUserPasswordSuccess'] == false) { 
                echo '<h5 class="col-sm-8 mx-auto alert alert-danger alert-dismissible fade show pb-2 text-center" role="alert">An error has occured while deleting the password!'.
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                unset($_SESSION['deleteUserPasswordSuccess']);
            }
            else if(isset($_SESSION['sharePasswordStatus']) && $_SESSION['sharePasswordStatus'] == true) { 
                echo '<h5 class="col-sm-8 mx-auto alert alert-success alert-dismissible fade show pb-2 text-center" role="alert">You have successfully shared the password!'.
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                unset($_SESSION['sharePasswordStatus']);
            }
            else if(isset($_SESSION['sharePasswordStatus']) && $_SESSION['sharePasswordStatus'] == false) { 
                echo '<h5 class="col-sm-8 mx-auto alert alert-danger alert-dismissible fade show pb-2 text-center" role="alert">An error has occured while sharing the password!'.
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                unset($_SESSION['sharePasswordStatus']);
            }
            else if(isset($_SESSION['removeUserSharingStatus']) && $_SESSION['removeUserSharingStatus'] == true) { 
                echo '<h5 class="col-sm-8 mx-auto alert alert-success alert-dismissible fade show pb-2 text-center" role="alert">You have successfully removed user from sharing the password!'.
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                unset($_SESSION['removeUserSharingStatus']);
            }
            else if(isset($_SESSION['removeUserSharingStatus']) && $_SESSION['removeUserSharingStatus'] == false) { 
                echo '<h5 class="col-sm-8 mx-auto alert alert-danger alert-dismissible fade show pb-2 text-center" role="alert">An error has occured while removing user from sharing the password!'.
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                unset($_SESSION['removeUserSharingStatus']);
            }
            if(isset($_SESSION['q'])) { 
                echo '<h5 class="col-sm-8 mx-auto alert alert-danger alert-dismissible fade show pb-2 text-center" role="alert">'.$_SESSION['q'].
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                unset($_SESSION['q']);
            }
            ?>
            
            <section id="savedPasswords" class="container row mx-auto text-left text-dark">
                
            </section>
            
            <script src="js/jquery/jquery-3.5.1.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/ulg/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
            <script src="js/bootstrap/bootstrap.bundle.min.js"></script>
            <script src="js/userPasswords.js"></script>
            <script src="js/sharePassword.js"></script>
        </body>
        </html>
    <?php
    }
    
    public static function addPasswordPageForm() {
        Form::headForm("Password Wallet - My Account");
    ?>
        <body class="bg-dark text-light text-center">
            <header>
                <nav class="navbar navbar-expand-lg navbar-light bg-light py-sm-3">
                    <h3 class="text-primary">Password Wallet</h3>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                      <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                      <ul class="navbar-nav mr-auto">
                        <li class="nav-item h5 px-sm-3 ml-lg-3">
                          <a class="nav-link" href="passwords.php">Your passwords</a>
                        </li>
                        <li class="nav-item active px-sm-3 h5">
                          <a class="nav-link" href="addPassword.php">Add password</a>
                        </li>
                        <li class="nav-item px-sm-3 h5">
                          <a class="nav-link" href="account.php">Account</a>
                        </li>
                      </ul>
                      <ul class="navbar-nav ml-auto mr-lg-3">
                        <li class="nav-item h5 ">
                            <form method="POST" action="controller.php">
                                <button class="btn btn-outline-info" type="submit" name="submit" value="logout" id="logoutBTN">Logout</button>
                            </form>
                        </li>
                      </ul>
                    </div>
                </nav>                      
            </header>
            
            <section>
                <header class="text-primary py-4">
                    <h2>Add your new password</h2>
                </header>
                <?php
                    if(isset($_SESSION['addPasswordSuccess']) && $_SESSION['addPasswordSuccess'] == false) {
                        echo '<h5 class="col-sm-8 mx-auto alert alert-danger alert-dismissible fade show pb-2 text-center" role="alert">An error has occurred while adding the password. Try again.'.
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                        unset($_SESSION['addPasswordSuccess']);
                    }
                    if(isset($_SESSION['addPasswordValidation'])) {
                        echo '<h5 class="col-sm-8 mx-auto alert alert-danger alert-dismissible fade show pb-2 text-center" role="alert">'.$_SESSION['addPasswordValidation'].
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                        unset($_SESSION['addPasswordValidation']);
                    }
                ?>
                <form method="POST" action="controller.php" class="col-sm-8 col-lg-4 mx-auto">
                    <div class="form-group mb-sm-4">
                      <label for="addressAddInput">Please enter your web address</label>
                      <input type="text" class="form-control" id="addressAddInput" name="addressAdd">
                    </div>
                    <div class="form-group mb-sm-4">
                      <label for="loginAddInput">Please enter your login</label>
                      <input type="text" class="form-control" id="loginAddInput" name="loginAdd">
                    </div>
                    <div class="form-group mb-sm-4">
                      <label for="passwordAddInput">Please enter your password for the account</label>
                      <div class="row col-12 m-0 px-0">
                        <input type="password" class="form-control col-10" id="passwordAddInput" name="passwordAdd">
                        <button type="button" class="btn btn-primary col-2" id="eyePassword"><img src="eye-fill.svg" alt="Eye"/></button>
                      </div>
                    </div>
                    <div class="form-group mb-sm-4">
                      <label for="descriptionAddInput">Please enter short description (optional)</label>
                      <textarea class="form-control" id="descriptionAddInput" name="descriptionAdd" title="Please enter text text with a maximum of 250 characters"></textarea>
                    </div>
                    <div class="row mt-3">
                        <button type="submit" name="submit" value="addPassword" class="btn btn-outline-success col-sm-4 mx-auto">Add</button>
                        <button type="reset" class="btn btn-outline-danger col-sm-4 mx-auto my-4 my-sm-0">Reset</button>
                    </div>
                  </form>
            </section>
            
            <script src="js/jquery/jquery-3.5.1.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/ulg/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
            <script src="js/bootstrap/bootstrap.bundle.min.js"></script>
            <script src="js/user.js"></script>
        </body>
        </html>
    <?php
    }
    
    public static function accountPageForm() {
        Form::headForm("Password Wallet - My Account");
    ?>
        <body class="bg-dark text-light text-center">
            <header>
                <nav class="navbar navbar-expand-lg navbar-light bg-light py-sm-3">
                    <h3 class="text-primary">Password Wallet</h3>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                      <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                      <ul class="navbar-nav mr-auto">
                        <li class="nav-item h5 px-sm-3 ml-lg-3">
                          <a class="nav-link" href="passwords.php">Your passwords</a>
                        </li>
                        <li class="nav-item px-sm-3 h5">
                          <a class="nav-link" href="addPassword.php">Add password</a>
                        </li>
                        <li class="nav-item active px-sm-3 h5">
                          <a class="nav-link" href="account.php">Account</a>
                        </li>
                      </ul>
                      <ul class="navbar-nav ml-auto mr-lg-3">
                        <li class="nav-item h5 ">
                            <form method="POST" action="controller.php">
                                <button class="btn btn-outline-info" type="submit" name="submit" value="logout" id="logoutBTN">Logout</button>
                            </form>
                        </li>
                      </ul>
                      
                    </div>
                </nav>                      
            </header>       
            
            <section>
                <header class="text-primary py-4">
                    <h2>Change your master password</h2>
                </header>
                
            <?php
                if(isset($_SESSION['changeMasterPasswordSuccess']) && $_SESSION['changeMasterPasswordSuccess'] == false) {
                    echo '<h5 class="col-sm-8 mx-auto alert alert-danger alert-dismissible fade show pb-2 text-center" role="alert">An error has occurred while changing the password. Try again.'.
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                    unset($_SESSION['changeMasterPasswordSuccess']);
                }
                else if(isset($_SESSION['changeMasterPasswordSuccess']) && $_SESSION['changeMasterPasswordSuccess'] == true) {
                    echo '<h5 class="col-sm-8 mx-auto alert alert-success alert-dismissible fade show pb-2 text-center" role="alert">You have successfully changed your master password!'.
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                    unset($_SESSION['changeMasterPasswordSuccess']);
                }
                if(isset($_SESSION['changeMasterPasswordValidation'])) {
                    echo '<h5 class="col-sm-8 mx-auto alert alert-danger alert-dismissible fade show pb-2 text-center" role="alert">'.$_SESSION['changeMasterPasswordValidation'].
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></h5>';
                    unset($_SESSION['changeMasterPasswordValidation']);
                }
            ?>
                
                <form method="POST" action="controller.php" class="col-sm-8 col-lg-4 mx-auto">
                    <div class="form-group mb-sm-4">
                      <label for="oldPassword">Please enter your old password</label>
                      <input type="password" class="form-control" id="oldPassword" name="oldPassword">
                    </div>
                    <div class="form-group mb-sm-4">
                      <label for="newPassword">Please enter new password</label>
                      <input type="password" class="form-control" id="newPassword" name="newPassword" title="Please enter a password of at least 3 characters">
                    </div>
                    <div class="form-group mb-sm-4">
                      <label for="newPassword2">Please enter your new password again</label>
                      <input type="password" class="form-control" id="newPassword2" name="newPassword2" >
                    </div>
                    <div class="row mt-5">
                        <button type="submit" name="submit" value="changePassword" class="btn btn-outline-success col-sm-4 mx-auto">Change</button>
                        <button type="reset" class="btn btn-outline-danger col-sm-4 mx-auto my-4 my-sm-0">Reset</button>
                    </div>
                  </form>
            </section>
            
        <section class="pt-5 col-sm-10 mx-auto">
                <h3 class="text-primary">See your login activity</h3>
                <div id="userLoginActivity"></div>
            </section>
            
            <script src="js/jquery/jquery-3.5.1.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/ulg/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
            <script src="js/bootstrap/bootstrap.bundle.min.js"></script>
            <script src="js/userLoginActivity.js"></script>
        </body>
        </html>
    <?php
    }
    
    public static function sharePasswordPageForm() {
        Form::headForm("Password Wallet - Share Password");
    ?>
        <body class="bg-dark text-light text-center">
            <header>
                <nav class="navbar navbar-expand-lg navbar-light bg-light py-sm-3">
                    <h3 class="text-primary">Password Wallet</h3>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                      <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                      <ul class="navbar-nav mr-auto">
                        <li class="nav-item h5 px-sm-3 ml-lg-3">
                          <a class="nav-link" href="passwords.php">Your passwords</a>
                        </li>
                        <li class="nav-item px-sm-3 h5">
                          <a class="nav-link" href="addPassword.php">Add password</a>
                        </li>
                        <li class="nav-item active px-sm-3 h5">
                          <a class="nav-link" href="account.php">Account</a>
                        </li>
                      </ul>
                      <ul class="navbar-nav ml-auto mr-lg-3">
                        <li class="nav-item h5 ">
                            <form method="POST" action="controller.php">
                                <button class="btn btn-outline-info" type="submit" name="submit" value="logout" id="logoutBTN">Logout</button>
                            </form>
                        </li>
                      </ul>
                    </div>
                </nav>                      
            </header>     
            
            <section class="container">
                <h2 class="text-info py-4">Share your password</h2>
                      <h5 classs="text-light">Select users to share password</h5>
                      <div class="col-sm-8 mx-auto input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1">User login</span>
                        </div>
                        <input type="text" id="userLoginInput" class="form-control"  aria-label="Username" aria-describedby="basic-addon1">
                        <button class="btn btn-primary text-light" id="addUserBTN">Add user</button>
                      </div>
                      <div id="addedUsers" class="pt-3 row "></div>
                      
                    <button id="sharePasswordBTN" class="btn btn-success col-10 col-sm-4 col-xl-2 mt-5">Share password</button>
                  </form>
            </section>
         
            <script src="js/jquery/jquery-3.5.1.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/ulg/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
            <script src="js/bootstrap/bootstrap.bundle.min.js"></script>
            <script src="js/sharePassword.js"></script>
        </body>
        </html>
    <?php
    }
    
    
}