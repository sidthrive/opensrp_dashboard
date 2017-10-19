  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form action="<?= site_url('login') ?>" method="post">
              <h1>Login Form</h1>
              <div>
                <input name="username" type="text" class="form-control" placeholder="Username" required />
              </div>
              <div>
                <input name="password" type="password" class="form-control" placeholder="Password" required />
              </div>
              <div>
                <button class="btn btn-default submit">Log in</button>
              </div>

              <div class="clearfix"></div>

              <div class="separator">

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1>Summit Institute of Development</h1>
                  <p>©2017 All Rights Reserved</p>
                </div>
              </div>
            </form>
          </section>
        </div>
    </div>
  </body>
</html>
