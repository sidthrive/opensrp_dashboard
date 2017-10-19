<!-- footer content -->
        <footer>
          <div class="pull-right">
            Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>

    <?php if(isset($js)){
        foreach($js as $j){
            echo '<script src="'.assets_url().$j.'"></script>'."\n";
    }} ?>
	
  </body>
</html>