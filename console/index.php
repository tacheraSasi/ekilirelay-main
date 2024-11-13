<?php 
  session_start();
  include_once "../config.php";
  if (!isset($_SESSION['relay_user'])) {
    header("location: ../auth/login/");
  }
  $user_unique_id = $_SESSION['relay_user'];
  $select_user = "SELECT * FROM users WHERE unique_id = '$user_unique_id'";
  $result_user = mysqli_query($conn, $select_user);
  $user_info = mysqli_fetch_assoc($result_user);
  $username = $user_info['name'];

  $select_api = mysqli_query($conn,"SELECT * FROM data WHERE user = '$user_unique_id'");
  $data = mysqli_fetch_array($select_api);
  $api_key = $data['api_key'];
  $sent_count = $data['emails_sent'];
  $total = $data['requests'];
  $failed_count = $total - $sent_count;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>ekiliRelay Console</title>
  
  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">


  <meta name="author" content="Tachera Sasi">

  <link rel="canonical" href="https://relay.ekilie.com">

  <link rel="icon" href="https://relay.ekilie.com/img/favicon.png" type="image/x-icon">

  <link rel="apple-touch-icon" href="https://relay.ekilie.com/img/favicon.png">

  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <link href="../assets/css/style.css" rel="stylesheet">

  <style>
    .header, .footer {
      background-color: var(--card-bg-color);
    }

    .sidebar {
      background-color: var(--card-bg-color);
    }

    .card {
      background-color: var(--card-bg-color);
      border-color: var(--border-color);
    }

    .card-title {
      color: var(--title-color);
    }

    .btn-primary {
      background-color: var(--btn-bg);
      color: var(--btn-text);
    }

    pre code {
      background-color: #272822;
      color: #f8f8f2;
      padding: 1em;
      border-radius: 4px;
      display: block;
      overflow-x: auto;
    }
    span code {
      background-color: #272822;
      color: #f8f8f2;
      padding: 1em;
      border-radius: 4px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      overflow-x: auto;
    }

    a {
      color: var(--btn-text);
    }

    .pagetitle h1 {
      color: var(--title-color);
    }

    .btn-new {
      display: inline-flex;
      align-items: center;
      padding: 0.5em 1em;
      background-color: var(--btn-bg);
      color: var(--btn-text);
      border-radius: 4px;
      text-decoration: none;
    }

    .btn-new i {
      margin-right: 0.5em;
    }

    .faq-item {
      margin-bottom: 1em;
    }

    .faq-item h5 {
      color: var(--title-color);
    }
  </style>
</head>

<body>
  <!-- Header -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="#" class="logo d-flex align-items-center">
        <span class="d-none d-lg-block">ekiliRelay</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <!-- <li>
            <a href="https://www.producthunt.com/posts/ekilirelay?embed=true&utm_source=badge-featured&utm_medium=badge&utm_souce=badge-ekilirelay" target="_blank"><img src="https://api.producthunt.com/widgets/embed-image/v1/featured.svg?post_id=481838&theme=dark" alt="ekiliRelay - Completely&#0032;Free&#0032;Email&#0032;API&#0032; | Product Hunt" style="width: 250px; height: 40px;" width="250" height="54" /></a>
        </li> -->
        
        <li class="nav-item">
          <button style="border: none;margin-right:1rem" class="btn-new" id="shareBtn">
            <i class="bi bi-share"></i> 
            <span class="d-none d-md-block">Share</span>
          </button>
        </li>
        
        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="../assets/img/user.png" alt="Profile" class="">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?=$username?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6 id="school-name-h6"><?=$username?></h6>
              <span>ekiliRelay</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            
            <li>
              <a class="dropdown-item d-flex align-items-center" href="logout.php?ref=<?=$school_uid?>">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="logout.php?ref=<?=$school_uid?>">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->
  </header><!-- End Header -->

  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link " href="./">
          <i class="bi bi-file-arrow-down"></i>
          <span>Console</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="https://relay.ekilie.com/docs" target="blank">
          <i class="bi bi-file-arrow-down"></i>
          <span>Documentation</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#apikey">
          <i class="bi bi-file-arrow-down"></i>
          <span>Api key</span>
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="#contact">
          <i class="bi bi-envelope"></i>
          <span>Contact</span>
        </a>
      </li>
      
    </ul>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Console</h1>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">
          <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card" style="border-radius: 1rem;">

                <div class="card-body">
                  <h5 class="card-title">Sent <span>| all</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-check"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?=$sent_count?>/<?=$total?></h6>
                     
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- -->

            <!--  -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">

                

                <div class="card-body">
                  <h5 class="card-title">failed <span>| all</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-exclamation-octagon"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?=$failed_count?>/<?=$total?></h6>
                      <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span>
 -->
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End -->
            <div class="" id="apikey">
              <div class="card info-card customers-card">
                <div class="card-body">
                  <h5 class="card-title">Habari, <?=$username?></h5>
                  <p>This is you api key.</p>
                  
                  <span>
                  <code class="d-flex justify-between items-center">
                    <span id="api-key"><?=$api_key?> </span>
                    <button class="btn btn-secondary" id="copy-button">
                      <i class="bi bi-clipboard"></i> Copy
                    </button>
                  </code>
                </span>
                
                <script>
                  document.getElementById('copy-button').addEventListener('click', function() {
                    // Geting the API key from the span
                    var apiKey = document.getElementById('api-key').innerText.trim();
                
                    // Creating a temporary textarea element to copy the API key
                    var tempInput = document.createElement('textarea');
                    tempInput.value = apiKey;
                    document.body.appendChild(tempInput);
                
                    // Selecting the text inside the textarea and copy it
                    tempInput.select();
                    document.execCommand('copy');
                
                    // Removing the temporary textarea from the DOM
                    document.body.removeChild(tempInput);
                
                    // Optional: Provide user feedback, such as changing button text
                    this.innerHTML = '<i class="bi bi-clipboard-check"></i> Copied!';
                    
                    // Reseting button text after a short delay
                    setTimeout(() => {
                      this.innerHTML = '<i class="bi bi-clipboard"></i> Copy';
                    }, 2000);
                  });
                </script>
                
                </div>
              </div>
            </div>

            <!-- Contact -->
            <div class="" id="contact">
              <div class="card info-card customers-card">
                <div class="card-body">
                  <h5 class="card-title">Contact <span></span></h5>
                  <p>If you have any questions or need further assistance, please reach out to us:</p>
                  <ul>
                    <li>Email: support@ekilie.com</li>
                    <li>Visit: <a href="https://ekilie.com/" target="_blank">ekilie.com</a></li>
                    <li>Creator: <a href="https://tachera.com/" target="_blank">Tachera W</a></li>
                    <li>GitHub: <a href="https://github.com/tacheraSasi" target="_blank">github.com/tacheraSasi</a></li>
                  </ul>
                  <script type="text/javascript" src="https://cdnjs.buymeacoffee.com/1.0.0/button.prod.min.js" data-name="bmc-button" data-slug="xjwsJBTYFD" data-color="#8ff0a4" data-emoji="🙂"  data-font="Inter" data-text="Support ekiliRelay" data-outline-color="#000000" data-font-color="#000000" data-coffee-color="#FFDD00" ></script>
                  <img src="https://relay.ekilie.com/img/bmc_qr.png" class="mt-4" 
                  style="width: 300px;">
                </div>
              </div>
            </div><!-- End Contact -->
          </div>
        </div><!-- End Left side columns -->

        <div class="col-lg-4">
          <div class="alert alert-dark bg-dark border-0 text-light alert-dismissible fade show" role="alert">
              <h4 class="alert-heading"><i class="bi bi-envelope"></i> ekiliRelay </h4>
              <p>📧 Send emails effortlessly with our free email API. Reliable, fast, and secure.</p>
              <hr style="background-color: var(--border-color);">
              <p class="mb-0">🚀 Simplify email communication for your app with ekiliRelay!</p>
              <button type="button" class="btn-close text-light" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
      </div>

      </div>
    </section>
  </main><!-- End #main -->

  <!-- Footer -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>ekiliRelay</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      From <a href="https://ekilie.com">ekilie</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  
  <script data-name="BMC-Widget" data-cfasync="false" src="https://cdnjs.buymeacoffee.com/1.0.0/widget.prod.min.js" data-id="xjwsJBTYFD" data-description="Support me on Buy me a coffee!" data-message="Love ekiliRelay? Help keep it free and growing by buying me a coffee! ☕" data-color="#40DCA5" data-position="Right" data-x_margin="18" data-y_margin="18"></script>

  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>
  <script src="../assets/js/main.js"></script>
  <script>
    document.getElementById('shareBtn').addEventListener('click', () => {
      if (navigator.share) {
        navigator.share({
          title: 'ekiliRelay Documentation',
          text: 'Check out the ekiliRelay Documentation',
          url: window.location.href,
        }).then(() => {
          console.log('Thanks for sharing!');
          alert('Thanks for sharing!');
        }).catch(err => {
          console.error('Error sharing:', err);
        });
      } else {
        // Fallback for browsers that don't support the Web Share API
        alert('Web Share API not supported in this browser.');
      }
    });
  </script>
</body>

</html>
