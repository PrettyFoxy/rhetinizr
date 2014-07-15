<?php
defined( 'PHPFOX' ) or exit( 'NO DICE!' );
?>

<div data-role="page" id="rhetinaMainContainer">
  <header data-role="header"></header>
  <main class="page-content" data-role="content">
    <div class="text-center" style="padding: 100px 0">
      <img src="{$frontofficePath}/images/preloaders/Preloader_{$rand}.gif" alt=""/>
    </div>
  </main>
  <footer data-role="footer" class="footer navbar">
    <p>
      <a href="https://github.com/afurculita" target="_blank">Alexandru Furculita</a>
      of <a href="https://www.rhetina.com">Rhetina</a>
    </p>
  </footer>
</div>
<script>
  var Rhetina = window.Rhetina || {$emptyObject};
  Rhetina.config = {$applicationConfig};
</script>
<script type="text/javascript">var require={$requireJS.options}</script>
<script type="text/javascript" data-main="{$requireJS.main}" src="{$requireJS.src}"></script>