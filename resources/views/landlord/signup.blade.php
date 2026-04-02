<!doctype html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="msapplication-TileColor" content="#0E0E0E">
    <meta name="template-color" content="#0E0E0E">
    <link rel="manifest" href="manifest.json" crossorigin>
    <meta name="msapplication-config" content="browserconfig.xml">
    <meta name="description" content="Index page">
    <meta name="keywords" content="index, page">
    <meta name="author" content="">
    <link rel="shortcut icon" type="image/x-icon" href="landlord/custom/assets/imgs/template/favicon.png">
    <title>NEROON - Logiciel de Gestion de Cycle de Vente et de Processus Métier</title>
    <script defer="defer" src="landlord/custom/assets/js/app.40ee121.bundle.js"></script>
    <link href="landlord/custom/assets/css/app.2afad0c.bundle.css" rel="stylesheet">
  </head>

  <body>

    <header class="header sticky-bar">
      <div class="container">
        <div class="main-header">
          <div class="header-left">
            <div class="header-logo"><a class="d-flex" href="https://neroon.co/"><img alt="NEROON" src="landlord/custom/assets/imgs/template/logo.png"></a></div>
            <div class="header-nav">
              <nav class="nav-main-menu d-none d-xl-block">

              </nav>
              <div class="burger-icon burger-icon-white"><span class="burger-icon-top"></span><span class="burger-icon-mid"></span><span class="burger-icon-bottom"></span></div>
            </div>
          </div>
          <div class="header-right">
            <div class="block-signin"><a class="btn btn-default hover-up icon-arrow-right" href="page-signup.html">14 JOURS D'ESSAI</a></div>
          </div>
        </div>
      </div>
    </header>


    <main class="main">
      <section class="section-box">
        <div class="bg-6-opacity-30 pt-90">
          <div class="container">
            <div class="box-signup">
              <h1 class="text-heading-3 mb-50 text-center">Let&rsquo;s join us</h1>
              <div class="text-center">
                <div class="mt-40 box-line-throught mb-40"><span class="text-body-text color-gray-500">Innovation, efficacité et productivité</span></div>
              </div>
              <form action="{{route('tenant.checkout')}}" method="POST"  class="form row customer-signup-form">
                  @csrf
                  <div class="box-form-signup mb-200">
                    <input type="hidden" name="package_id" value="{{$request->package_id}}">
                    <input type="hidden" name="subscription_type" value="{{$request->subscription_type}}">
                    <input type="hidden" name="price" value="{{$request->price}}">
                    <div class="form-group"><input required name="company_name" class="form-control" placeholder="Nom de l'entreprise *"></div>
                    <div class="form-group"><input required name="email" class="form-control" placeholder="Votre email *"></div>
                    <div class="form-group"><input required name="phone_number" class="form-control" placeholder="Votre téléphone *"></div>
                    <div class="form-group"><input required name="name" class="form-control" placeholder="username *"></div>
                    <div class="form-group"><input required name="password" type="password" class="form-control" placeholder="password *"></div>
                    <div class="form-group"><input required name="tenant" class="form-control" placeholder="{{'your_sub_domain@'.env('CENTRAL_DOMAIN')}}"></div>
                    @if($terms_and_condition_page)
                    <div class="form-group"><label class="text-body-small color-gray-500"><input required class="chkbox" type="checkbox"> Acceptez <a href="{{url('page/'.$terms_and_condition_page->slug)}}">les termes &amp; conditions</a></label></div>
                    @endif
                    <div class="form-group"><button class="btn btn-green-full text-heading-6">Soumettre</button></div>
                  </div>
              </form>
            </div>
          </div>
          <div class="images-lists">
            <div class="row">
              <div class="col-lg-2 col-md-2 col-sm-6 pl-0"><img class="img-responsive img-full img-1" src="landlord/custom/assets/imgs/template/img-1.png" alt="Agon"></div>
              <div class="col-lg-2 col-md-2 col-sm-6"><img class="img-responsive img-full img-2" src="landlord/custom/assets/imgs/template/img-2.png" alt="Agon"></div>
              <div class="col-lg-4 col-md-4 col-sm-12"><img class="img-responsive img-full img-3" src="landlord/custom/assets/imgs/template/img-3.png" alt="Agon"></div>
              <div class="col-lg-2 col-md-2 col-sm-6"><img class="img-responsive img-full img-4" src="landlord/custom/assets/imgs/template/img-4.png" alt="Agon"></div>
              <div class="col-lg-2 col-md-2 col-sm-6 pr-0"><img class="img-responsive img-full img-5" src="landlord/custom/assets/imgs/template/img-5.png" alt="Agon"></div>
            </div>
          </div>
        </div>
      </section>
    </main>
     <footer class="footer mt-50">
      <div class="container">

        <div class="footer-bottom mt-20">
          <div class="row">
            <div class="col-md-6"><span class="color-gray-400 text-body-lead">&copy; NEROON</span>
              <div class="footer-social"><a class="icon-socials icon-facebook" href="https://www.facebook.com/nneroon" target="_blank"></a><a class="icon-socials icon-twitter" href="https://twitter.com/Neroonn_" target="_blank"></a><a class="icon-socials icon-instagram" href="https://www.instagram.com/neroonn_/" target="_blank"></a><a class="icon-socials icon-linkedin" href="https://www.linkedin.com/company/neroon/" target="_blank"></a></div>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <script src="landlord/custom/assets/js/vendors/modernizr-3.6.0.min.js"></script>
    <script src="landlord/custom/assets/js/vendors/jquery-3.6.0.min.js"></script>
    <script src="landlord/custom/assets/js/vendors/jquery-migrate-3.3.0.min.js"></script>
    <script src="landlord/custom/assets/js/vendors/bootstrap.bundle.min.js"></script>
    <script src="landlord/custom/assets/js/vendors/waypoints.js"></script>
    <script src="landlord/custom/assets/js/vendors/wow.js"></script>
    <script src="landlord/custom/assets/js/vendors/magnific-popup.js"></script>
    <script src="landlord/custom/assets/js/vendors/perfect-scrollbar.min.js"></script>
    <script src="landlord/custom/assets/js/vendors/select2.min.js"></script>
    <script src="landlord/custom/assets/js/vendors/isotope.js"></script>
    <script src="landlord/custom/assets/js/vendors/scrollup.js"></script>
    <script src="landlord/custom/assets/js/vendors/counterup.js"></script>
    <script src="landlord/custom/assets/js/vendors/slick.js"></script>
    <script src="landlord/custom/assets/js/vendors/jquery.elevatezoom.js"></script>
    <script src="landlord/custom/assets/js/vendors/swiper-bundle.min.js"></script>
    <script src="landlord/custom/assets/js/vendors/noUISlider.js"></script>
    <script src="landlord/custom/assets/js/vendors/slider.js"></script>
    <script src="landlord/custom/assets/js/main.js?v=1.0"></script>
  </body>

</html>
