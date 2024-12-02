<?php
add_action('wp_head', 'inject_matomo_script');

function inject_matomo_script() {
    if (defined('MATOMO_CONTAINER_ID') && !defined('MATOMO_URL')) {
        // This is for Matomo Tag Manager on premium.analys.cloud
        // It's the one we will use for all the new LTS sites.
        ?>
        <!-- Matomo Tag Manager -->
        <script>
          var _mtm = window._mtm = window._mtm || [];
          _mtm.push({'mtm.startTime': (new Date().getTime()), 'event': 'mtm.Start'});
          (function() {
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true;
            g.src='https://premium.analys.cloud/js/container_' + '<?php echo MATOMO_CONTAINER_ID; ?>' + '.js';
            s.parentNode.insertBefore(g,s);
          })();
        </script>
        <!-- End Matomo Tag Manager -->
        <?php
    } elseif (!defined('MATOMO_CONTAINER_ID') && defined('MATOMO_URL')) {
        // This is for regular Matomo tag. Use this during the transition period, and then
        // remove it.
        ?>
        <!-- Matomo -->
        <script>
          var _paq = window._paq = window._paq || [];
          _paq.push(['trackPageView']);
          _paq.push(['enableLinkTracking']);
          (function() {
            var u="<?php echo MATOMO_URL; ?>";
            _paq.push(['setTrackerUrl', u+'matomo.php']);
            _paq.push(['setSiteId', '1']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
          })();
        </script>
        <!-- End Matomo Code -->
        <?php
    }
}
