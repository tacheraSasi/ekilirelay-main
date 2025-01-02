<?php
include("../../config.php");
$otp = "";
$email = "";
if(isset($_GET['otp']) && isset($_GET['email'])){
    $otp = $_GET['otp'];
    $email = $_GET['email'];
    //checking if otp is valid
    $q = mysqli_query($conn, "SELECT * FROM otp WHERE email = '{$email}' AND value = '{$otp}'");
    if(mysqli_num_rows($q) == 0){
        header("Location: ../");
        exit();
    }
}else{
    header("Location: ../");
    exit();
}
?>


<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <meta name="creator" content="Tachera W. Sasi" />
        <meta name="ceo" content="Tachera W. Sasi" />

        <link
            rel="icon"
            href="https://relay.ekilie.com/img/favicon.png"
            type="image/x-icon"
        />

        <link
            rel="apple-touch-icon"
            href="https://relay.ekilie.com/img/favicon.png"
        />

        <link rel="canonical" href="https://relay.ekilie.com/auth/reset" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,300;0,400;0,500;1,100;1,400;1,600;1,800&display=swap"
            rel="stylesheet"
        />

        <title>Reset Password | ekiliRelay</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: "Montserrat", sans-serif;
            }
            *::-webkit-scrollbar {
                width: 5px;
                height: 8px;
                border-radius: 1rem;
                background-color: #000000b2;
            }

            *::-webkit-scrollbar-thumb {
                background-color: #aacfad;
                border-radius: 1rem;
            }
            body::before {
                content: url("../assets/img/reset.jpg")
                    url("../assets/img/onboard.jpg");
                display: none;
            }

            .reset {
                display: flex;
                background:
                    linear-gradient(
                        rgba(51, 153, 93, 0.4),
                        rgba(9, 66, 77, 0.586)
                    ),
                    url("../assets/img/reset.jpg") center;
                background-size: cover;
                height: 100vh;
                justify-content: center;
                align-items: center;
            }

            .card {
                display: flex;
                background:
                    linear-gradient(
                        rgba(51, 153, 93, 0.4),
                        rgba(9, 66, 77, 0.586)
                    ),
                    url("../assets/img/reset.jpg") center;
                background-size: cover;
                height: 100vh;
                display: flex;
                background-color: transparent;
                width: 100%;
                height: 100%;
                border-radius: 10px;
                box-shadow: 0 1px 15px 0 rgba(0, 0, 0, 0.863);
                overflow: hidden;
            }

            .left {
                flex: 1;
                color: white;
                height: 100vh;
                padding: 50px;
                display: flex;
                justify-content: space-between;
                flex-direction: column;
                gap: 20px;
            }

            h1 {
                font-size: 60px;
                line-height: 70px;
            }

            button {
                width: 50%;
                color: #33995d;
                padding: 10px;
                border: none;
                border-radius: 6px;
                font-weight: bold;
                cursor: pointer;
            }

            button:hover {
                transform: scale(0.968);
            }

            .logo-container {
                background-color: #000000a4;
                display: inline-flex;
                text-align: center;
                padding: 3px;
                border-radius: 1rem;
                cursor: pointer;
            }
            .logo {
                width: 25px;
                height: 25px;
                border-radius: 50%;
                margin-right: 8px;
            }
            .logo-text {
                margin: auto;
                font-size: 20px;
            }

            .right {
                flex: 1;
                background-color: #000000bf;
                margin: 6px;
                border-radius: 8px;
                padding: 3rem;
                display: flex;
                align-items: center;
                text-align: center;
                flex-direction: column;
                gap: 20px;
                color: #888;
                overflow-y: auto;
            }
            .sub-heading {
                font-size: small;
            }

            form {
                display: flex;
                text-align: center;
                gap: 30px;
                flex-direction: column;
                width: 65%;
            }
            input {
                color: #fff;
                background-color: transparent;
                border: solid 1.7px rgba(99, 167, 99, 0.822);
                outline: none;
                padding: 1rem;
                transition: all ease-in-out 0.7s;
                border-radius: 1.5rem;
            }
            input:focus {
                border: solid 1.7px rgb(153, 250, 153);
            }

            button {
                width: 100%;
                background-color: #65c08bc4;
                color: #fff;
                padding: 1rem;
                border: none;
                border-radius: 2rem;
                font-weight: bold;
                cursor: pointer;
            }

            button:hover {
                transform: scale(0.968);
            }

            /* updated styles */

            .error-text {
                display: none;
            }
            #typingText {
                font-weight: 450;
            }
            .google {
                margin: 8px 0;
            }
            .google button {
                padding: 1rem;
                width: 100%;
                border: solid lightgreen 2px;
                border-radius: 8px;
                background-color: transparent;
                color: lightgreen;
                cursor: pointer;
            }
            .google button:hover {
                background-color: #33995d;
                color: black;
                border: solid #33995d 2px;
            }
            .google button:active {
                transform: scale(0.98);
            }
            @media screen and (max-width: 850px) {
                .card {
                    width: 100%;
                }
                .left {
                    display: none;
                }
                .right {
                    padding: 1rem;
                }
            }

            #countrySelect {
                color: #646464;
                background-color: transparent;
                border: solid 1.7px rgba(99, 167, 99, 0.822);
                outline: none;
                padding: 1rem;
                transition: all ease-in-out 0.7s;
                border-radius: 1.5rem;
            }
            .redirect-container{
              display: none;
              background-color: #20613be7;
              border:1px solid #33995d;
              border-radius:8px;
              margin-top: 1rem;
              padding:1rem;
            }
            /* loader */
            .main-loader-success{
              display: flex;
              align-items: center;
              justify-content: center;
            }
            
            .loader-success {
              border: 2px solid #f3f3f3;
              border-radius: 50%;
              border-top: 6px solid rgb(110, 110, 170);
              border-right: 6px solid rgb(99, 146, 99);
              border-bottom: 7px solid rgb(158, 71, 71);
              border-left: 4px solid rgb(235, 112, 187);
              width: 35px;
              height: 35px;
              -webkit-animation: spin 2s linear infinite;
              animation: spin 2s linear infinite;
            }
            .loader-text{
              margin:auto 3px;
            }
            @-webkit-keyframes spin {
              0% { -webkit-transform: rotate(0deg); }
              100% { -webkit-transform: rotate(360deg); }
            }
            
            @keyframes spin {
              0% { transform: rotate(0deg); }
              100% { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body>
        <div class="reset">
            <div class="card">
                <div class="left">
                    <div class="top-container">
                        <div style="display: flex; justify-content: flex-start">
                            <div class="logo-container">
                                <img
                                    src="https://relay.ekilie.com/img/favicon.png"
                                    alt=""
                                    class="logo"
                                />
                                <div class="logo-text">ekiliRelay</div>
                            </div>
                        </div>
                        <div class="middle-content">
                            <h1>ekiliRelay</h1>
                            <h2 id="typingText" style="display: inline"></h2>
                            <span class="cursor"></span>
                        </div>
                    </div>
                    <div class="bottom-container">Connecting ideas</div>
                </div>
                <div class="right">
                    <h1>Reset password</h1>
                    
                    <form
                        action="#"
                        method="POST"
                        enctype="multipart/form-data"
                        autocomplete="off"
                    >
                        <div
                            class="error-text"
                            style="
                                background-color: rgba(243, 89, 89, 0.562);
                                color: #fff;
                                padding: 6px;
                                border-radius: 8px;
                            "
                        ></div>
                        <div class="field input">
                            <input
                                style="width: 100%"
                                type="password"
                                name="new-password"
                                placeholder="Your new password"
                                required
                            />
                        </div>
                        <div class="field input">
                            <input
                                style="width: 100%"
                                type="Password"
                                name="cpassword"
                                placeholder="Confirm new Password"
                                required
                            />
                        </div>
                        <input type="hidden" value="<?= $otp; ?>" name="otp" />
                        <input type="hidden" value="<?= $email; ?>" name="email" />

                        <div class="input-container field button">
                            <button id="submit" type="submit">Reset</button>
                        </div>
                        
                    </form>
                    
                    <div class="redirect-container">
                      <p>You're being redirect to the Console</p>
                      <div class='main-loader-success'>
                        <div class='loader-success'></div> 
                      </div>
                      <div id="redirect" style="font-weight: bold;">
                        Redirecting...
                      </div>
                    </div>
           
                </div>
            </div>
        </div>

        <script>
            // JavaScript logic
            document.addEventListener("DOMContentLoaded", function () {
                const typingTextElement = document.getElementById("typingText");
                const originalText =
                    "Hold onto your hats! You're about to explore the whimsical world of ekilirelay's features!";
                let currentIndex = 0;
        
                function typingAnimation() {
                    typingTextElement.textContent = originalText.substring(
                        0,
                        currentIndex
                    );
                    currentIndex++;
        
                    if (currentIndex <= originalText.length) {
                        setTimeout(typingAnimation, 150);
                    }
                }
        
                typingAnimation();
            });
        </script>
        <script src="javascript/reset-password.js"></script>
    </body>
</html>
