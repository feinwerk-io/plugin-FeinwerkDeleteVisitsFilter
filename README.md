# Feinwerk Delete Visits Filter

Delete visits from Matomo by segment, date range and website, for example bot or spam traffic that slipped
through, or large amounts of old data. Deletions run as jobs that continue in the background, so even millions of
visits can be removed without browser timeouts.

## Description

Bots that look like real visitors, referrer spam or test traffic that slipped through: once such visits are in Matomo,
they distort visits, bounce rate and conversion rates in every report. Matomo's GDPR tool can delete data, but it is
made for individual people, not for thousands of visits across several websites and months.

Feinwerk Delete Visits Filter deletes every visit that matches a Matomo segment, on one website, several or all of
them, in any date range. You see what will be deleted before anything happens, and large deletions keep running in the
background until they are done.

- **Choose exactly what goes:** one website, several (also all whose name contains a text) or all websites; a preset
  such as "Last 30 days" or any range from the calendar, counted in each website's time zone; a saved segment, Matomo's
  segment editor or plain text. Any Matomo segment works, for example IP ranges, referrers, browsers or page URLs.
- **Check before you delete:** the matching visits per website and in total, a sample with time, IP, browser, entry
  page and referrer, a link to the visitor profile and to the visitor log. From 10,000 visits on you type the number of
  visits to confirm.
- **Built for large clean-ups:** deletions run as jobs in small steps, so even millions of visits do not run into
  timeouts. If you reload or close the page, the deletion continues in the background; very large ones can also run
  on the console without a time limit.
- **Clean data, correct reports:** visits are removed with all their data from Matomo's raw data tables, with the same
  routine as Matomo's GDPR tool, and the affected days are processed again automatically.
- **History:** every deletion with who started it, websites, segment, status, deleted visits and duration. "Use as
  template" starts a new deletion with the same settings, "Try again" continues a failed one.
- **Console and API** for scripts and cron jobs, light and dark mode, English, German, French, Spanish and Italian.

**Works with Feinwerk Tracking Spam Score and Feinwerk Sentinel:** find the junk first. Tracking Spam Score finds spam
networks and bot traffic, and Sentinel (free) alerts you when tracking breaks.

**Get started:** open *Administration › Diagnostic › Delete Visits*, choose websites, a date range and a segment, then
*Check matches*.

**More Plugins for Matomo by Feinwerk:** install [Feinwerk Connector](https://plugins.matomo.org/FeinwerkMarketplaceConnector)
from the Matomo Marketplace to discover, install and update all Feinwerk plugins, free and paid, right in your Matomo.
Documentation: [plugins.feinwerk.io](https://plugins.feinwerk.io/en/plugins/FeinwerkDeleteVisitsFilter).

## Requirements

- Matomo 6.0 or newer (6.x)
- PHP 8.1+
- Super user access

Matomo 5: version 5.x, Matomo 6: version 6.x

## Installation

Install it from Matomo's Marketplace (**Administration → Platform → Marketplace**), or by hand:

1. Copy the `FeinwerkDeleteVisitsFilter` folder to your Matomo `plugins/` directory
2. Activate via Administration → Plugins or CLI:
   ```bash
   ./console plugin:activate FeinwerkDeleteVisitsFilter
   ```

### Upgrading from `DeleteVisitsFilter`

Up to version 1.x this plugin was called `DeleteVisitsFilter`. To upgrade, activate
`FeinwerkDeleteVisitsFilter` and then uninstall `DeleteVisitsFilter`. No settings or data need to be
migrated. The previous console command `deletevisitsfilter:delete` keeps working as an alias.

## How deletions work

- Every deletion is a **job**. Jobs are processed one after another.
- If the admin page is closed or a request is interrupted, the job is not lost: the **hourly background task**
  continues where it stopped (Administration → System → General settings →
  Feinwerk Delete Visits Filter: background processing time per hour; 0 turns it off). Large deletions can run without a
  time limit via `./console feinwerkdeletevisitsfilter:work`.
- Dates are whole days in the **website's timezone**.
- A segment on page or conversion data (e.g. `pageUrl=@/spam`) deletes the **whole** matching visit.
- Segments without a valid condition and segments longer than 8192 characters are refused.
- All of Matomo's log tables are cleaned, including those of other plugins. Matomo's shared action table is not
  touched; unused entries are removed by Matomo itself.
- The affected days are marked for re-processing (including roll-up websites); reports are rebuilt by the next
  archiving run.

## Usage

### Admin page

**Administration › Diagnostic › Delete Visits** has two tabs, "Delete" and "History".

1. **Websites**: one website, several websites (also all whose name contains a text) or all websites.
2. **Date range**: a preset such as "last 30 days" or "last year", or a range from the calendar.
3. **Segment**: a saved segment, the segment editor or plain text (switch between editor and text mode; text mode shows a syntax hint and a character counter).
4. **Check matches** shows the matching visits per website, a sample of visits (with the visitor profile) and a
   link to the visitor log. Nothing is deleted yet.
5. **Delete … visits** starts the deletion. From 10,000 visits on you must confirm by typing the number of visits.

Running deletions show progress, speed and the estimated remaining time. You can cancel them, reload the page or
close it: they continue in the background (if background processing is switched off in the settings, keep the page
open or use the console command). If the console or the background task is already working, the page tells
you so. The "History" tab lists all deletions with filters and details; "Use as template" fills the form with the
settings of an earlier deletion and "Try again" continues a failed one.

The admin page supports Matomo's dark mode: it follows the theme you choose in Matomo (light, dark or automatic).

### Feedback and support

Below the admin page you can tell us whether deleting visits works as expected (thumbs up or down). Your Matomo server
sends the answer to Feinwerk together with the plugin and Matomo version, the browser family and version and the
language of Matomo, nothing about you, your websites or your visitors. To switch it off, add to `config/config.ini.php`:

    [FeinwerkDeleteVisitsFilter]
    feedback = 0

With `enable_internet_features = 0` in Matomo's `[General]` section nothing is sent either, and the question is not
shown. "Support" opens the Feinwerk contact form in a new tab.

### Console

```bash
# Show matches without deleting
./console feinwerkdeletevisitsfilter:delete 1 "visitIp==192.168.1.1" 2024-01-01 2024-12-31 --dry-run

# Delete (asks for confirmation; --yes skips it)
./console feinwerkdeletevisitsfilter:delete 1,3 "visitIp==192.168.1.1" 2024-01-01 2024-12-31

# All websites, larger batches
./console feinwerkdeletevisitsfilter:delete all "browserCode==IE" 2024-01-01 2024-12-31 5000 --yes

# Work on the queue (e.g. from cron, or for very large deletions)
./console feinwerkdeletevisitsfilter:work
./console feinwerkdeletevisitsfilter:work --max-time=3000

# List jobs / cancel one
./console feinwerkdeletevisitsfilter:jobs
./console feinwerkdeletevisitsfilter:jobs --cancel=12
```

Interrupting a running `delete` command (Ctrl+C) does not cancel the deletion: the job continues in the background. Cancel it
with `jobs --cancel=<id>`. Without a terminal (cron, scripts) `delete` requires `--yes`.

### API

| Method | Purpose |
|---|---|
| `FeinwerkDeleteVisitsFilter.previewDeletion` | Matches per website, total, a sample of visits |
| `FeinwerkDeleteVisitsFilter.createJob` | Queue a deletion |
| `FeinwerkDeleteVisitsFilter.processJobs` | Work on the queue for a few seconds; returns active jobs |
| `FeinwerkDeleteVisitsFilter.getJob` / `getJobs` | Job details / history |
| `FeinwerkDeleteVisitsFilter.cancelJob` / `retryJob` | Cancel a job / continue a failed one |
| `FeinwerkDeleteVisitsFilter.countVisits` | Number of matching visits |
| `FeinwerkDeleteVisitsFilter.deleteVisits` | Deprecated: delete within one request |

`idSites` accepts one ID, a comma-separated list or `all`. Send `token_auth` via POST.

## Installing the Feinwerk Connector

The free Feinwerk Connector installs, updates and licenses Feinwerk plugins inside Matomo. The easiest way to get it is
the Matomo Marketplace. While it is not active, super users also find a section "Feinwerk Plugins" in Matomo's
Administration with the entry "Install Feinwerk Connector" ("Activate Feinwerk Connector" when its files are already
there). One click installs it from the Matomo Marketplace and activates it; as with every plugin install, Matomo first
asks for your password. Where Matomo cannot install from its Marketplace (Marketplace, internet features, plugin
administration or automatic updates switched off, or a multi-server setup), the page explains the manual way instead. In Matomo for WordPress the connector is
installed as a WordPress plugin through Matomo Analytics, Marketplace. The connector needs PHP 8.1. Nothing is
downloaded before you click. To hide the entry, add to `config/config.ini.php`:

    [FeinwerkDeleteVisitsFilter]
    connector_installer = 0

The same line in the section `[FeinwerkMarketplaceConnector]` hides it for all Feinwerk plugins.

## Tips from Feinwerk

The plugin shows short tips on other Feinwerk offers, for example Feinwerk Campaigns or Matomo hosting. Only users with
admin access to at least one website and super users see them, and only in Matomo's interface: never in exports, API
results, email reports or embedded widgets. You see at most one tip per place and page:

- one line under the admin page of this plugin,
- one banner on the start page of Matomo's administration,
- one box at the end of the Campaigns, Ecommerce overview and Goals overview reports.

A tip for a Feinwerk plugin you already have installed is not shown. "×" hides a tip for 30 days in your browser. The
tips are part of the plugin: nothing is loaded from outside and nothing is sent. Links to feinwerk.io pages open in a
new tab and carry only the campaign parameters `mtm_campaign`, `mtm_source` and `mtm_content`. The tip for Feinwerk
Connector takes a super user to the connector in Matomo's own Marketplace (same tab, no campaign parameters) and is
shown to super users only when this Matomo can install plugins from there; admins go to its page on plugins.matomo.org.
This tip is not shown inside Matomo for WordPress.

To switch the tips off for this plugin, add to `config/config.ini.php`:

    [FeinwerkDeleteVisitsFilter]
    promotions = 0

The same line in the section `[FeinwerkMarketplaceConnector]` switches them off for all Feinwerk plugins. Recent
versions of the Feinwerk Marketplace Connector (install it from Matomo's Marketplace) also have the setting "Show notes
on other Feinwerk offers".

## Privacy features

- **Deletes like Matomo's GDPR tool:** visits are removed with Matomo's own routine for deleting data subjects, from
  all of Matomo's raw data tables, including those of other plugins. Nothing is copied or exported before deleting.
- **What the history keeps:** for each deletion the websites, the date range, the segment, the status, the number of
  deleted rows and the login of the person who started it. The segment is kept as entered: if it names an IP address
  or a user ID, that stays in the history and in the activity log. When a user is deleted in Matomo, their login is
  removed from the history. Uninstalling the plugin removes the history.
- **Activity log:** with Matomo's Activity Log plugin, finished, failed and cancelled deletions are recorded there.
- **Feedback only on request:** the plugin sends something to Feinwerk only when a user clicks a thumb below the admin
  page, and then only the fields listed under "Feedback and support"; `feedback = 0` switches it off.
- **Tips without tracking:** the tips load nothing from other servers and set no cookies. Links to feinwerk.io carry
  campaign parameters, so Feinwerk sees which tip was clicked once someone follows such a link. `promotions = 0`
  switches them off (see "Tips from Feinwerk").

## FAQ

**Can deleted visits be restored?**
No. Visits are removed from Matomo's raw data tables for good. Make a database backup before large deletions.

**How is this different from Matomo's GDPR tool?**
It uses the same deletion routine, but selects visits by segment, date range and website instead of one person at a
time.

**Are my reports corrected?**
Yes. The affected days are marked for processing again, including roll-up websites; the next archiving run rebuilds the
reports.

**What happens if I close the page?**
The deletion continues: an hourly background task goes on where it stopped (unless a super user switched background
processing off in the settings), and very large deletions can run on the console without a time limit.

**Who can delete visits?**
Super users only.

**Does a segment on pages delete only those pages?**
No. A segment on page or conversion data deletes the whole matching visit.

**Where do I find more Feinwerk plugins?**
Install Feinwerk Connector from the Matomo Marketplace. It lists all Plugins for Matomo by Feinwerk, free and paid, and
installs and updates them with one click.

## License

GPL v3 or later

## Author

Peter Boehlke, [Feinwerk.io](https://feinwerk.io) · support@feinwerk.io
