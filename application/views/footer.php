</div>
<?php $q_num = $this->page->num_queries() ?>
<hr />
<footer class="footer">
	Generated in <?= $this->benchmark->elapsed_time();?> seconds, <?= $q_num ?> quer<?= ($q_num == 1) ? "y": "ies" ?>
</footer>
<?php  if($this->session->userdata('uid') == 1){$this->output->enable_profiler(TRUE);} ?>
<script src="/js/jquery.js"></script>
<script src="/js/jquery-ui.min.js"></script>
<script src="/js/CLEditor/jquery.cleditor.js"></script>
<script src="/js/CLEditor/jquery.cleditor.xhtml.js"></script>
<script src="/js/todo.js"></script>
<?php /*<?= $foot_js ?>*/ ?>
</body>
</html>