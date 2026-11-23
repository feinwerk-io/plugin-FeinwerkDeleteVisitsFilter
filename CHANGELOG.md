# Changelog

## 6.0.0

The first stable version for Matomo 6. Compared with the last beta under the old numbers (3.0.0-beta.1); users of
version 5.x already have most of these:

- New version numbers that follow Matomo: version 6.x is for Matomo 6, version 5.x for Matomo 5.
- Super users can install and activate the free Feinwerk Connector with one click under Administration, Feinwerk
  Plugins, while it is not active; where Matomo cannot install it from its Marketplace, the page explains the way
  instead. `connector_installer = 0` hides the entry.
- The plugin is now also available in French, Spanish and Italian.
- Below the admin page you can tell us in one line whether deleting visits works as expected (thumbs up or down), and
  "Support" opens the Feinwerk contact form. `feedback = 0` switches the question off (README, "Feedback and support").
- Short tips on other Feinwerk offers for administrators: one line under the admin page (it starts with the Feinwerk
  Plugins logo, on narrow screens only its round mark), one banner at the top of the start page of Matomo's
  administration and one box at the end of the Campaigns, Ecommerce overview and Goals overview reports. A tip can be
  hidden for 30 days, and `promotions = 0` switches them off (README, "Tips from Feinwerk").
- The tip under the admin page also recommends the Feinwerk Connector while it is not installed (not in Matomo for
  WordPress). Super users see it only where Matomo can install it from its Marketplace, and the link then opens the
  connector in their own Matomo Marketplace in the same tab.
- API errors for scripts and integrations come with a fitting HTTP status: 400 for invalid input, 404 for an unknown
  deletion.
- With Activity Log 6.1, its filter by item lists the deletions.
- The status labels and error texts use the colours of Matomo 6's theme; the plugin's settings in General settings are
  headed "Feinwerk Delete Visits Filter".
- Smaller fixes on the admin page: "1 website added." for a single website; the start of a deletion in the history is
  shown like the date ranges, and website names no longer wrap after every word; the total of the matching visits
  lines up with the counts above it; the confirmation field has room next to its label; long names of saved segments
  no longer run into the arrow of the list; the labels of the history filters start at the left edge of their field.

## 3.0.0-beta.1

- Works with Matomo 6. For Matomo 5, version 2.x stays available and is maintained in parallel.
- The history fits the page: its filters line up, and wide tables scroll inside their box instead of widening the page.
- The custom date range shows both calendars side by side.
- The field for website names has a label for screen readers.
- The list of saved segments offers "Please choose" as its empty entry, and empty values show a plain hyphen.
- The console job list and the Activity Log show date ranges as "2026-01-01 to 2026-01-31".
- Small wording improvements in the plugin description, on the admin page and in the settings help.

## 2.2.2

- This version is for Matomo 5. Matomo 6 support comes with the next major version of the plugin.

## 2.2.1

- Clearer segment input: a switch between the segment editor and text mode; in text mode a full-width field that grows with the segment, with a short syntax hint and a character counter (segments longer than 8,192 characters are refused right away).
- The admin page is now compatible with Matomo's dark mode and follows the theme chosen in Matomo (light, dark or automatic).
- Segments are shown in one consistent, quieter style in the progress area, the history and the confirmation dialog.
- Number column headers are right-aligned like their values.

## 2.2.0

- New admin page:
  - choose one website, several websites (also all whose name contains a text) or all websites;
  - pick the date range from presets such as "last 30 days" or "last year", or from a calendar;
  - use a saved segment, the segment editor or plain text.
- "Check matches" shows the matching visits per website, a sample of visits (with the visitor profile) and a link to the visitor log before anything is deleted.
- Deleting 10,000 or more visits must be confirmed by typing the number of visits.
- Running deletions show their progress, speed and estimated remaining time, can be cancelled and continue after the page is reloaded. If the console or the background task is already working, the page shows that instead of competing with it.
- New history of all deletions with filters, details, "use as template" and "try again" for failed deletions.
- The admin page refuses segment conditions without a value (for example from an untouched row in the segment editor); deliberate "is empty" conditions still work in the console and the API.
- Website names with special characters are shown correctly in previews and console output.
- Faster deletions with negated page conditions (e.g. `pageUrl!@/spam`) over long date ranges.
- The admin page responds faster when several deletions are queued: it no longer starts another queued deletion late in a request.

## 2.1.0

- Deletions now run as jobs that keep going in the background: if the page is closed or a request times out, the
  hourly background task continues where the deletion stopped. Very large deletions can run without a time limit via
  `./console feinwerkdeletevisitsfilter:work`.
- Date ranges are interpreted in each website's timezone (previously UTC). The affected days are marked for
  re-processing in the website's timezone, including roll-up websites.
- Data of other plugins that store visit data in their own log tables (e.g. forms, heatmaps, media, A/B tests) is
  deleted together with the visits.
- Console: `feinwerkdeletevisitsfilter:delete` accepts several website IDs or `all`, shows the matches per website,
  asks for confirmation (`--yes` skips it) and offers `--dry-run`. New commands `feinwerkdeletevisitsfilter:work`
  and `feinwerkdeletevisitsfilter:jobs`. Scripts that run `feinwerkdeletevisitsfilter:delete` without a terminal must
  now pass `--yes`; without it the command stops with an error instead of deleting.
- New API methods `previewDeletion`, `createJob`, `processJobs`, `getJob`, `getJobs`, `cancelJob`, `retryJob`.
  `findVisits` and `deleteVisits` are deprecated but keep working.
- New settings: background processing time per hour and default batch size.
- Finished, failed and cancelled deletions appear in the Activity Log if that plugin is installed.
- Requires Matomo 5.1 or newer.
- Segments that Matomo would silently change are refused: a segment without a valid condition (it would match every visit) and segments longer than 8192 characters.
- Segments with a negated page condition (e.g. `pageUrl!@/spam`) follow Matomo's report semantics: a visit matches if none of its actions matches (this also includes visits without any page view).
- The admin page now shows errors returned by the API (previously some errors were shown as a completed deletion with 0 visits), and the deprecated `deleteVisits` API method reports an error unless the deletion finished within the request.

## 2.0.0

- The plugin is now published by Feinwerk.io and has been renamed from `DeleteVisitsFilter` to
  `FeinwerkDeleteVisitsFilter`.
- API methods are now `FeinwerkDeleteVisitsFilter.countVisits` and `FeinwerkDeleteVisitsFilter.deleteVisits`.
- The console command is now `feinwerkdeletevisitsfilter:delete`; the previous name
  `deletevisitsfilter:delete` keeps working as an alias.
- Upgrading from `DeleteVisitsFilter`: activate `FeinwerkDeleteVisitsFilter`, then uninstall
  `DeleteVisitsFilter`. No settings or data need to be migrated.

## 1.0.0

- Initial release.
