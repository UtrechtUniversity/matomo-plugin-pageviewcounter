# matomo-plugin-pageviewcounter

This plugin configures Matomo so that it is possible to add a visit
counter to pages that are tracked by Matomo.

# Installation

A complete Matomo setup including this plugin is available at <https://github.com/utrechtuniversity/matomo-ansible>.
Alternatively, you can install the plugin manually by unpacking the code from this repository in a
subdirectory of the Matomo plugins directory (e.g. `/var/www/html/matomo/plugins`).

# Configuration steps

## Configure CORS domains

You need to allow cross-origin requests from the domain of the pages that have the counter to
the Matomo website. See <https://matomo.org/faq/how-to/faq_18694/> for instructions.

## Configure allowed site IDs

The plugin makes visit data of a particular site public. An administrator has to specifically
enable it for a particular site ID using the `enabledSiteIds` plugin setting (accessible in the Matomo web
interface via *General settings -> Page View Counter* once the plugin has been installed). Set this parameter to
a comma-separated list of site IDs for which visit data should be made public, e.g. `1,2`.

## Add tracking code to page

In order to track visits to the page, you need to add the standard Matomo tracker code.
See <https://developer.matomo.org/guides/tracking-javascript-guide#finding-the-piwik-tracking-code> for
more information.

## Add page counter display code to website

In order to display the counter on a page, you'd need to send a request to the `PageViewCounter.getVisits`
API endpoint from a script, and pass the site ID of the website in Matomo as well as the URL of the page as parameters.

For example:
```
https://www.matomo.test/index.php?module=API&method=PageViewCounter.getVisits&format=JSON&siteId=1&url=https%3A%2F%2Fmysite.test/foo.html
```

Example page source:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

<!-- Matomo tracking code-->
<script>
  var _paq = window._paq = window._paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="https://www.matomo.test/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '2']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Matomo tracking code -->

<!-- Matomo visit counter -->
<script>
async function updateVisitCounter() {
  const pageUrl = encodeURIComponent(window.location.href);
  const matomoServer = "https://www.matomo.test";
  const siteId = "2";
  const requestUrl = `${matomoServer}/index.php?module=API&method=PageViewCounter.getVisits&format=JSON&siteId=${siteId}&url=${pageUrl}`;

  try {
    const response = await fetch(requestUrl, { mode: 'cors' } );

    if (!response.ok) {
      throw new Error(`Cannot fetch visit counter data: ${response.status} for ${requestUrl}`);
    }

    const text = await response.text();

    let data;
    try {
      data = JSON.parse(text);
    } catch {
      console.error("Visit count data is not valid JSON:", text);
      return;
    }

    if (!Object.hasOwn(data, "nb_visits")) {
      console.error('No "nb_visits" key found in response:', data);
      return;
    }

    const el = document.getElementById("visitcounter");
    if (!el) {
      console.error('Element with id "visitcounter" not found.');
      return;
    }
    el.textContent = data.nb_visits;

  } catch (error) {
    console.error("Failed to fetch stats:", error);
  }
}
document.addEventListener("DOMContentLoaded", updateVisitCounter);
</script>
<!-- End Matomo visit counter -->

</head>
<body>
<p>Number of page visits: <span id="visitcounter"> ... </span>
</body>
</html>
```

When using this example code, ensure that the URL of the Matomo server (`www.matomo.test`) and the site ID (`2`) are changed so that
they match with your Matomo server.

# See also

* [Classic Counter Matomo plugin](https://plugins.matomo.org/ClassicCounter): a similar plugin that displays a visit counter in a retro digital clock format.
