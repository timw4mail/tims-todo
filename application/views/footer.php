</div>
<?php $q_num = $this->page->num_queries() ?>
<hr />
<footer class="footer">
	Generated in <?= $this->benchmark->elapsed_time();?> seconds, <?= $q_num ?> quer<?= ($q_num == 1) ? "y": "ies" ?>
</footer>
<!-- Piwik -->
<script type="text/javascript"> 
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://static.timshomepage.net/piwik/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 1]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
    g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();

</script>
<noscript><p><img src="http://static.timshomepage.net/piwik/piwik.php?idsite=1" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Code -->
<?php  if($this->session->userdata('uid') == 1){$this->output->enable_profiler(TRUE);} ?>
<script src="/js/jquery.js"></script>
<script src="/js/jquery-ui.min.js"></script>
<?= $foot_js ?>
</body>
</html>