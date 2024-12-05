<?php
add_action('wp_head', 'inject_matomo_script');

function inject_matomo_script() {
    ?>
    <!-- Matomo Cookie Consent -->
    <script>
    var waitForTrackerCount = 0;
    function matomoWaitForTracker() {
      if (typeof _paq === 'undefined') {
        if (waitForTrackerCount < 40) {
          setTimeout(matomoWaitForTracker, 250);
          waitForTrackerCount++;
          return;
        }
      } else {
        document.addEventListener("cookieyes_consent_update", function (eventData) {
            const data = eventData.detail;
            consentSet(data);
        });   
      }
    }
    function consentSet(data) {
       if (data.accepted.includes("analytics")) {
           _paq.push(['rememberCookieConsentGiven']);
           _paq.push(['setConsentGiven']);
       } else {
           _paq.push(['forgetCookieConsentGiven']);  
           _paq.push(['deleteCookies']);         
       }
    }
    document.addEventListener('DOMContentLoaded', matomoWaitForTracker());
    </script>
    <!-- End Matomo Cookie Consent -->
    <?php

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
    }  elseif (defined('MATOMO_CONTAINER_ID') && defined('MATOMO_URL')) {
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
          g.src='<?php echo MATOMO_URL; ?>js/container_' + '<?php echo MATOMO_CONTAINER_ID; ?>' + '.js';
          s.parentNode.insertBefore(g,s);
        })();
      </script>
      <!-- End Matomo Tag Manager -->
      <?php
    } elseif (!defined('MATOMO_CONTAINER_ID') && defined('MATOMO_URL') && defined('MATOMO_SITE_ID')) {
        // This is for regular Matomo tag. Use this during the transition period, and then
        // remove it.
        ?>
        <!-- Matomo -->
        <script>
          var _paq = window._paq = window._paq || [];
          _paq.push(['requireCookieConsent']);
          _paq.push(['trackPageView']);
          _paq.push(['enableLinkTracking']);
          (function() {
            var u="<?php echo MATOMO_URL; ?>";
            _paq.push(['setTrackerUrl', u+'matomo.php']);
            _paq.push(['setSiteId', '<?php echo MATOMO_SITE_ID; ?>']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
          })();
        </script>
        <!-- End Matomo Code -->
        <?php
    }
}
