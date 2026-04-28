# matomo-plugin-pageviewcounter

This plugin configures Matomo so that it is possible to add a visit
counter to pages that are tracked by Matomo.

# Installation steps

## Configure CORS domains

You need to allow cross-origin requests from the domain of the pages with the counter to
the Matomo website. See <https://matomo.org/faq/how-to/faq_18694/> for instructions.

## Configure allowed site IDs

The plugin makes visit data of a particular site public. An administrator has to specifically
enable it for a particular site ID using the `enabledSiteIds` plugin setting. Set this parameter to
a comma-separated list of site IDs for which data should be made public, e.g. `1,2`.

## Add tracking code to page

In order to track visits to the page, you need to add the standard Matomo tracker code.
See <https://developer.matomo.org/guides/tracking-javascript-guide#finding-the-piwik-tracking-code> for
additional information.

## Add page counter display code to website

In order to display the counter on a page, you'd need to send a request to the `PageViewCounter.getVisits`
API endpoint from a script, and pass the site ID of the website in Matomo as well as the URL of the page as parameters.

For example:
```
https://www.matomo.test/index.php?module=API&method=PageViewCounter.getVisits&format=JSON&siteId=1&url=https%3A%2F%2Fmysite.test/foo.html
```

# See also

* [Classic Counter Matomo plugin](https://plugins.matomo.org/ClassicCounter): a similar plugin that displays a visit counter in a retro digital clock format.
