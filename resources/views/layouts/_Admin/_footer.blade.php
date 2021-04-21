<!-- Footer Structure -->
<footer class="page-footer #1b5e20 green darken-4">
    <div class="container ">
      <div class="row">
        <div class="col l6 s12">
          <h5 class="white-text"><a href="{{ route('home') }}" class="brand-logo">
                  SgiWeb</a></h5>
          <p class="grey-text text-lighten-4">You can use rows and columns here to organize your footer content.</p>
        </div>
        <div class="col l4 offset-l2 s12">
          <h5 class="white-text">Links</h5>
          <ul>

          <li><a href="{{ route('site.sobre') }}">Sobre</a></li>
          <li><a href="{{ route('site.contato') }}">Contato</a>
            <li><a class="grey-text text-lighten-3" href="#!"></a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="copyright float-right">
        <div class="container">
            &copy;
            <script>
                document.write(new Date().getFullYear())
            </script>, Copyright By Abilio.adm.br      |   Laravel v{{ Illuminate\Foundation\Application::VERSION }}  |  (PHP v{{ PHP_VERSION }}) <i class="material-icons">favorite</i> by
            <a href="https://www.abilio.adm.br" target="_blank">Developer</a>.
        </div>
    </div>

  </footer>
<div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">

</div>
