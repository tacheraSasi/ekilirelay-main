<?php 
  session_start();
  if (isset($_SESSION['relay_user'])) {
    header("location: ../apikey");
  }
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Discover Ekilie, a vibrant social media platform tailored for sharing ideas, perspectives, and insights. Meet Tachera W. Sasi, the visionary CEO of Ekilie, on his website at tachera.com. Ekilie transcends traditional social networking by offering an advanced, with its headquarters in Tanzania. As you delve into Ekilie's diverse features, you'll find the unique ability for users to share not only text but also music, audio, and podcasts on the social media side.Join the Ekilie community to unlock the full potential of meaningful connections and innovative learning experiences. Whether you're an individual seeking a platform for intellectual exchange or a school administrator looking to streamline operations with cutting-edge technology, Ekilie has something to offer.Explore the dynamic landscape of Ekilie, where creativity meets education, and connect with like-minded individuals. As you navigate the platform, you'll witness the commitment of Tachera W. Sasi to driving positive change in the realms of social interaction and education.If you are searching for the CEO of Ekilie, Tachera Sasi, rest assured that this visionary leader is at the helm, guiding Ekilie towards a future where ideas flourish, perspectives converge, and learning knows no bounds.">

    <meta name="keywords" content="Ekilie, social media, ideas, perspectives, insights, Tachera W. Sasi, CEO, AI, automated school management system, education, Tanzania">

    <meta name="author" content="Ekilie - Connecting Ideas, Empowering Learning">

    <meta property="og:title" content="Ekilie - Connecting Ideas, Empowering Learning">
    <meta property="og:description" content="Discover Ekilie, a vibrant social media platform tailored for sharing ideas, perspectives, and insights. Explore an advanced AI automated school management system, with its base in Tanzania. Join the Ekilie community for meaningful connections and innovative learning experiences.">
    <meta property="og:image" content="https://www.ekilie.com/assets/img/favicon.jpeg">
    <meta property="og:url" content="https://www.ekilie.com">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Ekilie - Connecting Ideas, Empowering Learning">
    <meta name="twitter:description" content="Discover Ekilie, a vibrant social media platform tailored for sharing ideas, perspectives, and insights. Explore an advanced AI automated school management system, with its base in Tanzania. Join the Ekilie community for meaningful connections and innovative learning experiences.">
    <meta name="twitter:image" content="https://www.ekilie.com/assets/img/favicon.jpeg">

    <meta name="robots" content="index, follow">

    <meta name="googlebot" content="index, follow">

    <meta name="application-name" content="ekilie">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/img/favicon.jpeg">

    <meta name="creator" content="Tachera W. Sasi">
    <meta name="ceo" content="Tachera W. Sasi">

    <link rel="canonical" href="https://www.ekilie.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,300;0,400;0,500;1,100;1,400;1,600;1,800&display=swap" rel="stylesheet">
  
    <title>ekiliRelay</title>
<style>
  
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family: 'Montserrat', sans-serif;
  
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


.login {
  display: flex;
  background: linear-gradient(rgba(51, 153, 93, 0.4),
   rgba(9, 66, 77, 0.586)), url("../assets/img/login.jpg") center;
  background-size: cover;
  height: 100vh;
  justify-content: center;
  align-items: center;
}

.card {
  display: flex;
  background: linear-gradient(rgba(51, 153, 93, 0.4),
   rgba(9, 66, 77, 0.586)), url("../assets/img/login.jpg") center;
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

.logo-container{
  background-color:#000000a4 ;
  display: inline-flex;
  text-align: center;
  padding:3px;
  border-radius: 1rem;
  cursor: pointer;
}
.logo{
  width: 25px;
  height: 25px;
  border-radius:50%;
  margin-right:8px
}
.logo-text{
  margin:auto;
  font-size: 20px;
}

.right {
  flex: 1;
  background-color: #000000bf;
  margin:6px;
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
.sub-heading{
  font-size:small;
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
  border:solid 1.7px rgba(99, 167, 99, 0.822);
  outline:none;
  padding: 1rem;
  transition:all ease-in-out .7s;
  border-radius:1.5rem;
}
input:focus{
  border:solid 1.7px rgb(153, 250, 153);
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

.error-text{
  display: none;
}
#typingText{
  font-weight:450;
}
.google{
  margin:8px 0;

}
.google button{
  padding :1rem;
  width: 100%;
  border:solid lightgreen 2px;
  border-radius:8px;
  background-color: transparent;
  color:lightgreen ;
  cursor: pointer;
}
.google button:hover{
  background-color: #33995d;
  color:black;
  border:solid #33995d 2px;
}
.google button:active{
  transform:scale(.98)
}
@media screen and (max-width:850px) {
  
  .card{
    width: 100%;

  }
  .left{
    display: none;
  }
  .right{
    padding: 1rem;
  }
  
}

#countrySelect {
  color: #646464;
  background-color: transparent;
  border:solid 1.7px rgba(99, 167, 99, 0.822);
  outline:none;
  padding: 1rem;
  transition:all ease-in-out .7s;
  border-radius:1.5rem;
}

</style>
</head>
<body>
  <div class="login">
    <div class="card">
    <div class="left">
        <div class="top-container">
          <div style="
          display: flex;
          justify-content: flex-start;">
            <div class="logo-container">
              <img src="https://relay.ekilie.com/img/ekilirelay.jpeg" alt="" class="logo">
              <div class="logo-text">ekilie</div>
            </div>
          </div>
          <div class="middle-content">
            <h1>ekiliRelay</h1>
            <h2 id="typingText" style="display: inline;"></h2>
            <span class="cursor"></span>
          </div>
        </div>
        <div class="bottom-container">
          Embark on an Odyssey of Technological Marvels with EkiliSense:
          Traverse the Digital Frontiers of AI-Driven Education
          Immerse Yourself in the Wonders of Machine Learning and Automation
          Uncover Hidden Gems and Revolutionary Insights
          Together, Let's Forge a Brighter Future for Learning!
        </div>
        
    </div>
      <div class="right">
        <h1>Login</h1>
        <p class="sub-heading">
          Unlock the Gates of Insight: 
          Traverse the Digital Landscapes of Tomorrow's Learning Odyssey
        </p>
        <form  action="#" method="POST" enctype="multipart/form-data" autocomplete="off" class="create-groove-form">
        <div class="error-text" style="
              background-color: rgba(243, 89, 89, 0.562);
              color:#fff;
              padding:6px;
              border-radius:8px">
            </div>
          <div class=" field input">
            <input style="width: 100%;" type="text" name="email"  placeholder="Your email" required>
          </div>
          <div class=" field input">
            <input style="width: 100%;" type="password" name="password"  placeholder="Your password" required>
          </div>
          
          <div class="input-container field button">
              <button  id="submit" type="submit">Sign in</button>
          </div>
          <div class="link" style="color:lightgrey">Don't have an ekiliRelay account yet?
            <a href="../signup" style="color:#33995d;text-decoration:none">
             Join
            </a>
          </div>
          <div class="link" style="color:lightgrey">Forgot password?
            <a href="../reset" style="color:#33995d;text-decoration:none">
             Click here to reset.
            </a>
          </div>

        </form>
      </div>
    </div>
  </div>

  <script>
    // JavaScript logic
    document.addEventListener("DOMContentLoaded", function() {
      const typingTextElement = document.getElementById("typingText");
      const originalText = "Welcome back to ekilie";
      let currentIndex = 0;

      function typingAnimation() {
        typingTextElement.textContent = originalText.substring(0, currentIndex);
        currentIndex++;

        if (currentIndex <= originalText.length) {
          setTimeout(typingAnimation, 150);
        }
      }

      typingAnimation();
    });

  </script>
  <script src="javascript/login.js"></script>
</body>
</html>
