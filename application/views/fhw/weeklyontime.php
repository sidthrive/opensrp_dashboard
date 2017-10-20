<!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Weely On Time Submission</h3>
              </div>

              <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="well" style="overflow: auto">
              <div class="col-md-12">
                Periode
                <form class="form-horizontal">
                  <fieldset>
                    <div class="control-group">
                      <div class="controls">
                        <div class="input-prepend input-group">
                          <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                          <input type="text" style="width: 200px" name="by_datepicker" id="by_datepicker" class="form-control"/>
                        </div>
                      </div>
                    </div>
                  </fieldset>
                </form>
              </div>
            </div>

            <div class="clearfix"></div>

            <?php foreach ($dusuns as $key => $dusun) {
            ?>
            <div class="row">
              <div class="col-md-12 col-sm-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2><?=$dusun?></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <ul class="dropdown-menu" role="menu"></ul>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="by_tanggal" id="<?=str_replace(' ', '_', $dusun);?>" align="center"></div>
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>


          </div>
        </div>
        <!-- /page content -->